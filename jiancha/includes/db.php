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
        try {
            $st = self::init()->prepare($sql);
            $st->execute($params);
            return $st->rowCount();
        } catch (Exception $e) { return 0; }
    }
    public static function query($sql, $params = []) {
        try {
            $st = self::init()->prepare($sql);
            $st->execute($params);
            return $st;
        } catch (Exception $e) { return null; }
    }
    public static function fetchAll($sql, $params = []) {
        try {
            $st = self::query($sql, $params);
            return $st ? $st->fetchAll() : [];
        } catch (Exception $e) { return []; }
    }
    public static function fetchOne($sql, $params = []) {
        try {
            $st = self::query($sql, $params);
            return $st ? ($st->fetch() ?: null) : null;
        } catch (Exception $e) { return null; }
    }
    public static function insert($table, $data) {
        try {
            $cols = implode(',', array_keys($data));
            $ph = implode(',', array_fill(0, count($data), '?'));
            $st = self::init()->prepare("INSERT INTO {$table} ({$cols}) VALUES ({$ph})");
            $st->execute(array_values($data));
            return (int) self::init()->lastInsertId();
        } catch (Exception $e) { return 0; }
    }
    public static function update($table, $data, $where, $wparams = []) {
        try {
            $set = implode(',', array_map(fn($k) => "{$k}=?", array_keys($data)));
            $st = self::init()->prepare("UPDATE {$table} SET {$set} WHERE {$where}");
            $st->execute(array_merge(array_values($data), $wparams));
            return $st->rowCount();
        } catch (Exception $e) { return 0; }
    }
    public static function delete($table, $where, $params = []) {
        try {
            $st = self::init()->prepare("DELETE FROM {$table} WHERE {$where}");
            $st->execute($params);
            return $st->rowCount();
        } catch (Exception $e) { return 0; }
    }
}
