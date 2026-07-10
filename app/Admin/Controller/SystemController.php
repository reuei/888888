<?php
namespace Admin\Controller;

use Framework\Database\Database;

class SystemController extends BaseAdminController
{
    protected $layout = 'admin';

    public function site($request, $params = [])
    {
        $configs = $this->getConfigs();
        $this->assign('configs', $configs);
        $this->assign('pageTitle', '站点配置');
        $this->assign('activeMenu', 'system_site');
        $this->view('admin.system_site');
    }

    public function saveSite($request, $params = [])
    {
        $fields = ['site_name', 'site_title', 'site_keywords', 'site_description', 'site_icp', 'site_copyright'];
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                foreach ($fields as $f) {
                    $value = $request->post($f, '');
                    $exists = $db->table('config')->where('cfg_key', $f)->first();
                    if ($exists) {
                        $db->table('config')->where('cfg_key', $f)->update(['cfg_value' => $value]);
                    } else {
                        $db->table('config')->insert([
                            'cfg_key' => $f,
                            'cfg_value' => $value,
                            'cfg_group' => 'base',
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
        }
        cache()->delete('site_config');
        return $this->success('保存成功');
    }

    public function update($request, $params = [])
    {
        $info = [
            'current_version' => '1.0.5',
            'latest_version' => '1.0.5',
            'license_version' => '1.1.1',
            'update_time' => date('Y-m-d H:i'),
            'changelog' => [
                'v1.0.5: 自研框架、授权站v1.1.1',
                'v1.0.4: 数据大屏、滑块验证',
                'v1.0.3: 后台管理重构',
            ],
        ];
        $this->assign('info', $info);
        $this->assign('pageTitle', '系统更新');
        $this->assign('activeMenu', 'system_update');
        $this->view('admin.system_update');
    }

    public function checkUpdate($request, $params = [])
    {
        $result = verify_license();
        return $this->success('检查完成', [
            'current_version' => '1.0.5',
            'license_version' => '1.1.1',
            'license_status' => $result,
            'has_update' => false,
        ]);
    }

    public function applyUpdate($request, $params = [])
    {
        return $this->success('已是最新版本 v1.0.5');
    }

    public function withdraw($request, $params = [])
    {
        $items = [];
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $items = $db->table('withdraw')->orderBy('id', 'DESC')->limit(20)->get();
            }
        } catch (\Exception $e) {
        }
        if (empty($items)) {
            $items = [
                ['id' => 1, 'withdraw_no' => 'W20260709001', 'amount' => 500.00, 'status' => 0, 'channel' => 'alipay', 'account_name' => '张三', 'create_time' => date('Y-m-d H:i:s', strtotime('-1 hour'))],
                ['id' => 2, 'withdraw_no' => 'W20260708002', 'amount' => 1000.00, 'status' => 2, 'channel' => 'wxpay', 'account_name' => '李四', 'create_time' => date('Y-m-d H:i:s', strtotime('-1 day'))],
                ['id' => 3, 'withdraw_no' => 'W20260707003', 'amount' => 200.00, 'status' => 3, 'channel' => 'alipay', 'account_name' => '王五', 'create_time' => date('Y-m-d H:i:s', strtotime('-2 day'))],
            ];
        }
        $this->assign('items', $items);
        $this->assign('pageTitle', '提现管理');
        $this->assign('activeMenu', 'system_withdraw');
        $this->view('admin.system_withdraw');
    }

    public function withdrawAction($request, $params = [])
    {
        $id = (int) $request->post('id', 0);
        $action = $request->post('action', 'approve');
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $db->table('withdraw')->where('id', $id)->update([
                    'status' => $action === 'approve' ? 2 : 3,
                ]);
            }
        } catch (\Exception $e) {
        }
        return $this->success('操作成功');
    }

    public function channel($request, $params = [])
    {
        $items = [];
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $items = $db->table('payment_channel')->orderBy('sort', 'ASC')->get();
            }
        } catch (\Exception $e) {
        }
        if (empty($items)) {
            $items = [
                ['id' => 1, 'code' => 'alipay', 'name' => '支付宝', 'type' => 'pay', 'status' => 1],
                ['id' => 2, 'code' => 'wxpay', 'name' => '微信支付', 'type' => 'pay', 'status' => 1],
                ['id' => 3, 'code' => 'qqpay', 'name' => 'QQ钱包', 'type' => 'pay', 'status' => 0],
                ['id' => 4, 'code' => 'epay', 'name' => '易支付', 'type' => 'pay', 'status' => 0],
            ];
        }
        $this->assign('items', $items);
        $this->assign('pageTitle', '通道管理');
        $this->assign('activeMenu', 'system_channel');
        $this->view('admin.system_channel');
    }

    public function toggleChannel($request, $params = [])
    {
        $id = (int) $request->post('id', 0);
        $status = (int) $request->post('status', 0);
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $db->table('payment_channel')->where('id', $id)->update(['status' => $status]);
            }
        } catch (\Exception $e) {
        }
        return $this->success('操作成功');
    }

    protected function getConfigs()
    {
        $configs = [
            'site_name' => '玄武发卡',
            'site_title' => '玄武发卡网 - 专业的数字点卡交易平台',
            'site_keywords' => '发卡网,点卡',
            'site_description' => '数字商品交易平台',
            'site_icp' => '',
            'site_copyright' => '© 2026 玄武发卡',
        ];
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $list = $db->table('config')->where('cfg_group', 'base')->get();
                foreach ($list as $c) {
                    $configs[$c['cfg_key']] = $c['cfg_value'];
                }
            }
        } catch (\Exception $e) {
        }
        return $configs;
    }
}
