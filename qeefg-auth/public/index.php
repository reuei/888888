<?php
/**
 * QEEFG授权站系统 v3.0
 * 入口文件 - 修复所有路由和目录绑定问题
 */

// 错误报告设置
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 设置时区
date_default_timezone_set('Asia/Shanghai');

// 检查PHP版本
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>环境错误</title></head><body><h1>PHP版本错误</h1><p>系统需要 PHP >= 8.1，当前版本：' . PHP_VERSION . '</p></body></html>';
    exit;
}

// 定义常量
define('ROOT_PATH', dirname(__DIR__) . '/');
define('APP_PATH', ROOT_PATH . 'app/');
define('CONFIG_PATH', ROOT_PATH . 'config/');
define('PUBLIC_PATH', __DIR__ . '/');

// 检查目录绑定是否正确
if (!file_exists(ROOT_PATH . 'install.sql')) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>目录绑定错误</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
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
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .icon { font-size: 72px; margin-bottom: 25px; }
        h1 { color: #333; margin-bottom: 25px; font-size: 32px; }
        p { color: #666; line-height: 1.8; margin-bottom: 20px; font-size: 16px; }
        .step-box {
            background: #fff5f5;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            text-align: left;
            border-left: 4px solid #ff6b6b;
        }
        .step-title {
            color: #ff6b6b;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 18px;
        }
        .cmd {
            background: #2d2d2d;
            color: #50fa7b;
            padding: 15px 20px;
            border-radius: 8px;
            font-family: "Monaco", "Menlo", monospace;
            margin: 10px 0;
            display: block;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
            color: white;
            padding: 16px 40px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 10px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 107, 107, 0.4);
        }
        .info {
            background: #f8f9ff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-family: monospace;
            font-size: 13px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⚠️</div>
        <h1>目录绑定错误</h1>
        <p>网站运行目录未正确设置，请按照以下步骤操作：</p>
        
        <div class="step-box">
            <div class="step-title">正确配置方法</div>
            <p>请将网站根目录绑定到 <strong>/public</strong> 目录</p>
            <p>当前检测路径: <code>' . ROOT_PATH . '</code></p>
        </div>
        
        <div class="step-box">
            <div class="step-title">Nginx配置示例</div>
            <code class="cmd">root /var/www/qeefg-auth/public;</code>
            <code class="cmd">try_files $uri $uri/ /index.php?$query_string;</code>
        </div>
        
        <div class="step-box">
            <div class="step-title">Apache配置示例</div>
            <p>确保网站根目录指向 <strong>public</strong> 文件夹</p>
        </div>
        
        <div class="info">
            项目结构:<br>
            qeefg-auth/<br>
            ├── app/        # 应用代码<br>
            ├── config/     # 配置文件<br>
            ├── public/     # ← 网站根目录应该指向这里<br>
            │   └── index.php<br>
            └── install.sql<br>
        </div>
        
        <a href="javascript:location.reload();" class="btn">重新检测</a>
    </div>
</body>
</html>';
    exit;
}

// 自动加载类
spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class) . '.php';
    
    if (file_exists(APP_PATH . $file)) {
        require APP_PATH . $file;
    } elseif (file_exists(ROOT_PATH . $file)) {
        require ROOT_PATH . $file;
    }
});

// 简单路由系统
$uri = $_SERVER['REQUEST_URI'];
$uri = parse_url($uri, PHP_URL_PATH);

// 移除index.php前缀
$uri = str_replace('/index.php', '', $uri);
$uri = rtrim($uri, '/') ?: '/';

// 路由映射表
$routes = [
    '/' => 'Index@index',
    '/index' => 'Index@index',
    
    // 登录注册
    '/login' => 'User@login',
    '/register' => 'User@register',
    '/logout' => 'User@logout',
    
    // 用户中心
    '/user/dashboard' => 'User@dashboard',
    '/user/workplace' => 'User@workplace',
    '/user/products' => 'User@products',
    '/user/my-products' => 'User@myProducts',
    '/user/balance' => 'User@balance',
    '/user/settings' => 'User@settings',
    '/user/feedback' => 'User@feedback',
    
    // 后台管理
    '/admin/login' => 'Admin@login',
    '/admin/logout' => 'Admin@logout',
    '/admin/dashboard' => 'Admin@dashboard',
    '/admin/users' => 'Admin@users',
    '/admin/products' => 'Admin@products',
    '/admin/licenses' => 'Admin@licenses',
    '/admin/orders' => 'Admin@orders',
    '/admin/settings' => 'Admin@settings',
    
    // 其他页面
    '/license-query' => 'Index@licenseQuery',
    '/documents' => 'Index@documents',
];

// 处理POST请求路由
$postRoutes = [
    '/user/dologin' => 'User@dologin',
    '/user/doregister' => 'User@doregister',
    '/user/updateSettings' => 'User@updateSettings',
    '/user/submitFeedback' => 'User@submitFeedback',
    '/admin/dologin' => 'Admin@dologin',
    '/admin/saveSettings' => 'Admin@saveSettings',
];

// 匹配路由
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($postRoutes[$uri])) {
    list($controller, $action) = explode('@', $postRoutes[$uri]);
    $controllerClass = "\\app\\controller\\{$controller}";
} elseif (isset($routes[$uri])) {
    list($controller, $action) = explode('@', $routes[$uri]);
    $controllerClass = "\\app\\controller\\{$controller}";
} else {
    // 404页面
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>404 - 页面不存在</title>
    <style>
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
            padding: 60px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .icon { font-size: 100px; margin-bottom: 30px; }
        h1 { color: #333; margin-bottom: 20px; font-size: 48px; }
        p { color: #666; line-height: 1.8; margin-bottom: 30px; font-size: 18px; }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 40px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            transition: transform 0.3s;
        }
        .btn:hover { transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔍</div>
        <h1>404</h1>
        <p>抱歉，您访问的页面不存在</p>
        <a href="/" class="btn">返回首页</a>
    </div>
</body>
</html>';
    exit;
}

// 实例化控制器并执行方法
try {
    if (class_exists($controllerClass)) {
        $instance = new $controllerClass();
        if (method_exists($instance, $action)) {
            echo $instance->$action();
        } else {
            throw new Exception("方法不存在: {$action}");
        }
    } else {
        throw new Exception("控制器不存在: {$controller}");
    }
} catch (Exception $e) {
    // 错误处理页面
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>服务器错误</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a5a 100%);
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
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .icon { font-size: 72px; margin-bottom: 25px; }
        h1 { color: #333; margin-bottom: 25px; font-size: 32px; }
        p { color: #666; line-height: 1.8; margin-bottom: 20px; font-size: 16px; }
        .error-info {
            background: #fff5f5;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            font-family: monospace;
            font-size: 14px;
            color: #ff6b6b;
            text-align: left;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 40px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🚨</div>
        <h1>服务器错误</h1>
        <p>系统遇到了一个错误，请稍后重试</p>
        <div class="error-info">
            ' . htmlspecialchars($e->getMessage()) . '<br>
            File: ' . htmlspecialchars($e->getFile()) . '<br>
            Line: ' . $e->getLine() . '
        </div>
        <a href="/" class="btn">返回首页</a>
    </div>
</body>
</html>';
    exit;
}