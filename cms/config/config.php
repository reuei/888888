<?php
define('SITE_NAME', '人民检察');
define('SITE_TITLE', '人民检察 - 人民检察院监督举报与检察信息公开平台');
define('SITE_KEYWORDS', '人民检察,人民检察院,检察监督,公益诉讼,检务公开,职务犯罪,刑事检察,民事检察,行政检察');
define('SITE_DESCRIPTION', '人民检察网是人民检察院面向社会公众的官方信息公开平台，提供检察新闻、检务公开、信访举报、典型案例发布、检察普法等服务');

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