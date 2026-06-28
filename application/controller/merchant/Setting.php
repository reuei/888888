<?php
/**
 * 商户后台 - 店铺设置
 */
class Merchant_Setting extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/merchant');
        if (!session('merchant_user')) {
            redirect(url('login') . '?type=merchant');
        }
    }

    private function getMerchantId()
    {
        return (int) (session('merchant_user')['id'] ?? 0);
    }

    /**
     * 店铺信息
     */
    public function index()
    {
        $merchant = Db::fetch("SELECT * FROM jz_merchant WHERE id = ?", [$this->getMerchantId()]);
        $this->assign('title', '店铺信息');
        $this->assign('merchant', $merchant);
        $this->fetch('merchant/setting/index');
    }

    /**
     * 修改密码
     */
    public function password()
    {
        $this->assign('title', '修改密码');
        $this->fetch('merchant/setting/password');
    }

    /**
     * 保存设置
     */
    public function save()
    {
        $merchantId = $this->getMerchantId();
        $shopName = trim(input('shop_name', ''));
        $mobile = trim(input('mobile', ''));
        $realName = trim(input('real_name', ''));
        $email = trim(input('email', ''));

        if (!$shopName) {
            json_error('店铺名称不能为空');
        }
        if ($mobile && !preg_match('/^1[3-9]\d{9}$/', $mobile)) {
            json_error('手机号格式错误');
        }
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            json_error('邮箱格式错误');
        }

        $data = [
            'shop_name' => $shopName,
            'mobile' => $mobile,
            'real_name' => $realName,
            'email' => $email,
            'update_time' => date('Y-m-d H:i:s'),
        ];

        Db::update('jz_merchant', $data, 'id = ?', [$merchantId]);
        admin_log('merchant_setting_update', ['merchant_id' => $merchantId, 'shop_name' => $shopName]);
        json_success('店铺信息更新成功');
    }

    /**
     * 保存密码
     */
    public function savePassword()
    {
        $merchantId = $this->getMerchantId();
        $oldPassword = input('old_password', '');
        $newPassword = input('new_password', '');
        $confirmPassword = input('confirm_password', '');

        if (!$oldPassword || !$newPassword || !$confirmPassword) {
            json_error('请填写完整信息');
        }
        if ($newPassword !== $confirmPassword) {
            json_error('两次输入密码不一致');
        }
        if (strlen($newPassword) < 6) {
            json_error('密码长度不能少于6位');
        }

        $merchant = Db::fetch("SELECT password FROM jz_merchant WHERE id = ?", [$merchantId]);
        if (!$merchant || !password_verify($oldPassword, $merchant['password'])) {
            json_error('原密码错误');
        }

        $data = [
            'password' => password_hash_custom($newPassword),
            'update_time' => date('Y-m-d H:i:s'),
        ];
        Db::update('jz_merchant', $data, 'id = ?', [$merchantId]);
        admin_log('merchant_password_update', ['merchant_id' => $merchantId]);
        json_success('密码修改成功，请重新登录', ['redirect' => url('login?type=merchant')]);
    }
}
