<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rootPath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

if (file_exists($rootPath . 'install/installed.lock')) {
    $success = '系统已安装，如需重新安装请删除 install/installed.lock 后刷新页面。';
    $step = 'done';
}

function checkEnv()
{
    global $rootPath;

    $configDir = $rootPath . 'config';
    if (!is_dir($configDir)) {
        @mkdir($configDir, 0755, true);
    }

    $items = [];
    $items['PHP >= 8.2'] = version_compare(PHP_VERSION, '8.2.0', '>=');
    $items['PDO 扩展'] = extension_loaded('pdo');
    $items['PDO_MySQL 扩展'] = extension_loaded('pdo_mysql');
    $items['GD 扩展'] = extension_loaded('gd');
    $items['mbstring 扩展'] = extension_loaded('mbstring');
    $items['JSON 扩展'] = extension_loaded('json');
    $items['openssl 扩展'] = extension_loaded('openssl');
    $items['session 扩展'] = extension_loaded('session');
    $items['fileinfo 扩展'] = extension_loaded('fileinfo');
    $items['curl 扩展'] = extension_loaded('curl');
    $items['zip 扩展'] = extension_loaded('zip');
    $items['config 可写'] = is_writable($rootPath . 'config') || @chmod($rootPath . 'config', 0755);
    $items['runtime 可写'] = is_writable($rootPath . 'runtime') || @mkdir($rootPath . 'runtime', 0755, true);
    $items['public 可写'] = is_writable($rootPath . 'public') || @chmod($rootPath . 'public', 0755);
    $items['安装目录可写'] = is_writable($rootPath . 'install');
    return $items;
}

