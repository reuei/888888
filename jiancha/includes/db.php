<?php
class DB {
    private static $pdo;
    private static function init() {
        if (!self::$pdo) {
            self::$pdo = new PDO('sqlite:' . DB_PATH);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$pdo->exec('PRAGMA foreign_keys = ON');
        }
        return self::$pdo;
    }
    public static function exec($sql, $params = []) {
        $st = self::init()->prepare($sql);
        $st->execute($params);
        return $st->rowCount();
    }
    public static function query($sql, $params = []) {
        $st = self::init()->prepare($sql);
        $st->execute($params);
        return $st;
    }
    public static function fetchAll($sql, $params = []) {
        return self::query($sql, $params)->fetchAll();
    }
    public static function fetchOne($sql, $params = []) {
        $r = self::query($sql, $params)->fetch();
        return $r ?: null;
    }
    public static function insert($table, $data) {
        $cols = implode(',', array_keys($data));
        $ph = implode(',', array_fill(0, count($data), '?'));
        $st = self::init()->prepare("INSERT INTO {$table} ({$cols}) VALUES ({$ph})");
        $st->execute(array_values($data));
        return (int) self::init()->lastInsertId();
    }
    public static function update($table, $data, $where, $wparams = []) {
        $set = implode(',', array_map(fn($k) => "{$k}=?", array_keys($data)));
        $st = self::init()->prepare("UPDATE {$table} SET {$set} WHERE {$where}");
        $st->execute(array_merge(array_values($data), $wparams));
        return $st->rowCount();
    }
    public static function delete($table, $where, $params = []) {
        $st = self::init()->prepare("DELETE FROM {$table} WHERE {$where}");
        $st->execute($params);
        return $st->rowCount();
    }
}
