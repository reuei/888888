<?php
/**
 * Migrated from main_legacy/controller/app/controller/subsite/Dashboard.php
 */
namespace app\controller\subsite;

/**
 * 分站后台 - 仪表盘
 */
class Dashboard extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/subsite');
        if (!$this->checkAuth()) {
            redirect(url('login') . '?type=admin');
        }
    }

    private function checkAuth()
    {
        $admin = session('admin_user');
        if (!$admin) return false;
        if (in_array($admin['role'] ?? '', ['super', 'admin'], true)) return true;
        if (($admin['role'] === 'subsite_super' || $admin['role'] === 'subsite_admin') && ($admin['subsite_id'] ?? 0) > 0) {
            return true;
        }
        return false;
    }

    private function getSubsiteId()
    {
        $admin = session('admin_user');
        if (in_array($admin['role'] ?? '', ['super', 'admin'], true)) {
            return (int) input('subsite_id', $admin['subsite_id'] ?? 0);
        }
        return (int) ($admin['subsite_id'] ?? 0);
    }

    public function index()
    {
        $subsiteId = $this->getSubsiteId();
        $today = date('Y-m-d 00:00:00');

        $subsite = Db::fetch("SELECT * FROM jz_subsite WHERE id = ?", [$subsiteId]);

        // 商户统计
        $merchantStat = Db::fetch(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS normal
            FROM jz_merchant WHERE subsite_id = ?",
            [$subsiteId]
        );

        // 今日订单与成交额
        $todayStat = Db::fetch(
            "SELECT COUNT(*) AS total, COALESCE(SUM(pay_amount), 0) AS amount
             FROM jz_order WHERE subsite_id = ? AND status >= 1 AND create_time >= ?",
            [$subsiteId, $today]
        );

        // 待发货订单
        $pendingOrders = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_order WHERE subsite_id = ? AND status = 1",
            [$subsiteId]
        );

        // 商品统计
        $goodsStat = Db::fetch(
            "SELECT COUNT(*) AS total, SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS onsale FROM jz_goods WHERE subsite_id = ?",
            [$subsiteId]
        );

        // 近 7 天趋势
        $trend = Db::query(
            "SELECT DATE(create_time) AS day, COUNT(*) AS orders, COALESCE(SUM(pay_amount), 0) AS amount
             FROM jz_order
             WHERE subsite_id = ? AND status >= 1 AND create_time >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             GROUP BY DATE(create_time)
             ORDER BY day ASC",
            [$subsiteId]
        );

        // 最近订单
        $latestOrders = Db::query(
            "SELECT * FROM jz_order WHERE subsite_id = ? ORDER BY id DESC LIMIT 5",
            [$subsiteId]
        );

        $kpi = [
            'merchant_total' => $merchantStat['total'] ?? 0,
            'merchant_pending' => $merchantStat['pending'] ?? 0,
            'today_orders' => $todayStat['total'] ?? 0,
            'today_amount' => number_format($todayStat['amount'] ?? 0, 2),
            'pending_orders' => $pendingOrders['total'] ?? 0,
            'goods_total' => $goodsStat['total'] ?? 0,
            'goods_onsale' => $goodsStat['onsale'] ?? 0,
        ];

        $this->assign('title', '分站概览');
        $this->assign('subsite', $subsite);
        $this->assign('kpi', $kpi);
        $this->assign('trend', $trend);
        $this->assign('latestOrders', $latestOrders);
        $this->fetch('subsite/dashboard/index');
    }
}
