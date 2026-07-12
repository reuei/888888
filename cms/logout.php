<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$_SESSION = [];
session_destroy();

setcookie('remember_user', '', time() - 3600, '/');

redirect('index.php');
