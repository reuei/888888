<?php
namespace app\controller;

use app\model\Users;
use app\model\Products;
use app\model\Licenses;
use app\model\Orders;
use app\model\BalanceLogs;
use app\model\LoginLogs;
use app\model\OperationLogs;
use app\model\Feedback;
use think\facade\Session;
use think\facade\Db;

/**
 * 用户控制器
 */
class User extends BaseController
{
    /**
     * 登录页面
     */
    public function login()
    {
        if (Session::get('user_id')) {
            return redirect('/user/dashboard');
        }
        return $this->fetch();
    }

    /**
     * 执行登录
     */
    public function dologin()
    {
        $username = $this->request->post('username', '');
        $password = $this->request->post('password', '');
        
        if (empty($username) || empty($password)) {
            return $this->error('请填写完整信息');
        }
        
        $user = Users::where('username', $username)
            ->whereOr('email', $username)
            ->find();
        
        if (!$user) {
            $this->recordLoginLog(null, $username, 0, '用户不存在');
            return $this->error('用户不存在');
        }
        
        if ($user->status != 1) {
            $this->recordLoginLog($user->id, $username, 0, '账户已被禁用');
            return $this->error('账户已被禁用');
        }
        
        if (!password_verify($password, $user->password)) {
            $this->recordLoginLog($user->id, $username, 0, '密码错误');
            return $this->error('密码错误');
        }
        
        // 更新登录信息
        $user->login_ip   = $this->request->ip();
        $user->login_time = date('Y-m-d H:i:s');
        $user->save();
        
        Session::set('user_id', $user->id);
        Session::set('username', $user->username);
        Session::set('email', $user->email);
        
        $this->recordLoginLog($user->id, $username, 1, '登录成功');
        
        return $this->success(['redirect' => '/user/dashboard'], '登录成功');
    }

    /**
     * 注册页面
     */
    public function register()
    {
        return $this->fetch();
    }

    /**
     * 执行注册
     */
    public function doregister()
    {
        $data = $this->request->post();
        
        $validate = $this->validate($data, [
            'username|用户名' => 'require|min:3|max:20',
            'email|邮箱'     => 'require|email|unique:users',
            'password|密码'  => 'require|min:6|max:32',
        ]);
        
        // 检查用户名是否存在
        if (Users::where('username', $data['username'])->find()) {
            return $this->error('用户名已存在');
        }
        
        $user = new Users();
        $user->username = $data['username'];
        $user->email    = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $user->qq       = $data['qq'] ?? '';
        $user->phone    = $data['phone'] ?? '';
        
        if ($user->save()) {
            return $this->success(['redirect' => '/user/login'], '注册成功');
        }
        
        return $this->error('注册失败');
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        Session::delete('user_id');
        Session::delete('username');
        Session::delete('email');
        return redirect('/user/login');
    }

    /**
     * 用户中心首页
     */
    public function dashboard()
    {
        $this->checkLogin();
        
        $userId = Session::get('user_id');
        $user   = Users::find($userId);
        
        $stats = [
            'balance'    => $user->balance,
            'products'   => Licenses::where('user_id', $userId)->count(),
            'orders'     => Orders::where('user_id', $userId)->count(),
            'login_count' => LoginLogs::where('user_id', $userId)
                ->where('status', 1)
                ->count(),
        ];
        
        return $this->fetch('', ['stats' => $stats, 'user' => $user]);
    }

    /**
     * 工作台
     */
    public function workplace()
    {
        $this->checkLogin();
        
        $userId = Session::get('user_id');
        $user   = Users::find($userId);
        
        // 最近授权
        $licenses = Licenses::where('user_id', $userId)
            ->with('product')
            ->order('created_at', 'desc')
            ->limit(5)
            ->select();
        
        // 最近订单
        $orders = Orders::where('user_id', $userId)
            ->with('product')
            ->order('created_at', 'desc')
            ->limit(5)
            ->select();
        
        return $this->fetch('', [
            'user'     => $user,
            'licenses' => $licenses,
            'orders'   => $orders
        ]);
    }

    /**
     * 余额管理
     */
    public function balance()
    {
        $this->checkLogin();
        
        $userId = Session::get('user_id');
        $user   = Users::find($userId);
        
        $logs = BalanceLogs::where('user_id', $userId)
            ->order('created_at', 'desc')
            ->paginate(10);
        
        return $this->fetch('', ['user' => $user, 'logs' => $logs]);
    }

    /**
     * 余额充值
     */
    public function recharge()
    {
        $this->checkLogin();
        return $this->fetch();
    }

    /**
     * 产品中心
     */
    public function products()
    {
        $this->checkLogin();
        
        $products = Products::where('status', 1)
            ->order('sort', 'desc')
            ->paginate(12);
        
        return $this->fetch('', ['products' => $products]);
    }

    /**
     * 购买产品
     */
    public function buy()
    {
        $this->checkLogin();
        
        $productId = $this->request->get('product_id');
        $product   = Products::find($productId);
        
        if (!$product) {
            return redirect('/user/products');
        }
        
        return $this->fetch('', ['product' => $product]);
    }

