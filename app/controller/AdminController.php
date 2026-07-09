<?php
namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Session;
use think\facade\Request;
use think\facade\Cache;

class AdminController extends BaseController
{
    public function login()
    {
        if ($this->isAdminLogin()) {
            return redirect('/admin');
        }
        return $this->fetch('admin/login');
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

        if ($username === 'admin' && $password === 'admin123') {
            Session::set('admin_id', 1);
            Session::set('admin_username', $username);
            return $this->jsonSuccess('登录成功', ['url' => '/admin']);
        }

        return $this->jsonError('用户名或密码错误');
    }

    public function logout()
    {
        Session::delete('admin_id');
        Session::delete('admin_username');
        return redirect('/admin/login');
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => 12580,
            'today_users' => 156,
            'total_orders' => 45678,
            'today_orders' => 234,
            'total_amount' => 568900.50,
            'today_amount' => 12580.00,
            'total_goods' => 256,
            'online_users' => 89,
        ];

        $orderTrend = [
            'dates' => ['7/2', '7/3', '7/4', '7/5', '7/6', '7/7', '7/8'],
            'orders' => [180, 210, 195, 220, 205, 240, 234],
            'amount' => [8500, 9200, 8800, 10500, 9800, 11200, 12580],
        ];

        $categorySales = [
            ['name' => '游戏点卡', 'value' => 35],
            ['name' => '视频会员', 'value' => 28],
            ['name' => '音乐会员', 'value' => 15],
            ['name' => '软件激活', 'value' => 12],
            ['name' => '其他', 'value' => 10],
        ];

        $recentOrders = [
            ['order_no' => '20260708123456', 'username' => 'user001', 'goods' => '腾讯视频VIP月卡', 'amount' => 19.90, 'status' => 1, 'time' => '14:30'],
            ['order_no' => '20260708234567', 'username' => 'user002', 'goods' => '网易云年卡', 'amount' => 88.00, 'status' => 1, 'time' => '14:15'],
            ['order_no' => '20260708345678', 'username' => 'user003', 'goods' => 'Steam100元', 'amount' => 95.00, 'status' => 0, 'time' => '14:00'],
        ];

