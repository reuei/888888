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
     * 实名认证
     */
    public function auth()
    {
        $merchant = Db::fetch("SELECT real_name, id_card_no, id_card_front, id_card_back, auth_status, auth_remark, auth_time FROM jz_merchant WHERE id = ?", [$this->getMerchantId()]);

        $statusMap = [
            0 => ['text' => '未认证', 'class' => 'tag-orange'],
            1 => ['text' => '待审核', 'class' => 'tag-blue'],
            2 => ['text' => '已认证', 'class' => 'tag-green'],
            3 => ['text' => '已驳回', 'class' => 'tag-red'],
        ];
        $merchant['auth_status_text'] = $statusMap[$merchant['auth_status'] ?? 0]['text'] ?? '未知';
        $merchant['auth_status_class'] = $statusMap[$merchant['auth_status'] ?? 0]['class'] ?? 'tag-orange';

        $this->assign('title', '实名认证');
        $this->assign('merchant', $merchant);
        $this->fetch('merchant/setting/auth');
    }

    /**
     * 保存实名认证
     */
    public function saveAuth()
    {
        $merchantId = $this->getMerchantId();
        $merchant = Db::fetch("SELECT auth_status FROM jz_merchant WHERE id = ?", [$merchantId]);
        if (!$merchant) {
            json_error('商户不存在');
        }
        if ((int) $merchant['auth_status'] === 2) {
            json_error('实名认证已通过，无需重复提交');
        }
        if ((int) $merchant['auth_status'] === 1) {
            json_error('实名认证正在审核中，请勿重复提交');
        }

        $realName = trim(input('real_name', ''));
        $idCardNo = trim(input('id_card_no', ''));

        if (!$realName || mb_strlen($realName) < 2 || mb_strlen($realName) > 20) {
            json_error('真实姓名格式错误');
        }
        if (!$idCardNo || !preg_match('/^\d{17}[\dXx]$/', $idCardNo)) {
            json_error('身份证号格式错误');
        }

        // 上传身份证图片
        $front = !empty($_FILES['id_card_front']) && $_FILES['id_card_front']['error'] === UPLOAD_ERR_OK
            ? upload_file('id_card_front', 'merchant/auth')
            : ['code' => 1, 'msg' => '请上传身份证正面'];
        if ($front['code'] !== 0) {
            json_error('身份证正面：' . $front['msg']);
        }

        $back = !empty($_FILES['id_card_back']) && $_FILES['id_card_back']['error'] === UPLOAD_ERR_OK
            ? upload_file('id_card_back', 'merchant/auth')
            : ['code' => 1, 'msg' => '请上传身份证反面'];
        if ($back['code'] !== 0) {
            json_error('身份证反面：' . $back['msg']);
        }

        $data = [
            'real_name' => $realName,
            'id_card_no' => $idCardNo,
            'id_card_front' => $front['path'],
            'id_card_back' => $back['path'],
            'auth_status' => 1,
            'auth_remark' => '',
            'auth_time' => null,
            'update_time' => date('Y-m-d H:i:s'),
        ];

        Db::update('jz_merchant', $data, 'id = ?', [$merchantId]);
        admin_log('merchant_auth_submit', ['merchant_id' => $merchantId, 'real_name' => $realName]);
        json_success('实名认证资料已提交，请等待审核');
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
