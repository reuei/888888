<?php
/**
 * QEEFG 授权站应用启动文件
 */

require APP_PATH . 'functions.php';

spl_autoload_register(function ($class) {
    $file = APP_PATH . str_replace(['\\', '_'], DIRECTORY_SEPARATOR, $class) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

class App
{
    public function run()
    {
        $config = require APP_PATH . 'config.php';
        Config::set($config);

        $dbConfig = Config::get('database', []);
        if (!empty($dbConfig)) {
            Db::init($dbConfig);
        }

        Route::dispatch();
    }
}

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
