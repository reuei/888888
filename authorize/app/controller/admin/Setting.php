<?php
/**
 * 后台系统设置
 */

namespace app\controller\admin;

use app\BaseController;
use app\Db;
use think\App;

class Setting extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        require_admin_login();
        $this->setLayout('layout/admin');
    }

    public function index()
    {
        $config = Db::query("SELECT * FROM qef_config WHERE cfg_group = 'base' ORDER BY id ASC");
        $this->assign('title', '系统设置');
        $this->assign('config', $config);
        $this->fetch('admin/setting/index');
    }

    public function save()
    {
        $data = input();
        foreach ($data as $key => $value) {
            if (strpos($key, 'cfg_') !== 0) {
                continue;
            }
            $exists = Db::fetch("SELECT id FROM qef_config WHERE cfg_key = ?", [$key]);
            if ($exists) {
                Db::update('qef_config', ['cfg_value' => $value], 'id = ?', [$exists['id']]);
            } else {
                Db::insert('qef_config', [
                    'cfg_key' => $key,
                    'cfg_value' => $value,
                    'cfg_group' => 'base',
                    'description' => '',
                ]);
            }
        }
        admin_log('修改系统设置');
        json_success('保存成功');
    }
}
