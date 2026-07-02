<?php
/**
 * 总站后台 - 系统设置
 */
class Admin_Setting extends Controller
{
    private $configGroup = 'base';

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
     * 通用配置读取
     */
    private function getConfig($keys)
    {
        $result = [];
        foreach ($keys as $key => $default) {
            $row = Db::fetch("SELECT cfg_value FROM jz_config WHERE cfg_key = ?", [$key]);
            $result[$key] = $row['cfg_value'] ?? $default;
        }
        return $result;
    }

    /**
     * 通用配置保存
     */
    public function save()
    {
        $group = input('group', 'base');
        $allowedGroups = ['base', 'email', 'sms', 'storage', 'security'];
        if (!in_array($group, $allowedGroups, true)) {
            json_error('配置分组错误');
        }

        $data = input();
        unset($data['group']);

        foreach ($data as $key => $value) {
            $cfgKey = $group . '_' . $key;
            Db::execute(
                "INSERT INTO jz_config (cfg_key, cfg_value, cfg_group, description) VALUES (?, ?, ?, '')
                 ON DUPLICATE KEY UPDATE cfg_value = VALUES(cfg_value), update_time = NOW()",
                [$cfgKey, $value, $group]
            );
        }

        admin_log('setting_save', ['group' => $group, 'keys' => array_keys($data)]);
        json_success('配置保存成功');
    }

    /**
     * 站点基础设置
     */
    public function index()
    {
        $keys = [
            'base_site_name' => '鲸商城 Pro',
            'base_logo' => '',
            'base_icp' => '',
            'base_contact' => '',
            'base_copyright' => '鲸商城 Pro v1.0.0',
        ];
        $config = $this->getConfig($keys);

        $this->assign('title', '站点基础设置');
        $this->assign('config', $config);
        $this->fetch('admin/setting/index');
    }

    /**
     * 邮件系统
     */
    public function email()
    {
        $keys = [
            'email_host' => '',
            'email_port' => '465',
            'email_user' => '',
            'email_pass' => '',
            'email_from' => '',
            'email_secure' => 'ssl',
        ];
        $config = $this->getConfig($keys);

        $this->assign('title', '邮件系统');
        $this->assign('config', $config);
        $this->fetch('admin/setting/email');
    }

    /**
     * 短信通知
     */
    public function sms()
    {
        $keys = [
            'sms_gateway' => '',
            'sms_app_id' => '',
            'sms_app_key' => '',
            'sms_sign' => '',
        ];
        $config = $this->getConfig($keys);

        $this->assign('title', '短信通知');
        $this->assign('config', $config);
        $this->fetch('admin/setting/sms');
    }

    /**
     * 文件存储
     */
    public function storage()
    {
        $keys = [
            'storage_type' => 'local',
            'storage_domain' => '',
            'storage_bucket' => '',
            'storage_ak' => '',
            'storage_sk' => '',
        ];
        $config = $this->getConfig($keys);

        $this->assign('title', '文件存储');
        $this->assign('config', $config);
        $this->fetch('admin/setting/storage');
    }

    /**
     * 安全防护
     */
    public function security()
    {
        $keys = [
            'security_admin_2fa' => '0',
            'security_merchant_2fa' => '0',
            'security_login_fail_limit' => '5',
            'security_login_lock_minutes' => '30',
            'security_captcha_login' => '0',
            'security_captcha_join' => '0',
        ];
        $config = $this->getConfig($keys);

        $blacklist = Db::query("SELECT * FROM jz_ip_blacklist ORDER BY id DESC LIMIT 100");

        $this->assign('title', '安全防护');
        $this->assign('config', $config);
        $this->assign('blacklist', $blacklist);
        $this->fetch('admin/setting/security');
    }

    /**
     * IP 黑名单保存
     */
    public function ipBlacklistSave()
    {
        $id = (int) input('id', 0);
        $ip = trim(input('ip', ''));
        $reason = trim(input('reason', ''));
        $expireTime = input('expire_time', '');
        $status = (int) input('status', 1);

        if (!$ip) {
            json_error('请输入 IP 或 IP 段');
        }
        if (!preg_match('/^[0-9a-zA-Z.*:%\/_-]+$/', $ip)) {
            json_error('IP 格式不合法');
        }

        $data = [
            'ip' => $ip,
            'reason' => $reason,
            'status' => $status ? 1 : 0,
        ];
        $data['expire_time'] = $expireTime ? str_replace('T', ' ', $expireTime) : null;

        if ($id > 0) {
            Db::execute(
                "UPDATE jz_ip_blacklist SET ip = ?, reason = ?, expire_time = ?, status = ?, update_time = NOW() WHERE id = ?",
                [$data['ip'], $data['reason'], $data['expire_time'], $data['status'], $id]
            );
            admin_log('ip_blacklist_update', ['id' => $id, 'ip' => $ip]);
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            Db::insert('jz_ip_blacklist', $data);
            admin_log('ip_blacklist_create', ['ip' => $ip]);
        }

        json_success('保存成功');
    }

    /**
     * IP 黑名单删除
     */
    public function ipBlacklistDelete()
    {
        $id = (int) input('id', 0);
        if ($id <= 0) {
            json_error('参数错误');
        }

        $item = Db::fetch("SELECT ip FROM jz_ip_blacklist WHERE id = ?", [$id]);
        Db::execute("DELETE FROM jz_ip_blacklist WHERE id = ?", [$id]);
        admin_log('ip_blacklist_delete', ['id' => $id, 'ip' => $item['ip'] ?? '']);
        json_success('删除成功');
    }

    /**
     * IP 黑名单状态切换
     */
    public function ipBlacklistToggle()
    {
        $id = (int) input('id', 0);
        if ($id <= 0) {
            json_error('参数错误');
        }

        Db::execute(
            "UPDATE jz_ip_blacklist SET status = IF(status = 1, 0, 1), update_time = NOW() WHERE id = ?",
            [$id]
        );
        admin_log('ip_blacklist_toggle', ['id' => $id]);
        json_success('状态切换成功');
    }
}
