<?php
namespace app\controller;

/**
 * 后台管理控制器 - 简化版
 */
class Admin extends BaseController
{
    /**
     * 后台登录
     */
    public function login()
    {
        if (isset($_SESSION['admin_id'])) {
            header('Location: /admin/dashboard');
            exit;
        }
        return $this->render('admin/login');
    }

    /**
     * 处理后台登录
     */
    public function dologin()
    {
        $username = $this->post('username', '');
        $password = $this->post('password', '');

        if (empty($username) || empty($password)) {
            return $this->error('请填写完整信息');
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$admin) {
                return $this->error('管理员不存在');
            }

            if ($admin['status'] != 1) {
                return $this->error('账户已被禁用');
            }

            if (!password_verify($password, $admin['password'])) {
                return $this->error('密码错误');
            }

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            return $this->success(['redirect' => '/admin/dashboard'], '登录成功');
        } catch (\PDOException $e) {
            return $this->error('系统错误: ' . $e->getMessage());
        }
    }

    /**
     * 后台首页
     */
    public function dashboard()
    {
        $this->checkAdminLogin();

        $stats = [
            'users' => 0,
            'products' => 0,
            'licenses' => 0,
            'orders' => 0,
            'revenue' => 0
        ];

        try {
            $stats['users'] = $this->db->query("SELECT COUNT(*) FROM qf_users")->fetchColumn();
            $stats['products'] = $this->db->query("SELECT COUNT(*) FROM qf_products")->fetchColumn();
            $stats['licenses'] = $this->db->query("SELECT COUNT(*) FROM qf_licenses")->fetchColumn();
            $stats['orders'] = $this->db->query("SELECT COUNT(*) FROM qf_orders")->fetchColumn();

            $stmt = $this->db->query("SELECT SUM(amount) FROM qf_orders WHERE payment_status = 1");
            $revenue = $stmt->fetchColumn();
            $stats['revenue'] = $revenue ?: 0;
        } catch (\PDOException $e) {
            // 表可能不存在
        }

        return $this->render('admin/dashboard', ['stats' => $stats]);
    }

    /**
     * 用户管理
     */
    public function users()
    {
        $this->checkAdminLogin();

        $page = intval($this->get('page', 1));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;

        $users = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_users ORDER BY created_at DESC LIMIT ?, ?");
            $stmt->execute([$offset, $pageSize]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total = $this->db->query("SELECT COUNT(*) FROM qf_users")->fetchColumn();
        } catch (\PDOException $e) {
            // 表可能不存在
        }

        $totalPages = ceil($total / $pageSize);

        return $this->render('admin/users', ['users' => $users, 'page' => $page, 'totalPages' => $totalPages]);
    }

    /**
     * 产品管理
     */
    public function products()
    {
        $this->checkAdminLogin();

        $page = intval($this->get('page', 1));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;

        $products = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_products ORDER BY sort DESC LIMIT ?, ?");
            $stmt->execute([$offset, $pageSize]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total = $this->db->query("SELECT COUNT(*) FROM qf_products")->fetchColumn();
        } catch (\PDOException $e) {
            // 表可能不存在
        }

        $totalPages = ceil($total / $pageSize);

        return $this->render('admin/products', ['products' => $products, 'page' => $page, 'totalPages' => $totalPages]);
    }

    /**
     * 系统设置
     */
    public function settings()
    {
        $this->checkAdminLogin();

        $settings = [];
        try {
            $stmt = $this->db->query("SELECT * FROM qf_settings");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as $item) {
                $settings[$item['key']] = $item['value'];
            }
        } catch (\PDOException $e) {
            // 表可能不存在
        }

        return $this->render('admin/settings', ['settings' => $settings]);
    }

    /**
     * 后台退出
     */
    public function logout()
    {
        session_destroy();
        header('Location: /admin/login');
        exit;
    }
}