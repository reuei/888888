<?php
/**
 * Migrated from main_legacy/controller/app/controller/merchant/Dashboard.php
 */
namespace app\controller\merchant;

/**
 * 商户后台 - 仪表盘
 */
class Dashboard extends Controller
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
        $merchantId = $merchant['id'];
        $today = date('Y-m-d 00:00:00');

        // 今日成交额与订单数
        $todayStat = Db::fetch(
            "SELECT COUNT(*) AS total, COALESCE(SUM(pay_amount), 0) AS amount
             FROM jz_order
             WHERE merchant_id = ? AND status >= 1 AND create_time >= ?",
            [$merchantId, $today]
        );

        // 待处理订单（已支付未发货）
        $pendingOrders = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_order WHERE merchant_id = ? AND status = 1",
            [$merchantId]
        );

        // 商品总数与库存紧张
        $goodsStat = Db::fetch(
            "SELECT COUNT(*) AS total, SUM(CASE WHEN stock <= low_stock THEN 1 ELSE 0 END) AS low_stock
             FROM jz_goods WHERE merchant_id = ?",
            [$merchantId]
        );

        // 近 7 天销售趋势
        $trend = Db::query(
            "SELECT DATE(create_time) AS day, COUNT(*) AS orders, COALESCE(SUM(pay_amount), 0) AS amount
             FROM jz_order
             WHERE merchant_id = ? AND status >= 1 AND create_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             GROUP BY DATE(create_time)
             ORDER BY day ASC",
            [$merchantId]
        );

        // 最近订单
        $latestOrders = Db::query(
            "SELECT * FROM jz_order WHERE merchant_id = ? ORDER BY id DESC LIMIT 5",
            [$merchantId]
        );

        $kpi = [
            'today_amount' => number_format($todayStat['amount'] ?? 0, 2),
            'today_orders' => $todayStat['total'] ?? 0,
            'pending_orders' => $pendingOrders['total'] ?? 0,
            'balance' => $merchant['balance'] ?? '0.00',
            'frozen_balance' => $merchant['frozen_balance'] ?? '0.00',
            'goods_total' => $goodsStat['total'] ?? 0,
            'low_stock' => $goodsStat['low_stock'] ?? 0,
        ];

        $this->assign('title', '店铺概览');
        $this->assign('merchant', $merchant);
        $this->assign('kpi', $kpi);
        $this->assign('trend', $trend);
        $this->assign('latestOrders', $latestOrders);
        $this->fetch('merchant/dashboard/index');
    }
}
