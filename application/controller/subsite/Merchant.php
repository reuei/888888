<?php
/**
 * 分站后台 - 商户管理
 */
class Subsite_Merchant extends Controller
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
     * 分站商户列表
     */
    public function index()
    {
        $subsiteId = $this->getSubsiteId();
        $keyword = input('keyword', '');
        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = 'm.subsite_id = ?';
        $params = [$subsiteId];
        if ($keyword) {
            $where .= ' AND (m.shop_name LIKE ? OR m.shop_id LIKE ? OR m.username LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where .= ' AND m.status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_merchant m WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT m.*, s.name AS subsite_name
             FROM jz_merchant m
             LEFT JOIN jz_subsite s ON m.subsite_id = s.id
             WHERE {$where}
             ORDER BY m.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '分站商户列表');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('subsite/merchant/index');
    }

    /**
     * 分站入驻审核列表
     */
    public function audit()
    {
        $subsiteId = $this->getSubsiteId();
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = 'm.status = 0 AND m.subsite_id = ?';
        $params = [$subsiteId];

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_merchant m WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT m.*, s.name AS subsite_name,
                    CASE WHEN m.invite_code_id > 0 THEN '邀请码' ELSE '自助注册' END AS register_type
             FROM jz_merchant m
             LEFT JOIN jz_subsite s ON m.subsite_id = s.id
             WHERE {$where}
             ORDER BY m.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '分站入驻审核');
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('subsite/merchant/audit');
    }

    /**
     * 审核通过
     */
    public function auditPass()
    {
        $subsiteId = $this->getSubsiteId();
        $id = (int) input('id', 0);
        $remark = input('remark', '');
        if (!$id) {
            json_error('参数错误');
        }

        $merchant = Db::fetch("SELECT status, subsite_id FROM jz_merchant WHERE id = ?", [$id]);
        if (!$merchant || $merchant['status'] != 0) {
            json_error('商户状态不正确');
        }
        if ((int) $merchant['subsite_id'] !== $subsiteId) {
            json_error('无权操作该商户');
        }

        Db::execute(
            "UPDATE jz_merchant SET status = 1, audit_remark = ?, audit_time = ?, open_time = ?, update_time = ? WHERE id = ?",
            [$remark, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $id]
        );
        admin_log('subsite_merchant_audit_pass', ['id' => $id, 'subsite_id' => $subsiteId, 'remark' => $remark]);
        json_success('审核通过');
    }

    /**
     * 审核驳回
     */
    public function auditReject()
    {
        $subsiteId = $this->getSubsiteId();
        $id = (int) input('id', 0);
        $remark = input('remark', '');
        if (!$id) {
            json_error('参数错误');
        }
        if (!$remark) {
            json_error('请填写驳回原因');
        }

        $merchant = Db::fetch("SELECT status, subsite_id FROM jz_merchant WHERE id = ?", [$id]);
        if (!$merchant || $merchant['status'] != 0) {
            json_error('商户状态不正确');
        }
        if ((int) $merchant['subsite_id'] !== $subsiteId) {
            json_error('无权操作该商户');
        }

        Db::execute(
            "UPDATE jz_merchant SET status = 2, audit_remark = ?, audit_time = ?, update_time = ? WHERE id = ?",
            [$remark, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $id]
        );
        admin_log('subsite_merchant_audit_reject', ['id' => $id, 'subsite_id' => $subsiteId, 'remark' => $remark]);
        json_success('已驳回');
    }

    /**
     * 添加/更新审核备注
     */
    public function auditRemark()
    {
        $subsiteId = $this->getSubsiteId();
        $id = (int) input('id', 0);
        $remark = input('remark', '');
        if (!$id) {
            json_error('参数错误');
        }
        if (!$remark) {
            json_error('请填写备注内容');
        }

        $merchant = Db::fetch("SELECT id, subsite_id FROM jz_merchant WHERE id = ?", [$id]);
        if (!$merchant) {
            json_error('商户不存在');
        }
        if ((int) $merchant['subsite_id'] !== $subsiteId) {
            json_error('无权操作该商户');
        }

        Db::execute(
            "UPDATE jz_merchant SET audit_remark = ?, update_time = ? WHERE id = ?",
            [$remark, date('Y-m-d H:i:s'), $id]
        );
        admin_log('subsite_merchant_audit_remark', ['id' => $id, 'subsite_id' => $subsiteId, 'remark' => $remark]);
        json_success('备注已保存');
    }

    /**
     * 切换商户状态
     */
    public function toggleStatus()
    {
        $subsiteId = $this->getSubsiteId();
        $id = (int) input('id', 0);
        $status = (int) input('status', 0);
        $remark = input('remark', '');
        if (!$id || !in_array($status, [1, 2, 3], true)) {
            json_error('参数错误');
        }

        $merchant = Db::fetch("SELECT status, subsite_id FROM jz_merchant WHERE id = ?", [$id]);
        if (!$merchant) {
            json_error('商户不存在');
        }
        if ((int) $merchant['subsite_id'] !== $subsiteId) {
            json_error('无权操作该商户');
        }

        Db::execute(
            "UPDATE jz_merchant SET status = ?, audit_remark = ?, update_time = ? WHERE id = ?",
            [$status, $remark, date('Y-m-d H:i:s'), $id]
        );

        if ($status !== 1) {
            Db::execute("UPDATE jz_goods SET status = 0 WHERE merchant_id = ? AND status = 1", [$id]);
        }

        admin_log('subsite_merchant_toggle_status', ['id' => $id, 'subsite_id' => $subsiteId, 'status' => $status, 'remark' => $remark]);
        json_success('商户状态已更新');
    }

    /**
     * 强制下线所有商品
     */
    public function forceOffline()
    {
        $subsiteId = $this->getSubsiteId();
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }

        $merchant = Db::fetch("SELECT subsite_id FROM jz_merchant WHERE id = ?", [$id]);
        if (!$merchant) {
            json_error('商户不存在');
        }
        if ((int) $merchant['subsite_id'] !== $subsiteId) {
            json_error('无权操作该商户');
        }

        $affected = Db::execute("UPDATE jz_goods SET status = 0 WHERE merchant_id = ? AND status = 1", [$id]);
        admin_log('subsite_merchant_force_offline', ['id' => $id, 'subsite_id' => $subsiteId, 'affected' => $affected]);
        json_success('操作成功，已下线 ' . $affected . ' 个商品');
    }

    /**
     * 冻结/解冻资金
     */
    public function freezeFunds()
    {
        $subsiteId = $this->getSubsiteId();
        $id = (int) input('id', 0);
        $amount = input('amount', 0);
        $action = input('action', 'freeze');
        if (!$id || !is_numeric($amount) || $amount <= 0) {
            json_error('参数错误');
        }

        $merchant = Db::fetch("SELECT balance, frozen_balance, subsite_id FROM jz_merchant WHERE id = ?", [$id]);
        if (!$merchant) {
            json_error('商户不存在');
        }
        if ((int) $merchant['subsite_id'] !== $subsiteId) {
            json_error('无权操作该商户');
        }

        $amount = round($amount, 2);
        if ($action === 'freeze') {
            if ($merchant['balance'] < $amount) {
                json_error('可用余额不足');
            }
            Db::execute(
                "UPDATE jz_merchant SET balance = balance - ?, frozen_balance = frozen_balance + ?, update_time = ? WHERE id = ?",
                [$amount, $amount, date('Y-m-d H:i:s'), $id]
            );
            admin_log('subsite_merchant_freeze_funds', ['id' => $id, 'subsite_id' => $subsiteId, 'action' => 'freeze', 'amount' => $amount]);
            json_success('已冻结 ' . $amount . ' 元');
        } else {
            if ($merchant['frozen_balance'] < $amount) {
                json_error('冻结余额不足');
            }
            Db::execute(
                "UPDATE jz_merchant SET balance = balance + ?, frozen_balance = frozen_balance - ?, update_time = ? WHERE id = ?",
                [$amount, $amount, date('Y-m-d H:i:s'), $id]
            );
            admin_log('subsite_merchant_freeze_funds', ['id' => $id, 'subsite_id' => $subsiteId, 'action' => 'unfreeze', 'amount' => $amount]);
            json_success('已解冻 ' . $amount . ' 元');
        }
    }
}
