<?php
namespace Admin\Controller;

use Framework\Controller;
use Framework\Session;
use Framework\Database\Database;
use Framework\Response;

class BaseAdminController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!Session::has('admin_id')) {
            if (!($this instanceof AuthController)) {
                Response::redirect('/admin/login');
            }
        }
    }

    protected function admin()
    {
        return [
            'id' => Session::get('admin_id', 0),
            'username' => Session::get('admin_name', 'admin'),
        ];
    }
}
