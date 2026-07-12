<?php
define('SITE_NAME', '清廉在线');
define('SITE_TITLE', '清廉在线 - 党风廉政建设门户网站');
define('SITE_KEYWORDS', '党风廉政,反腐倡廉,纪检监察,纪律检查');
define('SITE_DESCRIPTION', '清廉在线是党风廉政建设和反腐败工作的综合性门户网站');

define('DB_PATH', __DIR__ . '/../data/cms.db');
define('DATA_DIR', __DIR__ . '/../data');
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('UPLOAD_URL', 'uploads/');

define('BASE_URL', '');
define('ADMIN_DIR', 'admin');

define('DEBUG', false);

if (DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

date_default_timezone_set('Asia/Shanghai');
session_start();
