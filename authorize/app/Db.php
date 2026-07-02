<?php
/**
 * QEEFG 授权站简易 PDO 数据库操作类（ThinkPHP 8 兼容封装）
 */

namespace app;

class Db
{
    private static $pdo = null;
    private static $config = [];

    public static function init($config)
    {
        self::$config = $config;
        if (self::$pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['hostname'],
                $config['hostport'] ?? 3306,
                $config['database'],
                $config['charset'] ?? 'utf8mb4'
            );
            self::$pdo = new \PDO($dsn, $config['username'], $config['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
    }

    public static function getPdo()
    {
        if (self::$pdo === null) {
            throw new \Exception('数据库未初始化');
        }
        return self::$pdo;
    }

    public static function query($sql, $params = [])
    {
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function fetch($sql, $params = [])
    {
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public static function execute($sql, $params = [])
    {
        $stmt = self::getPdo()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function insert($table, $data)
    {
        $keys = array_keys($data);
        $keys = array_map(function ($k) {
            return str_replace('`', '``', $k);
        }, $keys);
        $fields = implode('`, `', $keys);
        $table = str_replace('`', '``', $table);
        $placeholders = implode(', ', array_fill(0, count($keys), '?'));
        $sql = "INSERT INTO `{$table}` (`{$fields}`) VALUES ({$placeholders})";
        self::execute($sql, array_values($data));
        return self::getPdo()->lastInsertId();
    }

    public static function update($table, $data, $where, $whereParams = [])
    {
        $table = str_replace('`', '``', $table);
        $sets = [];
        foreach ($data as $k => $v) {
            $k = str_replace('`', '``', $k);
            $sets[] = "`{$k}` = ?";
        }
        $sql = "UPDATE `{$table}` SET " . implode(', ', $sets) . " WHERE {$where}";
        return self::execute($sql, array_merge(array_values($data), $whereParams));
    }

    public static function delete($table, $where, $whereParams = [])
    {
        $table = str_replace('`', '``', $table);
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        return self::execute($sql, $whereParams);
    }

    public static function table($table)
    {
        return new Query($table);
    }
}

class Query
{
    private $table;
    private $where = [];
    private $params = [];
    private $order = '';
    private $limit = '';

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function where($field, $op, $value = null)
    {
        if ($value === null) {
            $value = $op;
            $op = '=';
        }
        $this->where[] = "`{$field}` {$op} ?";
        $this->params[] = $value;
        return $this;
    }

    public function order($order)
    {
        $this->order = $order;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = (int) $limit;
        return $this;
    }

    public function select()
    {
        $sql = "SELECT * FROM `{$this->table}`";
        if (!empty($this->where)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->where);
        }
        if ($this->order) {
            $sql .= ' ORDER BY ' . $this->order;
        }
        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }
        return Db::query($sql, $this->params);
    }

    public function find()
    {
        $this->limit(1);
        $rows = $this->select();
        return $rows[0] ?? null;
    }
}
