<?php
/**
 * 应用启动文件
 */

// 加载公共函数
require APP_PATH . 'functions.php';

// 自动加载
spl_autoload_register(function ($class) {
    $file = APP_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

// 启动 SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * 应用类
 */
class App
{
    public function run()
    {
        // 加载配置
        $config = require APP_PATH . 'config.php';
        Config::set($config);

        // 数据库初始化
        Db::init(Config::get('database'));

        // 路由解析与分发
        Route::dispatch();
    }
}

/**
 * 配置类
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
