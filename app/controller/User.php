<?php
namespace app\controller;

/**
 * 用户控制器 - 简化版
 */
class User extends BaseController
{
    /**
     * 登录页面
     */
    public function login()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /user/dashboard');
            exit;
        }
        return $this->render('user/login');
    }

    /**
     * 处理登录
     */
    public function dologin()
    {
        $username = $this->post('username', '');
        $password = $this->post('password', '');

        if (empty($username) || empty($password)) {
            return $this->error('请填写完整信息');
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $this->recordLoginLog(null, $username, 0, '用户不存在');
                return $this->error('用户不存在');
            }

            if ($user['status'] != 1) {
                $this->recordLoginLog($user['id'], $username, 0, '账户已被禁用');
                return $this->error('账户已被禁用');
            }

            if (!password_verify($password, $user['password'])) {
                $this->recordLoginLog($user['id'], $username, 0, '密码错误');
                return $this->error('密码错误');
            }

            // 更新登录信息
            $updateStmt = $this->db->prepare("UPDATE qf_users SET login_ip = ?, login_time = ? WHERE id = ?");
            $updateStmt->execute([
                $_SERVER['REMOTE_ADDR'] ?? '',
                date('Y-m-d H:i:s'),
                $user['id']
            ]);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            $this->recordLoginLog($user['id'], $username, 1, '登录成功');

            return $this->success(['redirect' => '/user/dashboard'], '登录成功');
        } catch (\PDOException $e) {
            return $this->error('系统错误: ' . $e->getMessage());
        }
    }

    /**
     * 注册页面
     */
    public function register()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /user/dashboard');
            exit;
        }
        return $this->render('user/register');
    }

    /**
     * 处理注册
     */
    public function doregister()
    {
        $username = $this->post('username', '');
        $email = $this->post('email', '');
        $password = $this->post('password', '');

        if (empty($username) || empty($email) || empty($password)) {
            return $this->error('请填写完整信息');
        }

        if (strlen($username) < 3 || strlen($username) > 20) {
            return $this->error('用户名长度应在3-20个字符之间');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('邮箱格式不正确');
        }

        if (strlen($password) < 6) {
            return $this->error('密码长度至少6位');
        }

        try {
            // 检查用户名是否存在
            $stmt = $this->db->prepare("SELECT id FROM qf_users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                return $this->error('用户名已存在');
            }

            // 检查邮箱是否存在
            $stmt = $this->db->prepare("SELECT id FROM qf_users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return $this->error('邮箱已被注册');
            }

            // 创建用户
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO qf_users (username, email, password, created_at) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, date('Y-m-d H:i:s')]);

            return $this->success(['redirect' => '/login'], '注册成功，请登录');
        } catch (\PDOException $e) {
            return $this->error('注册失败: ' . $e->getMessage());
        }
    }

    /**
     * 用户中心首页
     */
    public function dashboard()
    {
        $this->checkLogin();

        $userId = $_SESSION['user_id'];

        // 获取用户信息
        $stmt = $this->db->prepare("SELECT * FROM qf_users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 获取统计数据
        $stats = [
            'balance' => $user['balance'] ?? 0,
            'products' => 0,
            'orders' => 0,
            'login_count' => 0
        ];

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_licenses WHERE user_id = ?");
            $stmt->execute([$userId]);
            $stats['products'] = $stmt->fetchColumn();

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_orders WHERE user_id = ?");
            $stmt->execute([$userId]);
            $stats['orders'] = $stmt->fetchColumn();

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_login_logs WHERE user_id = ? AND status = 1");
            $stmt->execute([$userId]);
            $stats['login_count'] = $stmt->fetchColumn();
        } catch (\PDOException $e) {
            // 表可能不存在，忽略错误
        }

        return $this->render('user/dashboard', ['user' => $user, 'stats' => $stats]);
    }

    /**
     * 工作台
     */
    public function workplace()
    {
        $this->checkLogin();

        $userId = $_SESSION['user_id'];

        // 获取用户信息
        $stmt = $this->db->prepare("SELECT * FROM qf_users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 获取最近授权
        $licenses = [];
        try {
            $stmt = $this->db->prepare("SELECT l.*, p.name as product_name FROM qf_licenses l LEFT JOIN qf_products p ON l.product_id = p.id WHERE l.user_id = ? ORDER BY l.created_at DESC LIMIT 5");
            $stmt->execute([$userId]);
            $licenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // 表可能不存在
        }

        // 获取最近订单
        $orders = [];
        try {
            $stmt = $this->db->prepare("SELECT o.*, p.name as product_name FROM qf_orders o LEFT JOIN qf_products p ON o.product_id = p.id WHERE o.user_id = ? ORDER BY o.created_at DESC LIMIT 5");
            $stmt->execute([$userId]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // 表可能不存在
        }

        return $this->render('user/workplace', ['user' => $user, 'licenses' => $licenses, 'orders' => $orders]);
    }

    /**
     * 产品列表
     */
    public function products()
    {
        $this->checkLogin();

        $page = intval($this->get('page', 1));
        $pageSize = 12;
        $offset = ($page - 1) * $pageSize;

        $products = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_products WHERE status = 1 ORDER BY sort DESC LIMIT ?, ?");
            $stmt->execute([$offset, $pageSize]);
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total = $this->db->query("SELECT COUNT(*) FROM qf_products WHERE status = 1")->fetchColumn();
        } catch (\PDOException $e) {
            // 表可能不存在
        }

        $totalPages = ceil($total / $pageSize);

        return $this->render('user/products', ['products' => $products, 'page' => $page, 'totalPages' => $totalPages]);
    }

    /**
     * 我的产品
     */
    public function myProducts()
    {
        $this->checkLogin();

        $userId = $_SESSION['user_id'];
        $page = intval($this->get('page', 1));
        $pageSize = 10;
        $offset = ($page - 1) * $pageSize;

        $licenses = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("SELECT l.*, p.name as product_name FROM qf_licenses l LEFT JOIN qf_products p ON l.product_id = p.id WHERE l.user_id = ? ORDER BY l.created_at DESC LIMIT ?, ?");
            $stmt->execute([$userId, $offset, $pageSize]);
            $licenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_licenses WHERE user_id = ?");
            $stmt->execute([$userId]);
            $total = $stmt->fetchColumn();
        } catch (\PDOException $e) {
            // 表可能不存在
        }

        $totalPages = ceil($total / $pageSize);

        return $this->render('user/my-products', ['licenses' => $licenses, 'page' => $page, 'totalPages' => $totalPages]);
    }

    /**
     * 余额管理
     */
    public function balance()
    {
        $this->checkLogin();

        $userId = $_SESSION['user_id'];

        // 获取用户信息
        $stmt = $this->db->prepare("SELECT * FROM qf_users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $page = intval($this->get('page', 1));
        $pageSize = 10;
        $offset = ($page - 1) * $pageSize;

        $logs = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_balance_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?, ?");
            $stmt->execute([$userId, $offset, $pageSize]);
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_balance_logs WHERE user_id = ?");
            $stmt->execute([$userId]);
            $total = $stmt->fetchColumn();
        } catch (\PDOException $e) {
            // 表可能不存在
        }

        $totalPages = ceil($total / $pageSize);

        return $this->render('user/balance', ['user' => $user, 'logs' => $logs, 'page' => $page, 'totalPages' => $totalPages]);
    }

    /**
     * 账户设置
     */
    public function settings()
    {
        $this->checkLogin();

        $userId = $_SESSION['user_id'];

        $stmt = $this->db->prepare("SELECT * FROM qf_users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->render('user/settings', ['user' => $user]);
    }

    /**
     * 更新设置
     */
    public function updateSettings()
    {
        $this->checkLogin();

        $userId = $_SESSION['user_id'];

        $email = $this->post('email', '');
        $qq = $this->post('qq', '');
        $phone = $this->post('phone', '');
        $oldPassword = $this->post('old_password', '');
        $newPassword = $this->post('password', '');

        if (empty($email)) {
            return $this->error('邮箱不能为空');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('邮箱格式不正确');
        }

        try {
            // 获取用户信息
            $stmt = $this->db->prepare("SELECT * FROM qf_users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // 如果要修改密码
            if (!empty($newPassword)) {
                if (empty($oldPassword)) {
                    return $this->error('请输入原密码');
                }

                if (!password_verify($oldPassword, $user['password'])) {
                    return $this->error('原密码错误');
                }

                if (strlen($newPassword) < 6) {
                    return $this->error('新密码长度至少6位');
                }

                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $this->db->prepare("UPDATE qf_users SET email = ?, qq = ?, phone = ?, password = ? WHERE id = ?");
                $stmt->execute([$email, $qq, $phone, $hashedPassword, $userId]);
            } else {
                $stmt = $this->db->prepare("UPDATE qf_users SET email = ?, qq = ?, phone = ? WHERE id = ?");
                $stmt->execute([$email, $qq, $phone, $userId]);
            }

            $_SESSION['email'] = $email;

            $this->recordOperationLog('更新设置', '修改账户信息');

            return $this->success([], '更新成功');
        } catch (\PDOException $e) {
            return $this->error('更新失败: ' . $e->getMessage());
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        session_destroy();
        header('Location: /login');
        exit;
    }

    /**
     * 记录登录日志
     */
    private function recordLoginLog($userId, $username, $status, $message)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO qf_login_logs (user_id, username, ip, user_agent, status, message, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $userId,
                $username,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $status,
                $message,
                date('Y-m-d H:i:s')
            ]);
        } catch (\PDOException $e) {
            // 忽略错误
        }
    }

    /**
     * 记录操作日志
     */
    private function recordOperationLog($action, $description)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO qf_operation_logs (user_id, action, description, ip, user_agent, created_at) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'] ?? null,
                $action,
                $description,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                date('Y-m-d H:i:s')
            ]);
        } catch (\PDOException $e) {
            // 忽略错误
        }
    }
}