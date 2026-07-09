<?php
namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Session;
use think\facade\Request;

class UserController extends BaseController
{
    public function login()
    {
        if ($this->isLogin()) {
            return redirect('/user/center');
        }
        return $this->fetch('user/login');
    }

    public function doLogin()
    {
        $username = Request::param('username', '');
        $password = Request::param('password', '');
        $sliderToken = Request::param('slider_token', '');
        $sliderX = Request::param('slider_x', 0);

        if (empty($username) || empty($password)) {
            return $this->jsonError('用户名和密码不能为空');
        }

        if (!slider_captcha_verify($sliderToken, $sliderX)) {
            return $this->jsonError('人机验证失败');
        }

        if ($username === 'test' && $password === '123456') {
            Session::set('user_id', 1);
            Session::set('username', $username);
            Session::set('email', 'test@example.com');
            Session::set('balance', 100.00);
            return $this->jsonSuccess('登录成功', ['url' => '/user/center']);
        }

        return $this->jsonError('用户名或密码错误');
    }

    public function register()
    {
        if ($this->isLogin()) {
            return redirect('/user/center');
        }
        return $this->fetch('user/register');
    }

    public function doRegister()
    {
        $username = Request::param('username', '');
        $password = Request::param('password', '');
        $email = Request::param('email', '');
        $sliderToken = Request::param('slider_token', '');
        $sliderX = Request::param('slider_x', 0);

        if (empty($username) || empty($password) || empty($email)) {
            return $this->jsonError('请填写完整信息');
        }

        if (strlen($username) < 3 || strlen($username) > 20) {
            return $this->jsonError('用户名长度需在3-20位之间');
        }

        if (strlen($password) < 6) {
            return $this->jsonError('密码长度不能少于6位');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->jsonError('邮箱格式不正确');
        }

        if (!slider_captcha_verify($sliderToken, $sliderX)) {
            return $this->jsonError('人机验证失败');
        }

        $userId = rand(1000, 9999);
        Session::set('user_id', $userId);
        Session::set('username', $username);
        Session::set('email', $email);
        Session::set('balance', 0);

        return $this->jsonSuccess('注册成功', ['url' => '/user/center']);
    }

    public function logout()
    {
        Session::clear();
        return redirect('/login');
    }

    public function center()
    {
        $user = $this->getUserInfo();
        $stats = [
            'orders' => 28,
            'pending' => 2,
            'balance' => $user['balance'],
            'total_spent' => 568.50,
        ];
        
        $recentOrders = [
            ['order_no' => '20260708123456', 'goods_name' => '腾讯视频VIP会员月卡', 'amount' => 19.90, 'status' => 1, 'create_time' => '2026-07-08 14:30'],
            ['order_no' => '20260707234567', 'goods_name' => '网易云音乐黑胶年卡', 'amount' => 88.00, 'status' => 1, 'create_time' => '2026-07-07 10:15'],
            ['order_no' => '20260706345678', 'goods_name' => 'Steam充值卡100元', 'amount' => 95.00, 'status' => 0, 'create_time' => '2026-07-06 16:45'],
        ];

        $messages = [
            ['id' => 1, 'title' => '您的订单已发货', 'content' => '订单号：20260708123456', 'time' => '5分钟前', 'read' => 0],
            ['id' => 2, 'title' => '系统维护通知', 'content' => '7月15日凌晨2-4点系统升级维护', 'time' => '2小时前', 'read' => 0],
            ['id' => 3, 'title' => '充值成功', 'content' => '您的账户充值50元成功', 'time' => '1天前', 'read' => 1],
        ];

        $this->assign('user', $user);
        $this->assign('stats', $stats);
        $this->assign('recentOrders', $recentOrders);
        $this->assign('messages', $messages);
        return $this->fetch('user/center');
    }

    public function orders()
    {
        $status = Request::param('status', 0);
        $orders = [
            ['order_no' => '20260708123456', 'goods_name' => '腾讯视频VIP会员月卡', 'amount' => 19.90, 'status' => 1, 'create_time' => '2026-07-08 14:30', 'card_info' => '账号：vip@example.com'],
            ['order_no' => '20260707234567', 'goods_name' => '网易云音乐黑胶年卡', 'amount' => 88.00, 'status' => 1, 'create_time' => '2026-07-07 10:15', 'card_info' => '激活码：WY1234567890'],
            ['order_no' => '20260706345678', 'goods_name' => 'Steam充值卡100元', 'amount' => 95.00, 'status' => 0, 'create_time' => '2026-07-06 16:45', 'card_info' => ''],
            ['order_no' => '20260705456789', 'goods_name' => '爱奇艺黄金会员季卡', 'amount' => 45.00, 'status' => 2, 'create_time' => '2026-07-05 09:20', 'card_info' => ''],
        ];

        $this->assign('status', $status);
        $this->assign('orders', $orders);
        return $this->fetch('user/orders');
    }

    public function recharge()
    {
        $user = $this->getUserInfo();
        $amounts = [10, 20, 50, 100, 200, 500];
        $this->assign('user', $user);
        $this->assign('amounts', $amounts);
        return $this->fetch('user/recharge');
    }

    public function doRecharge()
    {
        $amount = Request::param('amount', 0);
        if ($amount <= 0) {
            return $this->jsonError('充值金额必须大于0');
        }
        return $this->jsonSuccess('充值订单已创建', ['order_no' => generate_order_no()]);
    }

    public function profile()
    {
        $user = $this->getUserInfo();
        $this->assign('user', $user);
        return $this->fetch('user/profile');
    }

    public function saveProfile()
    {
        $nickname = Request::param('nickname', '');
        $email = Request::param('email', '');
        Session::set('username', $nickname);
        Session::set('email', $email);
        return $this->jsonSuccess('保存成功');
    }

    public function messages()
    {
        $messages = [
            ['id' => 1, 'title' => '您的订单已发货', 'content' => '订单号：20260708123456，腾讯视频VIP会员月卡已发货', 'time' => '2026-07-08 14:32', 'read' => 0, 'type' => 'order'],
            ['id' => 2, 'title' => '系统维护通知', 'content' => '尊敬的用户，系统将于7月15日凌晨2:00-4:00进行升级维护，届时部分功能可能无法使用，给您带来不便敬请谅解。', 'time' => '2026-07-08 10:00', 'read' => 0, 'type' => 'system'],
            ['id' => 3, 'title' => '充值成功', 'content' => '您的账户充值50元成功，当前余额100.00元', 'time' => '2026-07-07 16:30', 'read' => 1, 'type' => 'finance'],
            ['id' => 4, 'title' => '新用户专享优惠', 'content' => '新用户首单立减5元，快来选购吧！', 'time' => '2026-07-06 09:00', 'read' => 1, 'type' => 'activity'],
        ];
        $this->assign('messages', $messages);
        return $this->fetch('user/messages');
    }
}
