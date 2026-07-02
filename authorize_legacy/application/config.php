<?php
/**
 * QEEFG 授权站全局配置文件
 */

$dbConfig = [];
if (file_exists(APP_PATH . 'config/database.php')) {
    $dbConfig = require APP_PATH . 'config/database.php';
}

return array_merge([
    'app_name' => 'QEEFG 寄售系统售卖网站',
    'app_version' => '1.0.0',
    'default_controller' => 'Index',
    'default_action' => 'index',
    'template' => [
        'view_path' => APP_PATH . 'view' . DIRECTORY_SEPARATOR,
        'view_suffix' => 'php',
        'layout' => 'layout/main',
    ],
    'auth' => [
        'admin_session_key' => 'admin_user',
        'user_session_key' => 'user',
    ],
    'database' => $dbConfig,
], $dbConfig);
