<?php
/**
 * 分站后台 - 财务结算
 */
class Subsite_Finance extends Controller
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
     * 分站资金流水
     */
    public function flow()
    {
        $subsiteId = $this->getSubsiteId();
        $keyword = input('keyword', '');
        $type = input('type', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = 'm.subsite_id = ?';
        $params = [$subsiteId];
        if ($keyword) {
            $where .= ' AND (m.shop_name LIKE ? OR f.order_id = ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = is_numeric($keyword) ? (int) $keyword : 0;
        }
        if ($type) {
            $where .= ' AND f.type = ?';
            $params[] = $type;
        }

        // 统计
        $stat = Db::fetch(
            "SELECT
                COUNT(*) AS total_count,
                COALESCE(SUM(CASE WHEN f.type = 'income' THEN f.amount ELSE 0 END), 0) AS total_income,
                COALESCE(SUM(CASE WHEN f.type = 'settle' THEN f.amount ELSE 0 END), 0) AS total_settle,
                COALESCE(SUM(CASE WHEN f.type = 'fee' THEN f.amount ELSE 0 END), 0) AS total_fee
             FROM jz_finance_flow f
             LEFT JOIN jz_merchant m ON f.merchant_id = m.id
             WHERE {$where}",
            $params
        );

        $count = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_finance_flow f LEFT JOIN jz_merchant m ON f.merchant_id = m.id WHERE {$where}",
            $params
        );
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT f.*, m.shop_name
             FROM jz_finance_flow f
             LEFT JOIN jz_merchant m ON f.merchant_id = m.id
             WHERE {$where}
             ORDER BY f.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $typeMap = [
            'income' => '收入',
            'refund' => '退款',
            'fee' => '手续费',
            'freeze' => '冻结',
            'unfreeze' => '解冻',
            'settle' => '结算',
        ];

        $this->assign('title', '分站资金流水');
        $this->assign('list', $list);
        $this->assign('stat', $stat);
        $this->assign('typeMap', $typeMap);
        $this->assign('keyword', $keyword);
        $this->assign('type', $type);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('subsite/finance/flow');
    }

    /**
     * 分站结算管理
     */
    public function settle()
    {
        $subsiteId = $this->getSubsiteId();
        $keyword = input('keyword', '');
        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = 'm.subsite_id = ?';
        $params = [$subsiteId];
        if ($keyword) {
            $where .= ' AND (s.settle_no LIKE ? OR m.shop_name LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where .= ' AND s.status = ?';
            $params[] = (int) $status;
        }

        // 统计
        $stat = Db::fetch(
            "SELECT
                COUNT(*) AS total_count,
                COALESCE(SUM(s.amount), 0) AS total_amount,
                COALESCE(SUM(s.real_amount), 0) AS total_real,
                COALESCE(SUM(CASE WHEN s.status = 0 THEN s.real_amount ELSE 0 END), 0) AS pending_amount
             FROM jz_settlement s
             LEFT JOIN jz_merchant m ON s.merchant_id = m.id
             WHERE {$where}",
            $params
        );

        $count = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_settlement s LEFT JOIN jz_merchant m ON s.merchant_id = m.id WHERE {$where}",
            $params
        );
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT s.*, m.shop_name
             FROM jz_settlement s
             LEFT JOIN jz_merchant m ON s.merchant_id = m.id
             WHERE {$where}
             ORDER BY s.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $statusMap = [
            0 => '待处理',
            1 => '处理中',
            2 => '成功',
            3 => '失败',
        ];

        $this->assign('title', '分站结算管理');
        $this->assign('list', $list);
        $this->assign('stat', $stat);
        $this->assign('statusMap', $statusMap);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('subsite/finance/settle');
    }

    /**
     * 更新结算状态
     */
    public function settleUpdate()
    {
        $subsiteId = $this->getSubsiteId();
        $id = (int) input('id', 0);
        $status = (int) input('status', 0);
        $remark = input('remark', '');
        if (!$id || !in_array($status, [1, 2, 3], true)) {
            json_error('参数错误');
        }

        $settlement = Db::fetch(
            "SELECT s.*, m.subsite_id FROM jz_settlement s
             LEFT JOIN jz_merchant m ON s.merchant_id = m.id
             WHERE s.id = ?",
            [$id]
        );
        if (!$settlement) {
            json_error('结算记录不存在');
        }
        if ((int) $settlement['subsite_id'] !== $subsiteId) {
            json_error('无权操作该结算记录');
        }
        if ($settlement['status'] == 2) {
            json_error('已成功结算，不可修改');
        }

        Db::execute(
            "UPDATE jz_settlement SET status = ?, remark = ?, update_time = ? WHERE id = ?",
            [$status, $remark, date('Y-m-d H:i:s'), $id]
        );

        if ($status == 3) {
            Db::execute(
                "UPDATE jz_merchant SET balance = balance + ?, update_time = ? WHERE id = ?",
                [$settlement['real_amount'], date('Y-m-d H:i:s'), $settlement['merchant_id']]
            );
        }

        admin_log('subsite_settlement_update', [
            'id' => $id,
            'subsite_id' => $subsiteId,
            'settle_no' => $settlement['settle_no'],
            'merchant_id' => $settlement['merchant_id'],
            'status' => $status,
            'remark' => $remark,
        ]);
        json_success('状态更新成功');
    }
}
