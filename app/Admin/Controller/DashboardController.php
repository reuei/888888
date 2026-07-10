<?php
namespace Admin\Controller;

use Framework\Database\Database;

class DashboardController extends BaseAdminController
{
    protected $layout = 'admin';

    public function index($request, $params = [])
    {
        $stats = $this->getStats();
        $this->assign('stats', $stats);
        $this->assign('recentOrders', $this->getRecentOrders());
        $this->assign('trends', $this->getTrendData());
        $this->assign('pageTitle', '仪表盘');
        $this->assign('activeMenu', 'dashboard');
        $this->view('admin.dashboard');
    }

    public function screen($request, $params = [])
    {
        $stats = $this->getStats();
        $this->assign('stats', $stats);
        $this->assign('pageTitle', '数据大屏');
        $this->assign('activeMenu', 'screen');
        $this->assign('isScreen', true);
        $this->view('admin.screen');
    }

    protected function getStats()
    {
        $stats = [
            'total_orders' => 1247,
            'total_income' => 89542.30,
            'today_orders' => 86,
            'today_income' => 5678.90,
            'total_users' => 12580,
            'today_users' => 28,
            'total_goods' => 156,
            'total_shops' => 89,
        ];
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $stats['total_orders'] = (int) $db->table('order')->count();
                $stats['total_users'] = (int) $db->table('user')->count();
                $stats['total_goods'] = (int) $db->table('goods')->count();
                $stats['total_shops'] = (int) $db->table('shop')->count();
            }
        } catch (\Exception $e) {
        }
        return $stats;
    }

    protected function getRecentOrders()
    {
        $orders = [];
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $orders = $db->table('order')->orderBy('id', 'DESC')->limit(8)->get();
            }
        } catch (\Exception $e) {
        }
        if (empty($orders)) {
            $orders = [
                ['order_no' => '20260709123001', 'goods_name' => '腾讯视频VIP月卡', 'amount' => 19.90, 'status' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-1 hour'))],
                ['order_no' => '20260709123002', 'goods_name' => '爱奇艺黄金会员季卡', 'amount' => 45.00, 'status' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-2 hour'))],
                ['order_no' => '20260709123003', 'goods_name' => '网易云音乐年卡', 'amount' => 88.00, 'status' => 0, 'create_time' => date('Y-m-d H:i:s', strtotime('-3 hour'))],
                ['order_no' => '20260709123004', 'goods_name' => 'Steam充值卡', 'amount' => 95.00, 'status' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-5 hour'))],
            ];
        }
        return $orders;
    }

    protected function getTrendData()
    {
        return [
            'days' => ['7-03', '7-04', '7-05', '7-06', '7-07', '7-08', '7-09'],
            'orders' => [68, 92, 78, 105, 87, 95, 112],
            'income' => [4250, 5680, 4820, 6780, 5460, 5920, 7280],
        ];
    }
}
