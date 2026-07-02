<?php
/**
 * Migrated from main_legacy/controller/app/controller/admin/Merchant.php
 */
namespace app\controller\admin;

/**
 * 总站后台 - 商户管理
 */
class Merchant extends Controller
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
     * 商户列表
     */
    public function index()
    {
        $keyword = input('keyword', '');
        $status = input('status', '');
        $subsiteId = input('subsite_id', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
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
        if ($subsiteId !== '') {
            $where .= ' AND m.subsite_id = ?';
            $params[] = (int) $subsiteId;
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

        $subsites = Db::query("SELECT id, name FROM jz_subsite WHERE status = 1 ORDER BY id DESC");

        $this->assign('title', '商户列表');
        $this->assign('list', $list);
        $this->assign('subsites', $subsites);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('subsiteId', $subsiteId);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/merchant/index');
    }

    /**
     * 入驻审核列表
     */
    public function audit()
    {
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = 'm.status = 0';
        $params = [];

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

        $this->assign('title', '入驻审核');
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/merchant/audit');
    }

    /**
     * 审核通过
     */
    public function auditPass()
    {
        $id = (int) input('id', 0);
        $remark = input('remark', '');
        if (!$id) {
            json_error('参数错误');
        }

        $merchant = Db::fetch("SELECT status FROM jz_merchant WHERE id = ?", [$id]);
        if (!$merchant || $merchant['status'] != 0) {
            json_error('商户状态不正确');
        }

        Db::execute(
            "UPDATE jz_merchant SET status = 1, audit_remark = ?, audit_time = ?, open_time = ?, update_time = ? WHERE id = ?",
            [$remark, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $id]
        );
        admin_log('merchant_audit_pass', ['id' => $id, 'remark' => $remark]);
        json_success('审核通过');
    }

    /**
     * 审核驳回
     */
    public function auditReject()
    {
        $id = (int) input('id', 0);
        $remark = input('remark', '');
        if (!$id) {
            json_error('参数错误');
        }
        if (!$remark) {
            json_error('请填写驳回原因');
        }

        $merchant = Db::fetch("SELECT status FROM jz_merchant WHERE id = ?", [$id]);
        if (!$merchant || $merchant['status'] != 0) {
            json_error('商户状态不正确');
        }

        Db::execute(
            "UPDATE jz_merchant SET status = 2, audit_remark = ?, audit_time = ?, update_time = ? WHERE id = ?",
            [$remark, date('Y-m-d H:i:s'), date('Y-m-d H:i:s'), $id]
        );
        admin_log('merchant_audit_reject', ['id' => $id, 'remark' => $remark]);
        json_success('已驳回');
    }

    /**
     * 添加/更新审核备注（不改变状态）
     */
    public function auditRemark()
    {
        $id = (int) input('id', 0);
        $remark = input('remark', '');
        if (!$id) {
            json_error('参数错误');
        }
        if (!$remark) {
            json_error('请填写备注内容');
        }

        $merchant = Db::fetch("SELECT id FROM jz_merchant WHERE id = ?", [$id]);
        if (!$merchant) {
            json_error('商户不存在');
        }

        Db::execute(
            "UPDATE jz_merchant SET audit_remark = ?, update_time = ? WHERE id = ?",
            [$remark, date('Y-m-d H:i:s'), $id]
        );
        admin_log('merchant_audit_remark', ['id' => $id, 'remark' => $remark]);
        json_success('备注已保存');
    }

    /**
     * 邀请码列表
     */
    public function invite()
    {
        $subsiteId = input('subsite_id', '');
        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
        if ($subsiteId !== '') {
            $where .= ' AND ic.subsite_id = ?';
            $params[] = (int) $subsiteId;
        }
        if ($status !== '') {
            $where .= ' AND ic.status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_invite_code ic WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT ic.*, s.name AS subsite_name, rg.name AS rate_group_name,
                    (SELECT COUNT(*) FROM jz_merchant m WHERE m.invite_code_id = ic.id) AS used_count_real
             FROM jz_invite_code ic
             LEFT JOIN jz_subsite s ON ic.subsite_id = s.id
             LEFT JOIN jz_rate_group rg ON ic.rate_group_id = rg.id
             WHERE {$where}
             ORDER BY ic.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $subsites = Db::query("SELECT id, name FROM jz_subsite WHERE status = 1 ORDER BY id DESC");
        $rateGroups = Db::query("SELECT id, name FROM jz_rate_group WHERE status = 1 ORDER BY id ASC");

        $this->assign('title', '邀请码管理');
        $this->assign('list', $list);
        $this->assign('subsites', $subsites);
        $this->assign('rateGroups', $rateGroups);
        $this->assign('subsiteId', $subsiteId);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/merchant/invite');
    }

    /**
     * 生成邀请码
     */
    public function inviteCreate()
    {
        $quantity = max(1, min(100, (int) input('quantity', 1)));
        $subsiteId = (int) input('subsite_id', 0);
        $rateGroupId = (int) input('rate_group_id', 0);
        $maxUses = max(0, (int) input('max_uses', 0));
        $expireTime = input('expire_time', '');
        if ($expireTime) {
            $timestamp = strtotime($expireTime);
            $expireTime = $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
        }

        $codes = [];
        admin_log('invite_code_create', ['quantity' => $quantity, 'subsite_id' => $subsiteId, 'rate_group_id' => $rateGroupId]);
        for ($i = 0; $i < $quantity; $i++) {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 12));
            // 确保唯一
            while (Db::fetch("SELECT id FROM jz_invite_code WHERE code = ?", [$code])) {
                $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 12));
            }
            $codes[] = $code;
            Db::insert('jz_invite_code', [
                'code' => $code,
                'subsite_id' => $subsiteId,
                'rate_group_id' => $rateGroupId,
                'max_uses' => $maxUses,
                'used_count' => 0,
                'expire_time' => $expireTime ?: null,
                'status' => 1,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ]);
        }

        json_success('生成成功', ['codes' => $codes]);
    }

    /**
     * 禁用/启用邀请码
     */
    public function inviteToggle()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 0);
        if (!$id) {
            json_error('参数错误');
        }
        Db::execute(
            "UPDATE jz_invite_code SET status = ?, update_time = ? WHERE id = ?",
            [$status ? 1 : 0, date('Y-m-d H:i:s'), $id]
        );
        admin_log('invite_code_toggle', ['id' => $id, 'status' => $status ? 1 : 0]);
        json_success('状态更新成功');
    }

    /**
     * 切换商户状态（封禁 / 解禁 / 冻结 / 恢复）
     */
    public function toggleStatus()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 0);
        $remark = input('remark', '');
        if (!$id || !in_array($status, [1, 2, 3], true)) {
            json_error('参数错误');
        }

        $merchant = Db::fetch("SELECT status FROM jz_merchant WHERE id = ?", [$id]);
        if (!$merchant) {
            json_error('商户不存在');
        }

        Db::execute(
            "UPDATE jz_merchant SET status = ?, audit_remark = ?, update_time = ? WHERE id = ?",
            [$status, $remark, date('Y-m-d H:i:s'), $id]
        );

        if ($status !== 1) {
            // 封禁或冻结时强制下线所有商品
            Db::execute("UPDATE jz_goods SET status = 0 WHERE merchant_id = ? AND status = 1", [$id]);
        }

        admin_log('merchant_toggle_status', ['id' => $id, 'status' => $status, 'remark' => $remark]);
        json_success('商户状态已更新');
    }

    /**
     * 强制下线所有商品
     */
    public function forceOffline()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }
        $affected = Db::execute("UPDATE jz_goods SET status = 0 WHERE merchant_id = ? AND status = 1", [$id]);
        admin_log('merchant_force_offline', ['id' => $id, 'affected' => $affected]);
        json_success('操作成功，已下线 ' . $affected . ' 个商品');
    }

    /**
     * 冻结/解冻资金
     */
    public function freezeFunds()
    {
        $id = (int) input('id', 0);
        $amount = input('amount', 0);
        $action = input('action', 'freeze'); // freeze / unfreeze
        if (!$id || !is_numeric($amount) || $amount <= 0) {
            json_error('参数错误');
        }

        $merchant = Db::fetch("SELECT balance, frozen_balance FROM jz_merchant WHERE id = ?", [$id]);
        if (!$merchant) {
            json_error('商户不存在');
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
            admin_log('merchant_freeze_funds', ['id' => $id, 'action' => 'freeze', 'amount' => $amount]);
            json_success('已冻结 ' . $amount . ' 元');
        } else {
            if ($merchant['frozen_balance'] < $amount) {
                json_error('冻结余额不足');
            }
            Db::execute(
                "UPDATE jz_merchant SET balance = balance + ?, frozen_balance = frozen_balance - ?, update_time = ? WHERE id = ?",
                [$amount, $amount, date('Y-m-d H:i:s'), $id]
            );
            admin_log('merchant_freeze_funds', ['id' => $id, 'action' => 'unfreeze', 'amount' => $amount]);
            json_success('已解冻 ' . $amount . ' 元');
        }
    }
}
