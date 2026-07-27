<?php
namespace app\controller;

class Admin extends BaseController
{
    public function login()
    {
        if ($this->getAdmin()) {
            header('Location: /admin/dashboard');
            exit;
        }
        
        return $this->render('admin/login');
    }
    
    public function dologin()
    {
        $username = $this->post('username');
        $password = $this->post('password');
        
        if (!$username || !$password) {
            return $this->error('请输入用户名和密码');
        }
        
        $stmt = $this->db->prepare("SELECT * FROM qf_admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if (!$admin || !password_verify($password, $admin['password'])) {
            return $this->error('用户名或密码错误');
        }
        
        if ($admin['status'] != 1) {
            return $this->error('账户已被禁用');
        }
        
        $_SESSION['admin_id'] = $admin['id'];
        
        return $this->success(['redirect' => '/admin/dashboard'], '登录成功');
    }
    
    public function dashboard()
    {
        $this->checkAdminLogin();
        $admin = $this->getAdmin();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM qf_users");
        $userCount = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM qf_products WHERE status = 1");
        $productCount = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM qf_licenses WHERE status = 1");
        $licenseCount = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT SUM(amount) FROM qf_orders WHERE status = 1");
        $revenue = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT u.* FROM qf_users ORDER BY created_at DESC LIMIT 5");
        $recentUsers = $stmt->fetchAll();
        
        $stmt = $this->db->query("SELECT o.*, p.name as product_name, u.username as user_name FROM qf_orders o 
            LEFT JOIN qf_products p ON o.product_id = p.id 
            LEFT JOIN qf_users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC LIMIT 5");
        $recentOrders = $stmt->fetchAll();
        
        return $this->render('admin/dashboard', [
            'admin' => $admin,
            'userCount' => $userCount,
            'productCount' => $productCount,
            'licenseCount' => $licenseCount,
            'revenue' => $revenue,
            'recentUsers' => $recentUsers,
            'recentOrders' => $recentOrders
        ]);
    }
    
    public function users()
    {
        $this->checkAdminLogin();
        $admin = $this->getAdmin();
        
        $page = $this->get('page', 1);
        $pageSize = 10;
        $offset = ($page - 1) * $pageSize;
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM qf_users");
        $total = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT * FROM qf_users ORDER BY created_at DESC LIMIT {$offset}, {$pageSize}");
        $users = $stmt->fetchAll();
        
        $totalPages = ceil($total / $pageSize);
        
        return $this->render('admin/users', [
            'admin' => $admin,
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }
    
    public function products()
    {
        $this->checkAdminLogin();
        $admin = $this->getAdmin();
        
        $page = $this->get('page', 1);
        $pageSize = 10;
        $offset = ($page - 1) * $pageSize;
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM qf_products");
        $total = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT * FROM qf_products ORDER BY sort ASC LIMIT {$offset}, {$pageSize}");
        $products = $stmt->fetchAll();
        
        $totalPages = ceil($total / $pageSize);
        
        return $this->render('admin/products', [
            'admin' => $admin,
            'products' => $products,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }
    
    public function licenses()
    {
        $this->checkAdminLogin();
        $admin = $this->getAdmin();
        
        $page = $this->get('page', 1);
        $pageSize = 10;
        $offset = ($page - 1) * $pageSize;
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM qf_licenses");
        $total = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT l.*, p.name as product_name, u.username as user_name FROM qf_licenses l 
            LEFT JOIN qf_products p ON l.product_id = p.id 
            LEFT JOIN qf_users u ON l.user_id = u.id 
            ORDER BY l.created_at DESC LIMIT {$offset}, {$pageSize}");
        $licenses = $stmt->fetchAll();
        
        $totalPages = ceil($total / $pageSize);
        
        return $this->render('admin/licenses', [
            'admin' => $admin,
            'licenses' => $licenses,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }
    
    public function orders()
    {
        $this->checkAdminLogin();
        $admin = $this->getAdmin();
        
        $page = $this->get('page', 1);
        $pageSize = 10;
        $offset = ($page - 1) * $pageSize;
        
        $stmt = $this->db->query("SELECT COUNT(*) FROM qf_orders");
        $total = $stmt->fetchColumn();
        
        $stmt = $this->db->query("SELECT o.*, p.name as product_name, u.username as user_name FROM qf_orders o 
            LEFT JOIN qf_products p ON o.product_id = p.id 
            LEFT JOIN qf_users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC LIMIT {$offset}, {$pageSize}");
        $orders = $stmt->fetchAll();
        
        $totalPages = ceil($total / $pageSize);
        
        return $this->render('admin/orders', [
            'admin' => $admin,
            'orders' => $orders,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }
    
    public function settings()
    {
        $this->checkAdminLogin();
        $admin = $this->getAdmin();
        
        $stmt = $this->db->query("SELECT * FROM qf_settings");
        $settings = $stmt->fetchAll();
        
        $settingsMap = [];
        foreach ($settings as $setting) {
            $settingsMap[$setting['key']] = $setting['value'];
        }
        
        return $this->render('admin/settings', [
            'admin' => $admin,
            'settings' => $settingsMap
        ]);
    }
    
    public function saveSettings()
    {
        $this->checkAdminLogin();
        
        $settings = $this->post();
        
        foreach ($settings as $key => $value) {
            $stmt = $this->db->prepare("SELECT id FROM qf_settings WHERE `key` = ?");
            $stmt->execute([$key]);
            if ($stmt->fetch()) {
                $stmt = $this->db->prepare("UPDATE qf_settings SET value = ? WHERE `key` = ?");
                $stmt->execute([$value, $key]);
            }
        }
        
        return $this->success(['redirect' => '/admin/settings'], '设置保存成功');
    }
    
    public function logout()
    {
        session_destroy();
        header('Location: /admin/login');
        exit;
    }
}
