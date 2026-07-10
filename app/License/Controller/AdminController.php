<?php
namespace License\Controller;

use Framework\Controller;
use Framework\Session;
use Framework\Response;

class AdminController extends Controller
{
    protected $layout = 'admin';

    public function login($request, $params = [])
    {
        if (Session::has('license_admin_id')) {
            return Response::redirect('/license/admin/dashboard');
        }
        $this->layout = false;
        $this->view('license.login');
    }

    public function doLogin($request, $params = [])
    {
        $username = trim($request->post('username', ''));
        $password = $request->post('password', '');
        if (empty($username) || empty($password)) {
            return $this->error('请填写完整');
        }
        if ($username === 'admin' && $password === 'license888') {
            Session::set('license_admin_id', 1);
            Session::set('license_admin_name', $username);
            return $this->success('登录成功', ['url' => '/license/admin/dashboard']);
        }
        return $this->error('账号或密码错误');
    }

    public function dashboard($request, $params = [])
    {
        $this->checkAuth();
        $stats = [
            'total' => 5280,
            'active' => 4120,
            'expired' => 1160,
            'today' => 23,
        ];
        $this->assign('stats', $stats);
        $this->assign('pageTitle', '仪表盘');
        $this->assign('activeMenu', 'dashboard');
        $this->view('license.dashboard');
    }

    public function licenses($request, $params = [])
    {
        $this->checkAuth();
        $list = $this->getLicenses();
        $this->assign('list', $list);
        $this->assign('pageTitle', '授权管理');
        $this->assign('activeMenu', 'licenses');
        $this->view('license.licenses');
    }

    public function domains($request, $params = [])
    {
        $this->checkAuth();
        $list = $this->getDomains();
        $this->assign('list', $list);
        $this->assign('pageTitle', '域名管理');
        $this->assign('activeMenu', 'domains');
        $this->view('license.domains');
    }

    public function logs($request, $params = [])
    {
        $this->checkAuth();
        $list = $this->getLogs();
        $this->assign('list', $list);
        $this->assign('pageTitle', '调用日志');
        $this->assign('activeMenu', 'logs');
        $this->view('license.logs');
    }

    public function logout($request, $params = [])
    {
        Session::delete('license_admin_id');
        Session::delete('license_admin_name');
        return Response::redirect('/license');
    }

    protected function checkAuth()
    {
        if (!Session::has('license_admin_id')) {
            Response::redirect('/license/admin');
        }
    }

    protected function getLicenses()
    {
        return [
            ['code' => 'XUANWU-DEMO-2026', 'product' => 'xuanwu_card', 'version' => '1.0.5', 'max_domains' => 5, 'expire_time' => '2099-12-31 23:59:59', 'status' => 1],
            ['code' => 'XW2026001ABCDE', 'product' => 'xuanwu_card', 'version' => '1.0.5', 'max_domains' => 3, 'expire_time' => '2027-06-30 23:59:59', 'status' => 1],
            ['code' => 'XW2026002FGHIJ', 'product' => 'xuanwu_card', 'version' => '1.0.5', 'max_domains' => 3, 'expire_time' => '2026-12-31 23:59:59', 'status' => 1],
            ['code' => 'XW2025050KLMNO', 'product' => 'xuanwu_card', 'version' => '1.0.4', 'max_domains' => 5, 'expire_time' => '2025-12-31 23:59:59', 'status' => 0],
        ];
    }

    protected function getDomains()
    {
        return [
            ['domain' => 'demo.xuanwu.com', 'license' => 'XUANWU-DEMO-2026', 'ip' => '127.0.0.1', 'create_time' => '2026-07-01 10:30:00'],
            ['domain' => 'shop.example.com', 'license' => 'XW2026001ABCDE', 'ip' => '192.168.1.1', 'create_time' => '2026-07-05 14:22:00'],
            ['domain' => 'mall.example.cn', 'license' => 'XW2026002FGHIJ', 'ip' => '10.0.0.5', 'create_time' => '2026-07-08 09:15:00'],
        ];
    }

    protected function getLogs()
    {
        return [
            ['license' => 'XUANWU-DEMO-2026', 'domain' => 'demo.xuanwu.com', 'action' => 'heartbeat', 'ip' => '127.0.0.1', 'status' => 1, 'create_time' => date('Y-m-d H:i:s')],
            ['license' => 'XW2026001ABCDE', 'domain' => 'shop.example.com', 'action' => 'verify', 'ip' => '192.168.1.1', 'status' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-1 hour'))],
            ['license' => 'XW2026002FGHIJ', 'domain' => 'mall.example.cn', 'action' => 'activate', 'ip' => '10.0.0.5', 'status' => 1, 'create_time' => date('Y-m-d H:i:s', strtotime('-2 hour'))],
        ];
    }
}
