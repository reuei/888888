<?php
class DB {
    private static $instance = null;
    private $pdo;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->connect();
        }
        return self::$instance;
    }

    private function connect() {
        $dir = dirname(DB_PATH);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $this->pdo = new PDO('sqlite:' . DB_PATH);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('PRAGMA journal_mode=WAL');
        $this->pdo->exec('PRAGMA foreign_keys=ON');
    }

    public static function exec($sql) { return self::getInstance()->pdo->exec($sql); }
    public static function query($sql, $params = []) {
        $stmt = self::getInstance()->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    public static function fetchAll($sql, $params = []) {
        return self::query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function fetchOne($sql, $params = []) {
        return self::query($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }
    public static function insert($table, $data) {
        $keys = array_keys($data);
        $placeholders = array_fill(0, count($keys), '?');
        $sql = "INSERT INTO $table (" . implode(',', $keys) . ") VALUES (" . implode(',', $placeholders) . ")";
        self::query($sql, array_values($data));
        return self::getInstance()->pdo->lastInsertId();
    }
    public static function update($table, $data, $where, $params = []) {
        $sets = [];
        foreach ($data as $k => $v) $sets[] = "$k=?";
        $sql = "UPDATE $table SET " . implode(',', $sets) . " WHERE $where";
        self::query($sql, array_merge(array_values($data), $params));
    }
    public static function delete($table, $where, $params = []) {
        $sql = "DELETE FROM $table WHERE $where";
        self::query($sql, $params);
    }
    public static function lastInsertId() {
        return self::getInstance()->pdo->lastInsertId();
    }
}