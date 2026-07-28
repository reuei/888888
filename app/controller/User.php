<?php
namespace app\controller;

/**
 * 用户控制器
 */
class User extends BaseController
{
    /**
     * 登录页面
     */
    public function login()
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        $siteSettings = $this->getSiteSettings();
        return $this->render('user/login', ['siteSettings' => $siteSettings]);
    }

    /**
     * 处理登录
     */
    public function dologin()
    {
        $username = $this->post('username', '');
        $password = $this->post('password', '');

        if (empty($username) || empty($password)) {
            echo $this->error('请填写完整信息');
            exit;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                $this->recordLoginLog(null, $username, 0, '用户不存在');
                echo $this->error('用户不存在');
                exit;
            }

            if ($user['status'] != 1) {
                $this->recordLoginLog($user['id'], $username, 0, '账户已被禁用');
                echo $this->error('账户已被禁用');
                exit;
            }

            if (!password_verify($password, $user['password'])) {
                $this->recordLoginLog($user['id'], $username, 0, '密码错误');
                echo $this->error('密码错误');
                exit;
            }

            $updateStmt = $this->db->prepare("UPDATE qf_users SET login_ip = ?, login_time = ? WHERE id = ?");
            $updateStmt->execute([
                $_SERVER['REMOTE_ADDR'] ?? '',
                date('Y-m-d H:i:s'),
                $user['id'],
            ]);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            $this->recordLoginLog($user['id'], $username, 1, '登录成功');

            echo $this->success(['redirect' => '/dashboard'], '登录成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('系统错误: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 注册页面
     */
    public function register()
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        $siteSettings = $this->getSiteSettings();
        return $this->render('user/register', ['siteSettings' => $siteSettings]);
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
            echo $this->error('请填写完整信息');
            exit;
        }

        if (strlen($username) < 3 || strlen($username) > 20) {
            echo $this->error('用户名长度应在3-20个字符之间');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo $this->error('邮箱格式不正确');
            exit;
        }

        if (strlen($password) < 6) {
            echo $this->error('密码长度至少6位');
            exit;
        }

        try {
            $stmt = $this->db->prepare("SELECT id FROM qf_users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                echo $this->error('用户名已存在');
                exit;
            }

            $stmt = $this->db->prepare("SELECT id FROM qf_users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                echo $this->error('邮箱已被注册');
                exit;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO qf_users (username, email, password, created_at) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, date('Y-m-d H:i:s')]);

            echo $this->success(['redirect' => '/login'], '注册成功，请登录');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('注册失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 用户中心首页
     */
    public function dashboard()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $user = $this->getUser();

        $stats = [
            'balance' => $user['balance'] ?? 0,
            'products' => 0,
            'orders' => 0,
            'login_count' => 0,
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
        }

        return $this->renderUser('user/dashboard', [
            'user' => $user,
            'stats' => $stats,
            'title' => '用户中心',
        ], 'dashboard', '用户中心');
    }

    /**
     * 工作台
     */
    public function workplace()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $user = $this->getUser();

        $licenses = [];
        try {
            $stmt = $this->db->prepare("
                SELECT l.*, p.name AS product_name
                FROM qf_licenses l
                LEFT JOIN qf_products p ON l.product_id = p.id
                WHERE l.user_id = ?
                ORDER BY l.created_at DESC LIMIT 5
            ");
            $stmt->execute([$userId]);
            $licenses = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
        }

        $orders = [];
        try {
            $stmt = $this->db->prepare("
                SELECT o.*, p.name AS product_name
                FROM qf_orders o
                LEFT JOIN qf_products p ON o.product_id = p.id
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC LIMIT 5
            ");
            $stmt->execute([$userId]);
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
        }

        return $this->renderUser('user/workplace', [
            'user' => $user,
            'licenses' => $licenses,
            'orders' => $orders,
        ], 'workplace', '工作台');
    }

    /**
     * 产品列表
     */
    public function products()
    {
        $this->requireLogin();
        $user = $this->getUser();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 12;
        $offset = ($page - 1) * $pageSize;

        $products = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_products WHERE status = 1 ORDER BY sort DESC LIMIT ?, ?");
            $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(2, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $total = $this->db->query("SELECT COUNT(*) FROM qf_products WHERE status = 1")->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderUser('user/products', [
            'user' => $user,
            'products' => $products,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'products', '产品中心');
    }

    /**
     * 我的产品
     */
    public function myProducts()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];
        $user = $this->getUser();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 10;
        $offset = ($page - 1) * $pageSize;

        $orders = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("
                SELECT o.*, p.name AS product_name, p.download_file
                FROM qf_orders o
                LEFT JOIN qf_products p ON o.product_id = p.id
                WHERE o.user_id = ? AND o.status = 1
                ORDER BY o.created_at DESC
                LIMIT ?, ?
            ");
            $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(3, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_orders WHERE user_id = ? AND status = 1");
            $stmt->execute([$userId]);
            $total = $stmt->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderUser('user/my-products', [
            'user' => $user,
            'orders' => $orders,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'myProducts', '我的产品');
    }

    /**
     * 我的订单
     */
    public function orders()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];
        $user = $this->getUser();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 10;
        $offset = ($page - 1) * $pageSize;

        $orders = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("
                SELECT o.*, p.name AS product_name, p.download_file
                FROM qf_orders o
                LEFT JOIN qf_products p ON o.product_id = p.id
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC
                LIMIT ?, ?
            ");
            $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(3, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_orders WHERE user_id = ?");
            $stmt->execute([$userId]);
            $total = $stmt->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderUser('user/orders', [
            'user' => $user,
            'orders' => $orders,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'orders', '我的订单');
    }

    /**
     * 余额管理
     */
    public function balance()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];
        $user = $this->getUser();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 10;
        $offset = ($page - 1) * $pageSize;

        $logs = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_balance_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?, ?");
            $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(3, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_balance_logs WHERE user_id = ?");
            $stmt->execute([$userId]);
            $total = $stmt->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderUser('user/balance', [
            'user' => $user,
            'logs' => $logs,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'balance', '余额管理');
    }

    /**
     * 操作日志
     */
    public function logs()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];
        $user = $this->getUser();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;

        $logs = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_balance_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?, ?");
            $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(3, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_balance_logs WHERE user_id = ?");
            $stmt->execute([$userId]);
            $total = $stmt->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderUser('user/logs', [
            'user' => $user,
            'logs' => $logs,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'logs', '操作日志');
    }

    /**
     * 账户设置
     */
    public function settings()
    {
        $this->requireLogin();
        $user = $this->getUser();

        return $this->renderUser('user/settings', [
            'user' => $user,
        ], 'settings', '账户设置');
    }

    /**
     * 更新设置
     */
    public function updateSettings()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $email = $this->post('email', '');
        $qq = $this->post('qq', '');
        $phone = $this->post('phone', '');

        if (empty($email)) {
            echo $this->error('邮箱不能为空');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo $this->error('邮箱格式不正确');
            exit;
        }

        try {
            $stmt = $this->db->prepare("UPDATE qf_users SET email = ?, qq = ?, phone = ? WHERE id = ?");
            $stmt->execute([$email, $qq, $phone, $userId]);

            $_SESSION['email'] = $email;

            echo $this->success([], '更新成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('更新失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 修改密码
     */
    public function updatePassword()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $oldPassword = $this->post('old_password', '');
        $newPassword = $this->post('password', '');

        if (empty($oldPassword) || empty($newPassword)) {
            echo $this->error('请填写完整信息');
            exit;
        }

        if (strlen($newPassword) < 6) {
            echo $this->error('新密码长度至少6位');
            exit;
        }

        try {
            $stmt = $this->db->prepare("SELECT password FROM qf_users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!password_verify($oldPassword, $user['password'])) {
                echo $this->error('原密码错误');
                exit;
            }

            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("UPDATE qf_users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $userId]);

            echo $this->success([], '密码修改成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('修改失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 意见反馈页面
     */
    public function feedback()
    {
        $this->requireLogin();
        $user = $this->getUser();

        return $this->renderUser('user/feedback', [
            'user' => $user,
        ], 'feedback', '意见反馈');
    }

    /**
     * 提交反馈
     */
    public function submitFeedback()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $content = $this->post('content', '');
        $contact = $this->post('contact', '');

        if (empty($content)) {
            echo $this->error('请填写反馈内容');
            exit;
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO qf_feedback (user_id, content, contact, created_at) VALUES (?, ?, ?, ?)");
            $stmt->execute([$userId, $content, $contact, date('Y-m-d H:i:s')]);

            echo $this->success([], '反馈提交成功，感谢您的意见');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('提交失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 购买产品
     */
    public function buyProduct()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $productId = intval($this->post('product_id', 0));

        if ($productId <= 0) {
            echo $this->error('产品参数错误');
            exit;
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT * FROM qf_products WHERE id = ? AND status = 1");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$product) {
                $this->db->rollBack();
                echo $this->error('产品不存在或已下架');
                exit;
            }

            $stmt = $this->db->prepare("SELECT balance FROM qf_users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($user['balance'] < $product['price']) {
                $this->db->rollBack();
                echo $this->error('余额不足');
                exit;
            }

            $newBalance = $user['balance'] - $product['price'];
            $stmt = $this->db->prepare("UPDATE qf_users SET balance = ? WHERE id = ?");
            $stmt->execute([$newBalance, $userId]);

            $orderNo = date('YmdHis') . mt_rand(1000, 9999);
            $stmt = $this->db->prepare("
                INSERT INTO qf_orders (order_no, user_id, product_id, amount, payment_method, payment_status, status, created_at)
                VALUES (?, ?, ?, ?, 'balance', 1, 1, ?)
            ");
            $stmt->execute([$orderNo, $userId, $productId, $product['price'], date('Y-m-d H:i:s')]);
            $orderId = $this->db->lastInsertId();

            $licenseKey = $this->generateLicenseKey();
            $expiresAt = null;
            if ($product['duration'] > 0) {
                $expiresAt = date('Y-m-d H:i:s', strtotime("+{$product['duration']} days"));
            }
            $stmt = $this->db->prepare("
                INSERT INTO qf_licenses (license_key, user_id, product_id, order_id, expires_at, status, created_at)
                VALUES (?, ?, ?, ?, ?, 1, ?)
            ");
            $stmt->execute([$licenseKey, $userId, $productId, $orderId, $expiresAt, date('Y-m-d H:i:s')]);

            $stmt = $this->db->prepare("
                INSERT INTO qf_balance_logs (user_id, type, amount, balance_before, balance_after, description, created_at)
                VALUES (?, 'consume', ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $product['price'],
                $user['balance'],
                $newBalance,
                "购买产品：{$product['name']}",
                date('Y-m-d H:i:s'),
            ]);

            $this->db->commit();

            echo $this->success(['redirect' => '/user/my-products'], '购买成功');
            exit;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            echo $this->error('购买失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 下载产品文件
     */
    public function download()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $orderId = intval($this->get('order_id', 0));

        if ($orderId <= 0) {
            echo '订单参数错误';
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT o.*, p.name AS product_name, p.download_file
                FROM qf_orders o
                LEFT JOIN qf_products p ON o.product_id = p.id
                WHERE o.id = ? AND o.user_id = ? AND o.status = 1
            ");
            $stmt->execute([$orderId, $userId]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order) {
                echo '订单不存在或无权下载';
                exit;
            }

            if (empty($order['download_file'])) {
                echo '该产品暂无下载文件';
                exit;
            }

            $filePath = STORAGE_PATH . 'products/' . $order['download_file'];
            if (!file_exists($filePath)) {
                echo '文件不存在';
                exit;
            }

            $fileName = $order['product_name'] . '.zip';
            $fileSize = filesize($filePath);

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . rawurlencode($fileName) . '"');
            header('Content-Length: ' . $fileSize);
            header('Cache-Control: no-cache');
            readfile($filePath);
            exit;
        } catch (\PDOException $e) {
            echo '下载失败: ' . htmlspecialchars($e->getMessage());
            exit;
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }

    /**
     * 生成授权密钥
     */
    private function generateLicenseKey()
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $segments = [];
        for ($i = 0; $i < 4; $i++) {
            $seg = '';
            for ($j = 0; $j < 4; $j++) {
                $seg .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $segments[] = $seg;
        }
        return implode('-', $segments);
    }

    /**
     * 记录登录日志
     */
    private function recordLoginLog($userId, $username, $status, $message)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO qf_login_logs (user_id, username, ip, user_agent, status, message, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $username,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $status,
                $message,
                date('Y-m-d H:i:s'),
            ]);
        } catch (\PDOException $e) {
        }
    }
}