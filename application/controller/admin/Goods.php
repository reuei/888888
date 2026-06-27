<?php
/**
 * 总站后台 - 商品管理
 */
class Admin_Goods extends Controller
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
     * 全平台商品列表
     */
    public function index()
    {
        $keyword = input('keyword', '');
        $status = input('status', '');
        $subsiteId = input('subsite_id', '');
        $categoryId = input('category_id', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
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
        if ($subsiteId !== '') {
            $where .= ' AND g.subsite_id = ?';
            $params[] = (int) $subsiteId;
        }
        if ($categoryId !== '') {
            $where .= ' AND g.category_id = ?';
            $params[] = (int) $categoryId;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_goods g LEFT JOIN jz_merchant m ON g.merchant_id = m.id WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT g.*, m.shop_name, s.name AS subsite_name, c.name AS category_name
             FROM jz_goods g
             LEFT JOIN jz_merchant m ON g.merchant_id = m.id
             LEFT JOIN jz_subsite s ON g.subsite_id = s.id
             LEFT JOIN jz_category c ON g.category_id = c.id
             WHERE {$where}
             ORDER BY g.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $subsites = Db::query("SELECT id, name FROM jz_subsite WHERE status = 1 ORDER BY id DESC");
        $categories = Db::query("SELECT id, name FROM jz_category WHERE status = 1 ORDER BY sort ASC, id ASC");

        $this->assign('title', '全平台商品列表');
        $this->assign('list', $list);
        $this->assign('subsites', $subsites);
        $this->assign('categories', $categories);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('subsiteId', $subsiteId);
        $this->assign('categoryId', $categoryId);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/goods/index');
    }

    /**
     * 商品详情
     */
    public function detail()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            redirect(url('admin/goods'));
        }

        $goods = Db::fetch(
            "SELECT g.*, m.shop_name, s.name AS subsite_name, c.name AS category_name
             FROM jz_goods g
             LEFT JOIN jz_merchant m ON g.merchant_id = m.id
             LEFT JOIN jz_subsite s ON g.subsite_id = s.id
             LEFT JOIN jz_category c ON g.category_id = c.id
             WHERE g.id = ?",
            [$id]
        );
        if (!$goods) {
            redirect(url('admin/goods'));
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

        $cards = Db::query(
            "SELECT content, status, order_id, sale_time FROM jz_card WHERE goods_id = ? ORDER BY id DESC LIMIT 10",
            [$id]
        );

        $this->assign('title', '商品详情');
        $this->assign('goods', $goods);
        $this->assign('cardStats', $cardStats);
        $this->assign('orders', $orders);
        $this->assign('cards', $cards);
        $this->fetch('admin/goods/detail');
    }

    /**
     * 切换商品状态（上架 / 下架 / 违规下架）
     */
    public function toggleStatus()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 0);
        $reason = input('reason', '');
        if (!$id || !in_array($status, [0, 1, 2], true)) {
            json_error('参数错误');
        }

        $goods = Db::fetch("SELECT id, status FROM jz_goods WHERE id = ?", [$id]);
        if (!$goods) {
            json_error('商品不存在');
        }

        Db::execute(
            "UPDATE jz_goods SET status = ?, reason = ?, update_time = ? WHERE id = ?",
            [$status, $reason, date('Y-m-d H:i:s'), $id]
        );

        $labels = [0 => '已下架', 1 => '已上架', 2 => '已违规下架'];
        json_success($labels[$status]);
    }

    /**
     * 批量下架
     */
    public function batchOffline()
    {
        $ids = input('ids', []);
        $reason = input('reason', '平台批量下架');
        if (empty($ids) || !is_array($ids)) {
            json_error('请选择商品');
        }

        $ids = array_map('intval', $ids);
        $in = implode(',', $ids);
        $affected = Db::execute(
            "UPDATE jz_goods SET status = 0, reason = ?, update_time = ? WHERE id IN ({$in})",
            [$reason, date('Y-m-d H:i:s')]
        );
        json_success('批量下架成功，共 ' . $affected . ' 个商品');
    }

    /**
     * 商品分类列表
     */
    public function category()
    {
        $all = Db::query("SELECT * FROM jz_category ORDER BY sort ASC, id ASC");
        $map = [];
        foreach ($all as $c) {
            $map[$c['id']] = $c;
        }

        $tree = [];
        foreach ($all as $c) {
            $c['parent_name'] = $map[$c['parent_id']]['name'] ?? '顶级';
            $c['level'] = $c['parent_id'] == 0 ? 0 : 1;
            if ($c['parent_id'] == 0) {
                $tree[] = $c;
                foreach ($all as $child) {
                    if ($child['parent_id'] == $c['id']) {
                        $child['parent_name'] = $c['name'];
                        $child['level'] = 1;
                        $tree[] = $child;
                    }
                }
            }
        }

        $this->assign('title', '商品分类管理');
        $this->assign('list', $tree);
        $this->fetch('admin/goods/category');
    }

    /**
     * 保存分类（新增/编辑）
     */
    public function categorySave()
    {
        $id = (int) input('id', 0);
        $name = trim(input('name', ''));
        $parentId = (int) input('parent_id', 0);
        $sort = (int) input('sort', 0);
        $isNav = (int) input('is_nav', 1);
        $status = (int) input('status', 1);

        if (!$name) {
            json_error('请输入分类名称');
        }

        $data = [
            'name' => $name,
            'parent_id' => $parentId,
            'sort' => $sort,
            'is_nav' => $isNav,
            'status' => $status,
        ];

        if ($id) {
            Db::update('jz_category', $data, 'id = ?', [$id]);
            json_success('分类更新成功');
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            Db::insert('jz_category', $data);
            json_success('分类添加成功');
        }
    }

    /**
     * 删除分类
     */
    public function categoryDelete()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }

        $hasChild = Db::fetch("SELECT id FROM jz_category WHERE parent_id = ?", [$id]);
        if ($hasChild) {
            json_error('请先删除子分类');
        }

        $goodsCount = Db::fetch("SELECT COUNT(*) AS total FROM jz_goods WHERE category_id = ?", [$id]);
        if ($goodsCount['total'] > 0) {
            json_error('该分类下存在商品，无法删除');
        }

        Db::execute("DELETE FROM jz_category WHERE id = ?", [$id]);
        json_success('分类已删除');
    }

    /**
     * 禁售目录（示例：关键词/类目黑名单）
     */
    public function ban()
    {
        $keyword = input('keyword', '');
        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND keyword LIKE ?';
            $params[] = '%' . $keyword . '%';
        }

        $list = Db::query("SELECT * FROM jz_banned_keyword WHERE {$where} ORDER BY id DESC LIMIT 100", $params);
        $this->assign('title', '禁售目录');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->fetch('admin/goods/ban');
    }

    /**
     * 添加禁售关键词
     */
    public function banSave()
    {
        $keyword = trim(input('keyword', ''));
        $type = input('type', 'goods');
        if (!$keyword) {
            json_error('请输入关键词');
        }

        $exists = Db::fetch("SELECT id FROM jz_banned_keyword WHERE keyword = ?", [$keyword]);
        if ($exists) {
            json_error('该关键词已存在');
        }

        Db::insert('jz_banned_keyword', [
            'keyword' => $keyword,
            'type' => $type,
            'status' => 1,
            'create_time' => date('Y-m-d H:i:s'),
        ]);
        json_success('已添加');
    }

    /**
     * 删除禁售关键词
     */
    public function banDelete()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }
        Db::execute("DELETE FROM jz_banned_keyword WHERE id = ?", [$id]);
        json_success('已删除');
    }
}
