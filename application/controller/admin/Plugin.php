<?php
/**
 * 总站后台 - 插件 / 机器人对接管理
 */
class Admin_Plugin extends Controller
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
        $list = Db::query("SELECT * FROM jz_plugin ORDER BY id DESC");
        $this->assign('title', '插件管理');
        $this->assign('list', $list);
        $this->fetch('admin/plugin/index');
    }

    public function save()
    {
        $id = (int) input('id', 0);
        $data = [
            'code' => trim(input('code', '')),
            'name' => trim(input('name', '')),
            'type' => input('type', 'webhook'),
            'event_types' => trim(input('event_types', '')),
            'status' => (int) input('status', 1),
        ];

        if (!$data['code'] || !$data['name']) {
            json_error('插件编码和名称不能为空');
        }
        if (!in_array($data['type'], ['webhook', 'bot'], true)) {
            json_error('插件类型错误');
        }

        $config = [];
        $config['url'] = trim(input('config_url', ''));
        $config['secret'] = trim(input('config_secret', ''));
        $config['token'] = trim(input('config_token', ''));
        $data['config'] = json_encode($config, JSON_UNESCAPED_UNICODE);

        if ($id > 0) {
            Db::update('jz_plugin', $data, 'id = ?', [$id]);
        } else {
            $exist = Db::fetch("SELECT id FROM jz_plugin WHERE code = ?", [$data['code']]);
            if ($exist) {
                json_error('插件编码已存在');
            }
            $data['create_time'] = date('Y-m-d H:i:s');
            Db::insert('jz_plugin', $data);
        }

        admin_log('plugin_save', ['id' => $id, 'data' => $data]);
        json_success('保存成功');
    }

    public function delete()
    {
        $id = (int) input('id', 0);
        if ($id <= 0) {
            json_error('参数错误');
        }
        Db::execute("DELETE FROM jz_plugin WHERE id = ?", [$id]);
        admin_log('plugin_delete', ['id' => $id]);
        json_success('删除成功');
    }

    public function log()
    {
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;
        $pluginId = (int) input('plugin_id', 0);

        $where = '1=1';
        $params = [];
        if ($pluginId > 0) {
            $where .= ' AND plugin_id = ?';
            $params[] = $pluginId;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_plugin_log WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT l.*, p.name AS plugin_name, p.code FROM jz_plugin_log l LEFT JOIN jz_plugin p ON l.plugin_id = p.id WHERE {$where} ORDER BY l.id DESC LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '插件执行日志');
        $this->assign('list', $list);
        $this->assign('pluginId', $pluginId);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/plugin/log');
    }
}
