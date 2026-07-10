<?php
/**
 * 自研轻量级MVC框架 v1.0.5
 * 核心应用类
 */

namespace Framework;

use Framework\Router;
use Framework\Request;
use Framework\Response;
use Framework\Database\Database;
use Framework\Cache\Cache;

class App
{
    protected static $instance;
    protected $router;
    protected $request;
    protected $config = [];

    public function __construct()
    {
        self::$instance = $this;
        $this->loadConfig();
        $this->initErrorHandler();
        $this->request = new Request();
        $this->router = new Router();
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public function getConfig($key = null)
    {
        if ($key === null) {
            return $this->config;
        }
        return $this->config[$key] ?? null;
    }

    protected function loadConfig()
    {
        $configFile = dirname(__DIR__) . '/config/app.php';
        if (file_exists($configFile)) {
            $this->config = require $configFile;
        }
        $this->config['version'] = '1.0.5';
        $this->config['root_path'] = dirname(__DIR__);
        $this->config['app_path'] = dirname(__DIR__) . '/app';
        $this->config['runtime_path'] = dirname(__DIR__) . '/runtime';
    }

    protected function initErrorHandler()
    {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if (!(error_reporting() & $errno)) {
                return false;
            }
            $this->logError("[$errno] $errstr in $errfile:$errline");
            return true;
        });

        set_exception_handler(function ($e) {
            $this->logError('Uncaught: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            if ($this->config['debug'] ?? false) {
                echo '<pre style="background:#fee;color:#900;padding:20px;font-family:monospace;">';
                echo 'Error: ' . htmlspecialchars($e->getMessage()) . "\n";
                echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n\n";
                echo $e->getTraceAsString();
                echo '</pre>';
            } else {
                $this->renderError('System Error');
            }
        });
    }

    public function logError($message)
    {
        $logFile = $this->config['runtime_path'] . '/log/error.log';
        $dir = dirname($logFile);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        @file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n", FILE_APPEND);
    }

    public function renderError($message)
    {
        Response::html('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>系统错误</title>'
            . '<style>body{font-family:sans-serif;background:#0a0a0a;color:#e5e5e5;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}'
            . '.box{text-align:center;padding:40px}.code{font-size:64px;font-weight:bold;color:#10b981;margin:0}.msg{color:#a3a3a3;margin-top:16px}</style></head>'
            . '<body><div class="box"><h1 class="code">500</h1><p class="msg">' . htmlspecialchars($message) . '</p>'
            . '<a href="/" style="color:#10b981;text-decoration:none;margin-top:24px;display:inline-block">返回首页</a></div></body></html>');
    }

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function run()
    {
        try {
            $this->router->dispatch($this->request);
        } catch (\Throwable $e) {
            $this->logError('Router dispatch: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            if ($this->config['debug'] ?? false) {
                throw $e;
            }
            $this->renderError($e->getMessage());
        }
    }

    public static function db()
    {
        static $db = null;
        if ($db === null) {
            $db = Database::getInstance();
        }
        return $db;
    }

    public static function cache()
    {
        static $cache = null;
        if ($cache === null) {
            $cache = new Cache();
        }
        return $cache;
    }
}
