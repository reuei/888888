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
     * 二次认证
     */
    public function security()
    {
        $keys = [
            'security_admin_2fa' => '0',
            'security_merchant_2fa' => '0',
            'security_login_fail_limit' => '5',
            'security_login_lock_minutes' => '30',
        ];
        $config = $this->getConfig($keys);

        $this->assign('title', '二次认证');
        $this->assign('config', $config);
        $this->fetch('admin/setting/security');
    }
}
