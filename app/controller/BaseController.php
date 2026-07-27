<?php
namespace app\controller;

/**
 * 控制器基类 - 简化版
 */
class BaseController
{
    protected $db;
    
    public function __construct()
    {
        $this->initDatabase();
        $this->startSession();
    }
    
    /**
     * 初始化数据库连接
     */
    protected function initDatabase()
    {
        try {
            $config = include ROOT_PATH . 'config/database.php';
            $dsn = "mysql:host={$config['connections']['mysql']['hostname']};";
            $dsn .= "dbname={$config['connections']['mysql']['database']};";
            $dsn .= "charset={$config['connections']['mysql']['charset']}";
            
            $this->db = new PDO($dsn, 
                $config['connections']['mysql']['username'],
                $config['connections']['mysql']['password']
            );
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('数据库连接失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 启动Session
     */
    protected function startSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
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
            return "模板文件不存在: {$template}";
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
        header('Content-Type: application/json');
        return json_encode($data);
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
        if ($key === null) {
            return $_POST;
        }
        return $_POST[$key] ?? $default;
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
}