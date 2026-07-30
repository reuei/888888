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

            // 优先尝试 MySQL
            $dbConfig = $config['connections']['mysql'] ?? null;
            if ($dbConfig && !empty($dbConfig['hostname'])) {
                try {
                    $dsn = "mysql:host={$dbConfig['hostname']};port={$dbConfig['hostport']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
                    $this->db = new \PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                        \PDO::ATTR_EMULATE_PREPARES => false,
                    ]);
                    return;
                } catch (\PDOException $e) {
                    // MySQL 连接失败，回退到 SQLite
                }
            }

            // 回退到 SQLite
            $dbFile = ROOT_PATH . 'storage/database.sqlite';
            $isNew = !file_exists($dbFile);
            $this->db = new \PDO('sqlite:' . $dbFile, null, null, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $this->db->exec('PRAGMA journal_mode=WAL');
            $this->db->exec('PRAGMA foreign_keys=ON');

            if ($isNew) {
                $this->initSqliteSchema();
            }
        } catch (\PDOException $e) {
            die('数据库连接失败: ' . $e->getMessage());
        }
    }

    /**
     * 初始化 SQLite 数据库表结构
     */
    protected function initSqliteSchema()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS qf_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL DEFAULT '',
            password TEXT NOT NULL,
            avatar TEXT DEFAULT NULL,
            qq TEXT DEFAULT NULL,
            phone TEXT DEFAULT NULL,
            balance REAL NOT NULL DEFAULT 0.00,
            login_ip TEXT DEFAULT NULL,
            login_time TEXT DEFAULT NULL,
            is_developer INTEGER NOT NULL DEFAULT 0,
            developer_status TEXT DEFAULT NULL,
            status INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT,
            type TEXT NOT NULL DEFAULT 'software',
            price REAL NOT NULL DEFAULT 0,
            duration INTEGER NOT NULL DEFAULT 0,
            download_file TEXT DEFAULT NULL,
            features TEXT,
            status INTEGER NOT NULL DEFAULT 1,
            sort INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_no TEXT NOT NULL UNIQUE,
            user_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            amount REAL NOT NULL,
            payment_method TEXT DEFAULT NULL,
            payment_status INTEGER NOT NULL DEFAULT 0,
            payment_time TEXT DEFAULT NULL,
            status INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_licenses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            license_key TEXT NOT NULL UNIQUE,
            user_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            order_id INTEGER DEFAULT NULL,
            device_limit INTEGER NOT NULL DEFAULT 1,
            devices TEXT,
            expires_at TEXT DEFAULT NULL,
            status INTEGER NOT NULL DEFAULT 1,
            activated_at TEXT DEFAULT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_balance_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            type TEXT NOT NULL,
            amount REAL NOT NULL,
            balance_before REAL NOT NULL,
            balance_after REAL NOT NULL,
            description TEXT DEFAULT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_login_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER DEFAULT NULL,
            username TEXT DEFAULT NULL,
            ip TEXT DEFAULT NULL,
            user_agent TEXT DEFAULT NULL,
            status INTEGER NOT NULL DEFAULT 0,
            message TEXT DEFAULT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_operation_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            action TEXT NOT NULL,
            description TEXT DEFAULT NULL,
            ip TEXT DEFAULT NULL,
            user_agent TEXT DEFAULT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            email TEXT DEFAULT NULL,
            status INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_feedback (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            title TEXT DEFAULT NULL,
            type TEXT NOT NULL DEFAULT 'feedback',
            content TEXT NOT NULL,
            contact TEXT DEFAULT NULL,
            status TEXT NOT NULL DEFAULT 'pending',
            reply TEXT,
            replied_at TEXT DEFAULT NULL,
            reject_reason TEXT DEFAULT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            key TEXT NOT NULL UNIQUE,
            value TEXT,
            description TEXT DEFAULT NULL
        );

        CREATE TABLE IF NOT EXISTS qf_documents (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            content TEXT,
            category TEXT NOT NULL DEFAULT '未分类',
            sort_order INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_feature_codes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            code TEXT NOT NULL UNIQUE,
            description TEXT DEFAULT NULL,
            status INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL DEFAULT 0,
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            type TEXT NOT NULL DEFAULT 'system',
            is_read INTEGER NOT NULL DEFAULT 0,
            is_email_sent INTEGER NOT NULL DEFAULT 0,
            status INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_developer_applications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            real_name TEXT NOT NULL,
            reason TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'pending',
            reject_reason TEXT DEFAULT NULL,
            reviewed_at TEXT DEFAULT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_plugins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            name TEXT NOT NULL,
            description TEXT,
            version TEXT DEFAULT NULL,
            price REAL NOT NULL DEFAULT 0.00,
            status TEXT NOT NULL DEFAULT 'pending',
            reject_reason TEXT DEFAULT NULL,
            file_path TEXT DEFAULT NULL,
            download_count INTEGER NOT NULL DEFAULT 0,
            sort_order INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_email_pool (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL UNIQUE,
            smtp_user TEXT DEFAULT NULL,
            smtp_pass TEXT DEFAULT NULL,
            smtp_host TEXT NOT NULL,
            smtp_port INTEGER NOT NULL DEFAULT 587,
            smtp_encryption TEXT NOT NULL DEFAULT 'tls',
            status INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_payment_channels (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            channel_code TEXT NOT NULL UNIQUE,
            api_url TEXT DEFAULT NULL,
            merchant_id TEXT DEFAULT NULL,
            merchant_key TEXT DEFAULT NULL,
            fee_rate REAL NOT NULL DEFAULT 0.0000,
            status INTEGER NOT NULL DEFAULT 1,
            sort_order INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_email_templates (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            code TEXT NOT NULL UNIQUE,
            subject TEXT NOT NULL,
            content TEXT,
            description TEXT DEFAULT NULL,
            status INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_upload_files (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            file_type TEXT NOT NULL,
            file_name TEXT NOT NULL,
            file_path TEXT NOT NULL,
            original_name TEXT NOT NULL,
            file_size INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );

        CREATE TABLE IF NOT EXISTS qf_verify_codes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            target TEXT NOT NULL,
            type TEXT NOT NULL,
            code TEXT NOT NULL,
            expires_at TEXT NOT NULL,
            used INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );
        ";

        $this->db->exec($sql);

        // 插入默认数据
        $now = date('Y-m-d H:i:s');
        $adminHash = '$2y$12$Mq02eK7gFrFQL5QtFt2Mz.gz3wrMmY6C6OZOrpQYFX9gLWH6QMN3.';

        $this->db->exec("INSERT OR IGNORE INTO qf_admins (id, username, password, email, status, created_at, updated_at) VALUES (1, 'admin', '{$adminHash}', 'admin@qeefg.com', 1, '{$now}', '{$now}')");

        $settings = [
            ['site_name', '熵云', '网站名称'],
            ['site_url', 'https://auth.qeefg.com', '网站地址'],
            ['site_keywords', '授权站,软件授权,授权管理', 'SEO关键词'],
            ['site_description', '便捷快速的授权管理系统', '网站描述'],
            ['qq_group', '123456789', 'QQ群'],
            ['contact_qq', '123456789', '联系QQ'],
            ['require_email_register', '1', '注册是否需要邮箱:0否1是'],
            ['phone_verify', '0', '注册时手机验证码开关:0否1是'],
            ['email_select_mode', 'random', '邮箱选择模式'],
            ['site_announcement', '', '网站公告内容'],
            ['site_logo', '', '网站LOGO路径'],
            ['site_favicon', '', '网站Favicon路径'],
        ];
        $stmt = $this->db->prepare("INSERT OR IGNORE INTO qf_settings (key, value, description) VALUES (?, ?, ?)");
        foreach ($settings as $s) {
            $stmt->execute($s);
        }

        $templates = [
            ['注册验证码', 'register_verify', '邮箱验证码', '<p>您的验证码是：<strong>{code}</strong>，有效期{expire}分钟，请勿泄露给他人。</p>', '用户注册时发送的邮箱验证码模板'],
            ['登录验证码', 'login_verify', '登录验证码', '<p>您的登录验证码是：<strong>{code}</strong>，有效期{expire}分钟，请勿泄露给他人。</p>', '用户登录时发送的邮箱验证码模板'],
            ['插件审核通过', 'plugin_approved', '插件审核通过通知', '<p>您好！您提交的插件 <strong>{plugin_name}</strong> 已审核通过，现已上架。</p>', '插件审核通过时发送的通知模板'],
            ['插件审核被拒', 'plugin_rejected', '插件审核结果通知', '<p>您好！您提交的插件 <strong>{plugin_name}</strong> 未通过审核，原因：{reason}</p>', '插件审核被拒时发送的通知模板'],
            ['开发者申请通过', 'developer_approved', '开发者申请通过通知', '<p>恭喜！您的开发者申请已通过审核，现在可以提交插件了。</p>', '开发者申请通过时发送的通知模板'],
            ['开发者申请被拒', 'developer_rejected', '开发者申请结果通知', '<p>很遗憾，您的开发者申请未通过审核，原因：{reason}</p>', '开发者申请被拒时发送的通知模板'],
        ];
        $stmt = $this->db->prepare("INSERT OR IGNORE INTO qf_email_templates (name, code, subject, content, description, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, ?, ?)");
        foreach ($templates as $t) {
            $stmt->execute([$t[0], $t[1], $t[2], $t[3], $t[4], $now, $now]);
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