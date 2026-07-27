<?php
namespace app\controller;

class User extends BaseController
{
    public function login()
    {
        if ($this->getUser()) {
            header('Location: /user/dashboard');
            exit;
        }
        
        return $this->render('user/login');
    }
    
    public function dologin()
    {
        $username = $this->post('username');
        $password = $this->post('password');
        
        if (!$username || !$password) {
            return $this->error('请输入用户名和密码');
        }
        
        $stmt = $this->db->prepare("SELECT * FROM qf_users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($password, $user['password'])) {
            return $this->error('用户名或密码错误');
        }
        
        if ($user['status'] != 1) {
            return $this->error('账户已被禁用');
        }
        
        $_SESSION['user_id'] = $user['id'];
        
        return $this->success(['redirect' => '/user/dashboard'], '登录成功');
    }
    
    public function register()
    {
        if ($this->getUser()) {
            header('Location: /user/dashboard');
            exit;
        }
        
        return $this->render('user/register');
    }
    
    public function doregister()
    {
        $username = $this->post('username');
        $email = $this->post('email');
        $password = $this->post('password');
        $confirmPassword = $this->post('confirm_password');
        
        if (!$username || !$email || !$password || !$confirmPassword) {
            return $this->error('请填写所有必填项');
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            return $this->error('用户名格式不正确，只能包含字母、数字和下划线，长度3-20位');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('邮箱格式不正确');
        }
        
        if (strlen($password) < 6) {
            return $this->error('密码长度至少6位');
        }
        
        if ($password != $confirmPassword) {
            return $this->error('两次密码输入不一致');
        }
        
        $stmt = $this->db->prepare("SELECT id FROM qf_users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            return $this->error('用户名或邮箱已被注册');
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("INSERT INTO qf_users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword]);
        
        $_SESSION['user_id'] = $this->db->lastInsertId();
        
        return $this->success(['redirect' => '/user/dashboard'], '注册成功');
    }
    
    public function dashboard()
    {
        $this->checkLogin();
        $user = $this->getUser();
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_licenses WHERE user_id = ? AND status = 1");
        $stmt->execute([$user['id']]);
        $licenseCount = $stmt->fetchColumn();
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_orders WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $orderCount = $stmt->fetchColumn();
        
        $stmt = $this->db->prepare("SELECT l.*, p.name as product_name FROM qf_licenses l 
            LEFT JOIN qf_products p ON l.product_id = p.id 
            WHERE l.user_id = ? AND l.status = 1 ORDER BY l.created_at DESC LIMIT 5");
        $stmt->execute([$user['id']]);
        $recentLicenses = $stmt->fetchAll();
        
        return $this->render('user/dashboard', [
            'user' => $user,
            'licenseCount' => $licenseCount,
            'orderCount' => $orderCount,
            'recentLicenses' => $recentLicenses
        ]);
    }
    
    public function workplace()
    {
        $this->checkLogin();
        $user = $this->getUser();
        
        $stmt = $this->db->prepare("SELECT l.*, p.name as product_name FROM qf_licenses l 
            LEFT JOIN qf_products p ON l.product_id = p.id 
            WHERE l.user_id = ? ORDER BY l.created_at DESC");
        $stmt->execute([$user['id']]);
        $licenses = $stmt->fetchAll();
        
        $stmt = $this->db->prepare("SELECT * FROM qf_orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
        $stmt->execute([$user['id']]);
        $orders = $stmt->fetchAll();
        
        return $this->render('user/workplace', [
            'user' => $user,
            'licenses' => $licenses,
            'orders' => $orders
        ]);
    }
    
    public function products()
    {
        $this->checkLogin();
        $user = $this->getUser();
        
        $stmt = $this->db->query("SELECT * FROM qf_products WHERE status = 1 ORDER BY sort ASC");
        $products = $stmt->fetchAll();
        
        return $this->render('user/products', [
            'user' => $user,
            'products' => $products
        ]);
    }
    
    public function myProducts()
    {
        $this->checkLogin();
        $user = $this->getUser();
        
        $stmt = $this->db->prepare("SELECT DISTINCT p.*, COUNT(l.id) as license_count FROM qf_products p 
            LEFT JOIN qf_licenses l ON p.id = l.product_id AND l.user_id = ? AND l.status = 1 
            WHERE EXISTS (SELECT 1 FROM qf_licenses WHERE product_id = p.id AND user_id = ?)
            GROUP BY p.id ORDER BY p.sort ASC");
        $stmt->execute([$user['id'], $user['id']]);
        $myProducts = $stmt->fetchAll();
        
        return $this->render('user/my-products', [
            'user' => $user,
            'myProducts' => $myProducts
        ]);
    }
    
    public function balance()
    {
        $this->checkLogin();
        $user = $this->getUser();
        
        return $this->render('user/balance', [
            'user' => $user
        ]);
    }
    
    public function settings()
    {
        $this->checkLogin();
        $user = $this->getUser();
        
        return $this->render('user/settings', [
            'user' => $user
        ]);
    }
    
    public function updateSettings()
    {
        $this->checkLogin();
        
        $email = $this->post('email');
        $qq = $this->post('qq');
        $phone = $this->post('phone');
        $oldPassword = $this->post('old_password');
        $newPassword = $this->post('new_password');
        $confirmPassword = $this->post('confirm_password');
        
        $user = $this->getUser();
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('邮箱格式不正确');
        }
        
        $stmt = $this->db->prepare("SELECT id FROM qf_users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user['id']]);
        if ($stmt->fetch()) {
            return $this->error('该邮箱已被其他账户使用');
        }
        
        if ($oldPassword) {
            if (!password_verify($oldPassword, $user['password'])) {
                return $this->error('原密码错误');
            }
            if (strlen($newPassword) < 6) {
                return $this->error('新密码长度至少6位');
            }
            if ($newPassword != $confirmPassword) {
                return $this->error('两次密码输入不一致');
            }
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE qf_users SET email = ?, qq = ?, phone = ?, password = ? WHERE id = ?");
            $stmt->execute([$email, $qq, $phone, $hashedPassword, $user['id']]);
        } else {
            $stmt = $this->db->prepare("UPDATE qf_users SET email = ?, qq = ?, phone = ? WHERE id = ?");
            $stmt->execute([$email, $qq, $phone, $user['id']]);
        }
        
        return $this->success(['redirect' => '/user/settings'], '设置更新成功');
    }
    
    public function feedback()
    {
        $this->checkLogin();
        $user = $this->getUser();
        
        $stmt = $this->db->prepare("SELECT * FROM qf_feedbacks WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user['id']]);
        $feedbacks = $stmt->fetchAll();
        
        return $this->render('user/feedback', [
            'user' => $user,
            'feedbacks' => $feedbacks
        ]);
    }
    
    public function submitFeedback()
    {
        $this->checkLogin();
        $user = $this->getUser();
        
        $content = $this->post('content');
        
        if (!$content || strlen(trim($content)) < 10) {
            return $this->error('反馈内容至少需要10个字符');
        }
        
        $stmt = $this->db->prepare("INSERT INTO qf_feedbacks (user_id, content) VALUES (?, ?)");
        $stmt->execute([$user['id'], trim($content)]);
        
        return $this->success(['redirect' => '/user/feedback'], '反馈提交成功');
    }
    
    public function logout()
    {
        session_destroy();
        header('Location: /');
        exit;
    }
}
