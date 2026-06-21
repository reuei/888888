<?php
/**
 * 语云科技企业官网 - 用户认证类
 */

class Auth {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * 用户注册
     */
    public function register($email, $password, $name = '') {
        // 检查邮箱是否存在
        $existing = $this->db->where('users', ['email' => $email]);
        if (!empty($existing)) {
            return ['success' => false, 'message' => '该邮箱已被注册'];
        }

        $data = [
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'name' => $name ?: explode('@', $email)[0],
            'role' => 'user',
            'status' => 'active',
            'email_verified' => 0
        ];

        $userId = $this->db->insert('users', $data);

        if ($userId) {
            return ['success' => true, 'message' => '注册成功', 'user_id' => $userId];
        }
        return ['success' => false, 'message' => '注册失败，请稍后重试'];
    }

    /**
     * 密码登录
     */
    public function login($email, $password) {
        $users = $this->db->where('users', ['email' => $email]);

        if (empty($users)) {
            return ['success' => false, 'message' => '邮箱或密码错误'];
        }

        $user = reset($users);

        if ($user['status'] === 'banned') {
            return ['success' => false, 'message' => '账号已被禁用'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => '邮箱或密码错误'];
        }

        // 设置Session
        $this->setSession($user);

        return ['success' => true, 'message' => '登录成功', 'user' => $this->sanitizeUser($user)];
    }

    /**
     * 邮箱验证码登录
     */
    public function emailCodeLogin($email, $code) {
        // 验证验证码
        session_start();
        $stored_code = $_SESSION['email_code'] ?? '';
        $stored_time = $_SESSION['email_code_time'] ?? 0;
        $stored_email = $_SESSION['code_email'] ?? '';

        if (time() - $stored_time > 300) { // 5分钟过期
            return ['success' => false, 'message' => '验证码已过期，请重新获取'];
        }

        if ($stored_email !== $email || $stored_code !== $code) {
            return ['success' => false, 'message' => '验证码错误'];
        }

        // 清除验证码
        unset($_SESSION['email_code'], $_SESSION['email_code_time'], $_SESSION['code_email']);

        // 查找或创建用户
        $users = $this->db->where('users', ['email' => $email]);

        if (empty($users)) {
            // 自动注册
            $result = $this->register($email, '', explode('@', $email)[0]);
            if (!$result['success']) {
                return $result;
            }
            $users = $this->db->where('users', ['email' => $email]);
        }

        $user = reset($users);

        if ($user['status'] === 'banned') {
            return ['success' => false, 'message' => '账号已被禁用'];
        }

        $this->setSession($user);

        return ['success' => true, 'message' => '登录成功', 'user' => $this->sanitizeUser($user)];
    }

    /**
     * 发送邮箱验证码
     */
    public function sendEmailCode($email) {
        // 频率限制：60秒内只能发送一次
        session_start();
        if (!empty($_SESSION['email_code_time']) && time() - $_SESSION['email_code_time'] < 60) {
            return ['success' => false, 'message' => '发送过于频繁，请60秒后重试'];
        }

        $code = generate_code(6);
        $_SESSION['email_code'] = $code;
        $_SESSION['email_code_time'] = time();
        $_SESSION['code_email'] = $email;

        // 发送邮件
        $mailer = new Mailer();
        $subject = '【语云科技】登录验证码';
        $body = "
        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;padding:20px;'>
            <h2 style='color:#0066CC;'>语云科技 - 邮箱验证</h2>
            <p>您好！</p>
            <p>您的登录验证码为：</p>
            <div style='background:#f5f5f5;padding:20px;text-align:center;font-size:32px;font-weight:bold;color:#0066CC;border-radius:8px;margin:20px 0;'>
                {$code}
            </div>
            <p>验证码有效期为5分钟，请尽快使用。</p>
            <p>如非本人操作，请忽略此邮件。</p>
            <hr style='border:none;border-top:1px solid #eee;margin:20px 0;'/>
            <p style='color:#999;font-size:12px;'>此邮件由系统自动发送，请勿回复。</p>
            <p style='color:#0066CC;font-weight:bold;'>语云科技 © " . date('Y') . "</p>
        </div>";

        $result = $mailer->send($email, $subject, $body);

        if ($result) {
            return ['success' => true, 'message' => '验证码已发送至您的邮箱'];
        }
        return ['success' => false, 'message' => '邮件发送失败，请联系管理员'];
    }

    /**
     * 管理员登录
     */
    public function adminLogin($username, $password) {
        $config = get_config();

        // 默认管理员账号
        $default_admin = [
            'id' => 1,
            'email' => $config['admin_email'] ?? 'admin@yuyun.com',
            'password' => $config['admin_password'] ?? '',
            'name' => '超级管理员',
            'role' => 'admin'
        ];

        // 从数据库查找管理员
        $users = $this->db->where('users', ['email' => $username, 'role' => 'admin']);

        if (!empty($users)) {
            $admin = reset($users);
        } else {
            // 使用默认管理员
            if ($username !== $default_admin['email']) {
                return ['success' => false, 'message' => '账号或密码错误'];
            }
            $admin = $default_admin;
        }

        // 验证密码
        if (isset($admin['password']) && $admin['password'] && !password_verify($password, $admin['password'])) {
            return ['success' => false, 'message' => '账号或密码错误'];
        }

        // 如果是默认管理员且密码未设置
        if (empty($admin['password'])) {
            $config_pwd = $config['admin_password'] ?? '';
            if ($password !== $config_pwd) {
                return ['success' => false, 'message' => '账号或密码错误'];
            }
        }

        // 设置管理员Session
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_role'] = 'admin';
        $_SESSION['admin_login_time'] = time();

        return ['success' => true, 'message' => '登录成功'];
    }

    /**
     * 退出登录
     */
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }

    /**
     * 获取当前用户
     */
    public function getCurrentUser() {
        if (!is_logged_in()) {
            return null;
        }

        $users = $this->db->where('users', ['id' => $_SESSION['user_id']]);
        return !empty($users) ? $this->sanitizeUser(reset($users)) : null;
    }

    /**
     * 更新用户信息
     */
    public function updateUser($userId, $data) {
        unset($data['id'], $data['role']); // 不允许修改ID和角色
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('users', $userId, $data);
    }

    /**
     * 修改密码
     */
    public function changePassword($userId, $oldPassword, $newPassword) {
        $user = $this->db->getById('users', $userId);

        if (!$user) {
            return ['success' => false, 'message' => '用户不存在'];
        }

        if (!password_verify($oldPassword, $user['password'])) {
            return ['success' => false, 'message' => '原密码错误'];
        }

        return $this->db->update('users', $userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]) ?
            ['success' => true, 'message' => '密码修改成功'] :
            ['success' => false, 'message' => '修改失败'];
    }

    /**
     * 设置Session
     */
    private function setSession($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['login_time'] = time();
    }

    /**
     * 清理用户数据输出
     */
    private function sanitizeUser($user) {
        unset($user['password']);
        return $user;
    }
}
