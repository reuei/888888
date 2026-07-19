<?php
/**
 * 用户认证系统
 */
if (!defined('SYSTEM_INIT')) { die('未经授权的访问'); }

/**
 * 检查用户是否登录
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * 检查是否为管理员
 */
function is_admin() {
    return is_logged_in() && isset($_SESSION['user_role']) && in_array($_SESSION['user_role'], ['admin', 'super_admin']);
}

/**
 * 检查是否为超级管理员
 */
function is_super_admin() {
    return is_logged_in() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'super_admin';
}

/**
 * 获取当前用户ID
 */
function current_user_id() {
    return $_SESSION['user_id'] ?? 0;
}

/**
 * 获取当前用户名
 */
function current_username() {
    return $_SESSION['username'] ?? '';
}

/**
 * 获取当前用户信息
 */
function current_user() {
    if (!is_logged_in()) return null;
    return db_fetch("SELECT * FROM users WHERE id = ?", [current_user_id()]);
}

/**
 * 要求登录
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = current_url();
        redirect(site_url('login.php'));
    }
}

/**
 * 要求管理员权限
 * 在重定向到登录页之前，显式设置 redirect_after_login 确保登录后能正确跳回管理页面
 */
function require_admin() {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = current_url();
        redirect(site_url('login.php'));
    }
    if (!is_admin()) {
        redirect(site_url('index.php'));
    }
}

/**
 * 要求超级管理员权限
 */
function require_super_admin() {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = current_url();
        redirect(site_url('login.php'));
    }
    if (!is_super_admin()) {
        redirect(admin_url('index.php'));
    }
}

/**
 * 用户注册
 */
function register_user($username, $password, $email = '') {
    // 验证用户名
    if (!is_valid_username($username)) {
        return ['success' => false, 'message' => '用户名格式不正确（3-20位，支持字母、数字、下划线、中文）'];
    }
    
    // 验证密码
    if (!is_strong_password($password)) {
        return ['success' => false, 'message' => '密码长度需在6-50位之间'];
    }
    
    // 验证邮箱
    if ($email && !is_valid_email($email)) {
        return ['success' => false, 'message' => '邮箱格式不正确'];
    }
    
    // 检查用户名是否已存在
    $existing = db_fetch("SELECT id FROM users WHERE username = ?", [$username]);
    if ($existing) {
        return ['success' => false, 'message' => '用户名已被注册'];
    }
    
    // 检查邮箱是否已存在
    if ($email) {
        $existing_email = db_fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing_email) {
            return ['success' => false, 'message' => '邮箱已被注册'];
        }
    }
    
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    $user_id = db_insert('users', [
        'username' => $username,
        'password' => $hashed_password,
        'email' => $email,
        'role' => 'subscriber',
        'status' => 'active',
        'reg_time' => date('Y-m-d H:i:s'),
        'last_login' => date('Y-m-d H:i:s')
    ]);
    
    if ($user_id) {
        // 自动登录
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['user_role'] = 'subscriber';
        
        add_log('user_register', "用户 {$username} 注册成功");
        return ['success' => true, 'message' => '注册成功', 'user_id' => $user_id];
    }
    
    return ['success' => false, 'message' => '注册失败，请稍后重试'];
}

/**
 * 用户登录
 */
function login_user($username, $password, $remember = false) {
    $user = db_fetch("SELECT * FROM users WHERE username = ? AND status = 'active'", [$username]);
    
    if (!$user) {
        return ['success' => false, 'message' => '用户名或密码不正确'];
    }
    
    if (!password_verify($password, $user['password'])) {
        add_log('login_failed', "用户 {$username} 登录失败（密码错误）");
        return ['success' => false, 'message' => '用户名或密码不正确'];
    }
    
    // 更新最后登录时间
    db_update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
    
    // 设置会话
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_role'] = $user['role'];
    
    // 记住登录
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + 86400 * 30; // 30天
        setcookie('remember_token', $token, $expiry, '/', '', isset($_SERVER['HTTPS']), true);
        db_update('users', ['remember_token' => $token], 'id = ?', [$user['id']]);
    }
    
    add_log('user_login', "用户 {$username} 登录成功");
    return ['success' => true, 'message' => '登录成功', 'user' => $user];
}

/**
 * 用户注销
 */
function logout_user() {
    $username = $_SESSION['username'] ?? '';
    add_log('user_logout', "用户 {$username} 注销");
    
    // 清除会话
    session_unset();
    session_destroy();
    
    // 清除记住我cookie
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
}

/**
 * 修改密码
 */
function change_password($user_id, $old_password, $new_password) {
    $user = db_fetch("SELECT * FROM users WHERE id = ?", [$user_id]);
    
    if (!$user) {
        return ['success' => false, 'message' => '用户不存在'];
    }
    
    if (!password_verify($old_password, $user['password'])) {
        return ['success' => false, 'message' => '原密码不正确'];
    }
    
    if (!is_strong_password($new_password)) {
        return ['success' => false, 'message' => '新密码长度需在6-50位之间'];
    }
    
    $hashed = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
    db_update('users', ['password' => $hashed], 'id = ?', [$user_id]);
    
    add_log('password_change', "用户 {$user['username']} 修改了密码");
    return ['success' => true, 'message' => '密码修改成功'];
}

/**
 * 自动登录（记住我功能）
 */
function auto_login() {
    if (is_logged_in()) return;
    
    if (isset($_COOKIE['remember_token'])) {
        $user = db_fetch("SELECT * FROM users WHERE remember_token = ? AND status = 'active'", [$_COOKIE['remember_token']]);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
        }
    }
}