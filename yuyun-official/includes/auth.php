<?php
/**
 * 后台认证
 */

if (!defined('YUYUN_ROOT')) {
    define('YUYUN_ROOT', dirname(__DIR__));
}

require_once YUYUN_ROOT . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isAdminLoggedIn() {
    return !empty($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function adminLogin($username, $password) {
    $db = YuyunDB::getInstance();
    if ($db->getType() === 'json') {
        $admins = $db->jsonAll('admins', 'id', 'ASC');
        foreach ($admins as $admin) {
            if ($admin['username'] === $username && password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                return true;
            }
        }
    } else {
        $admin = $db->queryOne("SELECT * FROM admins WHERE username = ?", [$username]);
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            return true;
        }
    }
    return false;
}

function adminLogout() {
    unset($_SESSION['admin_id'], $_SESSION['admin_username']);
    session_destroy();
}

function currentAdmin() {
    if (!isAdminLoggedIn()) return null;
    return [
        'id' => $_SESSION['admin_id'],
        'username' => $_SESSION['admin_username'] ?? ''
    ];
}
