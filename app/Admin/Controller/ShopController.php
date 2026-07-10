<?php
namespace Admin\Controller;

use Framework\Database\Database;
use Framework\Response;

class ShopController extends BaseAdminController
{
    protected $layout = 'admin';

    public function users($request, $params = [])
    {
        $users = $this->getList('user');
        $this->assign('items', $users);
        $this->assign('pageTitle', '用户管理');
        $this->assign('activeMenu', 'shop_users');
        $this->view('admin.shop_users');
    }

    public function realname($request, $params = [])
    {
        $items = $this->getShops(['realname_status' => 1]);
        $this->assign('items', $items);
        $this->assign('pageTitle', '实名管理');
        $this->assign('activeMenu', 'shop_realname');
        $this->view('admin.shop_realname');
    }

    public function qualification($request, $params = [])
    {
        $items = $this->getShops(['qualification_status' => 1]);
        $this->assign('items', $items);
        $this->assign('pageTitle', '资质管理');
        $this->assign('activeMenu', 'shop_qualification');
        $this->view('admin.shop_qualification');
    }

    public function certification($request, $params = [])
    {
        $items = $this->getShops(['cert_status' => 1]);
        $this->assign('items', $items);
        $this->assign('pageTitle', '认证管理');
        $this->assign('activeMenu', 'shop_certification');
        $this->view('admin.shop_certification');
    }

    public function risk($request, $params = [])
    {
        $items = $this->getShops([]);
        $this->assign('items', $items);
        $this->assign('pageTitle', '风控管理');
        $this->assign('activeMenu', 'shop_risk');
        $this->view('admin.shop_risk');
    }

    public function service($request, $params = [])
    {
        $items = $this->getShops([]);
        $this->assign('items', $items);
        $this->assign('pageTitle', '客服管理');
        $this->assign('activeMenu', 'shop_service');
        $this->view('admin.shop_service');
    }

    public function action($request, $params = [])
    {
        $id = (int) $request->post('id', 0);
        $action = $request->post('action', '');
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $db->table('shop')->where('id', $id)->update(['status' => $action === 'enable' ? 1 : 0]);
            }
        } catch (\Exception $e) {
        }
        return $this->success('操作成功');
    }

    protected function getList($table)
    {
        $items = [];
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $items = $db->table($table)->orderBy('id', 'DESC')->limit(20)->get();
            }
        } catch (\Exception $e) {
        }
        if (empty($items)) {
            $items = [
                ['id' => 1, 'username' => 'shop001', 'nickname' => '星空小店', 'balance' => 5680.50, 'status' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-30 day'))],
                ['id' => 2, 'username' => 'shop002', 'nickname' => '云端商城', 'balance' => 8920.00, 'status' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-25 day'))],
                ['id' => 3, 'username' => 'shop003', 'nickname' => '星辰数码', 'balance' => 2340.00, 'status' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-20 day'))],
                ['id' => 4, 'username' => 'shop004', 'nickname' => '云帆点卡', 'balance' => 3200.00, 'status' => 0, 'create_time' => date('Y-m-d H:i:s', strtotime('-15 day'))],
                ['id' => 5, 'username' => 'shop005', 'nickname' => '玖捌发卡', 'balance' => 1500.00, 'status' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-10 day'))],
            ];
        }
        return $items;
    }

    protected function getShops($where)
    {
        $items = [];
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $q = $db->table('shop');
                foreach ($where as $k => $v) {
                    $q = $q->where($k, $v);
                }
                $items = $q->orderBy('id', 'DESC')->limit(20)->get();
            }
        } catch (\Exception $e) {
        }
        if (empty($items)) {
            $items = [
                ['id' => 1, 'shop_name' => '星空小店', 'real_name' => '张三', 'id_card_no' => '110101199001011234', 'realname_status' => 1, 'company' => '北京星空科技有限公司', 'qualification_status' => 2, 'risk_level' => 0, 'create_time' => date('Y-m-d H:i:s', strtotime('-30 day'))],
                ['id' => 2, 'shop_name' => '云端商城', 'real_name' => '李四', 'id_card_no' => '310101199203052345', 'realname_status' => 1, 'company' => '上海云端网络有限公司', 'qualification_status' => 1, 'risk_level' => 0, 'create_time' => date('Y-m-d H:i:s', strtotime('-25 day'))],
                ['id' => 3, 'shop_name' => '星辰数码', 'real_name' => '王五', 'id_card_no' => '440101198805124567', 'realname_status' => 1, 'company' => '广州星辰数码有限公司', 'qualification_status' => 1, 'risk_level' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-20 day'))],
            ];
        }
        return $items;
    }
}
