<?php
/**
 * 分站后台 - 订单管理
 */
class Subsite_Order extends Controller
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

    /**
     * 分站订单列表与统计
     */
    public function index()
    {
        $subsiteId = $this->getSubsiteId();
        $keyword = input('keyword', '');
        $status = input('status', '');
        $payChannel = input('pay_channel', '');
        $riskFlag = input('risk_flag', '');
        $startTime = input('start_time', '');
        $endTime = input('end_time', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = 'o.subsite_id = ?';
        $params = [$subsiteId];
        if ($keyword) {
            $where .= ' AND (o.order_no LIKE ? OR o.goods_name LIKE ? OR o.id = ? OR m.shop_name LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = is_numeric($keyword) ? (int) $keyword : 0;
            $params[] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where .= ' AND o.status = ?';
            $params[] = (int) $status;
        }
        if ($payChannel) {
            $where .= ' AND o.pay_channel = ?';
            $params[] = $payChannel;
        }
        if ($riskFlag !== '') {
            $where .= ' AND o.risk_flag = ?';
            $params[] = (int) $riskFlag;
        }
        if ($startTime) {
            $where .= ' AND o.create_time >= ?';
            $params[] = $startTime;
        }
        if ($endTime) {
            $where .= ' AND o.create_time <= ?';
            $params[] = $endTime . ' 23:59:59';
        }

        // 统计数据
        $stat = Db::fetch(
            "SELECT
                COUNT(*) AS total_orders,
                COALESCE(SUM(CASE WHEN o.status >= 1 THEN o.pay_amount ELSE 0 END), 0) AS total_amount,
                COALESCE(SUM(CASE WHEN o.status = 0 THEN 1 ELSE 0 END), 0) AS pending_pay,
                COALESCE(SUM(CASE WHEN o.status = 1 THEN 1 ELSE 0 END), 0) AS pending_ship,
                COALESCE(SUM(CASE WHEN o.risk_flag = 1 THEN 1 ELSE 0 END), 0) AS risk_orders
             FROM jz_order o
             LEFT JOIN jz_merchant m ON o.merchant_id = m.id
             WHERE {$where}",
            $params
        );

        $count = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_order o LEFT JOIN jz_merchant m ON o.merchant_id = m.id WHERE {$where}",
            $params
        );
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT o.*, m.shop_name, s.name AS subsite_name
             FROM jz_order o
             LEFT JOIN jz_merchant m ON o.merchant_id = m.id
             LEFT JOIN jz_subsite s ON o.subsite_id = s.id
             WHERE {$where}
             ORDER BY o.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $payChannels = Db::query(
            "SELECT DISTINCT pay_channel FROM jz_order WHERE subsite_id = ? AND pay_channel != '' ORDER BY pay_channel ASC",
            [$subsiteId]
        );

        $statusMap = [
            0 => '待支付',
            1 => '已支付',
            2 => '已发货',
            3 => '已完成',
            4 => '退款中',
            5 => '已关闭',
        ];

        $this->assign('title', '分站订单列表');
        $this->assign('list', $list);
        $this->assign('stat', $stat);
        $this->assign('payChannels', $payChannels);
        $this->assign('statusMap', $statusMap);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('payChannel', $payChannel);
        $this->assign('riskFlag', $riskFlag);
        $this->assign('startTime', $startTime);
        $this->assign('endTime', $endTime);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('subsite/order/index');
    }

    /**
     * 订单详情
     */
    public function detail()
    {
        $subsiteId = $this->getSubsiteId();
        $id = (int) input('id', 0);
        if (!$id) {
            redirect(url('subsite/order'));
        }

        $order = Db::fetch(
            "SELECT o.*, m.shop_name, m.mobile AS merchant_mobile, s.name AS subsite_name
             FROM jz_order o
             LEFT JOIN jz_merchant m ON o.merchant_id = m.id
             LEFT JOIN jz_subsite s ON o.subsite_id = s.id
             WHERE o.id = ? AND o.subsite_id = ?",
            [$id, $subsiteId]
        );
        if (!$order) {
            redirect(url('subsite/order'));
        }

        $cards = Db::query(
            "SELECT content, remark FROM jz_card WHERE order_id = ?",
            [$order['order_no']]
        );

        $complaint = Db::fetch(
            "SELECT * FROM jz_complaint WHERE order_id = ? ORDER BY id DESC LIMIT 1",
            [$id]
        );

        $statusMap = [
            0 => '待支付',
            1 => '已支付',
            2 => '已发货',
            3 => '已完成',
            4 => '退款中',
            5 => '已关闭',
        ];

        $this->assign('title', '分站订单详情');
        $this->assign('order', $order);
        $this->assign('cards', $cards);
        $this->assign('complaint', $complaint);
        $this->assign('statusMap', $statusMap);
        $this->fetch('subsite/order/detail');
    }

    /**
     * 标记发货
     */
    public function deliver()
    {
        $subsiteId = $this->getSubsiteId();
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }

        $order = Db::fetch("SELECT status, subsite_id FROM jz_order WHERE id = ?", [$id]);
        if (!$order) {
            json_error('订单不存在');
        }
        if ((int) $order['subsite_id'] !== $subsiteId) {
            json_error('无权操作该订单');
        }
        if ($order['status'] != 1) {
            json_error('订单状态不允许发货');
        }

        Db::execute(
            "UPDATE jz_order SET status = 2, update_time = ? WHERE id = ?",
            [date('Y-m-d H:i:s'), $id]
        );
        json_success('发货成功');
    }

    /**
     * 订单退款
     */
    public function refund()
    {
        $subsiteId = $this->getSubsiteId();
        $id = (int) input('id', 0);
        $reason = input('reason', '');
        if (!$id) {
            json_error('参数错误');
        }

        $order = Db::fetch("SELECT status, pay_amount, subsite_id FROM jz_order WHERE id = ?", [$id]);
        if (!$order) {
            json_error('订单不存在');
        }
        if ((int) $order['subsite_id'] !== $subsiteId) {
            json_error('无权操作该订单');
        }
        if (!in_array($order['status'], [1, 2], true)) {
            json_error('当前订单状态不允许退款');
        }

        Db::execute(
            "UPDATE jz_order SET status = 4, update_time = ? WHERE id = ?",
            [date('Y-m-d H:i:s'), $id]
        );
        json_success('已标记退款中');
    }

    /**
     * 关闭订单
     */
    public function close()
    {
        $subsiteId = $this->getSubsiteId();
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }

        $order = Db::fetch("SELECT status, subsite_id FROM jz_order WHERE id = ?", [$id]);
        if (!$order) {
            json_error('订单不存在');
        }
        if ((int) $order['subsite_id'] !== $subsiteId) {
            json_error('无权操作该订单');
        }
        if ($order['status'] != 0) {
            json_error('只能关闭待支付订单');
        }

        Db::execute(
            "UPDATE jz_order SET status = 5, update_time = ? WHERE id = ?",
            [date('Y-m-d H:i:s'), $id]
        );
        json_success('订单已关闭');
    }

    /**
     * 分站投诉列表
     */
    public function complaint()
    {
        $subsiteId = $this->getSubsiteId();
        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = 'o.subsite_id = ?';
        $params = [$subsiteId];
        if ($status !== '') {
            $where .= ' AND c.status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_complaint c
             LEFT JOIN jz_order o ON c.order_id = o.id
             WHERE {$where}",
            $params
        );
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT c.*, o.order_no, o.goods_name, o.total_amount, m.shop_name
             FROM jz_complaint c
             LEFT JOIN jz_order o ON c.order_id = o.id
             LEFT JOIN jz_merchant m ON c.merchant_id = m.id
             WHERE {$where}
             ORDER BY c.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '分站投诉管理');
        $this->assign('list', $list);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('subsite/order/complaint');
    }

    /**
     * 处理投诉
     */
    public function complaintHandle()
    {
        $subsiteId = $this->getSubsiteId();
        $id = (int) input('id', 0);
        $result = input('result', '');
        $remark = input('remark', '');
        if (!$id || !$result) {
            json_error('参数错误');
        }

        $complaint = Db::fetch(
            "SELECT c.*, o.subsite_id FROM jz_complaint c
             LEFT JOIN jz_order o ON c.order_id = o.id
             WHERE c.id = ?",
            [$id]
        );
        if (!$complaint) {
            json_error('投诉记录不存在');
        }
        if ((int) $complaint['subsite_id'] !== $subsiteId) {
            json_error('无权处理该投诉');
        }

        Db::execute(
            "UPDATE jz_complaint SET status = ?, result = ?, remark = ?, handle_time = ?, update_time = ? WHERE id = ?",
            [1, $result, $remark, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $id]
        );
        json_success('投诉已处理');
    }
}
