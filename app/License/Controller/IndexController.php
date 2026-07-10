<?php
namespace License\Controller;

use Framework\Controller;

class IndexController extends Controller
{
    protected $layout = 'license';

    public function index($request, $params = [])
    {
        $stats = [
            'total_licenses' => 5280,
            'active_licenses' => 4120,
            'expired_licenses' => 1160,
            'total_domains' => 8960,
        ];
        $this->assign('stats', $stats);
        $this->assign('pageTitle', '玄武授权站');
        $this->assign('activeMenu', 'home');
        $this->view('license.index');
    }
}
