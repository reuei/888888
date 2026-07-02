<?php
/**
 * Migrated from main_legacy/controller/app/controller/admin/User.php
 */
namespace app\controller\admin;

/**
 * 总站后台 - 用户管理
 */
class User extends Controller
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
     * 用户列表
     */
    public function index()
    {
        $keyword = input('keyword', '');
        $status = input('status', '');
        $subsiteId = input('subsite_id', '');
        $groupId = input('group_id', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (u.nickname LIKE ? OR u.mobile LIKE ? OR u.id = ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = is_numeric($keyword) ? (int) $keyword : 0;
        }
        if ($status !== '') {
            $where .= ' AND u.status = ?';
            $params[] = (int) $status;
        }
        if ($subsiteId !== '') {
            $where .= ' AND u.subsite_id = ?';
            $params[] = (int) $subsiteId;
        }
        if ($groupId !== '') {
            $where .= ' AND u.group_id = ?';
            $params[] = (int) $groupId;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_user u WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT u.*, s.name AS subsite_name, g.name AS group_name
             FROM jz_user u
             LEFT JOIN jz_subsite s ON u.subsite_id = s.id
             LEFT JOIN jz_user_group g ON u.group_id = g.id
             WHERE {$where}
             ORDER BY u.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $subsites = Db::query("SELECT id, name FROM jz_subsite WHERE status = 1 ORDER BY id DESC");
        $groups = Db::query("SELECT id, name FROM jz_user_group WHERE status = 1 ORDER BY level ASC");

        $this->assign('title', '用户列表');
        $this->assign('list', $list);
        $this->assign('subsites', $subsites);
        $this->assign('groups', $groups);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('subsiteId', $subsiteId);
        $this->assign('groupId', $groupId);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/user/index');
    }

    /**
     * 切换用户状态
     */
    public function toggleStatus()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 0);
        if (!$id || !in_array($status, [0, 1], true)) {
            json_error('参数错误');
        }

        Db::execute(
            "UPDATE jz_user SET status = ?, update_time = ? WHERE id = ?",
            [$status, date('Y-m-d H:i:s'), $id]
        );
        admin_log('user_toggle_status', ['id' => $id, 'status' => $status]);
        json_success($status ? '已启用' : '已禁用');
    }

    /**
     * 用户详情
     */
    public function detail()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            redirect(url('admin/user'));
        }

        $user = Db::fetch(
            "SELECT u.*, s.name AS subsite_name, g.name AS group_name
             FROM jz_user u
             LEFT JOIN jz_subsite s ON u.subsite_id = s.id
             LEFT JOIN jz_user_group g ON u.group_id = g.id
             WHERE u.id = ?",
            [$id]
        );
        if (!$user) {
            redirect(url('admin/user'));
        }

        $orders = Db::query(
            "SELECT order_no, goods_name, total_amount, status, create_time FROM jz_order WHERE user_id = ? ORDER BY id DESC LIMIT 10",
            [$id]
        );

        $this->assign('title', '用户详情');
        $this->assign('user', $user);
        $this->assign('orders', $orders);
        $this->fetch('admin/user/detail');
    }

    /**
     * 用户等级分组
     */
    public function group()
    {
        $list = Db::query("SELECT * FROM jz_user_group ORDER BY sort ASC, level ASC");
        $this->assign('title', '用户等级分组');
        $this->assign('list', $list);
        $this->fetch('admin/user/group');
    }

    /**
     * 保存用户分组
     */
    public function groupSave()
    {
        $id = (int) input('id', 0);
        $name = trim(input('name', ''));
        $level = (int) input('level', 1);
        $discount = input('discount', '1.0000');
        $sort = (int) input('sort', 0);
        $status = (int) input('status', 1);

        if (!$name) {
            json_error('请输入分组名称');
        }
        if (!is_numeric($discount) || $discount < 0 || $discount > 1) {
            json_error('折扣率必须在 0-1 之间');
        }

        $data = [
            'name' => $name,
            'level' => $level,
            'discount' => round($discount, 4),
            'sort' => $sort,
            'status' => $status,
        ];

        if ($id) {
            Db::update('jz_user_group', $data, 'id = ?', [$id]);
            admin_log('user_group_update', ['id' => $id, 'name' => $name]);
            json_success('分组更新成功');
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            $newId = Db::insert('jz_user_group', $data);
            admin_log('user_group_create', ['id' => $newId, 'name' => $name]);
            json_success('分组添加成功');
        }
    }

    /**
     * 删除用户分组
     */
    public function groupDelete()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }

        $userCount = Db::fetch("SELECT COUNT(*) AS total FROM jz_user WHERE group_id = ?", [$id]);
        if ($userCount['total'] > 0) {
            json_error('该分组下存在用户，无法删除');
        }

        Db::execute("DELETE FROM jz_user_group WHERE id = ?", [$id]);
        admin_log('user_group_delete', ['id' => $id]);
        json_success('分组已删除');
    }
}
