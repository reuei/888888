<?php
class DB {
    private static $pdo = null;

    public static function getInstance() {
        if (self::$pdo === null) {
            try {
                $dbDir = dirname(DB_PATH);
                if (!is_dir($dbDir)) {
                    mkdir($dbDir, 0755, true);
                }
                self::$pdo = new PDO('sqlite:' . DB_PATH);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$pdo->exec("PRAGMA journal_mode=WAL");
                self::$pdo->exec("PRAGMA foreign_keys=ON");
            } catch (PDOException $e) {
                die('数据库连接失败: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public static function query($sql, $params = []) {
        $stmt = self::getInstance()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchAll($sql, $params = []) {
        return self::query($sql, $params)->fetchAll();
    }

    public static function fetchOne($sql, $params = []) {
        return self::query($sql, $params)->fetch();
    }

    public static function insert($table, $data) {
        $keys = array_keys($data);
        $fields = implode(',', $keys);
        $placeholders = ':' . implode(',:', $keys);
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";
        self::query($sql, $data);
        return self::getInstance()->lastInsertId();
    }

    public static function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach (array_keys($data) as $key) {
            $set[] = "{$key}=:set_{$key}";
        }
        $setStr = implode(',', $set);
        $params = [];
        foreach ($data as $key => $val) {
            $params['set_' . $key] = $val;
        }
        foreach ($whereParams as $key => $val) {
            $params[$key] = $val;
        }
        $sql = "UPDATE {$table} SET {$setStr} WHERE {$where}";
        return self::query($sql, $params)->rowCount();
    }

    public static function delete($table, $where, $params = []) {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return self::query($sql, $params)->rowCount();
    }
}
