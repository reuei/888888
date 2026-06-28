<?php
/**
 * 总站后台 - 代理分销
 */
class Admin_Agent extends Controller
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
     * 代理商品列表
     */
    public function goods()
    {
        $keyword = input('keyword', '');
        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (g.name LIKE ? OR g.id = ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = is_numeric($keyword) ? (int) $keyword : 0;
        }
        if ($status !== '') {
            $where .= ' AND ag.status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_agent_goods ag LEFT JOIN jz_goods g ON ag.goods_id = g.id WHERE {$where}",
            $params
        );
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT ag.*, g.name AS goods_name, g.price, g.status AS goods_status
             FROM jz_agent_goods ag
             LEFT JOIN jz_goods g ON ag.goods_id = g.id
             WHERE {$where}
             ORDER BY ag.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '代理商品');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/agent/goods');
    }

    /**
     * 保存代理商品配置
     */
    public function goodsSave()
    {
        $id = (int) input('id', 0);
        $goodsId = (int) input('goods_id', 0);
        $commissionMode = (int) input('commission_mode', 1);
        $commissionRate = (float) input('commission_rate', 0);
        $commissionAmount = (float) input('commission_amount', 0);
        $multiLevel = (int) input('multi_level', 0);
        $level2Rate = (float) input('level2_rate', 0);
        $level3Rate = (float) input('level3_rate', 0);
        $status = (int) input('status', 1);

        if (!$goodsId) {
            json_error('请选择商品');
        }

        $goods = Db::fetch("SELECT id FROM jz_goods WHERE id = ?", [$goodsId]);
        if (!$goods) {
            json_error('商品不存在');
        }

        $exists = Db::fetch("SELECT id FROM jz_agent_goods WHERE goods_id = ? AND id != ?", [$goodsId, $id]);
        if ($exists) {
            json_error('该商品已配置代理');
        }

        $data = [
            'goods_id' => $goodsId,
            'commission_mode' => $commissionMode,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'multi_level' => $multiLevel,
            'level2_rate' => $level2Rate,
            'level3_rate' => $level3Rate,
            'status' => $status,
        ];

        if ($id) {
            Db::update('jz_agent_goods', $data, 'id = ?', [$id]);
            admin_log('agent_goods_update', ['id' => $id, 'goods_id' => $goodsId]);
            json_success('代理商品更新成功');
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            $newId = Db::insert('jz_agent_goods', $data);
            admin_log('agent_goods_create', ['id' => $newId, 'goods_id' => $goodsId]);
            json_success('代理商品添加成功');
        }
    }

    /**
     * 切换代理商品状态
     */
    public function goodsToggle()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 1);
        if (!$id) {
            json_error('参数错误');
        }

        Db::execute("UPDATE jz_agent_goods SET status = ? WHERE id = ?", [$status, $id]);
        admin_log('agent_goods_toggle', ['id' => $id, 'status' => $status]);
        json_success($status == 1 ? '已启用' : '已禁用');
    }

    /**
     * 删除代理商品配置
     */
    public function goodsDelete()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }

        Db::execute("DELETE FROM jz_agent_goods WHERE id = ?", [$id]);
        admin_log('agent_goods_delete', ['id' => $id]);
        json_success('已删除');
    }

    /**
     * 代理树
     */
    public function tree()
    {
        $keyword = input('keyword', '');
        $where = 'au.status = 1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (u.nickname LIKE ? OR u.mobile LIKE ? OR au.invite_code LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $list = Db::query(
            "SELECT au.*, u.nickname, u.mobile,
                    p.nickname AS parent_name,
                    (SELECT COUNT(*) FROM jz_agent_user WHERE parent_id = au.id) AS child_count
             FROM jz_agent_user au
             LEFT JOIN jz_user u ON au.user_id = u.id
             LEFT JOIN jz_user p ON au.parent_id = p.id
             WHERE {$where}
             ORDER BY au.level ASC, au.id DESC
             LIMIT 200",
            $params
        );

        $stats = Db::fetch(
            "SELECT COUNT(*) AS total,
                    SUM(total_commission) AS total_commission,
                    SUM(settled_commission) AS settled_commission,
                    SUM(pending_commission) AS pending_commission
             FROM jz_agent_user WHERE status = 1"
        );

        $this->assign('title', '代理树');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('stats', $stats);
        $this->fetch('admin/agent/tree');
    }

    /**
     * 佣金结算列表
     */
    public function settle()
    {
        $keyword = input('keyword', '');
        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (u.nickname LIKE ? OR u.mobile LIKE ? OR ast.settle_no LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where .= ' AND ast.status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_agent_settlement ast
             LEFT JOIN jz_agent_user au ON ast.user_id = au.user_id
             LEFT JOIN jz_user u ON ast.user_id = u.id
             WHERE {$where}",
            $params
        );
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT ast.*, u.nickname, u.mobile
             FROM jz_agent_settlement ast
             LEFT JOIN jz_user u ON ast.user_id = u.id
             WHERE {$where}
             ORDER BY ast.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $stats = Db::fetch(
            "SELECT COUNT(*) AS total,
                    SUM(amount) AS total_amount,
                    SUM(CASE WHEN status = 0 THEN amount ELSE 0 END) AS pending_amount,
                    SUM(CASE WHEN status = 2 THEN amount ELSE 0 END) AS paid_amount
             FROM jz_agent_settlement"
        );

        $this->assign('title', '佣金结算');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->assign('stats', $stats);
        $this->fetch('admin/agent/settle');
    }

    /**
     * 发起结算
     */
    public function settleCreate()
    {
        $userId = (int) input('user_id', 0);
        $amount = (float) input('amount', 0);
        $channel = input('channel', '');
        $account = input('account', '');
        $remark = input('remark', '');

        if (!$userId || $amount <= 0) {
            json_error('参数错误');
        }

        $agent = Db::fetch("SELECT * FROM jz_agent_user WHERE user_id = ?", [$userId]);
        if (!$agent) {
            json_error('代理不存在');
        }

        if ($agent['pending_commission'] < $amount) {
            json_error('待结算佣金不足');
        }

        $settleNo = 'AS' . date('YmdHis') . rand(1000, 9999);
        $fee = 0;
        $realAmount = $amount - $fee;

        Db::insert('jz_agent_settlement', [
            'settle_no' => $settleNo,
            'user_id' => $userId,
            'amount' => $amount,
            'fee' => $fee,
            'real_amount' => $realAmount,
            'status' => 0,
            'channel' => $channel,
            'account' => $account,
            'remark' => $remark,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        Db::execute(
            "UPDATE jz_agent_user SET pending_commission = pending_commission - ?, settled_commission = settled_commission + ? WHERE user_id = ?",
            [$amount, $amount, $userId]
        );

        admin_log('agent_settle_create', ['settle_no' => $settleNo, 'user_id' => $userId, 'amount' => $amount]);
        json_success('结算单创建成功');
    }

    /**
     * 结算打款/状态更新
     */
    public function settlePay()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 0);
        $remark = input('remark', '');

        if (!$id || !in_array($status, [1, 2, 3], true)) {
            json_error('参数错误');
        }

        $settle = Db::fetch("SELECT * FROM jz_agent_settlement WHERE id = ?", [$id]);
        if (!$settle) {
            json_error('结算单不存在');
        }

        if ($settle['status'] == 2) {
            json_error('该结算单已成功，无需修改');
        }

        $payTime = in_array($status, [2, 3], true) ? date('Y-m-d H:i:s') : null;
        $data = [
            'status' => $status,
            'remark' => $remark,
            'update_time' => date('Y-m-d H:i:s'),
        ];
        if ($payTime) {
            $data['pay_time'] = $payTime;
        }

        Db::update('jz_agent_settlement', $data, 'id = ?', [$id]);

        if ($status == 3) {
            // 失败退款回到待结算
            Db::execute(
                "UPDATE jz_agent_user SET pending_commission = pending_commission + ?, settled_commission = settled_commission - ? WHERE user_id = ?",
                [$settle['amount'], $settle['amount'], $settle['user_id']]
            );
        }

        admin_log('agent_settle_pay', ['id' => $id, 'settle_no' => $settle['settle_no'], 'user_id' => $settle['user_id'], 'status' => $status]);
        $labels = [1 => '已标记处理中', 2 => '打款成功', 3 => '已驳回'];
        json_success($labels[$status]);
    }

    /**
     * 佣金记录
     */
    public function commission()
    {
        $keyword = input('keyword', '');
        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (u.nickname LIKE ? OR ac.order_no LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where .= ' AND ac.status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_agent_commission ac
             LEFT JOIN jz_user u ON ac.user_id = u.id
             WHERE {$where}",
            $params
        );
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT ac.*, u.nickname, u.mobile, g.name AS goods_name
             FROM jz_agent_commission ac
             LEFT JOIN jz_user u ON ac.user_id = u.id
             LEFT JOIN jz_goods g ON ac.goods_id = g.id
             WHERE {$where}
             ORDER BY ac.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '佣金记录');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/agent/commission');
    }
}
