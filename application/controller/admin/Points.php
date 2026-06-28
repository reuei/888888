<?php
/**
 * 总站后台 - 积分商城与成长体系
 */
class Admin_Points extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/admin');
        if (!session('admin_user')) {
            redirect(url('login') . '?type=admin');
        }
        check_admin_role(['super', 'admin']);
    }

    /**
     * 积分规则列表
     */
    public function index()
    {
        $list = Db::query("SELECT * FROM jz_points_rule ORDER BY sort ASC, id ASC");
        $this->assign('title', '积分规则');
        $this->assign('list', $list);
        $this->fetch('admin/points/index');
    }

    /**
     * 保存积分规则
     */
    public function saveRule()
    {
        $id = (int) input('id', 0);
        $data = [
            'name' => input('name', ''),
            'type' => input('type', ''),
            'points' => (int) input('points', 0),
            'growth_value' => (int) input('growth_value', 0),
            'limit_type' => input('limit_type', 'day'),
            'limit_count' => (int) input('limit_count', 0),
            'status' => (int) input('status', 1),
            'sort' => (int) input('sort', 0),
        ];

        if (!$data['name'] || !$data['type']) {
            json_error('规则名称和类型不能为空');
        }

        if ($id) {
            Db::update('jz_points_rule', $data, 'id = ?', [$id]);
        } else {
            Db::insert('jz_points_rule', $data);
        }
        admin_log('points_rule_save', ['id' => $id, 'data' => $data]);
        json_success('保存成功');
    }

    /**
     * 删除积分规则
     */
    public function deleteRule()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }
        Db::execute("DELETE FROM jz_points_rule WHERE id = ?", [$id]);
        admin_log('points_rule_delete', ['id' => $id]);
        json_success('删除成功');
    }

    /**
     * 积分商品列表
     */
    public function goods()
    {
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;
        $keyword = input('keyword', '');

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND title LIKE ?';
            $params[] = '%' . $keyword . '%';
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_points_goods WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT * FROM jz_points_goods WHERE {$where} ORDER BY sort ASC, id DESC LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '积分商品');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/points/goods');
    }

    /**
     * 保存积分商品
     */
    public function saveGoods()
    {
        $id = (int) input('id', 0);
        $data = [
            'title' => input('title', ''),
            'image' => input('image', ''),
            'description' => input('description', ''),
            'points' => (int) input('points', 0),
            'stock' => (int) input('stock', 0),
            'sort' => (int) input('sort', 0),
            'status' => (int) input('status', 1),
        ];

        if (!$data['title'] || $data['points'] <= 0) {
            json_error('商品标题和积分不能为空');
        }

        if ($id) {
            Db::update('jz_points_goods', $data, 'id = ?', [$id]);
        } else {
            Db::insert('jz_points_goods', $data);
        }
        admin_log('points_goods_save', ['id' => $id]);
        json_success('保存成功');
    }

    /**
     * 上下架积分商品
     */
    public function toggleGoods()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 0);
        if (!$id) {
            json_error('参数错误');
        }
        Db::execute("UPDATE jz_points_goods SET status = ? WHERE id = ?", [$status, $id]);
        admin_log('points_goods_toggle', ['id' => $id, 'status' => $status]);
        json_success('操作成功');
    }

    /**
     * 删除积分商品
     */
    public function deleteGoods()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }
        Db::execute("DELETE FROM jz_points_goods WHERE id = ?", [$id]);
        admin_log('points_goods_delete', ['id' => $id]);
        json_success('删除成功');
    }

    /**
     * 积分兑换订单
     */
    public function order()
    {
        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
        if ($status !== '') {
            $where .= ' AND po.status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_points_order po WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT po.*, u.nickname, u.mobile
             FROM jz_points_order po
             LEFT JOIN jz_user u ON po.user_id = u.id
             WHERE {$where}
             ORDER BY po.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $statusMap = [0 => '待处理', 1 => '已发放', 2 => '已取消'];

        $this->assign('title', '积分兑换订单');
        $this->assign('list', $list);
        $this->assign('status', $status);
        $this->assign('statusMap', $statusMap);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/points/order');
    }

    /**
     * 处理兑换订单
     */
    public function handleOrder()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 0);
        $deliverContent = input('deliver_content', '');
        if (!$id || !in_array($status, [1, 2], true)) {
            json_error('参数错误');
        }

        $order = Db::fetch("SELECT * FROM jz_points_order WHERE id = ? AND status = 0", [$id]);
        if (!$order) {
            json_error('订单不存在或已处理');
        }

        Db::execute(
            "UPDATE jz_points_order SET status = ?, deliver_content = ?, update_time = ? WHERE id = ?",
            [$status, $deliverContent, date('Y-m-d H:i:s'), $id]
        );
        admin_log('points_order_handle', ['id' => $id, 'status' => $status]);
        json_success('处理成功');
    }

    /**
     * 用户积分流水
     */
    public function log()
    {
        $userId = (int) input('user_id', 0);
        $type = input('type', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
        if ($userId) {
            $where .= ' AND pl.user_id = ?';
            $params[] = $userId;
        }
        if ($type) {
            $where .= ' AND pl.type = ?';
            $params[] = $type;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_points_log pl WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT pl.*, u.nickname, u.mobile
             FROM jz_points_log pl
             LEFT JOIN jz_user u ON pl.user_id = u.id
             WHERE {$where}
             ORDER BY pl.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $typeMap = [
            'register' => '注册',
            'login' => '登录',
            'order' => '下单',
            'review' => '评价',
            'invite' => '邀请',
            'redeem' => '兑换',
            'system' => '系统',
        ];

        $this->assign('title', '积分流水');
        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('typeMap', $typeMap);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/points/log');
    }
}