function installDatabase($config, $adminUser, $adminPass)
{
    global $rootPath;

    $dsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $config['hostname'], $config['hostport'] ?? 3306);
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $dbname = $config['database'];
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbname}`");

    $sql = file_get_contents($rootPath . 'install/install.sql');
    if (!$sql) {
        throw new Exception('install.sql 文件不存在或读取失败');
    }

    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $statement) {
        if ($statement) {
            $pdo->exec($statement);
        }
    }

    $hash = password_hash($adminPass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO xw_admin (username, password, role, status, create_time) VALUES (?, ?, 'super', 1, ?)");
    $stmt->execute([$adminUser, $hash, date('Y-m-d H:i:s')]);

    $stmt = $pdo->prepare("INSERT INTO xw_config (cfg_key, cfg_value, cfg_group, description) VALUES (?, ?, 'base', '站点名称')");
    $stmt->execute(['site_name', '玄武发卡']);
    $stmt = $pdo->prepare("INSERT INTO xw_config (cfg_key, cfg_value, cfg_group, description) VALUES (?, ?, 'base', '站点标题')");
    $stmt->execute(['site_title', '玄武发卡网 - 专业的数字点卡交易平台']);
    $stmt = $pdo->prepare("INSERT INTO xw_config (cfg_key, cfg_value, cfg_group, description) VALUES (?, ?, 'base', '站点关键词')");
    $stmt->execute(['site_keywords', '发卡网,点卡,游戏点卡,视频会员']);
    $stmt = $pdo->prepare("INSERT INTO xw_config (cfg_key, cfg_value, cfg_group, description) VALUES (?, ?, 'base', '站点描述')");
    $stmt->execute(['site_description', '玄武发卡网是专业的数字点卡交易平台，提供游戏点卡、视频会员、音乐会员等各类数字商品。']);
    $stmt = $pdo->prepare("INSERT INTO xw_config (cfg_key, cfg_value, cfg_group, description) VALUES (?, ?, 'base', '备案号')");
    $stmt->execute(['site_icp', '京ICP备12345678号']);
    $stmt = $pdo->prepare("INSERT INTO xw_config (cfg_key, cfg_value, cfg_group, description) VALUES (?, ?, 'base', '版权信息')");
    $stmt->execute(['site_copyright', '© 2026 玄武发卡网']);

    $tpConfig = [
        'default'         => 'mysql',
        'time_query_rule' => [],
        'auto_timestamp'  => true,
        'datetime_format' => 'Y-m-d H:i:s',
        'datetime_field'  => '',
        'connections'     => [
            'mysql' => [
                'type'            => $config['type'] ?? 'mysql',
                'hostname'        => $config['hostname'],
                'database'        => $config['database'],
                'username'        => $config['username'],
                'password'        => $config['password'],
                'hostport'        => $config['hostport'] ?? 3306,
                'params'          => [],
                'charset'         => $config['charset'] ?? 'utf8mb4',
                'prefix'          => $config['prefix'] ?? 'xw_',
                'deploy'          => 0,
                'rw_separate'     => false,
                'master_num'      => 1,
                'slave_no'        => '',
                'fields_strict'   => true,
                'break_reconnect' => false,
                'trigger_sql'     => true,
                'fields_cache'    => false,
            ],
        ],
    ];
    $configContent = "<?php\nreturn " . var_export($tpConfig, true) . ";\n";
    file_put_contents($rootPath . 'config/database.php', $configContent);

    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 2) {
    $dbHost = $_POST['db_host'] ?? '127.0.0.1';
    $dbPort = $_POST['db_port'] ?? 3306;
    $dbName = $_POST['db_name'] ?? '';
    $dbUser = $_POST['db_user'] ?? '';
    $dbPass = $_POST['db_pass'] ?? '';
    $adminUser = $_POST['admin_user'] ?? 'admin';
    $adminPass = $_POST['admin_pass'] ?? '';

    if (!$dbName || !$dbUser || !$adminPass) {
        $error = '请填写完整的数据库信息和管理员密码';
    }

    if (!$error) {
        try {
            $config = [
                'type' => 'mysql',
                'hostname' => $dbHost,
                'hostport' => $dbPort,
                'database' => $dbName,
                'username' => $dbUser,
                'password' => $dbPass,
                'charset' => 'utf8mb4',
                'prefix' => 'xw_',
            ];
            installDatabase($config, $adminUser, $adminPass);
            @file_put_contents($rootPath . 'install/installed.lock', date('Y-m-d H:i:s'));
            $success = '安装成功，请删除 install 目录后 <a href="/">点击访问首页</a> 或 <a href="/admin/login">进入后台</a>';
            $step = 'done';
        } catch (Exception $e) {
            $error = '安装失败：' . $e->getMessage();
        }
    }
}

$envItems = checkEnv();
$allPass = !in_array(false, $envItems, true);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>玄武发卡网 v1.0.4 安装向导</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --bg: #0f172a;
            --card-bg: rgba(255, 255, 255, 0.95);
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            min-height: 100vh;
            background: linear-gradient(-45deg, #0f172a, #1e1b4b, #312e81, #0f172a);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: #1f2937;
            line-height: 1.6;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .bg-shapes {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }
        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.35;
            animation: float 20s infinite ease-in-out;
        }
        .shape:nth-child(1) { width: 300px; height: 300px; background: #6366f1; top: -100px; left: -100px; animation-delay: 0s; }
        .shape:nth-child(2) { width: 400px; height: 400px; background: #ec4899; bottom: -150px; right: -100px; animation-delay: -5s; }
        .shape:nth-child(3) { width: 250px; height: 250px; background: #06b6d4; top: 40%; left: 60%; animation-delay: -10s; }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }
        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 760px;
            background: var(--card-bg);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 40px 32px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(circle at top right, rgba(255,255,255,0.2) 0%, transparent 40%);
        }
        .header h1 { font-size: 28px; font-weight: 700; position: relative; letter-spacing: -0.5px; }
        .header p { margin-top: 10px; opacity: 0.9; font-size: 15px; position: relative; }
        .body { padding: 32px; }
        .step-list {
            display: flex;
            margin-bottom: 32px;
            position: relative;
        }
        .step-list::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 16%;
            right: 16%;
            height: 3px;
            background: #e2e8f0;
            border-radius: 3px;
            z-index: 0;
        }
        .step-list .step {
            flex: 1;
            text-align: center;
            padding: 0 8px;
            font-size: 13px;
            color: #94a3b8;
            position: relative;
            z-index: 1;
            transition: color 0.3s ease;
        }
        .step-list .step::before {
            content: attr(data-step);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            margin: 0 auto 10px;
            background: #fff;
            border: 3px solid #e2e8f0;
            border-radius: 50%;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .step-list .step.active {
            color: var(--primary);
            font-weight: 600;
        }
        .step-list .step.active::before {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            transform: scale(1.1);
            box-shadow: 0 0 0 6px rgba(99, 102, 241, 0.15);
        }
        .step-list .step.done {
            color: var(--success);
        }
        .step-list .step.done::before {
            background: var(--success);
            border-color: var(--success);
            color: #fff;
        }
        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title::before {
            content: '';
            width: 4px;
            height: 22px;
            background: var(--primary);
            border-radius: 2px;
        }
        .env-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 24px;
            font-size: 14px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            animation: fadeIn 0.6s ease 0.2s both;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .env-table th, .env-table td {
            padding: 14px 18px;
            text-align: left;
        }
        .env-table th {
            background: #f8fafc;
            font-weight: 600;
            color: #475569;
        }
        .env-table tr:not(:last-child) td { border-bottom: 1px solid #f1f5f9; }
        .env-table tr:nth-child(even) td { background: #fafafa; }
        .env-table td { transition: background 0.2s; }
        .env-table tr:hover td { background: #f1f5f9; }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-ok { background: #d1fae5; color: #065f46; }
        .status-fail { background: #fee2e2; color: #991b1b; }
        .status-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .form-group { margin-bottom: 20px; animation: fadeIn 0.5s ease both; }
        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #334155;
        }
        input[type="text"], input[type="password"], input[type="number"] {
            width: 100%;
            padding: 13px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            background: #fff;
            transition: all 0.25s ease;
        }
        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            transform: translateY(-1px);
        }
        .hint {
            color: #64748b;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 24px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35);
            position: relative;
            overflow: hidden;
        }
        .btn::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.25), transparent);
            transition: left 0.5s ease;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(99, 102, 241, 0.45); }
        .btn:hover::after { left: 100%; }
        .btn:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .btn-block { width: 100%; }
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: shakeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes shakeIn {
            0% { opacity: 0; transform: translateX(-20px); }
            100% { opacity: 1; transform: translateX(0); }
        }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-info { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
        .alert-icon { font-size: 18px; flex-shrink: 0; }
        .done-box {
            text-align: center;
            padding: 40px 20px;
            animation: scaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes scaleIn {
            0% { opacity: 0; transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }
        .done-box .icon {
            width: 72px;
            height: 72px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 36px;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35);
            animation: checkPop 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s both;
        }
        @keyframes checkPop {
            0% { transform: scale(0); }
            100% { transform: scale(1); }
        }
        .done-box h3 { font-size: 22px; color: #1e293b; margin-bottom: 12px; }
        .done-box p { color: #64748b; }
        .panel {
            background: #f8fafc;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
        }
        @media (max-width: 640px) {
            .body { padding: 24px; }
            .header { padding: 28px 20px; }
            .header h1 { font-size: 22px; }
            .step-list::before { left: 12%; right: 12%; }
        }
    </style>
</head>
<body>
<div class="bg-shapes">
    <div class="shape"></div>
    <div class="shape"></div>
    <div class="shape"></div>
</div>
<div class="container">
    <div class="header">
        <h1>玄武发卡网 v1.0.4 安装向导</h1>
        <p>专业的数字点卡交易平台 | 总站 + 用户中心 + 后台管理</p>
    </div>
    <div class="body">
        <div class="step-list">
            <div class="step <?php echo $step == 1 ? 'active' : ($step == 2 || $step == 'done' ? 'done' : ''); ?>" data-step="1">环境检测</div>
            <div class="step <?php echo $step == 2 ? 'active' : ($step == 'done' ? 'done' : ''); ?>" data-step="2">数据库配置</div>
            <div class="step <?php echo $step == 'done' ? 'active done' : ''; ?>" data-step="3">完成安装</div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><span class="alert-icon">&#9888;</span><div><?php echo $error; ?></div></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><span class="alert-icon">&#10003;</span><div><?php echo $success; ?></div></div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <div class="section-title">运行环境检测</div>
            <table class="env-table">
                <tr><th>检测项</th><th>状态</th></tr>
                <?php foreach ($envItems as $name => $ok): ?>
                <tr>
                    <td><?php echo $name; ?></td>
                    <td>
                        <span class="status-badge <?php echo $ok ? 'status-ok' : 'status-fail'; ?>">
                            <?php echo $ok ? '通过' : '未通过'; ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <a href="?step=2" class="btn btn-block" <?php echo $allPass ? '' : 'disabled'; ?>>下一步：配置数据库</a>
            <?php if (!$allPass): ?>
                <p class="hint" style="color: var(--danger); text-align: center; margin-top: 12px;">请修复未通过的检测项后刷新页面</p>
            <?php endif; ?>
            <div class="alert alert-info" style="margin-top: 20px;">
                <span class="alert-icon">&#9432;</span>
                <div>
                    <strong>虚拟主机部署提示：</strong>请将站点运行目录（web 根目录）设置为 <code>public</code>；PHP 版本选择 <code>8.2 及以上</code>；上传 zip 包后解压，访问 <code>/install</code> 完成安装。
                </div>
            </div>
        <?php elseif ($step == 2): ?>
            <form method="POST" action="?step=2" id="installForm">
                <div class="panel">
                    <div class="section-title">数据库配置</div>
                    <div class="form-group">
                        <label>数据库主机</label>
                        <input type="text" name="db_host" value="127.0.0.1" required>
                    </div>
                    <div class="form-group">
                        <label>数据库端口</label>
                        <input type="number" name="db_port" value="3306" required>
                    </div>
                    <div class="form-group">
                        <label>数据库名</label>
                        <input type="text" name="db_name" placeholder="如：xuanwu_card" required>
                        <p class="hint">若数据库不存在，安装程序会自动创建</p>
                    </div>
                    <div class="form-group">
                        <label>数据库用户名</label>
                        <input type="text" name="db_user" placeholder="如：root" required>
                    </div>
                    <div class="form-group">
                        <label>数据库密码</label>
                        <input type="password" name="db_pass" placeholder="">
                    </div>
                </div>

                <div class="panel">
                    <div class="section-title">管理员账号</div>
                    <div class="form-group">
                        <label>管理员账号</label>
                        <input type="text" name="admin_user" value="admin" required>
                    </div>
                    <div class="form-group">
                        <label>管理员密码</label>
                        <input type="password" name="admin_pass" placeholder="请设置强密码" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-block" id="submitBtn">立即安装</button>
            </form>
        <?php elseif ($step == 'done'): ?>
            <div class="done-box">
                <div class="icon">&#10003;</div>
                <h3>安装已完成</h3>
                <p>为了安全，请删除服务器上的 install 目录</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<script>
    document.getElementById('installForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> 安装中，请稍候...';
        btn.style.opacity = '0.8';
    });
</script>
<style>
    .spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,0.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
</body>
</html>
