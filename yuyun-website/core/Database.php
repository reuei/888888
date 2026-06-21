<?php
/**
 * 语云科技企业官网 - 数据库管理类
 * 支持MySQL和JSON双模式
 */

class Database {
    private static $instance = null;
    private $pdo = null;
    private $use_mysql = false;
    private $config = [];

    private function __construct() {
        $this->load_config();
        if ($this->use_mysql) {
            $this->connect_mysql();
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 加载数据库配置
     */
    private function load_config() {
        $config_file = dirname(__DIR__) . '/config.php';
        if (file_exists($config_file)) {
            $this->config = include $config_file;
            $this->use_mysql = !empty($this->config['db_host']);
        }
    }

    /**
     * 连接MySQL数据库
     */
    private function connect_mysql() {
        try {
            $dsn = "mysql:host={$this->config['db_host']};dbname={$this->config['db_name']};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->pdo = new PDO($dsn, $this->config['db_user'], $this->config['db_pass'], $options);
        } catch (PDOException $e) {
            log_message("MySQL连接失败: " . $e->getMessage(), 'error');
            $this->use_mysql = false;
        }
    }

    /**
     * 获取所有记录(JSON模式)
     */
    public function getAll($table) {
        if ($this->use_mysql && $this->pdo) {
            $stmt = $this->pdo->query("SELECT * FROM `$table` ORDER BY id DESC");
            return $stmt->fetchAll();
        }
        return get_content($table);
    }

    /**
     * 根据ID获取记录
     */
    public function getById($table, $id) {
        if ($this->use_mysql && $this->pdo) {
            $stmt = $this->pdo->prepare("SELECT * FROM `$table` WHERE `id` = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        }
        $items = get_content($table);
        foreach ($items as $item) {
            if ((int)$item['id'] === (int)$id) {
                return $item;
            }
        }
        return null;
    }

    /**
     * 插入记录
     */
    public function insert($table, $data) {
        if ($this->use_mysql && $this->pdo) {
            $fields = implode('`, `', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO `$table` (`$fields`) VALUES ($placeholders)";
            $stmt = $this->pdo->prepare($stmt);
            $stmt->execute(array_values($data));
            return (int)$this->pdo->lastInsertId();
        }

        // JSON模式
        $items = get_content($table);
        $data['id'] = !empty($items) ? max(array_column($items, 'id')) + 1 : 1;
        $data['created_at'] = date('Y-m-d H:i:s');
        $items[] = $data;
        save_content($table, $items);
        return $data['id'];
    }

    /**
     * 更新记录
     */
    public function update($table, $id, $data) {
        if ($this->use_mysql && $this->pdo) {
            $sets = [];
            foreach (array_keys($data) as $field) {
                $sets[] = "`$field` = ?";
            }
            $sql = "UPDATE `$table` SET " . implode(', ', $sets) . " WHERE `id` = ?";
            $values = array_values($data);
            $values[] = $id;
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($values);
        }

        // JSON模式
        $items = get_content($table);
        foreach ($items as &$item) {
            if ((int)$item['id'] === (int)$id) {
                $item = array_merge($item, $data);
                $item['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        return save_content($table, $items);
    }

    /**
     * 删除记录
     */
    public function delete($table, $id) {
        if ($this->use_mysql && $this->pdo) {
            $stmt = $this->pdo->prepare("DELETE FROM `$table` WHERE `id` = ?");
            return $stmt->execute([$id]);
        }

        // JSON模式
        $items = get_content($table);
        $items = array_filter($items, fn($item) => (int)$item['id'] !== (int)$id);
        return save_content($table, array_values($items));
    }

    /**
     * 查询记录(条件)
     */
    public function where($table, $conditions) {
        if ($this->use_mysql && $this->pdo) {
            $where = [];
            $values = [];
            foreach ($conditions as $field => $value) {
                $where[] = "`$field` = ?";
                $values[] = $value;
            }
            $sql = "SELECT * FROM `$table` WHERE " . implode(' AND ', $where);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            return $stmt->fetchAll();
        }

        // JSON模式
        $items = get_content($table);
        return array_filter($items, function($item) use ($conditions) {
            foreach ($conditions as $field => $value) {
                if (($item[$field] ?? null) != $value) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * 获取配置项
     */
    public function getConfig($key, $default = null) {
        if ($this->use_mysql && $this->pdo) {
            $stmt = $this->pdo->prepare("SELECT config_value FROM site_config WHERE config_key = ?");
            $stmt->execute([$key]);
            $result = $stmt->fetch();
            return $result ? $result['config_value'] : $default;
        }
        return get_config($key) ?: $default;
    }

    /**
     * 设置配置项
     */
    public function setConfig($key, $value) {
        if ($this->use_mysql && $this->pdo) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO site_config (config_key, config_value) VALUES (?, ?)
                 ON DUPLICATE KEY UPDATE config_value = ?, updated_at = NOW()"
            );
            return $stmt->execute([$key, $value, $value]);
        }
        return set_config($key, $value);
    }

    /**
     * 执行原生SQL(MySQL模式)
     */
    public function query($sql, $params = []) {
        if ($this->use_mysql && $this->pdo) {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        }
        return null;
    }

    /**
     * 初始化表结构(MySQL模式)
     */
    public function initTables() {
        if (!$this->use_mysql || !$this->pdo) return;

        $sql = "
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `email` VARCHAR(255) NOT NULL UNIQUE,
            `password` VARCHAR(255),
            `name` VARCHAR(100),
            `avatar` VARCHAR(500),
            `role` ENUM('user','admin') DEFAULT 'user',
            `email_verified` TINYINT(1) DEFAULT 0,
            `status` ENUM('active','banned') DEFAULT 'active',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `tickets` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `subject` VARCHAR(255) NOT NULL,
            `content` TEXT NOT NULL,
            `status` ENUM('open','replying','closed','resolved') DEFAULT 'open',
            `priority` ENUM('low','normal','high','urgent') DEFAULT 'normal',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `ticket_replies` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `ticket_id` INT NOT NULL,
            `user_id` INT NOT NULL,
            `content` TEXT NOT NULL,
            `is_admin` TINYINT(1) DEFAULT 0,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `products` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `description` TEXT,
            `icon` VARCHAR(100),
            `price` DECIMAL(10,2),
            `features` TEXT,
            `status` ENUM('active','inactive') DEFAULT 'active',
            `sort_order` INT DEFAULT 0,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `partners` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `logo_url` VARCHAR(500) NOT NULL,
            `link_url` VARCHAR(500),
            `sort_order` INT DEFAULT 0,
            `status` TINYINT(1) DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `staff` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `position` VARCHAR(100),
            `avatar` VARCHAR(500),
            `bio` TEXT,
            `social_link` VARCHAR(500),
            `sort_order` INT DEFAULT 0,
            `status` TINYINT(1) DEFAULT 1
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `site_config` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `config_key` VARCHAR(100) NOT NULL UNIQUE,
            `config_value` TEXT,
            `config_group` VARCHAR(50) DEFAULT 'general',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

        CREATE TABLE IF NOT EXISTS `feedback` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT,
            `type` ENUM('suggestion','report') NOT NULL,
            `title` VARCHAR(255),
            `content` TEXT NOT NULL,
            `status` ENUM('pending','processing','resolved') DEFAULT 'pending',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        $this->pdo->exec($sql);
    }
}
