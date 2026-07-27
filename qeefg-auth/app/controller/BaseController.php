<?php
namespace app\controller;

/**
 * 控制器基类 - 稳定可靠版
 */
class BaseController
{
    protected $db;
    protected $config;
    
    public function __construct()
    {
        $this->startSession();
        $this->loadConfig();
        $this->initDatabase();
    }
    
    /**
     * 启动Session
     */
    protected function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 86400,
                'path' => '/',
                'domain' => '',
                'secure' => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }
    }
    
    /**
     * 加载配置
     */
    protected function loadConfig()
    {
        $configFile = CONFIG_PATH . 'database.php';
        if (file_exists($configFile)) {
            $this->config = include $configFile;
        } else {
            $this->config = [
                'connections' => [
                    'mysql' => [
                        'hostname' => '127.0.0.1',
                        'database' => 'qeefg_auth',
                        'username' => 'root',
                        'password' => '',
                        'hostport' => '3306',
                        'charset' => 'utf8mb4',
                        'prefix' => 'qf_'
                    ]
                ]
            ];
        }
    }
    
    /**
     * 初始化数据库连接
     */
    protected function initDatabase()
    {
        try {
            $dbConfig = $this->config['connections']['mysql'];
            $dsn = "mysql:host={$dbConfig['hostname']};port={$dbConfig['hostport']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
            
            $this->db = new \PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (\PDOException $e) {
            header('Content-Type: text/html; charset=utf-8');
            echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>数据库连接失败</title>
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
        .error-info {
            background: #2d2d2d;
            color: #ff6b6b;
            padding: 15px;
            border-radius: 8px;
            font-family: monospace;
            margin-top: 10px;
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
        <div class="icon">🗄️</div>
        <h1>数据库连接失败</h1>
        <p>无法连接到数据库，请检查配置信息</p>
        
        <div class="step-box">
            <div class="step-title">解决方法</div>
            <p>1. 确保MySQL服务已启动</p>
            <p>2. 检查数据库配置文件: <strong>config/database.php</strong></p>
            <p>3. 确保数据库已创建并导入了 <strong>install.sql</strong></p>
            <div class="error-info">' . htmlspecialchars($e->getMessage()) . '</div>
        </div>
        
        <a href="/install.php" class="btn">运行安装程序</a>
    </div>
</body>
</html>';
            exit;
        }
    }
    
    /**
     * 渲染模板
     */
    protected function render($template, $data = [])
    {
        extract($data);
        $templateFile = APP_PATH . 'view/' . $template . '.php';
        
        if (!file_exists($templateFile)) {
            return $this->showError("模板文件不存在: {$template}");
        }
        
        ob_start();
        include $templateFile;
        return ob_get_clean();
    }
    
    /**
     * 返回JSON响应
     */
    protected function json($data)
    {
        header('Content-Type: application/json; charset=utf-8');
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 成功响应
     */
    protected function success($data = [], $msg = '操作成功')
    {
        return $this->json([
            'code' => 200,
            'msg' => $msg,
            'data' => $data
        ]);
    }
    
    /**
     * 失败响应
     */
    protected function error($msg = '操作失败')
    {
        return $this->json([
            'code' => 400,
            'msg' => $msg,
            'data' => []
        ]);
    }
    
    /**
     * 检查登录
     */
    protected function checkLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * 检查管理员登录
     */
    protected function checkAdminLogin()
    {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
    }
    
    /**
     * 获取POST数据
     */
    protected function post($key = null, $default = null)
    {
        $data = $_POST;
        
        // 处理JSON格式请求
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true) ?: [];
        }
        
        if ($key === null) {
            return $data;
        }
        return $data[$key] ?? $default;
    }
    
    /**
     * 获取GET数据
     */
    protected function get($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }
    
    /**
     * 获取当前用户信息
     */
    protected function getUser()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        $stmt = $this->db->prepare("SELECT id, username, email, balance, qq, phone FROM qf_users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    
    /**
     * 获取当前管理员信息
     */
    protected function getAdmin()
    {
        if (!isset($_SESSION['admin_id'])) {
            return null;
        }
        
        $stmt = $this->db->prepare("SELECT id, username, email FROM qf_admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        return $stmt->fetch();
    }
    
    /**
     * 显示错误页面
     */
    protected function showError($message)
    {
        header('Content-Type: text/html; charset=utf-8');
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>错误</title>
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
            max-width: 500px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .icon { font-size: 72px; margin-bottom: 25px; }
        h1 { color: #333; margin-bottom: 25px; font-size: 32px; }
        p { color: #666; line-height: 1.8; margin-bottom: 30px; font-size: 16px; }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 40px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🚨</div>
        <h1>发生错误</h1>
        <p>' . htmlspecialchars($message) . '</p>
        <a href="/" class="btn">返回首页</a>
    </div>
</body>
</html>';
    }
    
    /**
     * 获取分页数据
     */
    protected function paginate($sql, $params = [], $pageSize = 10)
    {
        $page = (int)($this->get('page', 1));
        $offset = ($page - 1) * $pageSize;
        
        // 获取总数
        $countSql = preg_replace('/SELECT.*FROM/i', 'SELECT COUNT(*) FROM', $sql);
        $countSql = preg_replace('/ORDER BY.*$/i', '', $countSql);
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        
        // 获取列表
        $sql .= " LIMIT {$offset}, {$pageSize}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $list = $stmt->fetchAll();
        
        // 计算分页
        $totalPages = ceil($total / $pageSize);
        
        return [
            'list' => $list,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => $totalPages
        ];
    }
}