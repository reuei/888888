<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/Auth.php';

Auth::logout();
redirect(SITE_URL . '/admin/login.php');