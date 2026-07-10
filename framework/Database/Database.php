<?php
namespace Framework\Database;

use PDO;
use PDOException;

class Database
{
    protected static $instance = null;
    protected $pdo = null;
    protected $config = [];

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $configFile = dirname(__DIR__, 2) . '/config/database.php';
        if (!file_exists($configFile)) {
            return;
        }
        $this->config = require $configFile;
        $this->connect();
    }

    public function isConnected()
    {
        return $this->pdo !== null;
    }

    protected function connect()
    {
        try {
            $cfg = $this->config;
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $cfg['hostname'],
                $cfg['hostport'] ?? 3306,
                $cfg['database'],
                $cfg['charset'] ?? 'utf8mb4'
            );
            $this->pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            $this->pdo = null;
        }
    }

    public function table($name)
    {
        $prefix = $this->config['prefix'] ?? '';
        return new QueryBuilder($this->pdo, $prefix . $name);
    }

    public function raw($sql, $params = [])
    {
        if (!$this->pdo) {
            return null;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function query($sql)
    {
        if (!$this->pdo) {
            return null;
        }
        return $this->pdo->query($sql);
    }

    public function insert($table, $data)
    {
        $fields = array_keys($data);
        $placeholders = array_map(fn($f) => ':' . $f, $fields);
        $sql = "INSERT INTO {$table} (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        $stmt = $this->pdo->prepare($sql);
        foreach ($data as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    public function update($table, $data, $where, $whereParams = [])
    {
        $sets = [];
        foreach (array_keys($data) as $f) {
            $sets[] = "$f = :$f";
        }
        $sql = "UPDATE {$table} SET " . implode(',', $sets) . " WHERE $where";
        $stmt = $this->pdo->prepare($sql);
        foreach ($data as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }
        foreach ($whereParams as $k => $v) {
            $stmt->bindValue(is_int($k) ? $k + 1 : $k, $v);
        }
        return $stmt->execute();
    }

    public function delete($table, $where, $params = [])
    {
        $sql = "DELETE FROM {$table} WHERE $where";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function row($sql, $params = [])
    {
        $stmt = $this->raw($sql, $params);
        return $stmt ? $stmt->fetch() : null;
    }

    public function column($sql, $params = [])
    {
        $stmt = $this->raw($sql, $params);
        if (!$stmt) {
            return [];
        }
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function pairs($sql, $params = [])
    {
        $stmt = $this->raw($sql, $params);
        if (!$stmt) {
            return [];
        }
        $result = [];
        foreach ($stmt->fetchAll() as $row) {
            $arr = array_values($row);
            if (count($arr) >= 2) {
                $result[$arr[0]] = $arr[1];
            }
        }
        return $result;
    }
}
