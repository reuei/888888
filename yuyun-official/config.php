<?php
/**
 * 语云科技官网配置文件
 * 安装程序会自动生成/覆盖此文件
 */

if (!defined('YUYUN_ROOT')) {
    define('YUYUN_ROOT', __DIR__);
}

define('DB_TYPE', 'sqlite');           // sqlite | mysql | json
define('DB_HOST', '');
define('DB_PORT', '');
define('DB_NAME', YUYUN_ROOT . '/data/yuyun.db');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('INSTALLED', false);             // 安装完成后设为 true
