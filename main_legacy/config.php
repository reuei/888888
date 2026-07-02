<?php
/**
 * 全局配置文件（安装后会被 database.php 合并）
 */

$dbConfig = [];
if (file_exists(APP_PATH . 'config/database.php')) {
    $dbConfig = require APP_PATH . 'config/database.php';
}

return array_merge([
    'app_name' => '鲸商城 Pro',
    'app' => [
        'app_version' => '1.0.0',
    ],
    'default_controller' => 'Index',
    'default_action' => 'index',
    'template' => [
        'view_path' => APP_PATH . 'view' . DIRECTORY_SEPARATOR,
        'view_suffix' => 'php',
        'layout' => 'layout/main',
    ],
    'auth' => [
        'admin_session_key' => 'admin_user',
        'merchant_session_key' => 'merchant_user',
    ],
], $dbConfig);
