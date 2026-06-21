<?php
/**
 * 数据库抽象层
 * 支持 SQLite / MySQL / JSON 降级
 */

if (!defined('YUYUN_ROOT')) {
    define('YUYUN_ROOT', dirname(__DIR__));
}

require_once YUYUN_ROOT . '/config.php';

class YuyunDB {
    private static $instance = null;
    private $type;
    private $pdo = null;
    private $jsonPath;

    private function __construct() {
        $this->type = defined('DB_TYPE') ? DB_TYPE : 'json';

        if ($this->type === 'sqlite') {
            $dbFile = defined('DB_NAME') ? DB_NAME : YUYUN_ROOT . '/data/yuyun.db';
            $this->pdo = new PDO('sqlite:' . $dbFile);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec("PRAGMA foreign_keys = ON;");
        } elseif ($this->type === 'mysql') {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . (DB_PORT ?: '3306') . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } elseif ($this->type === 'json') {
            $this->jsonPath = YUYUN_ROOT . '/data/json';
            if (!is_dir($this->jsonPath)) {
                mkdir($this->jsonPath, 0755, true);
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getType() {
        return $this->type;
    }

    public function getPdo() {
        return $this->pdo;
    }

    // 查询：SQL 模式
    public function query($sql, $params = []) {
        if ($this->type === 'json') {
            return [];
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function queryOne($sql, $params = []) {
        $rows = $this->query($sql, $params);
        return $rows ? $rows[0] : null;
    }

    public function execute($sql, $params = []) {
        if ($this->type === 'json') {
            return false;
        }
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function lastInsertId() {
        if ($this->type === 'json') {
            return 0;
        }
        return $this->pdo->lastInsertId();
    }

    // JSON 表操作
    private function jsonTablePath($table) {
        return $this->jsonPath . '/' . $table . '.json';
    }

    private function readJsonTable($table) {
        $path = $this->jsonTablePath($table);
        if (!file_exists($path)) {
            return [];
        }
        $content = file_get_contents($path);
        return $content ? json_decode($content, true) : [];
    }

    private function writeJsonTable($table, $data) {
        $path = $this->jsonTablePath($table);
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }

    public function jsonInsert($table, $row) {
        $data = $this->readJsonTable($table);
        $maxId = 0;
        foreach ($data as $item) {
            if (isset($item['id']) && $item['id'] > $maxId) {
                $maxId = $item['id'];
            }
        }
        $row['id'] = $maxId + 1;
        $row['created_at'] = date('Y-m-d H:i:s');
        $data[] = $row;
        $this->writeJsonTable($table, $data);
        return $row['id'];
    }

    public function jsonUpdate($table, $id, $row) {
        $data = $this->readJsonTable($table);
        foreach ($data as &$item) {
            if ($item['id'] == $id) {
                foreach ($row as $k => $v) {
                    $item[$k] = $v;
                }
                $item['updated_at'] = date('Y-m-d H:i:s');
                $this->writeJsonTable($table, $data);
                return true;
            }
        }
        return false;
    }

    public function jsonDelete($table, $id) {
        $data = $this->readJsonTable($table);
        $found = false;
        foreach ($data as $i => $item) {
            if ($item['id'] == $id) {
                unset($data[$i]);
                $found = true;
                break;
            }
        }
        if ($found) {
            $data = array_values($data);
            $this->writeJsonTable($table, $data);
        }
        return $found;
    }

    public function jsonFind($table, $id) {
        $data = $this->readJsonTable($table);
        foreach ($data as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }
        return null;
    }

    public function jsonAll($table, $orderBy = 'sort_order', $direction = 'ASC') {
        $data = $this->readJsonTable($table);
        usort($data, function($a, $b) use ($orderBy, $direction) {
            $va = isset($a[$orderBy]) ? $a[$orderBy] : 0;
            $vb = isset($b[$orderBy]) ? $b[$orderBy] : 0;
            if ($va == $vb) return 0;
            $cmp = ($va < $vb) ? -1 : 1;
            return strtoupper($direction) === 'DESC' ? -$cmp : $cmp;
        });
        return $data;
    }

    public function jsonWhere($table, $conditions, $orderBy = 'sort_order', $direction = 'ASC') {
        $data = $this->jsonAll($table, $orderBy, $direction);
        $result = [];
        foreach ($data as $item) {
            $match = true;
            foreach ($conditions as $k => $v) {
                if (!isset($item[$k]) || $item[$k] != $v) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $result[] = $item;
            }
        }
        return $result;
    }

    // 初始化表结构
    public function initTables() {
        if ($this->type === 'json') {
            $tables = ['settings', 'slides', 'products', 'partners', 'links', 'certificates', 'messages', 'admins', 'logs', 'testimonials'];
            foreach ($tables as $table) {
                if (!file_exists($this->jsonTablePath($table))) {
                    $this->writeJsonTable($table, []);
                }
            }
            return;
        }

        $sqlite = $this->type === 'sqlite';

        $ai = $sqlite ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT PRIMARY KEY AUTO_INCREMENT';
        $v100 = 'VARCHAR(100)';
        $v255 = 'VARCHAR(255)';
        $v50 = 'VARCHAR(50)';
        $txt = 'TEXT';
        $int = 'INT';
        $tiny = 'TINYINT';
        $dt = 'DATETIME';
        $now = 'CURRENT_TIMESTAMP';

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            id $ai,
            s_key $v100 NOT NULL UNIQUE,
            s_value $txt,
            updated_at $dt DEFAULT $now
        );");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS slides (
            id $ai,
            title $v255,
            subtitle $txt,
            image $v255,
            link $v255,
            btn_text $v100,
            sort_order $int DEFAULT 0,
            is_active $tiny DEFAULT 1,
            created_at $dt DEFAULT $now
        );");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS products (
            id $ai,
            icon $v100,
            title $v255,
            summary $txt,
            detail $txt,
            image $v255,
            sort_order $int DEFAULT 0,
            is_active $tiny DEFAULT 1
        );");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS partners (
            id $ai,
            name $v255,
            logo $v255,
            link $v255,
            sort_order $int DEFAULT 0,
            is_active $tiny DEFAULT 1
        );");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS links (
            id $ai,
            name $v255,
            url $v255,
            sort_order $int DEFAULT 0
        );");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS certificates (
            id $ai,
            name $v255,
            image $v255,
            description $txt,
            sort_order $int DEFAULT 0
        );");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS messages (
            id $ai,
            name $v100,
            phone $v50,
            email $v100,
            content $txt,
            status $tiny DEFAULT 0,
            created_at $dt DEFAULT $now
        );");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS admins (
            id $ai,
            username $v100 UNIQUE,
            password $v255,
            created_at $dt DEFAULT $now
        );");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS logs (
            id $ai,
            action $v100,
            detail $txt,
            ip $v50,
            created_at $dt DEFAULT $now
        );");

        $this->pdo->exec("CREATE TABLE IF NOT EXISTS testimonials (
            id $ai,
            content $txt,
            author $v100,
            company $v100,
            stars $int DEFAULT 5,
            sort_order $int DEFAULT 0,
            is_active $tiny DEFAULT 1
        );");
    }
}
