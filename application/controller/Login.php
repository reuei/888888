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

        if (!$username || !$password) {
            json_error('请输入账号和密码');
        }

        if ($type === 'merchant') {
            $user = Db::fetch("SELECT * FROM jz_merchant WHERE username = ? AND status = 1", [$username]);
            if (!$user || !password_verify($password, $user['password'])) {
                json_error('账号或密码错误');
            }
            session('merchant_user', $user);
            Db::execute("UPDATE jz_merchant SET last_login_time = ?, update_time = ? WHERE id = ?", [date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $user['id']]);
            json_success('登录成功', ['redirect' => url('merchant/dashboard')]);
        } else {
            $user = Db::fetch("SELECT * FROM jz_admin WHERE username = ? AND status = 1", [$username]);
            if (!$user || !password_verify($password, $user['password'])) {
                json_error('账号或密码错误');
            }
            session('admin_user', $user);
            Db::execute("UPDATE jz_admin SET last_login_time = ?, update_time = ? WHERE id = ?", [date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $user['id']]);

            // 分站超管进入分站后台
            if (($user['role'] === 'subsite_super' || $user['role'] === 'subsite_admin') && $user['subsite_id'] > 0) {
                json_success('登录成功', ['redirect' => url('subsite/dashboard')]);
            }

            json_success('登录成功', ['redirect' => url('admin/dashboard')]);
        }
    }

    public function logout()
    {
        unset($_SESSION['admin_user']);
        unset($_SESSION['merchant_user']);
        redirect(url('login'));
    }
}
