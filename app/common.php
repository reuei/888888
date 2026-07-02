<?php
/**
 * 应用公共兼容文件
 * 为原 main_legacy 框架的函数、类提供 ThinkPHP 8 运行环境支持
 */

// 应用根目录，兼容原框架常量
if (!defined('APP_PATH')) {
    define('APP_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}
if (!defined('RUNTIME_PATH')) {
    define('RUNTIME_PATH', APP_PATH . 'runtime' . DIRECTORY_SEPARATOR);
}

// 提前启动 Session，兼容原函数库直接读写 $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * 配置兼容类：桥接到 ThinkPHP 的 config() 助手
 */
class Config
{
    private static $data = [];

    public static function set($key, $value = null)
    {
        if (is_array($key)) {
            self::$data = array_merge(self::$data, $key);
        } else {
            self::$data[$key] = $value;
        }
    }

    public static function get($key = null, $default = null)
    {
        if ($key === 'database') {
            $cfg = function_exists('config') ? config('database') : [];
            if (!empty($cfg['connections']['mysql'])) {
                return $cfg['connections']['mysql'];
            }
            if (!empty($cfg['hostname'])) {
                return $cfg;
            }
            return self::$data['database'] ?? [];
        }
        if (function_exists('config')) {
            return config($key, $default);
        }
        if ($key === null) {
            return self::$data;
        }
        $keys = explode('.', $key);
        $value = self::$data;
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }
        return $value;
    }
}

/**
 * 数据库兼容类：保留原 Db::query/fetch/execute/insert/update/delete 等用法
 */
class Db
{
    private static $pdo = null;
    private static $config = [];

    public static function init($config)
    {
        self::$config = $config;
        if (self::$pdo === null && !empty($config)) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['hostname'],
                $config['hostport'] ?? 3306,
                $config['database'],
                $config['charset'] ?? 'utf8mb4'
            );
            self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
    }

    public static function getPdo()
    {
        if (self::$pdo === null) {
            self::init(Config::get('database', []));
        }
        if (self::$pdo === null) {
            throw new Exception('数据库未初始化');
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

/**
 * 模型兼容类
 */
class Model
{
    protected $table = '';
    protected $pk = 'id';

    public function __construct($table = '')
    {
        if ($table) {
            $this->table = $table;
        }
    }

    public function find($id)
    {
        return Db::table($this->table)->where($this->pk, $id)->find();
    }

    public function where($field, $op, $value = null)
    {
        return Db::table($this->table)->where($field, $op, $value);
    }

    public function insert($data)
    {
        return Db::insert($this->table, $data);
    }

    public function update($data, $where, $params = [])
    {
        return Db::update($this->table, $data, $where, $params);
    }

    public function delete($where, $params = [])
    {
        return Db::delete($this->table, $where, $params);
    }
}

// 为各控制器命名空间创建全局兼容类的别名，避免未加反斜杠的 Db/Config 等调用报错
$__compatNamespaces = [
    'app\\controller',
    'app\\controller\\admin',
    'app\\controller\\merchant',
    'app\\controller\\subsite',
];
foreach ($__compatNamespaces as $ns) {
    foreach (['Db', 'Config', 'Model', 'Query'] as $className) {
        $alias = $ns . '\\' . $className;
        if (!class_exists($alias, false)) {
            class_alias($className, $alias, false);
        }
    }
}
unset($__compatNamespaces);

// 载入原主站函数库；其中 url/input/session/redirect/json 等函数将覆盖 ThinkPHP 默认助手
require_once APP_PATH . 'main_legacy' . DIRECTORY_SEPARATOR . 'functions.php';
