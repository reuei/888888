<?php
/**
 * Migrated from main_legacy/controller/app/controller/admin/Message.php
 */
namespace app\controller\admin;

/**
 * 总站后台 - 短信 / 邮件模板管理
 */
class Message extends Controller
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
     * 模板列表
     */
    public function index()
    {
        $type = input('type', '');
        $keyword = input('keyword', '');

        $where = '1=1';
        $params = [];
        if (in_array($type, ['sms', 'email'], true)) {
            $where .= ' AND type = ?';
            $params[] = $type;
        }
        if ($keyword) {
            $where .= ' AND (name LIKE ? OR code LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $list = Db::query("SELECT * FROM jz_message_template WHERE {$where} ORDER BY sort ASC, id ASC", $params);

        $this->assign('title', '消息模板');
        $this->assign('list', $list);
        $this->assign('type', $type);
        $this->assign('keyword', $keyword);
        $this->fetch('admin/message/index');
    }

    /**
     * 保存模板（新增/编辑）
     */
    public function save()
    {
        $id = (int) input('id', 0);
        $data = [
            'type' => input('type', ''),
            'code' => trim(input('code', '')),
            'name' => trim(input('name', '')),
            'title' => trim(input('title', '')),
            'content' => input('content', ''),
            'variables' => trim(input('variables', '')),
            'status' => (int) input('status', 1),
            'description' => trim(input('description', '')),
            'sort' => (int) input('sort', 0),
        ];

        if (!in_array($data['type'], ['sms', 'email'], true)) {
            json_error('模板类型错误');
        }
        if (!$data['code'] || !$data['name'] || !$data['content']) {
            json_error('模板编码、名称和内容不能为空');
        }

        // 检查编码唯一性
        $exist = Db::fetch(
            "SELECT id FROM jz_message_template WHERE type = ? AND code = ? AND id != ?",
            [$data['type'], $data['code'], $id]
        );
        if ($exist) {
            json_error('同一类型下模板编码已存在');
        }

        if ($id > 0) {
            Db::update('jz_message_template', $data, 'id = ?', [$id]);
        } else {
            Db::insert('jz_message_template', $data);
        }

        admin_log('message_template_save', ['id' => $id, 'data' => $data]);
        json_success('保存成功');
    }

    /**
     * 删除模板
     */
    public function delete()
    {
        $id = (int) input('id', 0);
        if ($id <= 0) {
            json_error('参数错误');
        }

        Db::execute("DELETE FROM jz_message_template WHERE id = ?", [$id]);
        admin_log('message_template_delete', ['id' => $id]);
        json_success('删除成功');
    }

    /**
     * 测试发送
     */
    public function test()
    {
        $id = (int) input('id', 0);
        $recipient = trim(input('recipient', ''));
        $varsJson = input('vars', '{}');

        $template = Db::fetch("SELECT * FROM jz_message_template WHERE id = ?", [$id]);
        if (!$template) {
            json_error('模板不存在');
        }

        $vars = json_decode($varsJson, true);
        if (!is_array($vars)) {
            $vars = [];
        }

        if ($template['type'] === 'email') {
            $result = send_email($recipient, $template['code'], $vars);
        } else {
            $result = send_sms($recipient, $template['code'], $vars);
        }

        admin_log('message_template_test', [
            'id' => $id,
            'type' => $template['type'],
            'recipient' => $recipient,
            'result' => $result,
        ]);

        if ($result['code'] === 0) {
            json_success($result['msg'], $result);
        }
        json_error($result['msg']);
    }

    /**
     * 发送日志
     */
    public function log()
    {
        $type = input('type', 'email');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;
        $keyword = input('keyword', '');

        $table = $type === 'sms' ? 'jz_sms_log' : 'jz_email_log';
        $searchField = $type === 'sms' ? 'mobile' : 'recipient';

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= " AND {$searchField} LIKE ?";
            $params[] = '%' . $keyword . '%';
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM {$table} WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query("SELECT * FROM {$table} WHERE {$where} ORDER BY id DESC LIMIT {$offset}, {$pageSize}", $params);

        $this->assign('title', '发送日志');
        $this->assign('type', $type);
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/message/log');
    }
}
