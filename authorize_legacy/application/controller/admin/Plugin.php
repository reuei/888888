<?php
/**
 * 后台插件管理
 */
class Admin_Plugin extends Controller
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
        $status = input('status', '');

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND p.name LIKE ?';
            $params[] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where .= ' AND p.status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM qef_plugin p WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT p.*, u.nickname as author FROM qef_plugin p LEFT JOIN qef_user u ON p.user_id = u.id WHERE {$where} ORDER BY p.id DESC LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '插件管理');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/plugin/index');
    }

    public function review()
    {
        $id = (int) input('id', 0);
        $plugin = Db::fetch("SELECT p.*, u.nickname as author FROM qef_plugin p LEFT JOIN qef_user u ON p.user_id = u.id WHERE p.id = ?", [$id]);
        if (!$plugin) {
            throw new Exception('插件不存在');
        }
        $this->assign('title', '审核插件');
        $this->assign('plugin', $plugin);
        $this->fetch('admin/plugin/review');
    }

    public function doReview()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 0);
        if (!in_array($status, [1, 2], true)) {
            json_error('审核状态错误');
        }
        $plugin = Db::fetch("SELECT id FROM qef_plugin WHERE id = ?", [$id]);
        if (!$plugin) {
            json_error('插件不存在');
        }
        Db::update('qef_plugin', ['status' => $status], 'id = ?', [$id]);
        admin_log('审核插件', ['id' => $id, 'status' => $status]);
        json_success('审核完成', ['redirect' => url('admin/plugin')]);
    }

    public function toggleStatus()
    {
        $id = (int) input('id', 0);
        $plugin = Db::fetch("SELECT status FROM qef_plugin WHERE id = ?", [$id]);
        if (!$plugin) {
            json_error('插件不存在');
        }
        $status = $plugin['status'] == 1 ? 2 : 1;
        Db::update('qef_plugin', ['status' => $status], 'id = ?', [$id]);
        admin_log('切换插件状态', ['id' => $id, 'status' => $status]);
        json_success('操作成功');
    }

    public function delete()
    {
        $id = (int) input('id', 0);
        $plugin = Db::fetch("SELECT file_path FROM qef_plugin WHERE id = ?", [$id]);
        if ($plugin && $plugin['file_path']) {
            $fullPath = ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $plugin['file_path']);
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
        }
        Db::delete('qef_plugin', 'id = ?', [$id]);
        admin_log('删除插件', ['id' => $id]);
        json_success('删除成功');
    }
}
