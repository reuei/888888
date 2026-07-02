<?php
/**
 * 后台用户管理
 */

namespace app\controller\admin;

use app\BaseController;
use app\Db;
use think\App;

class User extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        require_admin_login();
        $this->setLayout('layout/admin');
    }

    public function index()
    {
        $page = max(1, (int) input('page', 1));
        $pageSize = 15;
        $keyword = trim(input('keyword', ''));

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (username LIKE ? OR nickname LIKE ? OR email LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM qef_user WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query("SELECT * FROM qef_user WHERE {$where} ORDER BY id DESC LIMIT {$offset}, {$pageSize}", $params);

        $this->assign('title', '用户管理');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/user/index');
    }

    public function edit()
    {
        $id = (int) input('id', 0);
        $user = Db::fetch("SELECT * FROM qef_user WHERE id = ?", [$id]);
        if (!$user) {
            throw new \Exception('用户不存在');
        }
        $this->assign('title', '编辑用户');
        $this->assign('user', $user);
        $this->fetch('admin/user/edit');
    }

    public function save()
    {
        $id = (int) input('id', 0);
        $nickname = trim(input('nickname', ''));
        $email = trim(input('email', ''));
        $mobile = trim(input('mobile', ''));
        $balance = (float) input('balance', 0);
        $status = (int) input('status', 1);
        $password = input('password', '');

        $user = Db::fetch("SELECT id FROM qef_user WHERE id = ?", [$id]);
        if (!$user) {
            json_error('用户不存在');
        }

        $data = [
            'nickname' => $nickname,
            'email' => $email,
            'mobile' => $mobile,
            'balance' => $balance,
            'status' => $status,
        ];
        if ($password) {
            $data['password'] = password_hash_custom($password);
        }

        Db::update('qef_user', $data, 'id = ?', [$id]);
        admin_log('编辑用户', ['id' => $id]);
        json_success('保存成功');
    }

    public function toggleStatus()
    {
        $id = (int) input('id', 0);
        $user = Db::fetch("SELECT status FROM qef_user WHERE id = ?", [$id]);
        if (!$user) {
            json_error('用户不存在');
        }
        $status = $user['status'] == 1 ? 0 : 1;
        Db::update('qef_user', ['status' => $status], 'id = ?', [$id]);
        admin_log('切换用户状态', ['id' => $id, 'status' => $status]);
        json_success('操作成功');
    }
}
