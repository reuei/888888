<?php
/**
 * 安装向导
 * 检测环境 -> 配置数据库 -> 导入结构 -> 创建管理员账号
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$rootPath = dirname(__DIR__) . DIRECTORY_SEPARATOR;
$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// 加载授权码配置
$authConfigFile = __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
$authConfig = [];
if (is_file($authConfigFile)) {
    $authConfig = require $authConfigFile;
}
$authConfig = array_merge([
    'auth_code' => '',
    'max_attempts' => 5,
], $authConfig);
$authCodeRequired = !empty($authConfig['auth_code']);

// 已安装检测
if (file_exists($rootPath . 'config/database.php')) {
    $success = '系统已安装，如需重新安装请删除 config/database.php 后刷新页面。';
    $step = 'done';
}

// 环境检测
function checkEnv()
{
    global $rootPath;

    // 配置文件目录不存在时自动创建
    $configDir = $rootPath . 'config';
    if (!is_dir($configDir)) {
        @mkdir($configDir, 0755, true);
    }

    $items = [];
    $items['PHP >= 7.4'] = version_compare(PHP_VERSION, '7.4.0', '>=');
    $items['PDO 扩展'] = extension_loaded('pdo');
    $items['PDO_MySQL 扩展'] = extension_loaded('pdo_mysql');
    $items['GD 扩展'] = extension_loaded('gd');
    $items['mbstring 扩展'] = extension_loaded('mbstring');
    $items['JSON 扩展'] = extension_loaded('json');
    $items['openssl 扩展'] = extension_loaded('openssl');
    $items['config 可写'] = is_writable($rootPath . 'config');
    $items['runtime 可写'] = is_writable($rootPath . 'runtime') || @mkdir($rootPath . 'runtime', 0755, true);
    $items['public/uploads 可写'] = is_writable($rootPath . 'public/uploads') || @mkdir($rootPath . 'public/uploads', 0755, true);
    return $items;
}

function installDatabase($config, $adminUser, $adminPass)
{
    global $rootPath;

    // 连接数据库（不指定库）
    $dsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $config['hostname'], $config['hostport'] ?? 3306);
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // 创建数据库
    $dbname = $config['database'];
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbname}`");

    // 导入 SQL 结构
    $sql = file_get_contents($rootPath . 'install/install.sql');
    if (!$sql) {
        throw new Exception('install.sql 文件不存在或读取失败');
    }

    // 拆分 SQL 语句
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $statement) {
        if ($statement) {
            $pdo->exec($statement);
        }
    }

    // 创建管理员
    $hash = password_hash($adminPass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO jz_admin (username, password, role, status, create_time) VALUES (?, ?, 'super', 1, ?)");
    $stmt->execute([$adminUser, $hash, date('Y-m-d H:i:s')]);

    // 写入配置文件（ThinkPHP 8 格式）
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
                'prefix'          => $config['prefix'] ?? 'jz_',
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
    $authCodeInput = trim($_POST['auth_code'] ?? '');

    // 授权码验证
    if ($authCodeRequired) {
        $sessionKey = 'install_auth_attempts';
        $attempts = (int) ($_SESSION[$sessionKey] ?? 0);
        $maxAttempts = (int) $authConfig['max_attempts'];

        if ($maxAttempts > 0 && $attempts >= $maxAttempts) {
            $error = '授权码验证失败次数过多，请稍后重试';
        } elseif ($authCodeInput === '') {
            $error = '请输入安装授权码';
        } elseif (!hash_equals((string) $authConfig['auth_code'], $authCodeInput)) {
            $_SESSION[$sessionKey] = $attempts + 1;
            $error = '授权码错误，请核对后重新输入';
        }
    }

    if (!$error && (!$dbName || !$dbUser || !$adminPass)) {
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
                'prefix' => 'jz_',
            ];
            installDatabase($config, $adminUser, $adminPass);
            $success = '安装成功，请删除 install 目录后 <a href="/">点击访问首页</a> 或 <a href="/admin/dashboard">进入总站后台</a>';
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
    <title>鲸商城 Pro 安装向导</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #F8FAFC;
            color: #1F2937;
            line-height: 1.5;
        }
        .container {
            max-width: 720px;
            margin: 40px auto;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            overflow: hidden;
        }
        .header {
            background: #2563EB;
            color: #fff;
            padding: 24px;
            text-align: center;
        }
        .header h1 { font-size: 20px; font-weight: 600; }
        .body { padding: 24px; }
        .step-list {
            display: flex;
            margin-bottom: 24px;
            border-bottom: 1px solid #E2E8F0;
        }
        .step-list .step {
            flex: 1;
            text-align: center;
            padding: 12px;
            font-size: 14px;
            color: #64748B;
            border-bottom: 2px solid transparent;
        }
        .step-list .step.active {
            color: #2563EB;
            border-bottom-color: #2563EB;
            font-weight: 600;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 14px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #E2E8F0;
        }
        th { background: #F8FAFC; font-weight: 600; }
        .status-ok { color: #10B981; }
        .status-fail { color: #EF4444; }
        .form-group {
            margin-bottom: 16px;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-size: 14px;
            font-weight: 500;
        }
        input[type="text"], input[type="password"], input[type="number"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            font-size: 14px;
        }
        input:focus {
            outline: none;
            border-color: #2563EB;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #2563EB;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:disabled { background: #94A3B8; cursor: not-allowed; }
        .btn-block { width: 100%; }
        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .alert-error { background: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }
        .alert-success { background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
        .hint { color: #64748B; font-size: 12px; margin-top: 4px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>鲸商城 Pro 安装向导</h1>
        <p style="margin-top: 8px; opacity: 0.9;">版本 1.0.0 | 总站 + 商户双端后台</p>
    </div>
    <div class="body">
        <div class="step-list">
            <div class="step <?php echo $step == 1 ? 'active' : ''; ?>">1. 环境检测</div>
            <div class="step <?php echo $step == 2 ? 'active' : ''; ?>">2. 数据库配置</div>
            <div class="step <?php echo $step == 'done' ? 'active' : ''; ?>">3. 完成安装</div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <h3 style="margin-bottom: 16px; font-size: 16px;">运行环境检测</h3>
            <table>
                <tr><th>检测项</th><th>状态</th></tr>
                <?php foreach ($envItems as $name => $ok): ?>
                <tr>
                    <td><?php echo $name; ?></td>
                    <td class="<?php echo $ok ? 'status-ok' : 'status-fail'; ?>">
                        <?php echo $ok ? '通过' : '未通过'; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <a href="?step=2" class="btn btn-block" <?php echo $allPass ? '' : 'disabled'; ?>>下一步：配置数据库</a>
            <?php if (!$allPass): ?>
                <p class="hint" style="color: #EF4444; text-align: center; margin-top: 8px;">请修复未通过的检测项后刷新页面</p>
            <?php endif; ?>
        <?php elseif ($step == 2): ?>
            <form method="POST" action="?step=2">
                <?php if ($authCodeRequired): ?>
                <h3 style="margin-bottom: 16px; font-size: 16px;">授权码验证</h3>
                <div class="form-group">
                    <label>安装授权码</label>
                    <input type="text" name="auth_code" placeholder="请输入安装授权码" required autocomplete="off">
                    <p class="hint">授权码由系统提供，请输入正确的授权码后继续安装。错误次数过多将暂时锁定。</p>
                </div>
                <?php endif; ?>

                <h3 style="margin-bottom: 16px; font-size: 16px;">数据库配置</h3>
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
                    <input type="text" name="db_name" placeholder="如：jing_mall" required>
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

                <h3 style="margin: 24px 0 16px; font-size: 16px;">管理员账号</h3>
                <div class="form-group">
                    <label>管理员账号</label>
                    <input type="text" name="admin_user" value="admin" required>
                </div>
                <div class="form-group">
                    <label>管理员密码</label>
                    <input type="password" name="admin_pass" placeholder="请设置强密码" required>
                </div>
                <button type="submit" class="btn btn-block">立即安装</button>
            </form>
        <?php elseif ($step == 'done'): ?>
            <div style="text-align: center; padding: 20px;">
                <p style="font-size: 16px; margin-bottom: 16px;">安装已完成</p>
                <p class="hint">为了安全，请删除服务器上的 install 目录</p>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
