<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Shanghai');

define('ROOT_PATH', dirname(__FILE__) . '/');
define('CONFIG_PATH', ROOT_PATH . 'config/');

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$config = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $config = $_POST;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QEEFG授权站 - 安装程序</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 16px;
            padding: 50px;
            max-width: 650px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo h1 {
            color: #667eea;
            font-size: 32px;
            margin-bottom: 10px;
        }
        .logo p {
            color: #666;
            font-size: 14px;
        }
        .steps {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0 20px;
            position: relative;
        }
        .step::before {
            content: '';
            position: absolute;
            left: -20px;
            top: 12px;
            width: 20px;
            height: 2px;
            background: #ddd;
        }
        .step:first-child::before { display: none; }
        .step.active::before { background: #667eea; }
        .step-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #ddd;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            transition: all 0.3s;
        }
        .step.active .step-num { background: #667eea; }
        .step.done .step-num { background: #10b981; }
        .step-text { font-size: 13px; color: #666; }
        .step.active .step-text { color: #333; font-weight: bold; }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }
        .check-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .check-item:last-child { border-bottom: none; }
        .check-icon {
            font-size: 20px;
            margin-right: 12px;
        }
        .check-item.success .check-icon { color: #10b981; }
        .check-item.error .check-icon { color: #ef4444; }
        .check-text { flex: 1; }
        .check-status { font-weight: bold; }
        .check-item.success .check-status { color: #10b981; }
        .check-item.error .check-status { color: #ef4444; }
        .success-box {
            text-align: center;
            padding: 40px 0;
        }
        .success-icon { font-size: 80px; margin-bottom: 20px; }
        .success-box h2 { color: #333; margin-bottom: 15px; }
        .success-box p { color: #666; line-height: 1.8; margin-bottom: 25px; }
        .info-box {
            background: #f8f9ff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        .info-box h4 { color: #667eea; margin-bottom: 10px; }
        .info-box code {
            background: #e5e7eb;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 13px;
        }
        .warning {
            background: #fffbeb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #d97706;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>QEEFG授权站 v4.0</h1>
            <p>专业软件授权管理平台</p>
        </div>
        
        <div class="steps">
            <div class="step <?php echo $step >= 1 ? ($step > 1 ? 'done' : 'active') : ''; ?>">
                <div class="step-num">1</div>
                <div class="step-text">环境检测</div>
            </div>
            <div class="step <?php echo $step >= 2 ? ($step > 2 ? 'done' : 'active') : ''; ?>">
                <div class="step-num">2</div>
                <div class="step-text">数据库配置</div>
            </div>
            <div class="step <?php echo $step >= 3 ? 'active' : ''; ?>">
                <div class="step-num">3</div>
                <div class="step-text">完成安装</div>
            </div>
        </div>

        <?php if ($step == 1): ?>
            <h2 style="color:#333; margin-bottom:20px;">环境检测</h2>
            <div class="check-item <?php echo version_compare(PHP_VERSION, '8.1.0', '>=') ? 'success' : 'error'; ?>">
                <div class="check-icon"><?php echo version_compare(PHP_VERSION, '8.1.0', '>=') ? '✓' : '✗'; ?></div>
                <div class="check-text">PHP版本要求</div>
                <div class="check-status"><?php echo PHP_VERSION; ?> <?php echo version_compare(PHP_VERSION, '8.1.0', '>=') ? '(满足)' : '(不满足)'; ?></div>
            </div>
            <div class="check-item <?php echo extension_loaded('pdo_mysql') ? 'success' : 'error'; ?>">
                <div class="check-icon"><?php echo extension_loaded('pdo_mysql') ? '✓' : '✗'; ?></div>
                <div class="check-text">PDO MySQL扩展</div>
                <div class="check-status"><?php echo extension_loaded('pdo_mysql') ? '已启用' : '未启用'; ?></div>
            </div>
            <div class="check-item <?php echo extension_loaded('session') ? 'success' : 'error'; ?>">
                <div class="check-icon"><?php echo extension_loaded('session') ? '✓' : '✗'; ?></div>
                <div class="check-text">Session扩展</div>
                <div class="check-status"><?php echo extension_loaded('session') ? '已启用' : '未启用'; ?></div>
            </div>
            <div class="check-item <?php echo is_writable(CONFIG_PATH) ? 'success' : 'error'; ?>">
                <div class="check-icon"><?php echo is_writable(CONFIG_PATH) ? '✓' : '✗'; ?></div>
                <div class="check-text">config/目录可写</div>
                <div class="check-status"><?php echo is_writable(CONFIG_PATH) ? '是' : '否'; ?></div>
            </div>
            
            <div class="warning">
                ⚠️ 请确保网站根目录已正确绑定到 <code>public/</code> 文件夹
            </div>
            
            <form method="get" action="install.php">
                <input type="hidden" name="step" value="2">
                <button type="submit" class="btn" <?php if (version_compare(PHP_VERSION, '8.1.0', '<') || !extension_loaded('pdo_mysql') || !is_writable(CONFIG_PATH)) echo 'disabled'; ?>>
                    继续安装
                </button>
            </form>
        <?php elseif ($step == 2): ?>
            <h2 style="color:#333; margin-bottom:20px;">数据库配置</h2>
            <?php if (!empty($_POST)): ?>
                <?php
                $error = '';
                try {
                    $dsn = "mysql:host={$_POST['hostname']};port={$_POST['hostport']};charset={$_POST['charset']}";
                    $pdo = new PDO($dsn, $_POST['username'], $_POST['password'], [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]);
                    
                    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$_POST['database']}` DEFAULT CHARACTER SET {$_POST['charset']}");
                    $pdo->exec("USE `{$_POST['database']}`");
                    
                    $sql = file_get_contents(ROOT_PATH . 'install.sql');
                    $queries = explode(';', $sql);
                    foreach ($queries as $query) {
                        $query = trim($query);
                        if (!empty($query)) {
                            $pdo->exec($query);
                        }
                    }
                    
                    $configContent = "<?php\nreturn [\n    'connections' => [\n        'mysql' => [\n            'hostname' => '{$_POST['hostname']}',\n            'database' => '{$_POST['database']}',\n            'username' => '{$_POST['username']}',\n            'password' => '{$_POST['password']}',\n            'hostport' => '{$_POST['hostport']}',\n            'charset' => '{$_POST['charset']}',\n            'prefix' => 'qf_'\n        ]\n    ],\n    'site' => [\n        'name' => 'QEEFG授权站',\n        'title' => 'QEEFG授权站 - 专业软件授权管理平台',\n        'description' => 'QEEFG授权站是一个专业的软件授权管理平台',\n        'keywords' => '授权,许可证,软件授权,授权管理',\n        'url' => 'https://auth.qeefg.com',\n        'email' => 'support@qeefg.com',\n        'qq' => '123456789',\n        'theme_color' => '#667eea'\n    ],\n    'auth' => [\n        'session_key' => 'qeefg_auth_session',\n        'token_expire' => 86400\n    ]\n];";
                    
                    file_put_contents(CONFIG_PATH . 'database.php', $configContent);
                    
                    header('Location: install.php?step=3');
                    exit;
                } catch (PDOException $e) {
                    $error = '数据库连接失败: ' . $e->getMessage();
                }
                ?>
                <?php if ($error): ?>
                    <div style="background:#fee2e2; padding:15px; border-radius:8px; color:#dc2626; margin-bottom:20px;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <form method="post" action="install.php?step=2">
                <div class="form-group">
                    <label>数据库主机</label>
                    <input type="text" name="hostname" value="<?php echo $config['hostname'] ?? '127.0.0.1'; ?>" required>
                </div>
                <div class="form-group">
                    <label>数据库端口</label>
                    <input type="text" name="hostport" value="<?php echo $config['hostport'] ?? '3306'; ?>" required>
                </div>
                <div class="form-group">
                    <label>数据库名</label>
                    <input type="text" name="database" value="<?php echo $config['database'] ?? 'qeefg_auth'; ?>" required>
                </div>
                <div class="form-group">
                    <label>数据库用户名</label>
                    <input type="text" name="username" value="<?php echo $config['username'] ?? 'root'; ?>" required>
                </div>
                <div class="form-group">
                    <label>数据库密码</label>
                    <input type="password" name="password" value="<?php echo $config['password'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label>字符集</label>
                    <input type="text" name="charset" value="<?php echo $config['charset'] ?? 'utf8mb4'; ?>" required>
                </div>
                <button type="submit" class="btn">安装数据库</button>
            </form>
        <?php elseif ($step == 3): ?>
            <div class="success-box">
                <div class="success-icon">🎉</div>
                <h2>安装成功！</h2>
                <p>QEEFG授权站已成功安装完成</p>
                
                <div class="info-box">
                    <h4>管理员账户</h4>
                    <p>用户名: <code>admin</code></p>
                    <p>密码: <code>password</code></p>
                </div>
                
                <div class="info-box">
                    <h4>访问地址</h4>
                    <p>前台首页: <code>/</code></p>
                    <p>用户登录: <code>/login</code></p>
                    <p>后台管理: <code>/admin/login</code></p>
                </div>
                
                <a href="/" class="btn">进入首页</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
