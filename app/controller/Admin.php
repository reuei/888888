<?php
namespace app\controller;

use app\model\Admins;
use app\model\Users;
use app\model\Products;
use app\model\Licenses;
use app\model\Orders;
use app\model\Settings;
use think\facade\Session;

/**
 * 后台管理控制器
 */
class Admin extends BaseController
{
    /**
     * 后台登录
     */
    public function login()
    {
        if (Session::get('admin_id')) {
            return redirect('/admin/dashboard');
        }
        return $this->fetch();
    }

    /**
     * 执行后台登录
     */
    public function dologin()
    {
        $username = $this->request->post('username', '');
        $password = $this->request->post('password', '');
        
        if (empty($username) || empty($password)) {
            return $this->error('请填写完整信息');
        }
        
        $admin = Admins::where('username', $username)->find();
        
        if (!$admin) {
            return $this->error('管理员不存在');
        }
        
        if ($admin->status != 1) {
            return $this->error('账户已被禁用');
        }
        
        if (!password_verify($password, $admin->password)) {
            return $this->error('密码错误');
        }
        
        Session::set('admin_id', $admin->id);
        Session::set('admin_username', $admin->username);
        
        return $this->success(['redirect' => '/admin/dashboard'], '登录成功');
    }

    /**
     * 后台退出
     */
    public function logout()
    {
        Session::delete('admin_id');
        Session::delete('admin_username');
        return redirect('/admin/login');
    }

    /**
     * 后台首页
     */
    public function dashboard()
    {
        $this->checkAdminLogin();
        
        $stats = [
            'users'    => Users::count(),
            'products' => Products::count(),
            'licenses' => Licenses::count(),
            'orders'   => Orders::count(),
            'revenue'  => Orders::where('payment_status', 1)->sum('amount'),
        ];
        
        return $this->fetch('', ['stats' => $stats]);
    }

    /**
     * 用户管理
     */
    public function users()
    {
        $this->checkAdminLogin();
        
        $users = Users::order('created_at', 'desc')
            ->paginate(15);
        
        return $this->fetch('', ['users' => $users]);
    }

    /**
     * 产品管理
     */
    public function products()
    {
        $this->checkAdminLogin();
        
        $products = Products::order('sort', 'desc')
            ->paginate(15);
        
        return $this->fetch('', ['products' => $products]);
    }

    /**
     * 授权管理
     */
    public function licenses()
    {
        $this->checkAdminLogin();
        
        $licenses = Licenses::with(['user', 'product'])
            ->order('created_at', 'desc')
            ->paginate(15);
        
        return $this->fetch('', ['licenses' => $licenses]);
    }

    /**
     * 订单管理
     */
    public function orders()
    {
        $this->checkAdminLogin();
        
        $orders = Orders::with(['user', 'product'])
            ->order('created_at', 'desc')
            ->paginate(15);
        
        return $this->fetch('', ['orders' => $orders]);
    }

    /**
     * 系统设置
     */
    public function settings()
    {
        $this->checkAdminLogin();
        
        $settings = Settings::all();
        $config = [];
        
        foreach ($settings as $item) {
            $config[$item->key] = $item->value;
        }
        
        return $this->fetch('', ['config' => $config]);
    }

    /**
     * 保存系统设置
     */
    public function saveSettings()
    {
        $this->checkAdminLogin();
        
        $data = $this->request->post();
        
        foreach ($data as $key => $value) {
            Settings::where('key', $key)->update(['value' => $value]);
        }
        
        return $this->success([], '保存成功');
    }

    /**
     * 检查管理员登录状态
     */
    private function checkAdminLogin()
    {
        if (!Session::get('admin_id')) {
            redirect('/admin/login')->send();
            exit;
        }
    }
}