<?php
/**
 * 后台仪表盘
 */

namespace app\controller\admin;

use app\BaseController;
use app\Db;
use think\App;

class Dashboard extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        require_admin_login();
        $this->setLayout('layout/admin');
    }

    public function index()
    {
        $today = date('Y-m-d 00:00:00');

        $stats = Db::fetch("SELECT COUNT(*) AS total_users FROM qef_user");
        $productStats = Db::fetch("SELECT COUNT(*) AS total_products FROM qef_product");
        $pluginStats = Db::fetch("SELECT COUNT(*) AS total_plugins FROM qef_plugin");
        $licenseStats = Db::fetch("SELECT COUNT(*) AS total_licenses FROM qef_license");
        $orderStats = Db::fetch("SELECT COUNT(*) AS total_orders, IFNULL(SUM(pay_amount), 0) AS total_amount FROM qef_order WHERE status = 1");
        $todayStats = Db::fetch("SELECT COUNT(*) AS today_orders, IFNULL(SUM(pay_amount), 0) AS today_amount FROM qef_order WHERE status = 1 AND create_time >= ?", [$today]);

        $recentOrders = Db::query("SELECT * FROM qef_order ORDER BY id DESC LIMIT 8");
        $pendingPlugins = Db::query("SELECT p.*, u.nickname as author FROM qef_plugin p LEFT JOIN qef_user u ON p.user_id = u.id WHERE p.status = 0 ORDER BY p.id DESC LIMIT 5");
        $pendingRecharges = Db::query("SELECT r.*, u.nickname FROM qef_recharge r LEFT JOIN qef_user u ON r.user_id = u.id WHERE r.status = 0 ORDER BY r.id DESC LIMIT 5");

        $this->assign('title', '仪表盘');
        $this->assign('stats', array_merge($stats, $productStats, $pluginStats, $licenseStats, $orderStats, $todayStats));
        $this->assign('recentOrders', $recentOrders);
        $this->assign('pendingPlugins', $pendingPlugins);
        $this->assign('pendingRecharges', $pendingRecharges);
        $this->fetch('admin/dashboard/index');
    }
}
