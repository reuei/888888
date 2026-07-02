<?php
/**
 * Migrated from main_legacy/controller/app/controller/admin/Finance.php
 */
namespace app\controller\admin;

/**
 * 总站后台 - 财务结算
 */
class Finance extends Controller
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
     * 资金流水
     */
    public function flow()
    {
        $keyword = input('keyword', '');
        $type = input('type', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (m.shop_name LIKE ? OR f.order_id = ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = is_numeric($keyword) ? (int) $keyword : 0;
        }
        if ($type) {
            $where .= ' AND f.type = ?';
            $params[] = $type;
        }

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

        $this->assign('title', '资金流水');
        $this->assign('list', $list);
        $this->assign('typeMap', $typeMap);
        $this->assign('keyword', $keyword);
        $this->assign('type', $type);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/finance/flow');
    }

    /**
     * 费率分组
     */
    public function rate()
    {
        $list = Db::query("SELECT * FROM jz_rate_group ORDER BY id ASC");
        $this->assign('title', '费率分组');
        $this->assign('list', $list);
        $this->fetch('admin/finance/rate');
    }

    /**
     * 保存费率分组
     */
    public function rateSave()
    {
        $id = (int) input('id', 0);
        $name = trim(input('name', ''));
        $rate = input('rate', '0.0000');
        $maxFee = input('max_fee', '0.00');
        $costRate = input('cost_rate', '0.0000');
        $isDefault = (int) input('is_default', 0);

        if (!$name) {
            json_error('请输入分组名称');
        }
        if (!is_numeric($rate) || $rate < 0 || $rate > 1) {
            json_error('手续费率必须在 0-1 之间');
        }
        if (!is_numeric($maxFee) || $maxFee < 0) {
            json_error('封顶费率不能为负数');
        }
        if (!is_numeric($costRate) || $costRate < 0 || $costRate > 1) {
            json_error('成本费率必须在 0-1 之间');
        }

        $data = [
            'name' => $name,
            'rate' => round($rate, 4),
            'max_fee' => round($maxFee, 2),
            'cost_rate' => round($costRate, 4),
            'is_default' => $isDefault,
        ];

        if ($isDefault) {
            Db::execute("UPDATE jz_rate_group SET is_default = 0");
        }

        if ($id) {
            Db::update('jz_rate_group', $data, 'id = ?', [$id]);
            admin_log('rate_group_update', ['id' => $id, 'name' => $name]);
            json_success('费率分组更新成功');
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            $newId = Db::insert('jz_rate_group', $data);
            admin_log('rate_group_create', ['id' => $newId, 'name' => $name]);
            json_success('费率分组添加成功');
        }
    }

    /**
     * 删除费率分组
     */
    public function rateDelete()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }

        $merchantCount = Db::fetch("SELECT COUNT(*) AS total FROM jz_merchant WHERE rate_group_id = ?", [$id]);
        if ($merchantCount['total'] > 0) {
            json_error('该分组已被商户使用，无法删除');
        }

        Db::execute("DELETE FROM jz_rate_group WHERE id = ?", [$id]);
        admin_log('rate_group_delete', ['id' => $id]);
        json_success('费率分组已删除');
    }

    /**
     * 结算打款
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
            $where .= ' AND (s.settle_no LIKE ? OR m.shop_name LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where .= ' AND s.status = ?';
            $params[] = (int) $status;
        }

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

        $this->assign('title', '结算打款');
        $this->assign('list', $list);
        $this->assign('statusMap', $statusMap);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/finance/settle');
    }

    /**
     * 更新结算状态
     */
    public function settleUpdate()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 0);
        $remark = input('remark', '');
        if (!$id || !in_array($status, [1, 2, 3], true)) {
            json_error('参数错误');
        }

        $settlement = Db::fetch("SELECT * FROM jz_settlement WHERE id = ?", [$id]);
        if (!$settlement) {
            json_error('结算记录不存在');
        }
        if ($settlement['status'] == 2) {
            json_error('已成功结算，不可修改');
        }

        Db::execute(
            "UPDATE jz_settlement SET status = ?, remark = ?, update_time = ? WHERE id = ?",
            [$status, $remark, date('Y-m-d H:i:s'), $id]
        );

        // 结算失败时退回商户余额
        if ($status == 3) {
            Db::execute(
                "UPDATE jz_merchant SET balance = balance + ?, update_time = ? WHERE id = ?",
                [$settlement['real_amount'], date('Y-m-d H:i:s'), $settlement['merchant_id']]
            );
        }

        admin_log('settlement_update', [
            'id' => $id,
            'settle_no' => $settlement['settle_no'],
            'merchant_id' => $settlement['merchant_id'],
            'status' => $status,
            'remark' => $remark,
        ]);
        json_success('状态更新成功');
    }
}
