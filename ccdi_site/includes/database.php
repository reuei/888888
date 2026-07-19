<?php
/**
 * 数据库操作类
 * SQLite 数据库封装
 */
if (!defined('SYSTEM_INIT')) { die('未经授权的访问'); }

class Database {
    private static $instance = null;
    private $pdo;
    private $lastInsertId;
    private $queryCount = 0;

    private function __construct() {
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        try {
            $this->pdo = new PDO(DB_DSN);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            // 启用 WAL 模式提升并发性能
            $this->pdo->exec("PRAGMA journal_mode=WAL");
            $this->pdo->exec("PRAGMA foreign_keys=ON");
            $this->pdo->exec("PRAGMA busy_timeout=5000");
        } catch (PDOException $e) {
            die('数据库连接失败：' . $e->getMessage());
        }
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $this->queryCount++;
            return $stmt;
        } catch (PDOException $e) {
            error_log('Database Query Error: ' . $e->getMessage() . ' SQL: ' . $sql);
            return false;
        }
    }

    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        if ($stmt) {
            return $stmt->fetch();
        }
        return false;
    }

    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        if ($stmt) {
            return $stmt->fetchAll();
        }
        return [];
    }

    public function fetchColumn($sql, $params = [], $column = 0) {
        $stmt = $this->query($sql, $params);
        if ($stmt) {
            return $stmt->fetchColumn($column);
        }
        return false;
    }

    public function insert($table, $data) {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        $sql = "INSERT INTO \"{$table}\" (\"" . implode('", "', $fields) . "\") VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $this->query($sql, array_values($data));
        if ($stmt) {
            $this->lastInsertId = $this->pdo->lastInsertId();
            return $this->lastInsertId;
        }
        return false;
    }

    public function update($table, $data, $where, $whereParams = []) {
        $sets = [];
        $params = [];
        foreach ($data as $field => $value) {
            $sets[] = "\"{$field}\" = ?";
            $params[] = $value;
        }
        $params = array_merge($params, $whereParams);
        $sql = "UPDATE \"{$table}\" SET " . implode(', ', $sets) . " WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->rowCount() : false;
    }

    public function delete($table, $where, $params = []) {
        $sql = "DELETE FROM \"{$table}\" WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->rowCount() : false;
    }

    public function count($table, $where = '1=1', $params = []) {
        $sql = "SELECT COUNT(*) FROM \"{$table}\" WHERE {$where}";
        return (int)$this->fetchColumn($sql, $params);
    }

    public function lastInsertId() {
        return $this->lastInsertId;
    }

    public function getQueryCount() {
        return $this->queryCount;
    }

    public function exec($sql) {
        try {
            return $this->pdo->exec($sql);
        } catch (PDOException $e) {
            error_log('Database Exec Error: ' . $e->getMessage());
            return false;
        }
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }

    public function tableExists($table) {
        $sql = "SELECT name FROM sqlite_master WHERE type='table' AND name=?";
        return (bool)$this->fetchColumn($sql, [$table]);
    }

    // 防止克隆
    private function __clone() {}
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// 便捷函数
function db() {
    return Database::getInstance();
}

function db_fetch($sql, $params = []) {
    return db()->fetch($sql, $params);
}

function db_fetch_all($sql, $params = []) {
    return db()->fetchAll($sql, $params);
}

function db_insert($table, $data) {
    return db()->insert($table, $data);
}

function db_update($table, $data, $where, $params = []) {
    return db()->update($table, $data, $where, $params);
}

function db_delete($table, $where, $params = []) {
    return db()->delete($table, $where, $params);
}

function db_count($table, $where = '1=1', $params = []) {
    return db()->count($table, $where, $params);
}