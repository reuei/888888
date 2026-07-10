<?php
namespace Admin\Controller;

class DataController extends BaseAdminController
{
    protected $layout = 'admin';

    public function log($request, $params = [])
    {
        $items = $this->getOperationLogs();
        $this->assign('items', $items);
        $this->assign('pageTitle', '日志管理');
        $this->assign('activeMenu', 'data_log');
        $this->view('admin.data_log');
    }

    public function server($request, $params = [])
    {
        $info = $this->getServerInfo();
        $this->assign('info', $info);
        $this->assign('pageTitle', '服务器日志');
        $this->assign('activeMenu', 'data_server');
        $this->view('admin.data_server');
    }

    public function database($request, $params = [])
    {
        $info = $this->getDatabaseInfo();
        $this->assign('info', $info);
        $this->assign('pageTitle', '数据库日志');
        $this->assign('activeMenu', 'data_database');
        $this->view('admin.data_database');
    }

    public function login($request, $params = [])
    {
        $items = $this->getLoginLogs();
        $this->assign('items', $items);
        $this->assign('pageTitle', '登录日志');
        $this->assign('activeMenu', 'data_login');
        $this->view('admin.data_login');
    }

    protected function getOperationLogs()
    {
        return [
            ['id' => 1, 'admin_name' => 'admin', 'action' => '编辑商品', 'content' => '商品ID:5 价格调整', 'ip' => '127.0.0.1', 'create_time' => date('Y-m-d H:i:s', strtotime('-1 hour'))],
            ['id' => 2, 'admin_name' => 'admin', 'action' => '审核提现', 'content' => '提现单号:W20260709001', 'ip' => '127.0.0.1', 'create_time' => date('Y-m-d H:i:s', strtotime('-3 hour'))],
            ['id' => 3, 'admin_name' => 'admin', 'action' => '发布公告', 'content' => '新用户首单立减', 'ip' => '127.0.0.1', 'create_time' => date('Y-m-d H:i:s', strtotime('-1 day'))],
        ];
    }

    protected function getLoginLogs()
    {
        return [
            ['id' => 1, 'username' => 'admin', 'type' => 'admin', 'ip' => '127.0.0.1', 'status' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-1 hour'))],
            ['id' => 2, 'username' => 'shop001', 'type' => 'user', 'ip' => '192.168.1.1', 'status' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-2 hour'))],
            ['id' => 3, 'username' => 'hacker', 'type' => 'user', 'ip' => '10.0.0.1', 'status' => 0, 'create_time' => date('Y-m-d H:i:s', strtotime('-3 hour'))],
        ];
    }

    protected function getServerInfo()
    {
        return [
            'os' => PHP_OS,
            'php_version' => PHP_VERSION,
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max' => ini_get('upload_max_filesize'),
            'post_max' => ini_get('post_max_size'),
            'timezone' => date_default_timezone_get(),
            'current_time' => date('Y-m-d H:i:s'),
            'uptime' => '运行中',
            'cpu_usage' => '12%',
            'memory_usage' => '32%',
            'disk_usage' => '45%',
            'extensions' => [
                'pdo' => extension_loaded('pdo'),
                'pdo_mysql' => extension_loaded('pdo_mysql'),
                'gd' => extension_loaded('gd'),
                'mbstring' => extension_loaded('mbstring'),
                'openssl' => extension_loaded('openssl'),
                'curl' => extension_loaded('curl'),
                'json' => extension_loaded('json'),
            ],
        ];
    }

    protected function getDatabaseInfo()
    {
        return [
            'driver' => 'MySQL',
            'version' => '5.7.0',
            'charset' => 'utf8mb4',
            'host' => '127.0.0.1:3306',
            'database' => 'xuanwu_card',
            'table_count' => 15,
            'total_size' => '2.4 MB',
            'slow_queries' => 0,
            'connections' => 1,
            'max_connections' => 100,
        ];
    }
}
