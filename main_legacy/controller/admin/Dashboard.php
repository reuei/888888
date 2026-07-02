<?php
/**
 * 总站后台 - 仪表盘
 */
class Admin_Dashboard extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/admin');
        if (!session('admin_user')) {
            redirect(url('login') . '?type=admin');
        }
    }

    public function index()
    {
        $admin = session('admin_user');

        // 模拟 KPI 数据（实际应从统计表汇总）
        $kpi = [
            'total_amount' => '1,284,590.00',
            'total_orders' => '12,450',
            'platform_income' => '38,537.70',
            'merchant_count' => 286,
            'user_count' => '5,820',
        ];

        $this->assign('title', '仪表盘');
        $this->assign('admin', $admin);
        $this->assign('kpi', $kpi);
        $this->fetch('admin/dashboard/index');
    }
}
