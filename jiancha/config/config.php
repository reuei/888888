<?php
define('SITE_NAME', '人民检察');
define('SITE_URL', '');
define('DB_PATH', __DIR__ . '/../data/jiancha.db');
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('UPLOAD_URL', 'uploads/');
date_default_timezone_set('Asia/Shanghai');
if (session_status() === PHP_SESSION_NONE) session_start();