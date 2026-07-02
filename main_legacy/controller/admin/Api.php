<?php
/**
 * 总站后台 - API 密钥管理
 */
class Admin_Api extends Controller
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

    public function index()
    {
        $list = Db::query("SELECT k.*, m.shop_name FROM jz_api_key k LEFT JOIN jz_merchant m ON k.merchant_id = m.id ORDER BY k.id DESC");
        $merchants = Db::query("SELECT id, shop_name FROM jz_merchant WHERE status = 1 ORDER BY id DESC");

        $this->assign('title', 'API 密钥管理');
        $this->assign('list', $list);
        $this->assign('merchants', $merchants);
        $this->fetch('admin/api/index');
    }

    public function save()
    {
        $id = (int) input('id', 0);
        $data = [
            'name' => trim(input('name', '')),
            'merchant_id' => (int) input('merchant_id', 0),
            'permissions' => trim(input('permissions', '')),
            'ips' => trim(input('ips', '')),
            'status' => (int) input('status', 1),
        ];

        if (!$data['name']) {
            json_error('密钥名称不能为空');
        }

        if ($id > 0) {
            Db::update('jz_api_key', $data, 'id = ?', [$id]);
        } else {
            $data['app_id'] = 'app_' . strtolower(generate_token(8));
            $data['app_secret'] = generate_token(32);
            $data['create_time'] = date('Y-m-d H:i:s');
            Db::insert('jz_api_key', $data);
        }

        admin_log('api_key_save', ['id' => $id, 'data' => $data]);
        json_success('保存成功');
    }

    public function resetSecret()
    {
        $id = (int) input('id', 0);
        if ($id <= 0) {
            json_error('参数错误');
        }

        $newSecret = generate_token(32);
        Db::update('jz_api_key', ['app_secret' => $newSecret], 'id = ?', [$id]);
        admin_log('api_key_reset', ['id' => $id]);
        json_success('重置成功', ['secret' => $newSecret]);
    }

    public function delete()
    {
        $id = (int) input('id', 0);
        if ($id <= 0) {
            json_error('参数错误');
        }

        Db::execute("DELETE FROM jz_api_key WHERE id = ?", [$id]);
        admin_log('api_key_delete', ['id' => $id]);
        json_success('删除成功');
    }

    public function log()
    {
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;
        $appId = input('app_id', '');

        $where = '1=1';
        $params = [];
        if ($appId) {
            $where .= ' AND app_id = ?';
            $params[] = $appId;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_api_log WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query("SELECT * FROM jz_api_log WHERE {$where} ORDER BY id DESC LIMIT {$offset}, {$pageSize}", $params);

        $this->assign('title', 'API 请求日志');
        $this->assign('list', $list);
        $this->assign('appId', $appId);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/api/log');
    }
}
