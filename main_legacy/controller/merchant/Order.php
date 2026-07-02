<?php
/**
 * 商户后台 - 订单管理
 */
class Merchant_Order extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/merchant');
        if (!session('merchant_user')) {
            redirect(url('login') . '?type=merchant');
        }
    }

    /**
     * 订单列表
     */
    public function index()
    {
        $merchant = session('merchant_user');
        $keyword = input('keyword', '');
        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = 'o.merchant_id = ?';
        $params = [$merchant['id']];

        if ($keyword) {
            $where .= ' AND (o.order_no LIKE ? OR o.goods_name LIKE ? OR o.contact LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where .= ' AND o.status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_order o WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT o.*, g.type AS goods_type
             FROM jz_order o
             LEFT JOIN jz_goods g ON o.goods_id = g.id
             WHERE {$where}
             ORDER BY o.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '订单列表');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('merchant/order/index');
    }

    /**
     * 订单详情
     */
    public function detail()
    {
        $merchant = session('merchant_user');
        $id = (int) input('id', 0);

        $order = Db::fetch(
            "SELECT o.*, g.type AS goods_type FROM jz_order o
             LEFT JOIN jz_goods g ON o.goods_id = g.id
             WHERE o.id = ? AND o.merchant_id = ?",
            [$id, $merchant['id']]
        );

        if (!$order) {
            throw new Exception('订单不存在');
        }

        $cards = [];
        if ($order['goods_type'] == 1) {
            $cards = Db::query(
                "SELECT * FROM jz_card WHERE order_id = ? ORDER BY id ASC",
                [$order['order_no']]
            );
        }

        $this->assign('title', '订单详情');
        $this->assign('order', $order);
        $this->assign('cards', $cards);
        $this->fetch('merchant/order/detail');
    }

    /**
     * 订单发货
     */
    public function deliver()
    {
        $merchant = session('merchant_user');
        $id = (int) input('id', 0);
        $content = input('content', '');

        if (!$id) {
            json_error('参数错误');
        }

        $order = Db::fetch(
            "SELECT o.*, g.type AS goods_type FROM jz_order o
             LEFT JOIN jz_goods g ON o.goods_id = g.id
             WHERE o.id = ? AND o.merchant_id = ? AND o.status = 1",
            [$id, $merchant['id']]
        );

        if (!$order) {
            json_error('订单不存在或状态不允许发货');
        }

        $now = date('Y-m-d H:i:s');

        // 卡密类自动取卡密发货
        if ($order['goods_type'] == 1) {
            $cards = Db::query(
                "SELECT * FROM jz_card WHERE goods_id = ? AND status = 0 ORDER BY id ASC LIMIT ?",
                [$order['goods_id'], (int) $order['quantity']]
            );

            if (count($cards) < $order['quantity']) {
                json_error('卡密库存不足，无法发货');
            }

            $cardIds = array_column($cards, 'id');
            $cardContent = implode("\n", array_column($cards, 'content'));
            $placeholders = implode(',', array_fill(0, count($cardIds), '?'));

            Db::execute(
                "UPDATE jz_card SET status = 1, order_id = ?, sale_time = ? WHERE id IN ({$placeholders})",
                array_merge([$order['order_no'], $now], $cardIds)
            );

            Db::execute(
                "UPDATE jz_order SET status = 2, deliver_content = ?, deliver_time = ?, update_time = ? WHERE id = ?",
                [$cardContent, $now, $now, $id]
            );
        } else {
            // 人工 / 自动类使用传入内容
            if (!$content) {
                json_error('请填写发货内容');
            }
            Db::execute(
                "UPDATE jz_order SET status = 2, deliver_content = ?, deliver_time = ?, update_time = ? WHERE id = ?",
                [$content, $now, $now, $id]
            );
        }

        json_success('发货成功');
    }

    /**
     * 关闭订单
     */
    public function close()
    {
        $merchant = session('merchant_user');
        $id = (int) input('id', 0);

        $order = Db::fetch(
            "SELECT * FROM jz_order WHERE id = ? AND merchant_id = ? AND status = 0",
            [$id, $merchant['id']]
        );
        if (!$order) {
            json_error('订单不存在或状态不允许关闭');
        }

        $now = date('Y-m-d H:i:s');
        Db::execute(
            "UPDATE jz_order SET status = 5, update_time = ? WHERE id = ?",
            [$now, $id]
        );

        json_success('订单已关闭');
    }
}
