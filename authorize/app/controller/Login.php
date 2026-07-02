<?php
/**
 * 前台登录/注册控制器
 */

namespace app\controller;

use app\BaseController;
use app\Db;
use think\App;
use think\facade\Session;

class Login extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    public function index()
    {
        if (get_user()) {
            return redirect(url('user'));
        }
        $this->assign('title', '用户登录');
        $this->fetch('login/index');
    }

    public function doLogin()
    {
        $username = trim(input('username', ''));
        $password = input('password', '');

        if (!$username || !$password) {
            json_error('请填写账号和密码');
        }

        $user = Db::fetch("SELECT * FROM qef_user WHERE username = ? AND status = 1", [$username]);
        if (!$user || !password_verify_custom($password, $user['password'])) {
            json_error('账号或密码错误');
        }

        session('user', $user);
        json_success('登录成功', ['redirect' => url('user')]);
    }

    public function register()
    {
        if (get_user()) {
            return redirect(url('user'));
        }
        $this->assign('title', '用户注册');
        $this->fetch('login/register');
    }

    public function doRegister()
    {
        $username = trim(input('username', ''));
        $password = input('password', '');
        $passwordConfirm = input('password_confirm', '');
        $nickname = trim(input('nickname', ''));
        $email = trim(input('email', ''));

        if (!$username || !$password || !$passwordConfirm) {
            json_error('请填写完整信息');
        }
        if (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
            json_error('账号为 4-20 位字母/数字/下划线');
        }
        if (strlen($password) < 6) {
            json_error('密码长度不能少于 6 位');
        }
        if ($password !== $passwordConfirm) {
            json_error('两次输入密码不一致');
        }
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            json_error('邮箱格式错误');
        }

        $exists = Db::fetch("SELECT id FROM qef_user WHERE username = ?", [$username]);
        if ($exists) {
            json_error('该账号已被注册');
        }

        $userId = Db::insert('qef_user', [
            'username' => $username,
            'nickname' => $nickname ?: $username,
            'email' => $email,
            'password' => password_hash_custom($password),
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        $user = Db::fetch("SELECT * FROM qef_user WHERE id = ?", [$userId]);
        session('user', $user);
        json_success('注册成功', ['redirect' => url('user')]);
    }

    public function logout()
    {
        Session::delete('user');
        return redirect(url('/'));
    }
}
