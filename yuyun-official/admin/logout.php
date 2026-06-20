<?php
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/includes/auth.php';
adminLogout();
header('Location: login.php');
exit;
