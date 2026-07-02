<?php
/**
 * 总站后台 - 优惠券管理
 */
class Admin_Coupon extends Controller
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
     * 自动标记已过期的用户优惠券
     */
    private function expireUserCoupons()
    {
        $now = date('Y-m-d H:i:s');
        Db::execute(
            "UPDATE jz_user_coupon SET status = 2 WHERE status = 0 AND expire_time IS NOT NULL AND expire_time < ?",
            [$now]
        );
    }

    /**
     * 优惠券列表
     */
    public function index()
    {
        $this->expireUserCoupons();

        $keyword = input('keyword', '');
        $status = input('status', '');
        $type = input('type', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (name LIKE ? OR code LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where .= ' AND status = ?';
            $params[] = (int) $status;
        }
        if ($type !== '') {
            $where .= ' AND type = ?';
            $params[] = (int) $type;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_coupon WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT * FROM jz_coupon WHERE {$where} ORDER BY id DESC LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '优惠券管理');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('type', $type);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/coupon/index');
    }

    /**
     * 保存优惠券
     */
    public function save()
    {
        $id = (int) input('id', 0);
        $name = trim(input('name', ''));
        $code = trim(input('code', ''));
        $type = (int) input('type', 1);
        $amount = (float) input('amount', 0);
        $minAmount = (float) input('min_amount', 0);
        $totalCount = (int) input('total_count', 0);
        $limitPerUser = (int) input('limit_per_user', 1);
        $startTime = input('start_time', '');
        $endTime = input('end_time', '');
        $scope = input('scope', 'all');
        $scopeId = (int) input('scope_id', 0);
        $status = (int) input('status', 1);

        if (!$name) {
            json_error('请输入优惠券名称');
        }
        if (!in_array($type, [1, 2, 3], true)) {
            json_error('优惠券类型错误');
        }
        if ($amount <= 0) {
            json_error('优惠金额必须大于0');
        }
        if ($type == 2 && $amount > 1) {
            json_error('折扣率不能大于1');
        }

        if ($code) {
            $exists = Db::fetch("SELECT id FROM jz_coupon WHERE code = ? AND id != ?", [$code, $id]);
            if ($exists) {
                json_error('优惠券码已存在');
            }
        }

        $data = [
            'name' => $name,
            'code' => $code,
            'type' => $type,
            'amount' => $amount,
            'min_amount' => $minAmount,
            'total_count' => $totalCount,
            'limit_per_user' => $limitPerUser,
            'start_time' => $startTime ?: null,
            'end_time' => $endTime ?: null,
            'scope' => $scope,
            'scope_id' => $scopeId,
            'status' => $status,
        ];

        if ($id) {
            Db::update('jz_coupon', $data, 'id = ?', [$id]);
            admin_log('coupon_update', ['id' => $id, 'name' => $name, 'type' => $type]);
            json_success('优惠券更新成功');
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            $newId = Db::insert('jz_coupon', $data);
            admin_log('coupon_create', ['id' => $newId, 'name' => $name, 'type' => $type]);
            json_success('优惠券创建成功');
        }
    }

    /**
     * 切换状态
     */
    public function toggleStatus()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 1);
        if (!$id) {
            json_error('参数错误');
        }

        Db::execute("UPDATE jz_coupon SET status = ? WHERE id = ?", [$status, $id]);
        admin_log('coupon_toggle_status', ['id' => $id, 'status' => $status]);
        json_success($status == 1 ? '已启用' : '已禁用');
    }

    /**
     * 删除优惠券
     */
    public function delete()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }

        Db::execute("DELETE FROM jz_coupon WHERE id = ?", [$id]);
        admin_log('coupon_delete', ['id' => $id]);
        json_success('已删除');
    }

    /**
     * 领取记录
     */
    public function records()
    {
        $this->expireUserCoupons();

        $keyword = input('keyword', '');
        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (u.nickname LIKE ? OR u.mobile LIKE ? OR c.name LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where .= ' AND uc.status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_user_coupon uc
             LEFT JOIN jz_user u ON uc.user_id = u.id
             LEFT JOIN jz_coupon c ON uc.coupon_id = c.id
             WHERE {$where}",
            $params
        );
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT uc.*, u.nickname, u.mobile, c.name AS coupon_name
             FROM jz_user_coupon uc
             LEFT JOIN jz_user u ON uc.user_id = u.id
             LEFT JOIN jz_coupon c ON uc.coupon_id = c.id
             WHERE {$where}
             ORDER BY uc.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '优惠券领取记录');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/coupon/records');
    }

    /**
     * 优惠券统计报表
     */
    public function stats()
    {
        $this->expireUserCoupons();

        // 优惠券总体统计
        $overview = Db::fetch(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS active,
                SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS inactive,
                SUM(receive_count) AS total_received,
                SUM(used_count) AS total_used
            FROM jz_coupon"
        );

        // 用户券状态统计
        $userCouponStats = Db::fetch(
            "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS unused,
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS used,
                SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) AS expired
            FROM jz_user_coupon"
        );

        // 今日 / 昨日 / 本月领取与使用
        $today = date('Y-m-d 00:00:00');
        $yesterday = date('Y-m-d 00:00:00', strtotime('-1 day'));
        $monthStart = date('Y-m-01 00:00:00');

        $todayStats = Db::fetch(
            "SELECT
                COUNT(*) AS receive_count,
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS use_count
            FROM jz_user_coupon WHERE create_time >= ?",
            [$today]
        );
        $yesterdayStats = Db::fetch(
            "SELECT
                COUNT(*) AS receive_count,
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS use_count
            FROM jz_user_coupon WHERE create_time >= ? AND create_time < ?",
            [$yesterday, $today]
        );
        $monthStats = Db::fetch(
            "SELECT
                COUNT(*) AS receive_count,
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS use_count
            FROM jz_user_coupon WHERE create_time >= ?",
            [$monthStart]
        );

        // 优惠券领取 / 使用排行 TOP10
        $rank = Db::query(
            "SELECT
                id, name, code, receive_count, used_count,
                CASE WHEN receive_count > 0 THEN ROUND(used_count * 100 / receive_count, 2) ELSE 0 END AS use_rate
            FROM jz_coupon
            ORDER BY receive_count DESC, id DESC
            LIMIT 10"
        );

        $this->assign('title', '优惠券统计报表');
        $this->assign('overview', $overview);
        $this->assign('userCouponStats', $userCouponStats);
        $this->assign('todayStats', $todayStats);
        $this->assign('yesterdayStats', $yesterdayStats);
        $this->assign('monthStats', $monthStats);
        $this->assign('rank', $rank);
        $this->fetch('admin/coupon/stats');
    }
}
