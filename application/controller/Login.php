<?php
/**
 * 登录控制器（总站 / 商户）
 */
class Login extends Controller
{
    public function index()
    {
        $type = input('type', 'admin');
        $this->disableLayout();
        $this->assign('type', $type);
        $this->fetch('login/index');
    }

    public function doLogin()
    {
        $type = input('type', 'admin');
        $username = input('username');
        $password = input('password');
        $captcha = input('captcha', '');

        if (!$username || !$password) {
            json_error('请输入账号和密码');
        }

        // IP 黑名单
        if (is_ip_blacklisted()) {
            record_login_attempt($username, $type, false, 'IP 黑名单');
            json_error('当前 IP 已被限制登录');
        }

        // 登录锁定
        if (is_login_locked($username, null, $type)) {
            record_login_attempt($username, $type, false, '登录已锁定');
            json_error('登录失败次数过多，请稍后再试');
        }

        // 验证码校验
        if (captcha_required('login') && !captcha_verify($captcha, 'login')) {
            record_login_attempt($username, $type, false, '验证码错误');
            json_error('验证码错误');
        }

        if ($type === 'merchant') {
            $user = Db::fetch("SELECT * FROM jz_merchant WHERE username = ? AND status = 1", [$username]);
            if (!$user || !password_verify($password, $user['password'])) {
                record_login_attempt($username, $type, false, '账号或密码错误');
                json_error('账号或密码错误');
            }
            session('merchant_user', $user);
            $ip = get_client_ip();
            Db::execute("UPDATE jz_merchant SET last_login_time = ?, last_login_ip = ?, update_time = ? WHERE id = ?", [date('Y-m-d H:i:s'), $ip, date('Y-m-d H:i:s'), $user['id']]);
            record_login_attempt($username, $type, true);
            json_success('登录成功', ['redirect' => url('merchant/dashboard')]);
        } else {
            $user = Db::fetch("SELECT * FROM jz_admin WHERE username = ? AND status = 1", [$username]);
            if (!$user || !password_verify($password, $user['password'])) {
                record_login_attempt($username, $type, false, '账号或密码错误');
                json_error('账号或密码错误');
            }
            session('admin_user', $user);
            $ip = get_client_ip();
            Db::execute("UPDATE jz_admin SET last_login_time = ?, last_login_ip = ?, update_time = ? WHERE id = ?", [date('Y-m-d H:i:s'), $ip, date('Y-m-d H:i:s'), $user['id']]);
            record_login_attempt($username, $type, true);

            // 分站超管进入分站后台
            if (($user['role'] === 'subsite_super' || $user['role'] === 'subsite_admin') && $user['subsite_id'] > 0) {
                json_success('登录成功', ['redirect' => url('subsite/dashboard')]);
            }

            json_success('登录成功', ['redirect' => url('admin/dashboard')]);
        }
    }

    /**
     * 图形验证码
     */
    public function captcha()
    {
        $key = input('key', 'login');
        $key = preg_replace('/[^a-zA-Z0-9_-]/', '', $key) ?: 'login';
        captcha_output($key);
    }

    public function logout()
    {
        unset($_SESSION['admin_user']);
        unset($_SESSION['merchant_user']);
        redirect(url('login'));
    }
}