    /**
     * 我的产品
     */
    public function myProducts()
    {
        $this->checkLogin();
        
        $userId = Session::get('user_id');
        $licenses = Licenses::where('user_id', $userId)
            ->with('product')
            ->order('created_at', 'desc')
            ->paginate(10);
        
        return $this->fetch('', ['licenses' => $licenses]);
    }

    /**
     * 插件中心
     */
    public function plugins()
    {
        $this->checkLogin();
        
        $plugins = Products::where('status', 1)
            ->where('type', 'plugin')
            ->order('sort', 'desc')
            ->paginate(12);
        
        return $this->fetch('', ['plugins' => $plugins]);
    }

    /**
     * 购买插件
     */
    public function buyPlugin()
    {
        $this->checkLogin();
        
        $pluginId = $this->request->get('product_id');
        $plugin   = Products::find($pluginId);
        
        if (!$plugin) {
            return redirect('/user/plugins');
        }
        
        return $this->fetch('', ['plugin' => $plugin]);
    }

    /**
     * 我的插件
     */
    public function myPlugins()
    {
        $this->checkLogin();
        
        $userId = Session::get('user_id');
        $licenses = Licenses::where('user_id', $userId)
            ->whereHas('product', function($query) {
                $query->where('type', 'plugin');
            })
            ->with('product')
            ->order('created_at', 'desc')
            ->paginate(10);
        
        return $this->fetch('', ['licenses' => $licenses]);
    }

    /**
     * 余额明细
     */
    public function balanceLog()
    {
        $this->checkLogin();
        
        $userId = Session::get('user_id');
        $logs = BalanceLogs::where('user_id', $userId)
            ->order('created_at', 'desc')
            ->paginate(15);
        
        return $this->fetch('', ['logs' => $logs]);
    }

    /**
     * 登录日志
     */
    public function loginLog()
    {
        $this->checkLogin();
        
        $userId = Session::get('user_id');
        $logs = LoginLogs::where('user_id', $userId)
            ->order('created_at', 'desc')
            ->paginate(15);
        
        return $this->fetch('', ['logs' => $logs]);
    }

    /**
     * 操作日志
     */
    public function operationLog()
    {
        $this->checkLogin();
        
        $userId = Session::get('user_id');
        $logs = OperationLogs::where('user_id', $userId)
            ->order('created_at', 'desc')
            ->paginate(15);
        
        return $this->fetch('', ['logs' => $logs]);
    }

    /**
     * 账户设置
     */
    public function settings()
    {
        $this->checkLogin();
        
        $userId = Session::get('user_id');
        $user   = Users::find($userId);
        
        return $this->fetch('', ['user' => $user]);
    }

    /**
     * 更新账户设置
     */
    public function updateSettings()
    {
        $this->checkLogin();
        
        $userId = Session::get('user_id');
        $user   = Users::find($userId);
        
        $data = $this->request->post();
        
        if (!empty($data['password'])) {
            if (empty($data['old_password']) || !password_verify($data['old_password'], $user->password)) {
                return $this->error('原密码错误');
            }
            $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $user->email  = $data['email'] ?? $user->email;
        $user->qq      = $data['qq'] ?? '';
        $user->phone   = $data['phone'] ?? '';
        
        if ($user->save()) {
            Session::set('email', $user->email);
            $this->recordOperationLog('更新设置', '修改账户信息');
            return $this->success([], '更新成功');
        }
        
        return $this->error('更新失败');
    }

    /**
     * 意见反馈
     */
    public function feedback()
    {
        $this->checkLogin();
        return $this->fetch();
    }

    /**
     * 提交意见反馈
     */
    public function submitFeedback()
    {
        $this->checkLogin();
        
        $userId = Session::get('user_id');
        $content = $this->request->post('content', '');
        
        if (empty($content)) {
            return $this->error('请填写反馈内容');
        }
        
        $feedback = new Feedback();
        $feedback->user_id = $userId;
        $feedback->content = $content;
        $feedback->contact = $this->request->post('contact', '');
        
        if ($feedback->save()) {
            $this->recordOperationLog('提交反馈', '提交意见反馈');
            return $this->success([], '提交成功');
        }
        
        return $this->error('提交失败');
    }

    /**
     * 检查登录状态
     */
    private function checkLogin()
    {
        if (!Session::get('user_id')) {
            redirect('/user/login')->send();
            exit;
        }
    }

    /**
     * 记录登录日志
     */
    private function recordLoginLog($userId, $username, $status, $message)
    {
        $log = new LoginLogs();
        $log->user_id    = $userId;
        $log->username   = $username;
        $log->ip         = $this->request->ip();
        $log->user_agent = $this->request->header('user-agent');
        $log->status     = $status;
        $log->message    = $message;
        $log->save();
    }

    /**
     * 记录操作日志
     */
    private function recordOperationLog($action, $description)
    {
        $log = new OperationLogs();
        $log->user_id    = Session::get('user_id');
        $log->action     = $action;
        $log->description = $description;
        $log->ip         = $this->request->ip();
        $log->user_agent = $this->request->header('user-agent');
        $log->save();
    }
}