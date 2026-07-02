<?php
/**
 * 后台充值申请管理
 */

namespace app\controller\admin;

use app\BaseController;
use app\Db;
use think\App;

class Recharge extends BaseController
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
        $status = input('status', '');

        $where = '1=1';
        $params = [];
        if ($status !== '') {
            $where .= ' AND r.status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM qef_recharge r WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT r.*, u.username, u.nickname FROM qef_recharge r LEFT JOIN qef_user u ON r.user_id = u.id WHERE {$where} ORDER BY r.id DESC LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '充值申请');
        $this->assign('list', $list);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/recharge/index');
    }

    public function approve()
    {
        $id = (int) input('id', 0);
        $recharge = Db::fetch("SELECT * FROM qef_recharge WHERE id = ? AND status = 0", [$id]);
        if (!$recharge) {
            json_error('申请不存在或已处理');
        }

        Db::execute("UPDATE qef_user SET balance = balance + ? WHERE id = ?", [$recharge['amount'], $recharge['user_id']]);
        Db::update('qef_recharge', ['status' => 1, 'update_time' => date('Y-m-d H:i:s')], 'id = ?', [$id]);
        admin_log('充值到账', ['id' => $id, 'amount' => $recharge['amount']]);
        json_success('充值已到账');
    }

    public function reject()
    {
        $id = (int) input('id', 0);
        Db::update('qef_recharge', ['status' => 2, 'update_time' => date('Y-m-d H:i:s')], 'id = ?', [$id]);
        admin_log('拒绝充值', ['id' => $id]);
        json_success('已拒绝');
    }
}
