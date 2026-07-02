<?php
/**
 * Migrated from main_legacy/controller/app/controller/admin/Stat.php
 */
namespace app\controller\admin;

/**
 * 总站后台 - 数据统计（经营报表 / 操作日志）
 */
class Stat extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/admin');
        if (!session('admin_user')) {
            redirect(url('login') . '?type=admin');
        }
    }

    /**
     * 经营报表
     */
    public function report()
    {
        $period = input('period', '7d');
        $subsiteId = input('subsite_id', '');
        $merchantId = input('merchant_id', '');

        $allowedPeriods = ['today' => 0, '7d' => 6, '30d' => 29, '90d' => 89];
        $days = $allowedPeriods[$period] ?? 6;
        $startDate = date('Y-m-d', strtotime("-{$days} day"));
        $endDate = date('Y-m-d');

        // 概览指标
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');
        $overviewWhere = 'o.status >= 1';
        $overviewParams = [];
        if ($subsiteId !== '') {
            $overviewWhere .= ' AND o.subsite_id = ?';
            $overviewParams[] = (int) $subsiteId;
        }
        if ($merchantId !== '') {
            $overviewWhere .= ' AND o.merchant_id = ?';
            $overviewParams[] = (int) $merchantId;
        }

        $overview = Db::fetch(
            "SELECT
                COUNT(*) AS total_orders,
                IFNULL(SUM(o.total_amount), 0) AS total_amount,
                IFNULL(SUM(o.pay_amount), 0) AS pay_amount,
                IFNULL(AVG(o.total_amount), 0) AS avg_amount
             FROM jz_order o
             WHERE {$overviewWhere} AND o.create_time BETWEEN ? AND ?",
            array_merge($overviewParams, [$todayStart, $todayEnd])
        );

        $todayUsers = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_user WHERE create_time BETWEEN ? AND ?",
            [$todayStart, $todayEnd]
        );

        $todayMerchants = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_merchant WHERE create_time BETWEEN ? AND ?",
            [$todayStart, $todayEnd]
        );

        // 日期趋势
        $dateRange = [];
        for ($i = $days; $i >= 0; $i--) {
            $dateRange[] = date('Y-m-d', strtotime("-{$i} day"));
        }

        $trendWhere = 'o.status >= 1';
        $trendParams = [];
        if ($subsiteId !== '') {
            $trendWhere .= ' AND o.subsite_id = ?';
            $trendParams[] = (int) $subsiteId;
        }
        if ($merchantId !== '') {
            $trendWhere .= ' AND o.merchant_id = ?';
            $trendParams[] = (int) $merchantId;
        }

        $trendRows = Db::query(
            "SELECT
                DATE(o.create_time) AS date,
                COUNT(*) AS order_count,
                IFNULL(SUM(o.total_amount), 0) AS total_amount,
                IFNULL(SUM(o.pay_amount), 0) AS pay_amount
             FROM jz_order o
             WHERE {$trendWhere} AND DATE(o.create_time) BETWEEN ? AND ?
             GROUP BY DATE(o.create_time)
             ORDER BY DATE(o.create_time) ASC",
            array_merge($trendParams, [$startDate, $endDate])
        );

        $trendMap = [];
        foreach ($trendRows as $row) {
            $trendMap[$row['date']] = $row;
        }

        $trend = [];
        foreach ($dateRange as $date) {
            $row = $trendMap[$date] ?? [];
            $trend[] = [
                'date' => $date,
                'order_count' => (int) ($row['order_count'] ?? 0),
                'total_amount' => (float) ($row['total_amount'] ?? 0),
                'pay_amount' => (float) ($row['pay_amount'] ?? 0),
            ];
        }

        // 分站排行
        $subsiteRank = Db::query(
            "SELECT
                s.id, s.name,
                COUNT(o.id) AS order_count,
                IFNULL(SUM(o.total_amount), 0) AS total_amount
             FROM jz_subsite s
             LEFT JOIN jz_order o ON o.subsite_id = s.id AND o.status >= 1 AND DATE(o.create_time) BETWEEN ? AND ?
             GROUP BY s.id, s.name
             ORDER BY total_amount DESC
             LIMIT 10",
            [$startDate, $endDate]
        );

        // 商品排行
        $goodsRankWhere = 'o.status >= 1';
        $goodsRankParams = [$startDate, $endDate];
        if ($subsiteId !== '') {
            $goodsRankWhere .= ' AND o.subsite_id = ?';
            $goodsRankParams[] = (int) $subsiteId;
        }
        if ($merchantId !== '') {
            $goodsRankWhere .= ' AND o.merchant_id = ?';
            $goodsRankParams[] = (int) $merchantId;
        }

        $goodsRank = Db::query(
            "SELECT
                g.id, g.name,
                COUNT(o.id) AS order_count,
                IFNULL(SUM(o.total_amount), 0) AS total_amount
             FROM jz_order o
             LEFT JOIN jz_goods g ON o.goods_id = g.id
             WHERE {$goodsRankWhere} AND DATE(o.create_time) BETWEEN ? AND ?
             GROUP BY g.id, g.name
             ORDER BY total_amount DESC
             LIMIT 10",
            $goodsRankParams
        );

        $subsites = Db::query("SELECT id, name FROM jz_subsite WHERE status = 1 ORDER BY id DESC");
        $merchants = Db::query("SELECT id, shop_name FROM jz_merchant WHERE status = 1 ORDER BY id DESC");

        $this->assign('title', '经营报表');
        $this->assign('period', $period);
        $this->assign('subsiteId', $subsiteId);
        $this->assign('merchantId', $merchantId);
        $this->assign('overview', $overview);
        $this->assign('todayUsers', $todayUsers['total'] ?? 0);
        $this->assign('todayMerchants', $todayMerchants['total'] ?? 0);
        $this->assign('trend', $trend);
        $this->assign('subsiteRank', $subsiteRank);
        $this->assign('goodsRank', $goodsRank);
        $this->assign('subsites', $subsites);
        $this->assign('merchants', $merchants);
        $this->fetch('admin/stat/report');
    }

    /**
     * 操作日志
     */
    public function log()
    {
        $keyword = input('keyword', '');
        $action = input('action', '');
        $adminName = input('admin_name', '');
        $startTime = input('start_time', '');
        $endTime = input('end_time', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (content LIKE ? OR action LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($action) {
            $where .= ' AND action = ?';
            $params[] = $action;
        }
        if ($adminName) {
            $where .= ' AND admin_name LIKE ?';
            $params[] = '%' . $adminName . '%';
        }
        if ($startTime) {
            $where .= ' AND create_time >= ?';
            $params[] = $startTime;
        }
        if ($endTime) {
            $where .= ' AND create_time <= ?';
            $params[] = $endTime . ' 23:59:59';
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_admin_log WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT * FROM jz_admin_log
             WHERE {$where}
             ORDER BY id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        // 常用操作类型
        $actions = Db::query("SELECT DISTINCT action FROM jz_admin_log ORDER BY action ASC");

        $this->assign('title', '操作日志');
        $this->assign('list', $list);
        $this->assign('actions', $actions);
        $this->assign('keyword', $keyword);
        $this->assign('action', $action);
        $this->assign('adminName', $adminName);
        $this->assign('startTime', $startTime);
        $this->assign('endTime', $endTime);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/stat/log');
    }
}
