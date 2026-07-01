<?php
/**
 * 后台授权码管理
 */
class Admin_License extends Controller
{
    public function __construct()
    {
        parent::__construct();
        require_admin_login();
        $this->setLayout('layout/admin');
    }

    public function index()
    {
        $page = max(1, (int) input('page', 1));
        $pageSize = 15;
        $keyword = trim(input('keyword', ''));
        $productId = (int) input('product_id', 0);

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (l.auth_code LIKE ? OR l.auth_domain LIKE ? OR u.username LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        if ($productId) {
            $where .= ' AND l.product_id = ?';
            $params[] = $productId;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM qef_license l WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT l.*, p.name as product_name, u.username, u.nickname
             FROM qef_license l
             LEFT JOIN qef_product p ON l.product_id = p.id
             LEFT JOIN qef_user u ON l.user_id = u.id
             WHERE {$where}
             ORDER BY l.id DESC LIMIT {$offset}, {$pageSize}",
            $params
        );

        $products = Db::query("SELECT id, name FROM qef_product WHERE status = 1 ORDER BY sort DESC, id DESC");

        $this->assign('title', '授权码管理');
        $this->assign('list', $list);
        $this->assign('products', $products);
        $this->assign('keyword', $keyword);
        $this->assign('productId', $productId);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/license/index');
    }

    public function toggleStatus()
    {
        $id = (int) input('id', 0);
        $license = Db::fetch("SELECT status FROM qef_license WHERE id = ?", [$id]);
        if (!$license) {
            json_error('授权不存在');
        }
        $status = $license['status'] == 1 ? 0 : 1;
        Db::update('qef_license', ['status' => $status], 'id = ?', [$id]);
        admin_log('切换授权状态', ['id' => $id, 'status' => $status]);
        json_success('操作成功');
    }

    public function delete()
    {
        $id = (int) input('id', 0);
        Db::delete('qef_license', 'id = ?', [$id]);
        admin_log('删除授权', ['id' => $id]);
        json_success('删除成功');
    }
}
