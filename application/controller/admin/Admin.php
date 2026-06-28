<?php
/**
 * 总站后台 - 管理员账号管理
 */
class Admin_Admin extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/admin');
        $admin = session('admin_user');
        if (!$admin) {
            redirect(url('login') . '?type=admin');
        }
        // 仅超级管理员可操作
        if (($admin['role'] ?? '') !== 'super') {
            throw new Exception('无权访问管理员管理');
        }
    }

    /**
     * 管理员列表
     */
    public function index()
    {
        $keyword = input('keyword', '');
        $role = input('role', '');
        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (username LIKE ? OR real_name LIKE ? OR mobile LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($role) {
            $where .= ' AND role = ?';
            $params[] = $role;
        }
        if ($status !== '') {
            $where .= ' AND status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_admin WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT a.*, s.name AS subsite_name
             FROM jz_admin a
             LEFT JOIN jz_subsite s ON a.subsite_id = s.id
             WHERE {$where}
             ORDER BY a.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $subsites = Db::query("SELECT id, name FROM jz_subsite WHERE status = 1 ORDER BY id DESC");

        $roleMap = [
            'super' => '超级管理员',
            'admin' => '管理员',
            'operator' => '运营人员',
            'subsite_super' => '分站超管',
            'subsite_admin' => '分站管理员',
        ];

        $this->assign('title', '管理员账号');
        $this->assign('list', $list);
        $this->assign('subsites', $subsites);
        $this->assign('roleMap', $roleMap);
        $this->assign('keyword', $keyword);
        $this->assign('role', $role);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/admin/index');
    }

    /**
     * 保存管理员
     */
    public function save()
    {
        $id = (int) input('id', 0);
        $username = trim(input('username', ''));
        $password = input('password', '');
        $role = input('role', 'operator');
        $subsiteId = (int) input('subsite_id', 0);
        $realName = trim(input('real_name', ''));
        $mobile = trim(input('mobile', ''));
        $status = (int) input('status', 1);

        if (!$username) {
            json_error('请输入账号');
        }
        if (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
            json_error('账号为 4-20 位字母/数字/下划线');
        }
        if (!$id && !$password) {
            json_error('新增管理员必须设置密码');
        }
        if ($password && strlen($password) < 6) {
            json_error('密码长度不能少于 6 位');
        }
        if (!in_array($role, ['super', 'admin', 'operator', 'subsite_super', 'subsite_admin'], true)) {
            json_error('角色错误');
        }
        if (in_array($role, ['subsite_super', 'subsite_admin'], true) && $subsiteId <= 0) {
            json_error('分站角色必须选择所属分站');
        }
        if (!in_array($role, ['subsite_super', 'subsite_admin'], true)) {
            $subsiteId = 0;
        }

        $exists = Db::fetch("SELECT id FROM jz_admin WHERE username = ? AND id != ?", [$username, $id]);
        if ($exists) {
            json_error('该账号已存在');
        }

        $data = [
            'username' => $username,
            'role' => $role,
            'subsite_id' => $subsiteId,
            'real_name' => $realName,
            'mobile' => $mobile,
            'status' => $status ? 1 : 0,
            'update_time' => date('Y-m-d H:i:s'),
        ];

        if ($id > 0) {
            $current = Db::fetch("SELECT role FROM jz_admin WHERE id = ?", [$id]);
            if (!$current) {
                json_error('管理员不存在');
            }
            if ($current['role'] === 'super' && $role !== 'super') {
                json_error('不能修改超级管理员角色');
            }
            if ($password) {
                $data['password'] = password_hash_custom($password);
            }
            Db::update('jz_admin', $data, 'id = ?', [$id]);
            admin_log('admin_update', ['id' => $id, 'username' => $username, 'role' => $role]);
            json_success('管理员更新成功');
        } else {
            $data['password'] = password_hash_custom($password);
            $data['create_time'] = date('Y-m-d H:i:s');
            $newId = Db::insert('jz_admin', $data);
            admin_log('admin_create', ['id' => $newId, 'username' => $username, 'role' => $role]);
            json_success('管理员添加成功');
        }
    }

    /**
     * 状态切换
     */
    public function toggle()
    {
        $id = (int) input('id', 0);
        $currentAdmin = session('admin_user');
        if ($id <= 0) {
            json_error('参数错误');
        }
        if ($id == ($currentAdmin['id'] ?? 0)) {
            json_error('不能操作当前登录账号');
        }

        $admin = Db::fetch("SELECT role, status FROM jz_admin WHERE id = ?", [$id]);
        if (!$admin) {
            json_error('管理员不存在');
        }
        if ($admin['role'] === 'super') {
            json_error('不能禁用超级管理员');
        }

        $newStatus = (int) $admin['status'] === 1 ? 0 : 1;
        Db::execute(
            "UPDATE jz_admin SET status = ?, update_time = NOW() WHERE id = ?",
            [$newStatus, $id]
        );
        admin_log('admin_toggle', ['id' => $id, 'status' => $newStatus]);
        json_success('状态切换成功');
    }

    /**
     * 删除管理员
     */
    public function delete()
    {
        $id = (int) input('id', 0);
        $currentAdmin = session('admin_user');
        if ($id <= 0) {
            json_error('参数错误');
        }
        if ($id == ($currentAdmin['id'] ?? 0)) {
            json_error('不能删除当前登录账号');
        }

        $admin = Db::fetch("SELECT role, username FROM jz_admin WHERE id = ?", [$id]);
        if (!$admin) {
            json_error('管理员不存在');
        }
        if ($admin['role'] === 'super') {
            json_error('不能删除超级管理员');
        }

        Db::execute("DELETE FROM jz_admin WHERE id = ?", [$id]);
        admin_log('admin_delete', ['id' => $id, 'username' => $admin['username']]);
        json_success('删除成功');
    }
}
