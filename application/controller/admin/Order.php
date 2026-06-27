<?php
/**
 * 总站后台 - 订单管理
 */
class Admin_Order extends Controller
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
     * 订单列表
     */
    public function index()
    {
        $keyword = input('keyword', '');
        $status = input('status', '');
        $subsiteId = input('subsite_id', '');
        $payChannel = input('pay_channel', '');
        $riskFlag = input('risk_flag', '');
        $startTime = input('start_time', '');
        $endTime = input('end_time', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
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
        if ($subsiteId !== '') {
            $where .= ' AND o.subsite_id = ?';
            $params[] = (int) $subsiteId;
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

        $subsites = Db::query("SELECT id, name FROM jz_subsite WHERE status = 1 ORDER BY id DESC");
        $payChannels = Db::query("SELECT DISTINCT pay_channel FROM jz_order WHERE pay_channel != '' ORDER BY pay_channel ASC");

        $statusMap = [
            0 => '待支付',
            1 => '已支付',
            2 => '已发货',
            3 => '已完成',
            4 => '退款中',
            5 => '已关闭',
        ];

        $this->assign('title', '订单列表');
        $this->assign('list', $list);
        $this->assign('subsites', $subsites);
        $this->assign('payChannels', $payChannels);
        $this->assign('statusMap', $statusMap);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('subsiteId', $subsiteId);
        $this->assign('payChannel', $payChannel);
        $this->assign('riskFlag', $riskFlag);
        $this->assign('startTime', $startTime);
        $this->assign('endTime', $endTime);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/order/index');
    }

    /**
     * 订单详情
     */
    public function detail()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            redirect(url('admin/order'));
        }

        $order = Db::fetch(
            "SELECT o.*, m.shop_name, m.mobile AS merchant_mobile, s.name AS subsite_name
             FROM jz_order o
             LEFT JOIN jz_merchant m ON o.merchant_id = m.id
             LEFT JOIN jz_subsite s ON o.subsite_id = s.id
             WHERE o.id = ?",
            [$id]
        );
        if (!$order) {
            redirect(url('admin/order'));
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

        $this->assign('title', '订单详情');
        $this->assign('order', $order);
        $this->assign('cards', $cards);
        $this->assign('complaint', $complaint);
        $this->assign('statusMap', $statusMap);
        $this->fetch('admin/order/detail');
    }

    /**
     * 标记发货
     */
    public function deliver()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }

        $order = Db::fetch("SELECT status FROM jz_order WHERE id = ?", [$id]);
        if (!$order) {
            json_error('订单不存在');
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
        $id = (int) input('id', 0);
        $reason = input('reason', '');
        if (!$id) {
            json_error('参数错误');
        }

        $order = Db::fetch("SELECT status, pay_amount FROM jz_order WHERE id = ?", [$id]);
        if (!$order) {
            json_error('订单不存在');
        }
        if (!in_array($order['status'], [1, 2], true)) {
            json_error('当前订单状态不允许退款');
        }

        // 示例：仅标记退款中状态，实际应调用支付通道退款
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
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }

        $order = Db::fetch("SELECT status FROM jz_order WHERE id = ?", [$id]);
        if (!$order) {
            json_error('订单不存在');
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
     * 投诉列表
     */
    public function complaint()
    {
        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
        if ($status !== '') {
            $where .= ' AND c.status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_complaint c WHERE {$where}", $params);
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

        $this->assign('title', '投诉管理');
        $this->assign('list', $list);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/order/complaint');
    }

    /**
     * 处理投诉
     */
    public function complaintHandle()
    {
        $id = (int) input('id', 0);
        $result = input('result', '');
        $remark = input('remark', '');
        if (!$id || !$result) {
            json_error('参数错误');
        }

        $complaint = Db::fetch("SELECT * FROM jz_complaint WHERE id = ?", [$id]);
        if (!$complaint) {
            json_error('投诉记录不存在');
        }

        Db::execute(
            "UPDATE jz_complaint SET status = ?, result = ?, remark = ?, handle_time = ?, update_time = ? WHERE id = ?",
            [1, $result, $remark, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $id]
        );
        json_success('投诉已处理');
    }
}
