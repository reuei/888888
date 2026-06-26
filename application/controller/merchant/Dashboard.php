<?php
/**
 * 商户后台 - 仪表盘
 */
class Merchant_Dashboard extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/merchant');
        if (!session('merchant_user')) {
            redirect(url('login') . '?type=merchant');
        }
    }

    public function index()
    {
        $merchant = session('merchant_user');

        $kpi = [
            'today_amount' => '3,240.00',
            'today_orders' => 48,
            'pending_orders' => 5,
            'balance' => $merchant['balance'] ?? '0.00',
            'frozen_balance' => $merchant['frozen_balance'] ?? '0.00',
        ];

        $this->assign('title', '店铺概览');
        $this->assign('merchant', $merchant);
        $this->assign('kpi', $kpi);
        $this->fetch('merchant/dashboard/index');
    }
}
