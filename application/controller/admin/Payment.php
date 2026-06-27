<?php
/**
 * 总站后台 - 支付网关
 */
class Admin_Payment extends Controller
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
     * 渠道配置
     */
    public function channel()
    {
        $keyword = input('keyword', '');
        $status = input('status', '');
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

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_payment_channel WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT * FROM jz_payment_channel WHERE {$where} ORDER BY sort ASC, id ASC LIMIT {$offset}, {$pageSize}",
            $params
        );

        $scopeMap = [
            'global' => '全局',
            'subsite' => '分站',
            'merchant' => '商户',
        ];

        $this->assign('title', '支付渠道配置');
        $this->assign('list', $list);
        $this->assign('scopeMap', $scopeMap);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/payment/channel');
    }

    /**
     * 保存渠道
     */
    public function channelSave()
    {
        $id = (int) input('id', 0);
        $code = trim(input('code', ''));
        $name = trim(input('name', ''));
        $config = trim(input('config', ''));
        $scope = input('scope', 'global');
        $scopeId = (int) input('scope_id', 0);
        $sort = (int) input('sort', 0);
        $status = (int) input('status', 1);

        if (!$code || !$name) {
            json_error('请输入渠道编码和名称');
        }
        if (!in_array($scope, ['global', 'subsite', 'merchant'], true)) {
            json_error('作用范围错误');
        }

        // 校验配置 JSON
        if ($config) {
            $decoded = json_decode($config, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                json_error('配置 JSON 格式错误：' . json_last_error_msg());
            }
            $config = json_encode($decoded, JSON_UNESCAPED_UNICODE);
        }

        $exists = Db::fetch("SELECT id FROM jz_payment_channel WHERE code = ? AND scope = ? AND scope_id = ? AND id != ?", [$code, $scope, $scopeId, $id]);
        if ($exists) {
            json_error('同一作用范围下渠道编码已存在');
        }

        $data = [
            'code' => $code,
            'name' => $name,
            'config' => $config,
            'scope' => $scope,
            'scope_id' => $scopeId,
            'sort' => $sort,
            'status' => $status,
        ];

        if ($id) {
            Db::update('jz_payment_channel', $data, 'id = ?', [$id]);
            json_success('渠道更新成功');
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            Db::insert('jz_payment_channel', $data);
            json_success('渠道添加成功');
        }
    }

    public function channelDelete()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }
        Db::execute("DELETE FROM jz_payment_channel WHERE id = ?", [$id]);
        json_success('渠道已删除');
    }

    public function channelStatus()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 1);
        if (!$id) {
            json_error('参数错误');
        }
        Db::execute("UPDATE jz_payment_channel SET status = ? WHERE id = ?", [$status, $id]);
        json_success('状态更新成功');
    }

    /**
     * 风控策略
     */
    public function risk()
    {
        $defaultRisk = [
            'amount_jitter' => '0',
            'jitter_range' => '0.01',
            'min_amount' => '0.01',
            'max_amount' => '50000.00',
            'ip_limit' => '0',
            'ip_limit_count' => '10',
            'blacklist_words' => '',
            'contact_required' => '1',
        ];

        $risk = [];
        foreach ($defaultRisk as $key => $value) {
            $row = Db::fetch("SELECT cfg_value FROM jz_config WHERE cfg_key = ?", ['risk_' . $key]);
            $risk[$key] = $row['cfg_value'] ?? $value;
        }

        $this->assign('title', '风控策略');
        $this->assign('risk', $risk);
        $this->fetch('admin/payment/risk');
    }

    public function riskSave()
    {
        $fields = [
            'risk_amount_jitter' => ['是否开启金额随机化（0/1）', '0'],
            'risk_jitter_range' => ['金额随机化范围（0-1）', '0.01'],
            'risk_min_amount' => ['单笔最小金额', '0.01'],
            'risk_max_amount' => ['单笔最大金额', '50000.00'],
            'risk_ip_limit' => ['是否限制同IP下单（0/1）', '0'],
            'risk_ip_limit_count' => ['同IP限购次数（0为不限）', '10'],
            'risk_blacklist_words' => ['黑名单关键词（逗号分隔）', ''],
            'risk_contact_required' => ['是否强制填写联系方式（0/1）', '1'],
        ];

        foreach ($fields as $key => $desc) {
            $shortKey = substr($key, 5);
            $value = input($shortKey, $desc[1]);
            Db::execute(
                "INSERT INTO jz_config (cfg_key, cfg_value, cfg_group, description) VALUES (?, ?, 'risk', ?)
                 ON DUPLICATE KEY UPDATE cfg_value = VALUES(cfg_value), update_time = NOW()",
                [$key, $value, $desc[0]]
            );
        }

        json_success('风控策略保存成功');
    }
}
