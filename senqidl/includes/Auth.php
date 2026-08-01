<?php
// 会话管理
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Auth {
    
    public static function login($username, $password) {
        $admin = DB::getRow('admins', ['username' => $username]);
        if (!$admin) return false;
        
        // 简单密码验证（实际项目建议使用 password_hash）
        if ($admin['password'] === $password || md5($admin['password']) === md5($password)) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['login_time'] = time();
            
            // 更新最后登录时间
            DB::update('admins', $admin['id'], ['last_login' => date('Y-m-d H:i:s')]);
            return true;
        }
        return false;
    }
    
    public static function logout() {
        unset($_SESSION['admin_id'], $_SESSION['admin_username'], $_SESSION['admin_role'], $_SESSION['login_time']);
        session_destroy();
        return true;
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['admin_id']);
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            redirect(SITE_URL . '/admin/login.php');
        }
    }
    
    public static function user() {
        if (!self::isLoggedIn()) return null;
        return DB::getRow('admins', ['id' => $_SESSION['admin_id']]);
    }
}
