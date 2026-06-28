<?php
/**
 * 分站后台 - 商品管理
 */
class Subsite_Goods extends Controller
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
     * 分站商品列表
     */
    public function index()
    {
        $subsiteId = $this->getSubsiteId();
        $keyword = input('keyword', '');
        $status = input('status', '');
        $categoryId = input('category_id', '');
        $merchantId = input('merchant_id', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = 'g.subsite_id = ?';
        $params = [$subsiteId];
        if ($keyword) {
            $where .= ' AND (g.name LIKE ? OR g.id = ? OR m.shop_name LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = is_numeric($keyword) ? (int) $keyword : 0;
            $params[] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where .= ' AND g.status = ?';
            $params[] = (int) $status;
        }
        if ($categoryId !== '') {
            $where .= ' AND g.category_id = ?';
            $params[] = (int) $categoryId;
        }
        if ($merchantId !== '') {
            $where .= ' AND g.merchant_id = ?';
            $params[] = (int) $merchantId;
        }

        $count = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_goods g LEFT JOIN jz_merchant m ON g.merchant_id = m.id WHERE {$where}",
            $params
        );
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT g.*, m.shop_name, c.name AS category_name
             FROM jz_goods g
             LEFT JOIN jz_merchant m ON g.merchant_id = m.id
             LEFT JOIN jz_category c ON g.category_id = c.id
             WHERE {$where}
             ORDER BY g.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $categories = Db::query("SELECT id, name FROM jz_category WHERE status = 1 ORDER BY sort ASC, id ASC");
        $merchants = Db::query("SELECT id, shop_name FROM jz_merchant WHERE subsite_id = ? AND status = 1 ORDER BY id DESC", [$subsiteId]);

        $this->assign('title', '分站商品列表');
        $this->assign('list', $list);
        $this->assign('categories', $categories);
        $this->assign('merchants', $merchants);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('categoryId', $categoryId);
        $this->assign('merchantId', $merchantId);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('subsite/goods/index');
    }

    /**
     * 库存监控
     */
    public function stock()
    {
        $subsiteId = $this->getSubsiteId();
        $keyword = input('keyword', '');
        $stockType = input('stock_type', 'low'); // low / out
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = 'g.subsite_id = ?';
        $params = [$subsiteId];
        if ($keyword) {
            $where .= ' AND (g.name LIKE ? OR g.id = ? OR m.shop_name LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = is_numeric($keyword) ? (int) $keyword : 0;
            $params[] = '%' . $keyword . '%';
        }
        if ($stockType === 'out') {
            $where .= ' AND g.stock <= 0';
        } else {
            $where .= ' AND g.stock > 0 AND g.stock <= g.low_stock';
        }

        $count = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_goods g LEFT JOIN jz_merchant m ON g.merchant_id = m.id WHERE {$where}",
            $params
        );
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT g.*, m.shop_name, c.name AS category_name,
                    (SELECT COUNT(*) FROM jz_card WHERE goods_id = g.id AND status = 0) AS card_stock
             FROM jz_goods g
             LEFT JOIN jz_merchant m ON g.merchant_id = m.id
             LEFT JOIN jz_category c ON g.category_id = c.id
             WHERE {$where}
             ORDER BY g.stock ASC, g.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        // 监控统计
        $stat = Db::fetch(
            "SELECT
                SUM(CASE WHEN g.stock <= 0 THEN 1 ELSE 0 END) AS out_stock,
                SUM(CASE WHEN g.stock > 0 AND g.stock <= g.low_stock THEN 1 ELSE 0 END) AS low_stock
             FROM jz_goods g
             WHERE g.subsite_id = ?",
            [$subsiteId]
        );

        $this->assign('title', '分站库存监控');
        $this->assign('list', $list);
        $this->assign('stat', $stat);
        $this->assign('stockType', $stockType);
        $this->assign('keyword', $keyword);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('subsite/goods/stock');
    }

    /**
     * 商品详情
     */
    public function detail()
    {
        $subsiteId = $this->getSubsiteId();
        $id = (int) input('id', 0);
        if (!$id) {
            redirect(url('subsite/goods'));
        }

        $goods = Db::fetch(
            "SELECT g.*, m.shop_name, c.name AS category_name
             FROM jz_goods g
             LEFT JOIN jz_merchant m ON g.merchant_id = m.id
             LEFT JOIN jz_category c ON g.category_id = c.id
             WHERE g.id = ? AND g.subsite_id = ?",
            [$id, $subsiteId]
        );
        if (!$goods) {
            redirect(url('subsite/goods'));
        }

        $cardStats = Db::fetch(
            "SELECT SUM(status = 0) AS unsold, SUM(status = 1) AS sold FROM jz_card WHERE goods_id = ?",
            [$id]
        );

        $orders = Db::query(
            "SELECT order_no, total_amount, pay_channel, status, create_time
             FROM jz_order WHERE goods_id = ? ORDER BY id DESC LIMIT 10",
            [$id]
        );

        $this->assign('title', '分站商品详情');
        $this->assign('goods', $goods);
        $this->assign('cardStats', $cardStats);
        $this->assign('orders', $orders);
        $this->fetch('subsite/goods/detail');
    }

    /**
     * 切换商品状态
     */
    public function toggleStatus()
    {
        $subsiteId = $this->getSubsiteId();
        $id = (int) input('id', 0);
        $status = (int) input('status', 0);
        if (!$id || !in_array($status, [0, 1], true)) {
            json_error('参数错误');
        }

        $goods = Db::fetch("SELECT id, subsite_id FROM jz_goods WHERE id = ?", [$id]);
        if (!$goods) {
            json_error('商品不存在');
        }
        if ((int) $goods['subsite_id'] !== $subsiteId) {
            json_error('无权操作该商品');
        }

        Db::execute(
            "UPDATE jz_goods SET status = ?, update_time = ? WHERE id = ?",
            [$status, date('Y-m-d H:i:s'), $id]
        );
        json_success($status == 1 ? '已上架' : '已下架');
    }

    /**
     * 批量下架
     */
    public function batchOffline()
    {
        $subsiteId = $this->getSubsiteId();
        $ids = input('ids', []);
        $reason = input('reason', '分站批量下架');
        if (empty($ids) || !is_array($ids)) {
            json_error('请选择商品');
        }

        $ids = array_map('intval', $ids);
        $in = implode(',', $ids);
        $affected = Db::execute(
            "UPDATE jz_goods SET status = 0, reason = ?, update_time = ? WHERE id IN ({$in}) AND subsite_id = ?",
            [$reason, date('Y-m-d H:i:s'), $subsiteId]
        );
        json_success('批量下架成功，共 ' . $affected . ' 个商品');
    }
}
