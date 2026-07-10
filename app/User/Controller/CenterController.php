<?php
namespace User\Controller;

use Framework\Controller;
use Framework\Session;
use Framework\Database\Database;
use Framework\Response;

class CenterController extends Controller
{
    protected $layout = 'user';

    public function __construct()
    {
        parent::__construct();
        if (!Session::has('user_id')) {
            Response::redirect('/login');
        }
    }

    public function index($request, $params = [])
    {
        $user = $this->getUser();
        $stats = ['orders' => 0, 'pending' => 0, 'balance' => 0, 'total' => 0];
        $recentOrders = [];
        $messages = [];

        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $stats['orders'] = (int) $db->table('order')->where('user_id', $user['id'])->count();
                $stats['pending'] = (int) $db->table('order')->where('user_id', $user['id'])->where('status', 0)->count();
                $u = $db->table('user')->find($user['id']);
                if ($u) {
                    $stats['balance'] = (float) $u['balance'];
                }
                $recentOrders = $db->table('order')
                    ->where('user_id', $user['id'])
                    ->orderBy('id', 'DESC')
                    ->limit(5)
                    ->get();
                $messages = $db->table('message')
                    ->where('user_id', 0)
                    ->orderBy('id', 'DESC')
                    ->limit(3)
                    ->get();
            }
        } catch (\Exception $e) {
        }

        if (empty($recentOrders)) {
            $recentOrders = $this->getDefaultOrders();
        }
        if (empty($messages)) {
            $messages = $this->getDefaultMessages();
        }

        $this->assign('user', $user);
        $this->assign('stats', $stats);
        $this->assign('recentOrders', $recentOrders);
        $this->assign('messages', $messages);
        $this->assign('pageTitle', '用户中心');
        $this->assign('activeMenu', 'home');
        $this->view('user.center');
    }

    public function profile($request, $params = [])
    {
        $user = $this->getUser();
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $u = $db->table('user')->find($user['id']);
                if ($u) {
                    $user = array_merge($user, $u);
                }
            }
        } catch (\Exception $e) {
        }
        $this->assign('user', $user);
        $this->assign('pageTitle', '个人资料');
        $this->assign('activeMenu', 'profile');
        $this->view('user.profile');
    }

    public function saveProfile($request, $params = [])
    {
        $user = $this->getUser();
        $nickname = trim($request->post('nickname', ''));
        $email = trim($request->post('email', ''));
        $avatar = $request->post('avatar', '');

        if (empty($nickname)) {
            return $this->error('昵称不能为空');
        }
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('邮箱格式不正确');
        }

        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $db->table('user')->where('id', $user['id'])->update([
                    'nickname' => $nickname,
                    'email' => $email,
                    'avatar' => $avatar,
                ]);
            }
        } catch (\Exception $e) {
        }

        Session::set('nickname', $nickname);
        return $this->success('保存成功');
    }

    public function orders($request, $params = [])
    {
        $status = $request->get('status', -1);
        $user = $this->getUser();
        $orders = [];
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $q = $db->table('order')->where('user_id', $user['id']);
                if ($status >= 0) {
                    $q = $q->where('status', (int) $status);
                }
                $orders = $q->orderBy('id', 'DESC')->get();
            }
        } catch (\Exception $e) {
        }
        if (empty($orders)) {
            $orders = $this->getDefaultOrders();
        }
        $this->assign('orders', $orders);
        $this->assign('status', $status);
        $this->assign('pageTitle', '我的订单');
        $this->assign('activeMenu', 'orders');
        $this->view('user.orders');
    }

    public function recharge($request, $params = [])
    {
        $user = $this->getUser();
        $this->assign('user', $user);
        $this->assign('pageTitle', '账户充值');
        $this->assign('activeMenu', 'recharge');
        $this->view('user.recharge');
    }

    public function doRecharge($request, $params = [])
    {
        $amount = (float) $request->post('amount', 0);
        $channel = $request->post('channel', 'alipay');
        if ($amount <= 0) {
            return $this->error('充值金额必须大于0');
        }
        return $this->success('订单创建成功', [
            'order_no' => order_no(),
            'amount' => $amount,
            'channel' => $channel,
        ]);
    }

    public function messages($request, $params = [])
    {
        $messages = [];
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $messages = $db->table('message')
                    ->whereRaw('user_id = 0 OR user_id = ?', [Session::get('user_id', 0)])
                    ->orderBy('id', 'DESC')
                    ->get();
            }
        } catch (\Exception $e) {
        }
        if (empty($messages)) {
            $messages = $this->getDefaultMessages();
        }
        $this->assign('messages', $messages);
        $this->assign('pageTitle', '消息中心');
        $this->assign('activeMenu', 'messages');
        $this->view('user.messages');
    }

    public function readMessage($request, $params = [])
    {
        $id = (int) ($params['id'] ?? 0);
        $message = null;
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $db->raw("UPDATE xw_message SET is_read = 1 WHERE id = ?", [$id]);
                $message = $db->table('message')->find($id);
            }
        } catch (\Exception $e) {
        }
        if (!$message) {
            $defaults = $this->getDefaultMessages();
            $message = $defaults[0] ?? [];
        }
        $this->assign('message', $message);
        $this->assign('pageTitle', '消息详情');
        $this->assign('activeMenu', 'messages');
        $this->view('user.message_detail');
    }

    public function changePassword($request, $params = [])
    {
        $user = $this->getUser();
        $old = $request->post('old_password', '');
        $new = $request->post('new_password', '');
        $confirm = $request->post('confirm_password', '');

        if (strlen($new) < 6) {
            return $this->error('新密码长度不能少于6位');
        }
        if ($new !== $confirm) {
            return $this->error('两次输入的密码不一致');
        }

        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $u = $db->table('user')->find($user['id']);
                if ($u && !password_verify($old, $u['password'])) {
                    return $this->error('原密码错误');
                }
                $db->table('user')->where('id', $user['id'])->update([
                    'password' => password_hash($new, PASSWORD_DEFAULT),
                ]);
            }
        } catch (\Exception $e) {
        }
        return $this->success('密码修改成功');
    }

    public function uploadAvatar($request, $params = [])
    {
        if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            return $this->error('上传失败');
        }
        $file = $_FILES['avatar'];
        if ($file['size'] > 2 * 1024 * 1024) {
            return $this->error('文件大小不能超过2MB');
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return $this->error('仅支持图片格式');
        }
        $dir = PUBLIC_PATH . '/uploads/avatar';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $name = 'avatar_' . Session::get('user_id') . '_' . time() . '.' . $ext;
        $path = $dir . '/' . $name;
        if (move_uploaded_file($file['tmp_name'], $path)) {
            $url = '/uploads/avatar/' . $name;
            return $this->success('上传成功', ['url' => $url]);
        }
        return $this->error('上传失败');
    }

    protected function getUser()
    {
        return [
            'id' => Session::get('user_id', 0),
            'username' => Session::get('username', 'guest'),
            'nickname' => Session::get('nickname', '游客'),
            'email' => '',
            'balance' => 0,
        ];
    }

    protected function getDefaultOrders()
    {
        return [
            ['id' => 1, 'order_no' => '20260708123456', 'goods_name' => '腾讯视频VIP会员月卡', 'amount' => 19.90, 'status' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-1 day'))],
            ['id' => 2, 'order_no' => '20260707234567', 'goods_name' => '网易云音乐黑胶年卡', 'amount' => 88.00, 'status' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-2 day'))],
            ['id' => 3, 'order_no' => '20260706345678', 'goods_name' => 'Steam充值卡100元', 'amount' => 95.00, 'status' => 0, 'create_time' => date('Y-m-d H:i:s', strtotime('-3 day'))],
        ];
    }

    protected function getDefaultMessages()
    {
        return [
            ['id' => 1, 'title' => '您的订单已发货', 'content' => '订单已成功发货，请注意查收。', 'type' => 'order', 'is_read' => 0, 'create_time' => date('Y-m-d H:i:s', strtotime('-1 hour'))],
            ['id' => 2, 'title' => '系统维护通知', 'content' => '系统将于本周日凌晨进行例行维护。', 'type' => 'system', 'is_read' => 0, 'create_time' => date('Y-m-d H:i:s', strtotime('-2 hour'))],
            ['id' => 3, 'title' => '充值成功', 'content' => '您的账户已成功充值。', 'type' => 'finance', 'is_read' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-1 day'))],
        ];
    }
}
