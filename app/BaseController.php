<?php
namespace app;

use think\facade\View;
use think\facade\Session;
use think\facade\Db;

class BaseController
{
    protected $middleware = [];

    public function __construct()
    {
        $this->initialize();
    }

    protected function initialize()
    {
        $siteName = config('app.app_name');
        $siteVersion = config('app.app_version');
        View::assign('site_name', $siteName);
        View::assign('site_version', $siteVersion);
        
        if ($this->isLogin()) {
            View::assign('userInfo', $this->getUserInfo());
        } else {
            View::assign('userInfo', null);
        }
    }

    protected function assign($name, $value = '')
    {
        View::assign($name, $value);
    }

    protected function fetch($template = '', $vars = [])
    {
        return View::fetch($template, $vars);
    }

    protected function jsonSuccess($msg = 'success', $data = null, $code = 0)
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    protected function jsonError($msg = 'error', $code = 1, $data = null)
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }

    protected function isLogin()
    {
        return Session::has('user_id');
    }

    protected function getUserId()
    {
        return Session::get('user_id');
    }

    protected function getUserInfo()
    {
        $userId = $this->getUserId();
        if (!$userId) return null;
        return [
            'id' => $userId,
            'username' => Session::get('username', '用户'),
            'email' => Session::get('email', ''),
            'avatar' => Session::get('avatar', ''),
            'balance' => Session::get('balance', 0),
        ];
    }

    protected function isAdminLogin()
    {
        return Session::has('admin_id');
    }

    protected function getAdminId()
    {
        return Session::get('admin_id');
    }
}
