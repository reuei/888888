<?php
/**
 * 后台订单管理
 */
class Admin_Order extends Controller
{
    public function __construct()
    {
        parent::__construct();
        require_admin_login();
        $this->setLayout('layout/admin');
    }

    public function index()
    {
        $page = max(1, (int) input('page', 1));
        $pageSize = 15;
        $keyword = trim(input('keyword', ''));
        $status = input('status', '');

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (order_no LIKE ? OR item_name LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where .= ' AND status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM qef_order WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT o.*, u.username, u.nickname FROM qef_order o LEFT JOIN qef_user u ON o.user_id = u.id WHERE {$where} ORDER BY o.id DESC LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '订单管理');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/order/index');
    }

    public function detail()
    {
        $id = (int) input('id', 0);
        $order = Db::fetch(
            "SELECT o.*, u.username, u.nickname FROM qef_order o LEFT JOIN qef_user u ON o.user_id = u.id WHERE o.id = ?",
            [$id]
        );
        if (!$order) {
            throw new Exception('订单不存在');
        }
        $licenses = [];
        if ($order['item_type'] === 'product' && $order['license_id'] > 0) {
            $licenses = Db::query(
                "SELECT * FROM qef_license WHERE user_id = ? AND product_id = ? AND create_time = ? ORDER BY id DESC",
                [$order['user_id'], $order['item_id'], $order['pay_time']]
            );
        }

        $this->assign('title', '订单详情');
        $this->assign('order', $order);
        $this->assign('licenses', $licenses);
        $this->fetch('admin/order/detail');
    }
}
