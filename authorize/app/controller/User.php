<?php
/**
 * 用户中心控制器
 */

namespace app\controller;

use app\BaseController;
use app\Db;
use think\App;
use think\facade\Session;

class User extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        require_user_login();
        $this->assign('currentUser', get_user());
    }

    public function index()
    {
        $user = get_user();
        $licenseCount = Db::fetch("SELECT COUNT(*) AS total FROM qef_license WHERE user_id = ?", [$user['id']]);
        $pluginCount = Db::fetch("SELECT COUNT(*) AS total FROM qef_user_plugin WHERE user_id = ?", [$user['id']]);
        $orderCount = Db::fetch("SELECT COUNT(*) AS total FROM qef_order WHERE user_id = ?", [$user['id']]);

        $this->assign('title', '个人中心');
        $this->assign('licenseCount', $licenseCount['total'] ?? 0);
        $this->assign('pluginCount', $pluginCount['total'] ?? 0);
        $this->assign('orderCount', $orderCount['total'] ?? 0);
        $this->fetch('user/index');
    }

    public function profile()
    {
        $user = get_user();
        $this->assign('title', '修改资料');
        $this->assign('user', $user);
        $this->fetch('user/profile');
    }

    public function saveProfile()
    {
        $user = get_user();
        $nickname = trim(input('nickname', ''));
        $email = trim(input('email', ''));
        $mobile = trim(input('mobile', ''));

        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            json_error('邮箱格式错误');
        }

        Db::update('qef_user', [
            'nickname' => $nickname,
            'email' => $email,
            'mobile' => $mobile,
        ], 'id = ?', [$user['id']]);

        $user = Db::fetch("SELECT * FROM qef_user WHERE id = ?", [$user['id']]);
        session('user', $user);
        json_success('保存成功');
    }

    public function password()
    {
        $this->assign('title', '修改密码');
        $this->fetch('user/password');
    }

    public function savePassword()
    {
        $user = get_user();
        $oldPassword = input('old_password', '');
        $newPassword = input('new_password', '');
        $confirmPassword = input('confirm_password', '');

        if (!$oldPassword || !$newPassword) {
            json_error('请填写完整');
        }
        if (!password_verify_custom($oldPassword, $user['password'])) {
            json_error('原密码错误');
        }
        if (strlen($newPassword) < 6) {
            json_error('新密码长度不能少于 6 位');
        }
        if ($newPassword !== $confirmPassword) {
            json_error('两次输入不一致');
        }

        Db::update('qef_user', [
            'password' => password_hash_custom($newPassword),
        ], 'id = ?', [$user['id']]);

        json_success('密码修改成功');
    }

    public function license()
    {
        $user = get_user();
        $page = max(1, (int) input('page', 1));
        $pageSize = 10;

        $count = Db::fetch("SELECT COUNT(*) AS total FROM qef_license WHERE user_id = ?", [$user['id']]);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT l.*, p.name as product_name FROM qef_license l LEFT JOIN qef_product p ON l.product_id = p.id WHERE l.user_id = ? ORDER BY l.id DESC LIMIT {$offset}, {$pageSize}",
            [$user['id']]
        );

        $this->assign('title', '我的授权');
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('user/license');
    }

    public function bindDomain()
    {
        $user = get_user();
        $id = (int) input('id', 0);
        $domain = trim(input('domain', ''));

        $license = Db::fetch("SELECT * FROM qef_license WHERE id = ? AND user_id = ?", [$id, $user['id']]);
        if (!$license) {
            json_error('授权不存在');
        }
        if ($license['license_type'] !== 'domain') {
            json_error('该授权不支持绑定域名');
        }
        if (!$domain) {
            json_error('请输入域名');
        }
        if (!preg_match('/^([a-zA-Z0-9_-]+\.)+[a-zA-Z0-9_-]+$/', $domain)) {
            json_error('域名格式错误');
        }

        Db::update('qef_license', ['auth_domain' => $domain], 'id = ?', [$id]);
        json_success('域名绑定成功');
    }

    public function unbindDomain()
    {
        $user = get_user();
        $id = (int) input('id', 0);
        $license = Db::fetch("SELECT * FROM qef_license WHERE id = ? AND user_id = ?", [$id, $user['id']]);
        if (!$license) {
            json_error('授权不存在');
        }
        Db::update('qef_license', ['auth_domain' => ''], 'id = ?', [$id]);
        json_success('域名解绑成功');
    }

    public function transfer()
    {
        $user = get_user();
        $id = (int) input('id', 0);
        $targetUsername = trim(input('target_username', ''));

        $license = Db::fetch("SELECT * FROM qef_license WHERE id = ? AND user_id = ?", [$id, $user['id']]);
        if (!$license) {
            json_error('授权不存在');
        }

        $target = Db::fetch("SELECT id FROM qef_user WHERE username = ? AND status = 1", [$targetUsername]);
        if (!$target) {
            json_error('目标用户不存在');
        }
        if ($target['id'] == $user['id']) {
            json_error('不能转让给自己');
        }

        Db::update('qef_license', ['user_id' => $target['id']], 'id = ?', [$id]);
        json_success('授权转让成功');
    }

    public function plugin()
    {
        $user = get_user();
        $page = max(1, (int) input('page', 1));
        $pageSize = 10;

        $count = Db::fetch("SELECT COUNT(*) AS total FROM qef_user_plugin WHERE user_id = ?", [$user['id']]);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT up.*, p.name, p.version, p.file_path, p.file_md5 FROM qef_user_plugin up LEFT JOIN qef_plugin p ON up.plugin_id = p.id WHERE up.user_id = ? ORDER BY up.id DESC LIMIT {$offset}, {$pageSize}",
            [$user['id']]
        );

        $this->assign('title', '我的插件');
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('user/plugin');
    }

    public function pluginUpload()
    {
        $this->assign('title', '上传插件');
        $this->fetch('user/plugin_upload');
    }

    public function doPluginUpload()
    {
        $user = get_user();
        $name = trim(input('name', ''));
        $description = trim(input('description', ''));
        $price = (float) input('price', 0);
        $version = trim(input('version', ''));

        if (!$name || !$version) {
            json_error('请填写插件名称和版本');
        }
        if ($price < 0) {
            json_error('价格不能为负数');
        }

        $result = upload_zip('file', 'plugin');
        if ($result['code'] !== 0) {
            json_error($result['msg']);
        }

        $filePath = $result['path'];
        $fullPath = public_path() . str_replace('/', DIRECTORY_SEPARATOR, $filePath);
        $fileMd5 = md5_file($fullPath);

        Db::insert('qef_plugin', [
            'user_id' => $user['id'],
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'version' => $version,
            'file_path' => $filePath,
            'file_md5' => $fileMd5,
            'status' => 0,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        json_success('插件上传成功，等待审核', ['redirect' => url('user/plugin')]);
    }

    public function order()
    {
        $user = get_user();
        $page = max(1, (int) input('page', 1));
        $pageSize = 10;
        $status = input('status', '');

        $where = 'user_id = ?';
        $params = [$user['id']];
        if ($status !== '') {
            $where .= ' AND status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM qef_order WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query("SELECT * FROM qef_order WHERE {$where} ORDER BY id DESC LIMIT {$offset}, {$pageSize}", $params);

        $this->assign('title', '我的订单');
        $this->assign('list', $list);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('user/order');
    }

    public function recharge()
    {
        $this->assign('title', '余额充值');
        $this->fetch('user/recharge');
    }

    public function doRecharge()
    {
        $user = get_user();
        $amount = (float) input('amount', 0);
        $remark = trim(input('remark', ''));

        if ($amount <= 0) {
            json_error('请输入正确的充值金额');
        }

        Db::insert('qef_recharge', [
            'user_id' => $user['id'],
            'amount' => $amount,
            'pay_channel' => 'offline',
            'pay_remark' => $remark,
            'status' => 0,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        json_success('充值申请已提交，等待管理员处理');
    }

    public function logout()
    {
        Session::delete('user');
        redirect(url('/'));
    }
}
