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
     * 优先读取 config/database.php，否则从 .env 解析
     */
    protected function initDatabase()
    {
        $dbConfig = null;
        $configFile = CONFIG_PATH . 'database.php';
        if (file_exists($configFile)) {
            try {
                $config = include $configFile;
                if (is_array($config) && isset($config['connections']['mysql'])) {
                    $dbConfig = $config['connections']['mysql'];
                }
            } catch (\Throwable $e) {
                $dbConfig = null;
            }
        }

        if (!$dbConfig) {
            $envFile = ROOT_PATH . '.env';
            if (file_exists($envFile)) {
                $envContent = file_get_contents($envFile);
                $dbConfig = [
                    'hostname' => $this->envValue($envContent, 'DB_HOST', '127.0.0.1'),
                    'database' => $this->envValue($envContent, 'DB_NAME', 'qeefg_auth'),
                    'username' => $this->envValue($envContent, 'DB_USER', 'root'),
                    'password' => $this->envValue($envContent, 'DB_PASS', ''),
                    'hostport' => $this->envValue($envContent, 'DB_PORT', '3306'),
                    'charset'  => $this->envValue($envContent, 'DB_CHARSET', 'utf8mb4'),
                ];
            }
        }

        if (!$dbConfig) {
            $exampleFile = CONFIG_PATH . 'database.php.example';
            if (file_exists($exampleFile)) {
                $config = include $exampleFile;
                if (is_array($config) && isset($config['connections']['mysql'])) {
                    $dbConfig = $config['connections']['mysql'];
                }
            }
        }

        if (!$dbConfig) {
            die('数据库配置不存在，请创建 config/database.php 或配置 .env 文件');
        }

        try {
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

    private function envValue($content, $key, $default = '')
    {
        if (preg_match('/' . preg_quote($key, '/') . '\s*=\s*(.+)/', $content, $m)) {
            return trim($m[1]);
        }
        return $default;
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

        // 获取未读消息数和最新消息
        $unreadCount = 0;
        $latestMessages = [];
        if ($user) {
            try {
                $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_messages WHERE user_id = ? AND is_read = 0");
                $stmt->execute([$user['id']]);
                $unreadCount = $stmt->fetchColumn();

                $stmt = $this->db->prepare("SELECT * FROM qf_messages WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
                $stmt->execute([$user['id']]);
                $latestMessages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
            }
        }

        $content = $this->render($template, $data);

        $templateFile = APP_PATH . 'view/user/layout.php';
        if (!file_exists($templateFile)) {
            return $content;
        }

        $pageTitle = $title;
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

        $pageTitle = $title;
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
            $stmt = $this->db->prepare("SELECT id, username, email, balance, qq, phone, avatar, status, is_developer, developer_status, created_at FROM qf_users WHERE id = ?");
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