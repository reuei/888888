<?php
namespace Framework;

class Session
{
    protected static $started = false;

    public static function start()
    {
        if (self::$started) {
            return;
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => 1,
                'cookie_samesite' => 'Lax',
                'use_strict_mode' => 0,
            ]);
        }
        self::$started = true;
    }

    public static function get($key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function has($key)
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function delete($key)
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function clear()
    {
        self::start();
        $_SESSION = [];
    }

    public static function flash($key, $value)
    {
        self::set('__flash__' . $key, $value);
    }

    public static function getFlash($key)
    {
        $value = self::get('__flash__' . $key);
        self::delete('__flash__' . $key);
        return $value;
    }
}
