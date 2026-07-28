<?php
namespace app\controller;

/**
 * 控制器基类
 */
class BaseController
{
    protected $db;

    public function __construct()
    {
        $this->startSession();
        $this->initDatabase();
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
     * 初始化数据库连接
     */
    protected function initDatabase()
    {
        try {
            $config = include CONFIG_PATH . 'database.php';
            $dbConfig = $config['connections']['mysql'];
            $dsn = "mysql:host={$dbConfig['hostname']};port={$dbConfig['hostport']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";

            $this->db = new \PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException $e) {
            die('数据库连接失败: ' . $e->getMessage());
        }
    }

    /**
     * 渲染模板
     */
    protected function render($template, $data = [])
    {
        $siteSettings = $this->getSiteSettings();
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
     * 渲染用户中心视图（包裹用户布局）
     */
    protected function renderUser($template, $data = [], $activeMenu = 'dashboard', $title = '用户中心')
    {
        $user = $this->getUser();
        $siteSettings = $this->getSiteSettings();
        $toast = $this->getToast();

        $data['user'] = $user;
        $data['siteSettings'] = $siteSettings;

        $content = $this->render($template, $data);

        $templateFile = APP_PATH . 'view/user/layout.php';
        if (!file_exists($templateFile)) {
            return $content;
        }

        ob_start();
        include $templateFile;
        return ob_get_clean();
    }

    /**
     * 渲染管理后台视图（包裹管理布局）
     */
    protected function renderAdmin($template, $data = [], $activeMenu = 'dashboard', $title = '管理后台')
    {
        $admin = $this->getAdmin();
        $siteSettings = $this->getSiteSettings();
        $toast = $this->getToast();

        $data['admin'] = $admin;
        $data['siteSettings'] = $siteSettings;

        $content = $this->render($template, $data);

        $templateFile = APP_PATH . 'view/admin/layout.php';
        if (!file_exists($templateFile)) {
            return $content;
        }

        ob_start();
        include $templateFile;
        return ob_get_clean();
    }

    /**
     * 获取站点设置
     */
    protected function getSiteSettings()
    {
        try {
            $stmt = $this->db->query("SELECT `key`, `value` FROM qf_settings");
            $settings = [];
            foreach ($stmt->fetchAll() as $row) {
                $settings[$row['key']] = $row['value'];
            }
            return $settings;
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * JSON响应
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
            'data' => $data,
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
            'data' => [],
        ]);
    }

    /**
     * 检查用户登录
     */
    protected function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * 检查管理员登录
     */
    protected function requireAdminLogin()
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

    /**
     * 设置Flash消息
     */
    protected function setToast($message, $type = 'success')
    {
        $_SESSION['toast'] = [
            'message' => $message,
            'type' => $type,
        ];
    }

    /**
     * 获取Flash消息
     */
    protected function getToast()
    {
        $toast = $_SESSION['toast'] ?? null;
        unset($_SESSION['toast']);
        return $toast;
    }

    /**
     * 重定向
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * 获取当前用户
     */
    protected function getUser()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("SELECT id, username, email, balance, qq, phone, avatar, status, created_at FROM qf_users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            return null;
        }
    }

    /**
     * 获取当前管理员
     */
    protected function getAdmin()
    {
        if (!isset($_SESSION['admin_id'])) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("SELECT id, username, email FROM qf_admins WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            return null;
        }
    }
}