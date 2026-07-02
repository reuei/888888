<?php
/**
 * 第三方登录 OAuth 控制器
 */
class Oauth extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/main');
        $this->disableLayout();
    }

    /**
     * 跳转到第三方授权页
     */
    public function redirect()
    {
        $type = input('type', '');
        if (!in_array($type, ['qq', 'weixin', 'github'], true)) {
            throw new Exception('不支持的登录方式');
        }

        $config = oauth_config($type);
        if (empty($config['enabled']) || empty($config['appid'])) {
            throw new Exception('该登录方式未启用或未配置');
        }

        $url = oauth_authorize_url($type);
        if (!$url) {
            throw new Exception('生成授权链接失败');
        }

        redirect($url);
    }

    /**
     * 第三方回调处理
     */
    public function callback()
    {
        $type = input('type', '');
        $code = input('code', '');
        $state = input('state', '');

        if (!in_array($type, ['qq', 'weixin', 'github'], true)) {
            throw new Exception('不支持的登录方式');
        }

        if (!$code) {
            throw new Exception('授权失败，未获取到授权码');
        }

        if (!oauth_verify_state($type, $state)) {
            throw new Exception('授权状态校验失败');
        }

        $tokenResult = oauth_get_token($type, $code);
        if ($tokenResult['code'] !== 0) {
            throw new Exception($tokenResult['msg']);
        }

        $token = $tokenResult['data']['access_token'];
        $openid = $tokenResult['data']['openid'] ?? '';

        // GitHub 需通过 token 获取用户信息中的 id 作为 openid
        if ($type === 'github') {
            $openid = '';
        }

        $userinfoResult = oauth_get_userinfo($type, $token, $openid);
        if ($userinfoResult['code'] !== 0) {
            throw new Exception($userinfoResult['msg']);
        }

        $oauthUser = $userinfoResult['data'];
        if ($type === 'github' && !empty($tokenResult['data']['openid_from_userinfo'])) {
            // 已在 oauth_get_userinfo 中处理
        }

        $loginResult = oauth_login_or_register($type, $oauthUser);
        if ($loginResult['code'] !== 0) {
            throw new Exception($loginResult['msg']);
        }

        redirect(url('index/user'));
    }
}
