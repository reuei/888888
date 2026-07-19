<?php
/**
 * 用户注销
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';

logout_user();
redirect(site_url());