        $this->assign('stats', $stats);
        $this->assign('orderTrend', $orderTrend);
        $this->assign('categorySales', $categorySales);
        $this->assign('recentOrders', $recentOrders);
        return $this->fetch('admin/dashboard');
    }

    public function screen()
    {
        $stats = [
            'total_users' => 12580,
            'today_users' => 156,
            'total_orders' => 45678,
            'today_orders' => 234,
            'total_amount' => 568900.50,
            'today_amount' => 12580.00,
            'conversion_rate' => 68.5,
            'avg_order' => 12.45,
        ];

        $hourlyOrders = array_map(function() { return rand(5, 30); }, range(0, 23));
        $provinceData = [
            ['name' => '广东', 'value' => 2580],
            ['name' => '浙江', 'value' => 1890],
            ['name' => '江苏', 'value' => 1650],
            ['name' => '北京', 'value' => 1420],
            ['name' => '上海', 'value' => 1380],
            ['name' => '山东', 'value' => 1150],
            ['name' => '四川', 'value' => 980],
            ['name' => '湖北', 'value' => 870],
        ];

        $this->assign('stats', $stats);
        $this->assign('hourlyOrders', $hourlyOrders);
        $this->assign('provinceData', $provinceData);
        return $this->fetch('admin/screen');
    }

    public function shopUsers()
    {
        $users = [
            ['id' => 1, 'shop_name' => '优品卡店', 'username' => 'merchant001', 'balance' => 12580.50, 'status' => 1, 'register_time' => '2026-05-01', 'orders' => 1256],
            ['id' => 2, 'shop_name' => '极速点卡', 'username' => 'merchant002', 'balance' => 8900.00, 'status' => 1, 'register_time' => '2026-05-15', 'orders' => 876],
            ['id' => 3, 'shop_name' => '数字商城', 'username' => 'merchant003', 'balance' => 5600.80, 'status' => 0, 'register_time' => '2026-06-01', 'orders' => 234],
            ['id' => 4, 'shop_name' => '云帆点卡', 'username' => 'merchant004', 'balance' => 3200.00, 'status' => 1, 'register_time' => '2026-06-10', 'orders' => 567],
        ];
        $this->assign('users', $users);
        return $this->fetch('admin/shop_users');
    }

    public function shopRealname()
    {
        $list = [
            ['id' => 1, 'shop_name' => '优品卡店', 'real_name' => '张三', 'id_card' => '110***********1234', 'status' => 1, 'submit_time' => '2026-06-01'],
            ['id' => 2, 'shop_name' => '极速点卡', 'real_name' => '李四', 'id_card' => '310***********5678', 'status' => 0, 'submit_time' => '2026-06-15'],
            ['id' => 3, 'shop_name' => '数字商城', 'real_name' => '王五', 'id_card' => '440***********9012', 'status' => 2, 'submit_time' => '2026-06-20'],
        ];
        $this->assign('list', $list);
        return $this->fetch('admin/shop_realname');
    }

    public function shopQualification()
    {
        $list = [
            ['id' => 1, 'shop_name' => '优品卡店', 'company' => '某某科技有限公司', 'license' => '91110000**********', 'status' => 1, 'submit_time' => '2026-06-01'],
            ['id' => 2, 'shop_name' => '极速点卡', 'company' => '', 'license' => '', 'status' => 0, 'submit_time' => ''],
        ];
        $this->assign('list', $list);
        return $this->fetch('admin/shop_qualification');
    }

    public function shopCertification()
    {
        $list = [
            ['id' => 1, 'shop_name' => '优品卡店', 'cert_type' => '企业认证', 'cert_no' => 'CERT20260601001', 'status' => 1, 'expire_time' => '2027-06-01'],
            ['id' => 2, 'shop_name' => '极速点卡', 'cert_type' => '个人认证', 'cert_no' => 'CERT20260615002', 'status' => 0, 'expire_time' => ''],
        ];
        $this->assign('list', $list);
        return $this->fetch('admin/shop_certification');
    }

    public function shopRisk()
    {
        $list = [
            ['id' => 1, 'shop_name' => '优品卡店', 'risk_level' => '低', 'risk_score' => 15, 'warn_count' => 0, 'last_check' => '2026-07-07'],
            ['id' => 2, 'shop_name' => '极速点卡', 'risk_level' => '中', 'risk_score' => 65, 'warn_count' => 2, 'last_check' => '2026-07-08'],
            ['id' => 3, 'shop_name' => '数字商城', 'risk_level' => '高', 'risk_score' => 85, 'warn_count' => 5, 'last_check' => '2026-07-08'],
        ];
        $this->assign('list', $list);
        return $this->fetch('admin/shop_risk');
    }

    public function shopService()
    {
        $list = [
            ['id' => 1, 'shop_name' => '优品卡店', 'service_qq' => '123456789', 'response_time' => '5分钟内', 'rating' => 4.8, 'status' => 1],
            ['id' => 2, 'shop_name' => '极速点卡', 'service_qq' => '987654321', 'response_time' => '10分钟内', 'rating' => 4.5, 'status' => 1],
        ];
        $this->assign('list', $list);
        return $this->fetch('admin/shop_service');
    }

    public function messagePublish()
    {
        return $this->fetch('admin/message_publish');
    }

    public function doMessagePublish()
    {
        $title = Request::param('title', '');
        $content = Request::param('content', '');
        $target = Request::param('target', 'all');
        return $this->jsonSuccess('消息发布成功');
    }

    public function messageList()
    {
        $list = [
            ['id' => 1, 'title' => '系统维护通知', 'target' => '全体用户', 'send_time' => '2026-07-08 10:00', 'read_count' => 8560, 'status' => 1],
            ['id' => 2, 'title' => '新用户专享优惠', 'target' => '新用户', 'send_time' => '2026-07-06 09:00', 'read_count' => 3200, 'status' => 1],
            ['id' => 3, 'title' => '充值优惠活动', 'target' => '全体用户', 'send_time' => '2026-07-01 00:00', 'read_count' => 9800, 'status' => 0],
        ];
        $this->assign('list', $list);
        return $this->fetch('admin/message_list');
    }

    public function noticePublish()
    {
        return $this->fetch('admin/notice_publish');
    }

    public function doNoticePublish()
    {
        $title = Request::param('title', '');
        $content = Request::param('content', '');
        $type = Request::param('type', 'system');
        return $this->jsonSuccess('公告发布成功');
    }

    public function noticeList()
    {
        $list = [
            ['id' => 1, 'title' => '系统维护通知', 'type' => '系统公告', 'create_time' => '2026-07-08 10:00', 'views' => 2580, 'status' => 1],
            ['id' => 2, 'title' => '新功能上线公告', 'type' => '更新公告', 'create_time' => '2026-07-05 14:00', 'views' => 3200, 'status' => 1],
            ['id' => 3, 'title' => '春节放假通知', 'type' => '活动公告', 'create_time' => '2026-06-25 09:00', 'views' => 1800, 'status' => 0],
        ];
        $this->assign('list', $list);
        return $this->fetch('admin/notice_list');
    }

    public function systemSite()
    {
        $config = [
            'site_name' => '玄武发卡',
            'site_title' => '玄武发卡网 - 专业的数字点卡交易平台',
            'site_keywords' => '发卡网,点卡,游戏点卡,视频会员',
            'site_description' => '玄武发卡网是专业的数字点卡交易平台，提供游戏点卡、视频会员、音乐会员等各类数字商品。',
            'site_logo' => '',
            'site_icp' => '京ICP备12345678号',
            'site_copyright' => '© 2026 玄武发卡网',
            'register_open' => 1,
            'site_status' => 1,
        ];
        $this->assign('config', $config);
        return $this->fetch('admin/system_site');
    }

    public function saveSystemSite()
    {
        return $this->jsonSuccess('保存成功');
    }

    public function systemUpdate()
    {
        $currentVersion = config('app.app_version');
        $updateInfo = [
            'has_update' => false,
            'latest_version' => $currentVersion,
            'release_time' => '2026-07-08',
            'update_content' => [],
        ];
        $this->assign('currentVersion', $currentVersion);
        $this->assign('updateInfo', $updateInfo);
        return $this->fetch('admin/system_update');
    }

    public function checkUpdate()
    {
        $result = update_check_remote();
        return $this->jsonSuccess('检查完成', $result);
    }

    public function doUpgrade()
    {
        $packageUrl = Request::param('package_url', '');
        if (empty($packageUrl)) {
            return $this->jsonError('更新包地址不能为空');
        }
        $result = update_apply_upgrade($packageUrl);
        if ($result) {
            return $this->jsonSuccess('更新成功，请刷新页面');
        }
        return $this->jsonError('更新失败');
    }

    public function systemWithdraw()
    {
        $config = [
            'withdraw_open' => 1,
            'min_amount' => 10,
            'max_amount' => 5000,
            'fee_rate' => 0.01,
            'fee_min' => 1,
            'withdraw_time' => '工作日9:00-18:00',
        ];
        $list = [
            ['id' => 1, 'shop_name' => '优品卡店', 'amount' => 500, 'fee' => 5, 'status' => 0, 'apply_time' => '2026-07-08 10:30'],
            ['id' => 2, 'shop_name' => '极速点卡', 'amount' => 1000, 'fee' => 10, 'status' => 1, 'apply_time' => '2026-07-07 14:20'],
        ];
        $this->assign('config', $config);
        $this->assign('list', $list);
        return $this->fetch('admin/system_withdraw');
    }

    public function systemChannel()
    {
        $channels = [
            ['id' => 1, 'name' => '支付宝', 'code' => 'alipay', 'type' => '支付', 'status' => 1, 'sort' => 1],
            ['id' => 2, 'name' => '微信支付', 'code' => 'wxpay', 'type' => '支付', 'status' => 1, 'sort' => 2],
            ['id' => 3, 'name' => 'QQ钱包', 'code' => 'qqpay', 'type' => '支付', 'status' => 0, 'sort' => 3],
            ['id' => 4, 'name' => '易支付', 'code' => 'epay', 'type' => '支付', 'status' => 1, 'sort' => 4],
            ['id' => 5, 'name' => '阿里云短信', 'code' => 'aliyun_sms', 'type' => '短信', 'status' => 1, 'sort' => 1],
            ['id' => 6, 'name' => '腾讯云短信', 'code' => 'tencent_sms', 'type' => '短信', 'status' => 0, 'sort' => 2],
        ];
        $this->assign('channels', $channels);
        return $this->fetch('admin/system_channel');
    }

    public function dataLog()
    {
        $logs = [
            ['id' => 1, 'level' => 'info', 'module' => 'order', 'message' => '订单创建成功', 'time' => '2026-07-08 14:30:25', 'ip' => '192.168.1.100'],
            ['id' => 2, 'level' => 'warning', 'module' => 'user', 'message' => '用户连续登录失败5次', 'time' => '2026-07-08 14:25:10', 'ip' => '192.168.1.101'],
            ['id' => 3, 'level' => 'error', 'module' => 'payment', 'message' => '支付回调验证失败', 'time' => '2026-07-08 14:20:33', 'ip' => '192.168.1.102'],
            ['id' => 4, 'level' => 'info', 'module' => 'user', 'message' => '用户注册成功', 'time' => '2026-07-08 14:15:42', 'ip' => '192.168.1.103'],
        ];
        $this->assign('logs', $logs);
        return $this->fetch('admin/data_log');
    }

    public function dataServer()
    {
        $serverInfo = [
            'os' => PHP_OS,
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'mysql_version' => '5.7.36',
            'zend_version' => zend_version(),
            'sapi' => PHP_SAPI,
            'server_time' => date('Y-m-d H:i:s'),
            'server_ip' => $_SERVER['SERVER_ADDR'] ?? '127.0.0.1',
        ];
        
        $sysLoad = [
            'cpu_usage' => 35,
            'memory_usage' => 62,
            'disk_usage' => 48,
            'memory_total' => '8GB',
            'memory_used' => '5GB',
            'disk_total' => '100GB',
            'disk_used' => '48GB',
        ];
        
        $this->assign('serverInfo', $serverInfo);
        $this->assign('sysLoad', $sysLoad);
        return $this->fetch('admin/data_server');
    }

    public function dataDatabase()
    {
        $tables = [
            ['name' => 'pre_user', 'engine' => 'InnoDB', 'rows' => 12580, 'size' => '2.5MB', 'collation' => 'utf8mb4_general_ci'],
            ['name' => 'pre_order', 'engine' => 'InnoDB', 'rows' => 45678, 'size' => '15.8MB', 'collation' => 'utf8mb4_general_ci'],
            ['name' => 'pre_goods', 'engine' => 'InnoDB', 'rows' => 256, 'size' => '1.2MB', 'collation' => 'utf8mb4_general_ci'],
            ['name' => 'pre_category', 'engine' => 'InnoDB', 'rows' => 24, 'size' => '64KB', 'collation' => 'utf8mb4_general_ci'],
            ['name' => 'pre_config', 'engine' => 'InnoDB', 'rows' => 56, 'size' => '128KB', 'collation' => 'utf8mb4_general_ci'],
        ];
        
        $this->assign('tables', $tables);
        return $this->fetch('admin/data_database');
    }

    public function dataLogin()
    {
        $logs = [
            ['id' => 1, 'username' => 'admin', 'type' => '后台', 'ip' => '192.168.1.100', 'location' => '北京', 'status' => 1, 'time' => '2026-07-08 14:00:00'],
            ['id' => 2, 'username' => 'user001', 'type' => '前台', 'ip' => '192.168.1.101', 'location' => '上海', 'status' => 1, 'time' => '2026-07-08 13:45:20'],
            ['id' => 3, 'username' => 'merchant001', 'type' => '商家', 'ip' => '192.168.1.102', 'location' => '广州', 'status' => 1, 'time' => '2026-07-08 12:30:15'],
            ['id' => 4, 'username' => 'admin', 'type' => '后台', 'ip' => '192.168.1.103', 'location' => '深圳', 'status' => 0, 'time' => '2026-07-08 11:20:00'],
        ];
        $this->assign('logs', $logs);
        return $this->fetch('admin/data_login');
    }

    public function sliderCaptcha()
    {
        $data = slider_captcha_generate();
        return $this->jsonSuccess('获取成功', $data);
    }

    public function sliderVerify()
    {
        $token = Request::param('token', '');
        $x = Request::param('x', 0);
        $result = slider_captcha_verify($token, $x);
        if ($result) {
            return $this->jsonSuccess('验证通过');
        }
        return $this->jsonError('验证失败');
    }
}
