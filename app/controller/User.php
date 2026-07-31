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
            $stmt = $this->db->prepare("SELECT * FROM qf_users WHERE username = ? OR email = ? OR phone = ?");
            $stmt->execute([$username, $username, $username]);
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
                $this->getRealIp(),
                date('Y-m-d H:i:s'),
                $user['id'],
            ]);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            $this->recordOperationLog($user['id'], 'login', '用户登录成功，IP: ' . $this->getRealIp());

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
        $phone = $this->post('phone', '');
        $password = $this->post('password', '');

        if (empty($username) || empty($password)) {
            echo $this->error('请填写完整信息');
            exit;
        }

        // 检查后台是否要求必须邮箱注册
        $settings = $this->getSiteSettings();
        $requireEmail = ($settings['require_email_register'] ?? '1') === '1';
        if ($requireEmail && empty($email)) {
            echo $this->error('邮箱为必填项');
            exit;
        }

        if (strlen($username) < 3 || strlen($username) > 20) {
            echo $this->error('用户名长度应在3-20个字符之间');
            exit;
        }

        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo $this->error('邮箱格式不正确');
            exit;
        }

        if (!empty($phone) && !preg_match('/^1[3-9]\d{9}$/', $phone)) {
            echo $this->error('手机号格式不正确');
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

            if (!empty($email)) {
                $stmt = $this->db->prepare("SELECT id FROM qf_users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    echo $this->error('邮箱已被注册');
                    exit;
                }
            }

            if (!empty($phone)) {
                $stmt = $this->db->prepare("SELECT id FROM qf_users WHERE phone = ?");
                $stmt->execute([$phone]);
                if ($stmt->fetch()) {
                    echo $this->error('手机号已被注册');
                    exit;
                }
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO qf_users (username, email, phone, password, balance, created_at) VALUES (?, ?, ?, ?, 0.00, ?)");
            $stmt->execute([$username, $email, $phone, $hashedPassword, date('Y-m-d H:i:s')]);

            $newId = $this->db->lastInsertId();
            $this->recordOperationLog($newId, 'register', '用户注册成功，IP: ' . $this->getRealIp());

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

            $this->recordOperationLog($userId, 'update_settings', '用户修改账户资料，邮箱: ' . $email . '，QQ: ' . $qq . '，手机号: ' . $phone . '，IP: ' . $this->getRealIp());

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

            $this->recordOperationLog($userId, 'change_password', '用户修改密码成功，IP: ' . $this->getRealIp());

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

            $this->recordOperationLog($userId, 'submit_feedback', '用户提交意见反馈，内容长度: ' . mb_strlen($content) . '，联系方式: ' . $contact . '，IP: ' . $this->getRealIp());

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
        $paymentMethod = $this->post('payment_method', 'balance');

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

            // 免费产品，直接完成购买
            if (floatval($product['price']) <= 0) {
                $orderNo = date('YmdHis') . mt_rand(1000, 9999);
                $stmt = $this->db->prepare("
                    INSERT INTO qf_orders (order_no, user_id, product_id, amount, payment_method, payment_status, status, created_at)
                    VALUES (?, ?, ?, 0.00, 'free', 1, 1, ?)
                ");
                $stmt->execute([$orderNo, $userId, $productId, date('Y-m-d H:i:s')]);
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

                $this->recordOperationLog($userId, 'create_order', '用户领取免费产品：' . $product['name'] . '，订单号：' . $orderNo . '，IP: ' . $this->getRealIp());

                $this->db->commit();
                echo $this->success(['redirect' => '/user/my-products'], '领取成功');
                exit;
            }

            // 余额支付
            if ($paymentMethod === 'balance') {
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

                $this->recordOperationLog($userId, 'create_order', '用户余额购买产品：' . $product['name'] . '，金额：' . $product['price'] . '，订单号：' . $orderNo . '，IP: ' . $this->getRealIp());

                $this->db->commit();
                echo $this->success(['redirect' => '/user/my-products'], '购买成功');
                exit;
            }

            // 第三方支付通道
            $stmt = $this->db->prepare("SELECT * FROM qf_payment_channels WHERE channel_code = ? AND status = 1");
            $stmt->execute([$paymentMethod]);
            $channel = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$channel) {
                $this->db->rollBack();
                echo $this->error('支付通道不可用');
                exit;
            }

            $orderNo = date('YmdHis') . mt_rand(1000, 9999);
            $stmt = $this->db->prepare("
                INSERT INTO qf_orders (order_no, user_id, product_id, amount, payment_method, payment_status, status, created_at)
                VALUES (?, ?, ?, ?, ?, 0, 0, ?)
            ");
            $stmt->execute([$orderNo, $userId, $productId, $product['price'], $paymentMethod, date('Y-m-d H:i:s')]);
            $orderId = $this->db->lastInsertId();

            $this->db->commit();

            $this->recordOperationLog($userId, 'create_order', '用户创建产品订单：' . $product['name'] . '，金额：' . $product['price'] . '，支付方式：' . $paymentMethod . '，订单号：' . $orderNo . '，IP: ' . $this->getRealIp());

            echo $this->success([
                'order_no' => $orderNo,
                'order_id' => $orderId,
                'redirect' => '/pay/' . $orderNo,
            ], '订单已创建，请完成支付');
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

            $this->recordOperationLog($userId, 'download', '用户下载产品文件：' . $order['product_name'] . '，订单ID：' . $orderId . '，IP: ' . $this->getRealIp());

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
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $this->recordOperationLog($userId, 'logout', '用户退出登录，IP: ' . $this->getRealIp());
        }
        session_destroy();
        $this->redirect('/login');
    }

    /**
     * 消息中心页面
     */
    public function messages()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];
        $user = $this->getUser();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 10;
        $offset = ($page - 1) * $pageSize;

        $messages = [];
        $total = 0;
        $unreadCount = 0;

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_messages WHERE user_id = ? AND is_read = 0");
            $stmt->execute([$userId]);
            $unreadCount = $stmt->fetchColumn();

            $stmt = $this->db->prepare("SELECT * FROM qf_messages WHERE user_id = ? ORDER BY created_at DESC LIMIT ?, ?");
            $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(3, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_messages WHERE user_id = ?");
            $stmt->execute([$userId]);
            $total = $stmt->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderUser('user/messages', [
            'user' => $user,
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'messages', '消息中心');
    }

    /**
     * 插件市场页面
     */
    public function pluginMarket()
    {
        $this->requireLogin();
        $user = $this->getUser();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 12;
        $offset = ($page - 1) * $pageSize;

        $plugins = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_plugins WHERE status = 1 ORDER BY created_at DESC LIMIT ?, ?");
            $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(2, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $plugins = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmt = $this->db->query("SELECT COUNT(*) FROM qf_plugins WHERE status = 1");
            $total = $stmt->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderUser('user/plugin-market', [
            'user' => $user,
            'plugins' => $plugins,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'pluginMarket', '插件市场');
    }

    /**
     * 开发者选项页面
     */
    public function developer()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];
        $user = $this->getUser();

        $developerInfo = null;
        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_developer_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$userId]);
            $developerInfo = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
        }

        $myPlugins = [];
        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_plugins WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$userId]);
            $myPlugins = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
        }

        return $this->renderUser('user/developer', [
            'user' => $user,
            'developerInfo' => $developerInfo,
            'myPlugins' => $myPlugins,
        ], 'developer', '开发者选项');
    }

    /**
     * 购买产品页面
     */
    public function buyPage()
    {
        $this->requireLogin();
        $user = $this->getUser();

        $productId = intval($this->get('product_id', 0));

        if ($productId <= 0) {
            $this->redirect('/user/products');
            return '';
        }

        $product = null;
        $paymentChannels = [];

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_products WHERE id = ? AND status = 1");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$product) {
                $this->redirect('/user/products');
                return '';
            }

            $stmt = $this->db->query("SELECT * FROM qf_payment_channels WHERE status = 1 ORDER BY sort_order DESC");
            $paymentChannels = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
        }

        return $this->renderUser('user/buy', [
            'user' => $user,
            'product' => $product,
            'paymentChannels' => $paymentChannels,
        ], 'products', '购买产品');
    }

    /**
     * 余额明细日志
     */
    public function balanceLogs()
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

        return $this->renderUser('user/balance-logs', [
            'user' => $user,
            'logs' => $logs,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'balance', '余额明细');
    }

    /**
     * 登录日志
     */
    public function loginLogs()
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
            $stmt = $this->db->prepare("SELECT * FROM qf_login_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?, ?");
            $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(3, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_login_logs WHERE user_id = ?");
            $stmt->execute([$userId]);
            $total = $stmt->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderUser('user/login-logs', [
            'user' => $user,
            'logs' => $logs,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'logs', '登录日志');
    }

    /**
     * 操作日志
     */
    public function operationLogs()
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
            $stmt = $this->db->prepare("SELECT * FROM qf_operation_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?, ?");
            $stmt->bindValue(1, $userId, \PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(3, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_operation_logs WHERE user_id = ?");
            $stmt->execute([$userId]);
            $total = $stmt->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderUser('user/operation-logs', [
            'user' => $user,
            'logs' => $logs,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'logs', '操作日志');
    }

    /**
     * 换绑邮箱/手机号页面
     */
    public function rebind()
    {
        $this->requireLogin();
        $user = $this->getUser();

        return $this->renderUser('user/rebind', [
            'user' => $user,
        ], 'settings', '换绑');
    }

    /**
     * 提交开发者申请
     */
    public function applyDeveloper()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $realName = $this->post('real_name', '');
        $description = $this->post('description', '');

        if (empty($realName)) {
            echo $this->error('请填写真实姓名');
            exit;
        }

        try {
            // 检查是否已是开发者
            $stmt = $this->db->prepare("SELECT is_developer, developer_status FROM qf_users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            if ($user && $user['is_developer'] == 1) {
                echo $this->error('您已成为开发者');
                exit;
            }
            if ($user && $user['developer_status'] === 'pending') {
                echo $this->error('您的开发者申请正在审核中');
                exit;
            }

            // 检查是否已有申请记录
            $stmt = $this->db->prepare("SELECT id FROM qf_developer_applications WHERE user_id = ? AND status = 'pending'");
            $stmt->execute([$userId]);
            if ($stmt->fetch()) {
                echo $this->error('您的开发者申请正在审核中');
                exit;
            }

            $stmt = $this->db->prepare("INSERT INTO qf_developer_applications (user_id, real_name, reason, status, created_at) VALUES (?, ?, ?, 'pending', ?)");
            $stmt->execute([$userId, $realName, $description, date('Y-m-d H:i:s')]);

            // 更新用户开发者状态为pending
            $this->db->prepare("UPDATE qf_users SET developer_status = 'pending' WHERE id = ?")->execute([$userId]);

            $this->recordOperationLog($userId, 'apply_developer', '用户提交开发者申请，真实姓名：' . $realName . '，申请说明长度：' . mb_strlen($description) . '，IP: ' . $this->getRealIp());

            echo $this->success([], '申请已提交，请等待审核');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('提交失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 提交插件
     */
    public function submitPlugin()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $name = $this->post('name', '');
        $description = $this->post('description', '');
        $version = $this->post('version', '1.0.0');

        if (empty($name) || empty($description)) {
            echo $this->error('请填写插件名称和描述');
            exit;
        }

        try {
            // 检查用户是否为开发者
            $stmt = $this->db->prepare("SELECT is_developer FROM qf_users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            if (!$user || $user['is_developer'] != 1) {
                echo $this->error('您尚未成为开发者或开发者审核未通过');
                exit;
            }

            $pluginFile = '';
            if (isset($_FILES['plugin_file']) && $_FILES['plugin_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = STORAGE_PATH . 'plugins/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $ext = pathinfo($_FILES['plugin_file']['name'], PATHINFO_EXTENSION);
                if (strtolower($ext) !== 'zip') {
                    echo $this->error('只允许上传ZIP格式的插件文件');
                    exit;
                }
                $pluginFile = 'plugin_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                move_uploaded_file($_FILES['plugin_file']['tmp_name'], $uploadDir . $pluginFile);
            }

            $stmt = $this->db->prepare("INSERT INTO qf_plugins (user_id, name, description, version, file_path, price, status, created_at) VALUES (?, ?, ?, ?, ?, 0.00, 'pending', ?)");
            $stmt->execute([$userId, $name, $description, $version, $pluginFile, date('Y-m-d H:i:s')]);

            $this->recordOperationLog($userId, 'submit_plugin', '用户提交插件：' . $name . '，版本：' . $version . '，描述长度：' . mb_strlen($description) . '，IP: ' . $this->getRealIp());

            echo $this->success([], '插件提交成功，请等待审核');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('提交失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 换绑邮箱
     */
    public function rebindEmail()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $email = $this->post('email', '');
        $code = $this->post('code', '');

        if (empty($email) || empty($code)) {
            echo $this->error('请填写完整信息');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo $this->error('邮箱格式不正确');
            exit;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_verify_codes WHERE user_id = ? AND target = ? AND code = ? AND type = 'email' AND used = 0 AND expires_at > ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$userId, $email, $code, date('Y-m-d H:i:s')]);
            $verifyCode = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$verifyCode) {
                echo $this->error('验证码错误或已过期');
                exit;
            }

            $stmt = $this->db->prepare("SELECT id FROM qf_users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                echo $this->error('该邮箱已被其他用户绑定');
                exit;
            }

            $stmt = $this->db->prepare("UPDATE qf_users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $userId]);

            $stmt = $this->db->prepare("UPDATE qf_verify_codes SET used = 1 WHERE id = ?");
            $stmt->execute([$verifyCode['id']]);

            $_SESSION['email'] = $email;

            $this->recordOperationLog($userId, 'rebind_email', '用户换绑邮箱成功，新邮箱：' . $email . '，IP: ' . $this->getRealIp());

            echo $this->success([], '邮箱换绑成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('换绑失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 换绑手机号
     */
    public function rebindPhone()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $phone = $this->post('phone', '');
        $code = $this->post('code', '');

        if (empty($phone) || empty($code)) {
            echo $this->error('请填写完整信息');
            exit;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_verify_codes WHERE user_id = ? AND target = ? AND code = ? AND type = 'phone' AND used = 0 AND expires_at > ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$userId, $phone, $code, date('Y-m-d H:i:s')]);
            $verifyCode = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$verifyCode) {
                echo $this->error('验证码错误或已过期');
                exit;
            }

            $stmt = $this->db->prepare("SELECT id FROM qf_users WHERE phone = ? AND id != ?");
            $stmt->execute([$phone, $userId]);
            if ($stmt->fetch()) {
                echo $this->error('该手机号已被其他用户绑定');
                exit;
            }

            $stmt = $this->db->prepare("UPDATE qf_users SET phone = ? WHERE id = ?");
            $stmt->execute([$phone, $userId]);

            $stmt = $this->db->prepare("UPDATE qf_verify_codes SET used = 1 WHERE id = ?");
            $stmt->execute([$verifyCode['id']]);

            $this->recordOperationLog($userId, 'rebind_phone', '用户换绑手机号成功，新手机号：' . $phone . '，IP: ' . $this->getRealIp());

            echo $this->success([], '手机号换绑成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('换绑失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 发送验证码
     */
    public function sendVerifyCode()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $target = $this->post('target', '');
        $type = $this->post('type', 'email');

        if (empty($target)) {
            echo $this->error('请填写目标地址');
            exit;
        }

        if (!in_array($type, ['email', 'phone'])) {
            echo $this->error('验证码类型错误');
            exit;
        }

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM qf_verify_codes WHERE user_id = ? AND target = ? AND type = ? AND created_at > ?");
            $stmt->execute([$userId, $target, $type, date('Y-m-d H:i:s', strtotime('-1 minute'))]);
            if ($stmt->fetchColumn() > 0) {
                echo $this->error('发送过于频繁，请稍后再试');
                exit;
            }

            $code = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            $stmt = $this->db->prepare("INSERT INTO qf_verify_codes (user_id, target, type, code, expires_at, used, created_at) VALUES (?, ?, ?, ?, ?, 0, ?)");
            $stmt->execute([$userId, $target, $type, $code, $expiresAt, date('Y-m-d H:i:s')]);

            echo $this->success(['code' => $code], '验证码已发送');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('发送失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 标记单条消息已读
     */
    public function readMessage()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        $messageId = intval($this->post('message_id', 0));

        if ($messageId <= 0) {
            echo $this->error('消息参数错误');
            exit;
        }

        try {
            $stmt = $this->db->prepare("UPDATE qf_messages SET is_read = 1 WHERE id = ? AND user_id = ?");
            $stmt->execute([$messageId, $userId]);

            $this->recordOperationLog($userId, 'read_message', '用户标记单条消息为已读，消息ID：' . $messageId . '，IP: ' . $this->getRealIp());

            echo $this->success([], '已标记为已读');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('操作失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 标记全部消息已读
     */
    public function readAllMessages()
    {
        $this->requireLogin();
        $userId = $_SESSION['user_id'];

        try {
            $stmt = $this->db->prepare("UPDATE qf_messages SET is_read = 1 WHERE user_id = ? AND is_read = 0");
            $stmt->execute([$userId]);

            $this->recordOperationLog($userId, 'read_all_messages', '用户标记全部消息为已读，IP: ' . $this->getRealIp());

            echo $this->success([], '全部消息已标记为已读');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('操作失败: ' . $e->getMessage());
            exit;
        }
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
    protected function recordLoginLog($userId, $username, $status, $message)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO qf_login_logs (user_id, username, ip, user_agent, status, message, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                $username,
                $this->getRealIp(),
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
                $status,
                $message,
            ]);
        } catch (\PDOException $e) {
        }
    }
}