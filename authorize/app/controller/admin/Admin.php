<?php
/**
 * 管理员登录控制器
 */

namespace app\controller\admin;

use app\BaseController;
use app\Db;
use think\App;
use think\facade\Session;

class Admin extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    public function index()
    {
        if (get_admin_user()) {
            redirect(url('admin/dashboard'));
        }
        $this->disableLayout();
        $this->assign('title', '管理员登录');
        $this->fetch('admin/login');
    }

    public function doLogin()
    {
        $username = trim(input('username', ''));
        $password = input('password', '');

        if (!$username || !$password) {
            json_error('请填写账号和密码');
        }

        $admin = Db::fetch("SELECT * FROM qef_admin WHERE username = ? AND status = 1", [$username]);
        if (!$admin || !password_verify_custom($password, $admin['password'])) {
            json_error('账号或密码错误');
        }

        Db::update('qef_admin', [
            'last_login_time' => date('Y-m-d H:i:s'),
            'last_login_ip' => get_client_ip(),
        ], 'id = ?', [$admin['id']]);

        session('admin_user', $admin);
        admin_log('登录后台');
        json_success('登录成功', ['redirect' => url('admin/dashboard')]);
    }

    public function logout()
    {
        Session::delete('admin_user');
        redirect(url('admin/admin'));
    }
}
