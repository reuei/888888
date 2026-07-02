<?php
/**
 * Migrated from main_legacy/controller/app/controller/admin/Subsite.php
 */
namespace app\controller\admin;

/**
 * 总站后台 - 分站管理
 */
class Subsite extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/admin');
        if (!session('admin_user')) {
            redirect(url('login') . '?type=admin');
        }
        check_admin_role(['super']);
    }

    /**
     * 分站列表
     */
    public function index()
    {
        $keyword = input('keyword', '');
        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (s.name LIKE ? OR s.domain_prefix LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where .= ' AND s.status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_subsite s WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT s.*, a.username AS admin_name, a.last_login_time AS admin_last_login, 
                    (SELECT COUNT(*) FROM jz_merchant m WHERE m.subsite_id = s.id) AS merchant_count,
                    (SELECT COUNT(*) FROM jz_goods g WHERE g.subsite_id = s.id) AS goods_count
             FROM jz_subsite s
             LEFT JOIN jz_admin a ON s.admin_id = a.id
             WHERE {$where}
             ORDER BY s.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '分站列表');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/subsite/index');
    }

    /**
     * 新建分站页面
     */
    public function create()
    {
        $rateGroups = Db::query("SELECT id, name FROM jz_rate_group WHERE status = 1 ORDER BY id ASC");
        $this->assign('title', '新建分站');
        $this->assign('rateGroups', $rateGroups);
        $this->fetch('admin/subsite/create');
    }

    /**
     * 保存分站
     */
    public function save()
    {
        $name = trim(input('name', ''));
        $domainPrefix = trim(input('domain_prefix', ''));
        $adminUser = trim(input('admin_user', ''));
        $adminPass = input('admin_pass', '');
        $rateGroupId = (int) input('rate_group_id', 0);
        $settleTemplate = input('settle_template', 'T+1');

        if (!$name) {
            json_error('请输入分站名称');
        }
        if (!$domainPrefix) {
            json_error('请输入域名前缀');
        }
        if (!preg_match('/^[a-z0-9_-]+$/', $domainPrefix)) {
            json_error('域名前缀只能包含小写字母、数字、下划线、中划线');
        }
        if (!$adminUser || !$adminPass) {
            json_error('请输入超管账号和密码');
        }

        // 检查域名前缀重复
        $exists = Db::fetch("SELECT id FROM jz_subsite WHERE domain_prefix = ?", [$domainPrefix]);
        if ($exists) {
            json_error('域名前缀已存在');
        }

        // 检查超管账号重复
        $adminExists = Db::fetch("SELECT id FROM jz_admin WHERE username = ?", [$adminUser]);
        if ($adminExists) {
            json_error('超管账号已存在');
        }

        $pdo = Db::getPdo();
        try {
            $pdo->beginTransaction();

            // 创建分站超管
            $adminId = Db::insert('jz_admin', [
                'username' => $adminUser,
                'password' => password_hash($adminPass, PASSWORD_DEFAULT),
                'role' => 'subsite_super',
                'subsite_id' => 0, // 创建后再更新
                'real_name' => '分站超管',
                'status' => 1,
                'two_factor' => 0,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ]);

            // 创建分站
            $subsiteId = Db::insert('jz_subsite', [
                'name' => $name,
                'domain_prefix' => $domainPrefix,
                'admin_id' => $adminId,
                'status' => 1,
                'rate_group_id' => $rateGroupId,
                'settle_template' => $settleTemplate,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s'),
            ]);

            // 更新超管的分站ID
            Db::execute("UPDATE jz_admin SET subsite_id = ? WHERE id = ?", [$subsiteId, $adminId]);

            $pdo->commit();
            admin_log('subsite_create', ['id' => $subsiteId, 'name' => $name, 'domain_prefix' => $domainPrefix, 'admin' => $adminUser]);
            json_success('分站创建成功', ['redirect' => url('admin/subsite')]);
        } catch (Exception $e) {
            $pdo->rollBack();
            json_error('创建失败：' . $e->getMessage());
        }
    }

    /**
     * 分站详情
     */
    public function detail()
    {
        $id = (int) input('id', 0);
        $tab = input('tab', 'info');

        $subsite = Db::fetch(
            "SELECT s.*, a.username AS admin_name, a.last_login_time AS admin_last_login, a.two_factor,
                    rg.name AS rate_group_name
             FROM jz_subsite s
             LEFT JOIN jz_admin a ON s.admin_id = a.id
             LEFT JOIN jz_rate_group rg ON s.rate_group_id = rg.id
             WHERE s.id = ?",
            [$id]
        );
        if (!$subsite) {
            redirect(url('admin/subsite'));
        }

        $data = [
            'info' => [],
            'merchant' => [],
            'goods' => [],
            'order' => [],
            'finance' => [],
            'log' => [],
        ];

        switch ($tab) {
            case 'merchant':
                $data['merchant'] = Db::query(
                    "SELECT id, shop_name, shop_id, mobile, status, create_time FROM jz_merchant WHERE subsite_id = ? ORDER BY id DESC LIMIT 20",
                    [$id]
                );
                break;
            case 'goods':
                $data['goods'] = Db::query(
                    "SELECT g.id, g.name, g.price, g.stock, g.status, m.shop_name
                     FROM jz_goods g
                     LEFT JOIN jz_merchant m ON g.merchant_id = m.id
                     WHERE g.subsite_id = ?
                     ORDER BY g.id DESC LIMIT 20",
                    [$id]
                );
                break;
            case 'order':
                $data['order'] = Db::query(
                    "SELECT order_no, goods_name, total_amount, pay_channel, status, create_time
                     FROM jz_order WHERE subsite_id = ? ORDER BY id DESC LIMIT 20",
                    [$id]
                );
                break;
            case 'finance':
                $data['finance'] = Db::query(
                    "SELECT settle_no, amount, fee, real_amount, status, channel, create_time
                     FROM jz_settlement WHERE merchant_id IN (SELECT id FROM jz_merchant WHERE subsite_id = ?)
                     ORDER BY id DESC LIMIT 20",
                    [$id]
                );
                break;
            case 'log':
                $data['log'] = Db::query(
                    "SELECT admin_name, action, content, ip, create_time FROM jz_admin_log
                     WHERE admin_id IN (SELECT id FROM jz_admin WHERE subsite_id = ?)
                     ORDER BY id DESC LIMIT 20",
                    [$id]
                );
                break;
        }

        $this->assign('title', '分站详情');
        $this->assign('subsite', $subsite);
        $this->assign('tab', $tab);
        $this->assign('data', $data);
        $this->fetch('admin/subsite/detail');
    }

    /**
     * 分站监控
     */
    public function monitor()
    {
        $sort = input('sort', 'amount');
        $period = input('period', '7d');

        $allowedSort = ['amount' => 'total_amount', 'order' => 'order_count', 'merchant' => 'merchant_count', 'complaint' => 'complaint_rate'];
        $orderBy = $allowedSort[$sort] ?? 'total_amount';

        // 模拟监控数据（实际应从统计表或订单表汇总）
        $list = Db::query(
            "SELECT s.id, s.name, s.domain_prefix, s.status,
                    (SELECT COUNT(*) FROM jz_merchant m WHERE m.subsite_id = s.id) AS merchant_count,
                    (SELECT COUNT(*) FROM jz_order o WHERE o.subsite_id = s.id AND o.status >= 1) AS order_count,
                    IFNULL((SELECT SUM(o.total_amount) FROM jz_order o WHERE o.subsite_id = s.id AND o.status >= 1), 0) AS total_amount,
                    IFNULL((SELECT COUNT(*) FROM jz_order o WHERE o.subsite_id = s.id AND o.risk_flag = 1), 0) AS risk_count,
                    IFNULL((SELECT COUNT(*) FROM jz_order o WHERE o.subsite_id = s.id AND o.risk_flag = 1), 0) /
                    NULLIF((SELECT COUNT(*) FROM jz_order o WHERE o.subsite_id = s.id AND o.status >= 1), 0) AS complaint_rate
             FROM jz_subsite s
             WHERE s.status = 1
             ORDER BY {$orderBy} DESC
             LIMIT 50"
        );

        $this->assign('title', '分站监控');
        $this->assign('list', $list);
        $this->assign('sort', $sort);
        $this->assign('period', $period);
        $this->fetch('admin/subsite/monitor');
    }

    /**
     * 重置分站超管密码
     */
    public function resetPassword()
    {
        $id = (int) input('id', 0);
        $newPass = input('password', '');
        if (!$id || !$newPass) {
            json_error('参数错误');
        }

        $subsite = Db::fetch("SELECT admin_id FROM jz_subsite WHERE id = ?", [$id]);
        if (!$subsite) {
            json_error('分站不存在');
        }

        Db::execute(
            "UPDATE jz_admin SET password = ?, update_time = ? WHERE id = ?",
            [password_hash($newPass, PASSWORD_DEFAULT), date('Y-m-d H:i:s'), $subsite['admin_id']]
        );
        admin_log('subsite_reset_password', ['subsite_id' => $id, 'admin_id' => $subsite['admin_id']]);
        json_success('密码重置成功');
    }

    /**
     * 切换分站状态（正常 / 冻结 / 关闭）
     */
    public function toggleStatus()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 0);
        if (!$id || !in_array($status, [0, 1, 2], true)) {
            json_error('参数错误');
        }

        Db::execute(
            "UPDATE jz_subsite SET status = ?, update_time = ? WHERE id = ?",
            [$status, date('Y-m-d H:i:s'), $id]
        );

        if ($status !== 1) {
            // 冻结分站下所有商户与商品
            Db::execute("UPDATE jz_merchant SET status = 3 WHERE subsite_id = ? AND status = 1", [$id]);
            Db::execute("UPDATE jz_goods SET status = 0 WHERE subsite_id = ? AND status = 1", [$id]);
        }

        admin_log('subsite_toggle_status', ['id' => $id, 'status' => $status]);
        json_success('状态更新成功');
    }

    /**
     * 切换分站超管二次认证
     */
    public function toggle2fa()
    {
        $id = (int) input('id', 0);
        $enabled = (int) input('enabled', 0);
        if (!$id) {
            json_error('参数错误');
        }

        $subsite = Db::fetch("SELECT admin_id FROM jz_subsite WHERE id = ?", [$id]);
        if (!$subsite) {
            json_error('分站不存在');
        }

        Db::execute(
            "UPDATE jz_admin SET two_factor = ?, update_time = ? WHERE id = ?",
            [$enabled ? 1 : 0, date('Y-m-d H:i:s'), $subsite['admin_id']]
        );
        admin_log('subsite_toggle_2fa', ['subsite_id' => $id, 'admin_id' => $subsite['admin_id'], 'enabled' => $enabled ? 1 : 0]);
        json_success('二次认证设置已更新');
    }
}
