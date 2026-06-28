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
     * 自定义支付
     */
    public function payment()
    {
        $fields = 'pay_type, pay_alipay_qr, pay_wechat_qr, pay_alipay_account, pay_wechat_account, pay_api_url, pay_api_key, pay_api_secret';
        $merchant = Db::fetch("SELECT {$fields} FROM jz_merchant WHERE id = ?", [$this->getMerchantId()]);

        $this->assign('title', '自定义支付');
        $this->assign('merchant', $merchant);
        $this->fetch('merchant/setting/payment');
    }

    /**
     * 保存自定义支付
     */
    public function savePayment()
    {
        $merchantId = $this->getMerchantId();
        $payType = (int) input('pay_type', 0);

        $data = [
            'pay_type' => in_array($payType, [0, 1, 2], true) ? $payType : 0,
            'update_time' => date('Y-m-d H:i:s'),
        ];

        if ($data['pay_type'] === 1) {
            $data['pay_alipay_account'] = trim(input('pay_alipay_account', ''));
            $data['pay_wechat_account'] = trim(input('pay_wechat_account', ''));

            // 上传收款二维码
            if (!empty($_FILES['pay_alipay_qr']) && $_FILES['pay_alipay_qr']['error'] === UPLOAD_ERR_OK) {
                $res = upload_file('pay_alipay_qr', 'merchant/payment');
                if ($res['code'] !== 0) {
                    json_error('支付宝二维码：' . $res['msg']);
                }
                $data['pay_alipay_qr'] = $res['path'];
            }
            if (!empty($_FILES['pay_wechat_qr']) && $_FILES['pay_wechat_qr']['error'] === UPLOAD_ERR_OK) {
                $res = upload_file('pay_wechat_qr', 'merchant/payment');
                if ($res['code'] !== 0) {
                    json_error('微信二维码：' . $res['msg']);
                }
                $data['pay_wechat_qr'] = $res['path'];
            }

            if (!$data['pay_alipay_qr'] && !$data['pay_wechat_qr']) {
                json_error('请至少上传一种个人收款二维码');
            }
        }

        if ($data['pay_type'] === 2) {
            $data['pay_api_url'] = trim(input('pay_api_url', ''));
            $data['pay_api_key'] = trim(input('pay_api_key', ''));
            $data['pay_api_secret'] = trim(input('pay_api_secret', ''));

            if (!$data['pay_api_url'] || !filter_var($data['pay_api_url'], FILTER_VALIDATE_URL)) {
                json_error('请输入有效的第三方接口地址');
            }
            if (!$data['pay_api_key']) {
                json_error('第三方接口 KEY 不能为空');
            }
        }

        Db::update('jz_merchant', $data, 'id = ?', [$merchantId]);
        admin_log('merchant_payment_update', ['merchant_id' => $merchantId, 'pay_type' => $data['pay_type']]);
        json_success('支付配置保存成功');
    }

    /**
     * 引导页
     */
    public function guide()
    {
        $fields = 'guide_status, guide_title, guide_content, guide_bg_image, guide_button_text, guide_button_link';
        $merchant = Db::fetch("SELECT {$fields} FROM jz_merchant WHERE id = ?", [$this->getMerchantId()]);

        $this->assign('title', '引导页');
        $this->assign('merchant', $merchant);
        $this->fetch('merchant/setting/guide');
    }

    /**
     * 保存引导页
     */
    public function saveGuide()
    {
        $merchantId = $this->getMerchantId();
        $guideStatus = (int) input('guide_status', 0);
        $guideTitle = trim(input('guide_title', ''));
        $guideContent = trim(input('guide_content', ''));
        $guideButtonText = trim(input('guide_button_text', '立即进入'));
        $guideButtonLink = trim(input('guide_button_link', ''));

        if ($guideStatus === 1) {
            if (!$guideTitle) {
                json_error('引导页标题不能为空');
            }
            if (!$guideContent) {
                json_error('引导页内容不能为空');
            }
            if (!$guideButtonText) {
                json_error('按钮文字不能为空');
            }
        }

        $data = [
            'guide_status' => $guideStatus ? 1 : 0,
            'guide_title' => $guideTitle,
            'guide_content' => $guideContent,
            'guide_button_text' => $guideButtonText,
            'guide_button_link' => $guideButtonLink,
            'update_time' => date('Y-m-d H:i:s'),
        ];

        if (!empty($_FILES['guide_bg_image']) && $_FILES['guide_bg_image']['error'] === UPLOAD_ERR_OK) {
            $res = upload_file('guide_bg_image', 'merchant/guide');
            if ($res['code'] !== 0) {
                json_error('背景图：' . $res['msg']);
            }
            $data['guide_bg_image'] = $res['path'];
        }

        Db::update('jz_merchant', $data, 'id = ?', [$merchantId]);
        admin_log('merchant_guide_update', ['merchant_id' => $merchantId, 'guide_status' => $data['guide_status']]);
        json_success('引导页配置保存成功');
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
