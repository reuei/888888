<?php
namespace app\controller;

/**
 * 管理后台控制器
 */
class Admin extends BaseController
{
    /**
     * 管理员登录页面
     */
    public function login()
    {
        if (isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/dashboard');
        }
        $siteSettings = $this->getSiteSettings();
        return $this->render('admin/login', ['siteSettings' => $siteSettings]);
    }

    /**
     * 处理管理员登录
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
            $stmt = $this->db->prepare("SELECT * FROM qf_admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$admin) {
                echo $this->error('管理员不存在');
                exit;
            }

            if ($admin['status'] != 1) {
                echo $this->error('账户已被禁用');
                exit;
            }

            if (!password_verify($password, $admin['password'])) {
                echo $this->error('密码错误');
                exit;
            }

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];

            echo $this->success(['redirect' => '/admin/dashboard'], '登录成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('系统错误: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 后台首页
     */
    public function dashboard()
    {
        $this->requireAdminLogin();

        $stats = [
            'users' => 0,
            'products' => 0,
            'licenses' => 0,
            'orders' => 0,
            'revenue' => 0,
        ];

        try {
            $stats['users'] = $this->db->query("SELECT COUNT(*) FROM qf_users")->fetchColumn();
            $stats['products'] = $this->db->query("SELECT COUNT(*) FROM qf_products")->fetchColumn();
            $stats['licenses'] = $this->db->query("SELECT COUNT(*) FROM qf_licenses")->fetchColumn();
            $stats['orders'] = $this->db->query("SELECT COUNT(*) FROM qf_orders")->fetchColumn();

            $stmt = $this->db->query("SELECT SUM(amount) FROM qf_orders WHERE status = 1");
            $revenue = $stmt->fetchColumn();
            $stats['revenue'] = $revenue ?: 0;
        } catch (\PDOException $e) {
        }

        return $this->renderAdmin('admin/dashboard', [
            'stats' => $stats,
        ], 'dashboard', '后台首页');
    }

    /**
     * 用户管理
     */
    public function users()
    {
        $this->requireAdminLogin();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;

        $users = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_users ORDER BY created_at DESC LIMIT ?, ?");
            $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(2, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $total = $this->db->query("SELECT COUNT(*) FROM qf_users")->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderAdmin('admin/users', [
            'users' => $users,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'users', '用户管理');
    }

    /**
     * 产品管理
     */
    public function products()
    {
        $this->requireAdminLogin();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;

        $products = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_products ORDER BY sort DESC LIMIT ?, ?");
            $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(2, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $total = $this->db->query("SELECT COUNT(*) FROM qf_products")->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderAdmin('admin/products', [
            'products' => $products,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'products', '产品管理');
    }

    /**
     * 添加产品
     */
    public function addProduct()
    {
        $this->requireAdminLogin();

        $name = $this->post('name', '');
        $description = $this->post('description', '');
        $type = $this->post('type', 'software');
        $price = floatval($this->post('price', 0));
        $duration = intval($this->post('duration', 0));
        $sort = intval($this->post('sort', 0));

        if (empty($name)) {
            echo $this->error('产品名称不能为空');
            exit;
        }

        if ($price <= 0) {
            echo $this->error('请输入有效的价格');
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO qf_products (name, description, type, price, duration, sort, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 1, ?)
            ");
            $stmt->execute([$name, $description, $type, $price, $duration, $sort, date('Y-m-d H:i:s')]);

            echo $this->success([], '产品添加成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('添加失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 编辑产品
     */
    public function editProduct()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));
        $name = $this->post('name', '');
        $description = $this->post('description', '');
        $type = $this->post('type', 'software');
        $price = floatval($this->post('price', 0));
        $duration = intval($this->post('duration', 0));
        $sort = intval($this->post('sort', 0));
        $status = intval($this->post('status', 1));

        if ($id <= 0) {
            echo $this->error('产品ID无效');
            exit;
        }

        if (empty($name)) {
            echo $this->error('产品名称不能为空');
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE qf_products SET name = ?, description = ?, type = ?, price = ?, duration = ?, sort = ?, status = ?
                WHERE id = ?
            ");
            $stmt->execute([$name, $description, $type, $price, $duration, $sort, $status, $id]);

            echo $this->success([], '产品更新成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('更新失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 删除产品
     */
    public function deleteProduct()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));

        if ($id <= 0) {
            echo $this->error('产品ID无效');
            exit;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM qf_products WHERE id = ?");
            $stmt->execute([$id]);

            echo $this->success([], '产品删除成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('删除失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 上传产品文件
     */
    public function uploadProductFile()
    {
        $this->requireAdminLogin();

        $productId = intval($this->post('product_id', 0));

        if ($productId <= 0) {
            echo $this->error('产品ID无效');
            exit;
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            echo $this->error('文件上传失败');
            exit;
        }

        $file = $_FILES['file'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        if (strtolower($ext) !== 'zip') {
            echo $this->error('仅支持ZIP格式文件');
            exit;
        }

        $uploadDir = STORAGE_PATH . 'products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = 'product_' . $productId . '_' . time() . '.zip';
        $destPath = $uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            echo $this->error('文件保存失败');
            exit;
        }

        try {
            $stmt = $this->db->prepare("UPDATE qf_products SET download_file = ? WHERE id = ?");
            $stmt->execute([$fileName, $productId]);

            echo $this->success(['file' => $fileName], '文件上传成功');
            exit;
        } catch (\PDOException $e) {
            unlink($destPath);
            echo $this->error('保存失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 删除产品文件
     */
    public function deleteProductFile()
    {
        $this->requireAdminLogin();

        $productId = intval($this->post('product_id', 0));

        if ($productId <= 0) {
            echo $this->error('产品ID无效');
            exit;
        }

        try {
            $stmt = $this->db->prepare("SELECT download_file FROM qf_products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($product && !empty($product['download_file'])) {
                $filePath = STORAGE_PATH . 'products/' . $product['download_file'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $stmt = $this->db->prepare("UPDATE qf_products SET download_file = NULL WHERE id = ?");
            $stmt->execute([$productId]);

            echo $this->success([], '文件删除成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('删除失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 授权管理
     */
    public function licenses()
    {
        $this->requireAdminLogin();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;

        $licenses = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("
                SELECT l.*, u.username, p.name AS product_name
                FROM qf_licenses l
                LEFT JOIN qf_users u ON l.user_id = u.id
                LEFT JOIN qf_products p ON l.product_id = p.id
                ORDER BY l.created_at DESC
                LIMIT ?, ?
            ");
            $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(2, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $licenses = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $total = $this->db->query("SELECT COUNT(*) FROM qf_licenses")->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderAdmin('admin/licenses', [
            'licenses' => $licenses,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'licenses', '授权管理');
    }

    /**
     * 创建功能码
     */
    public function createFeatureCode()
    {
        $this->requireAdminLogin();

        $name = $this->post('name', '');
        $code = $this->post('code', '');
        $description = $this->post('description', '');

        if (empty($name) || empty($code)) {
            echo $this->error('名称和功能码不能为空');
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO qf_feature_codes (name, code, description, status, created_at)
                VALUES (?, ?, ?, 1, ?)
            ");
            $stmt->execute([$name, $code, $description, date('Y-m-d H:i:s')]);

            echo $this->success([], '功能码创建成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('创建失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 更新功能码状态
     */
    public function updateFeatureCodeStatus()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));
        $status = intval($this->post('status', 1));

        if ($id <= 0) {
            echo $this->error('功能码ID无效');
            exit;
        }

        try {
            $stmt = $this->db->prepare("UPDATE qf_feature_codes SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);

            echo $this->success([], '状态更新成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('更新失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 订单管理
     */
    public function orders()
    {
        $this->requireAdminLogin();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;

        $orders = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("
                SELECT o.*, u.username, p.name AS product_name
                FROM qf_orders o
                LEFT JOIN qf_users u ON o.user_id = u.id
                LEFT JOIN qf_products p ON o.product_id = p.id
                ORDER BY o.created_at DESC
                LIMIT ?, ?
            ");
            $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(2, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $total = $this->db->query("SELECT COUNT(*) FROM qf_orders")->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderAdmin('admin/orders', [
            'orders' => $orders,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'orders', '订单管理');
    }

    /**
     * 系统设置
     */
    public function settings()
    {
        $this->requireAdminLogin();

        $settings = [];
        try {
            $stmt = $this->db->query("SELECT * FROM qf_settings");
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($results as $item) {
                $settings[$item['key']] = $item['value'];
            }
        } catch (\PDOException $e) {
        }

        return $this->renderAdmin('admin/settings', [
            'settings' => $settings,
        ], 'settings', '系统设置');
    }

    /**
     * 保存设置
     */
    public function saveSettings()
    {
        $this->requireAdminLogin();

        $settings = $this->post();
        unset($settings['controller'], $settings['action']);

        try {
            $this->db->beginTransaction();

            foreach ($settings as $key => $value) {
                $stmt = $this->db->prepare("SELECT id FROM qf_settings WHERE `key` = ?");
                $stmt->execute([$key]);
                if ($stmt->fetch()) {
                    $stmt = $this->db->prepare("UPDATE qf_settings SET `value` = ? WHERE `key` = ?");
                    $stmt->execute([$value, $key]);
                } else {
                    $stmt = $this->db->prepare("INSERT INTO qf_settings (`key`, `value`) VALUES (?, ?)");
                    $stmt->execute([$key, $value]);
                }
            }

            $this->db->commit();

            echo $this->success([], '设置保存成功');
            exit;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            echo $this->error('保存失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 文档管理
     */
    public function documents()
    {
        $this->requireAdminLogin();

        $editDoc = null;
        $editId = intval($this->get('edit', 0));
        if ($editId > 0) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM qf_documents WHERE id = ?");
                $stmt->execute([$editId]);
                $editDoc = $stmt->fetch(\PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
            }
        }

        $documents = [];
        try {
            $stmt = $this->db->query("SELECT * FROM qf_documents ORDER BY category ASC, sort_order ASC");
            $documents = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
        }

        return $this->renderAdmin('admin/documents', [
            'documents' => $documents,
            'editDoc' => $editDoc,
        ], 'documents', '文档管理');
    }

    /**
     * 保存文档
     */
    public function saveDocument()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));
        $title = $this->post('title', '');
        $content = $this->post('content', '');
        $category = $this->post('category', '');
        $sortOrder = intval($this->post('sort_order', 0));

        if (empty($title)) {
            echo $this->error('文档标题不能为空');
            exit;
        }

        try {
            if ($id > 0) {
                $stmt = $this->db->prepare("
                    UPDATE qf_documents SET title = ?, content = ?, category = ?, sort_order = ?, updated_at = ?
                    WHERE id = ?
                ");
                $stmt->execute([$title, $content, $category, $sortOrder, date('Y-m-d H:i:s'), $id]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO qf_documents (title, content, category, sort_order, created_at)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$title, $content, $category, $sortOrder, date('Y-m-d H:i:s')]);
            }

            echo $this->success([], '文档保存成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('保存失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 删除文档
     */
    public function deleteDocument()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));

        if ($id <= 0) {
            echo $this->error('文档ID无效');
            exit;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM qf_documents WHERE id = ?");
            $stmt->execute([$id]);

            echo $this->success([], '文档删除成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('删除失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 消息管理页面
     */
    public function messages()
    {
        $this->requireAdminLogin();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;

        $messages = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("
                SELECT m.*, u.username
                FROM qf_messages m
                LEFT JOIN qf_users u ON m.user_id = u.id
                ORDER BY m.created_at DESC
                LIMIT ?, ?
            ");
            $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(2, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $total = $this->db->query("SELECT COUNT(*) FROM qf_messages")->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderAdmin('admin/messages', [
            'messages' => $messages,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'messages', '消息管理');
    }

    /**
     * 发送消息
     */
    public function sendMessage()
    {
        $this->requireAdminLogin();

        $userId = intval($this->post('user_id', 0));
        $title = $this->post('title', '');
        $content = $this->post('content', '');
        $isEmailSent = intval($this->post('is_email_sent', 0));

        if (empty($title) || empty($content)) {
            echo $this->error('标题和内容不能为空');
            exit;
        }

        try {
            $this->db->beginTransaction();

            if ($userId == 0) {
                // 发送给全部用户
                $stmt = $this->db->query("SELECT id, email FROM qf_users");
                $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $insertStmt = $this->db->prepare("
                    INSERT INTO qf_messages (user_id, title, content, is_email_sent, created_at)
                    VALUES (?, ?, ?, ?, ?)
                ");

                $now = date('Y-m-d H:i:s');
                foreach ($users as $user) {
                    $insertStmt->execute([$user['id'], $title, $content, $isEmailSent, $now]);

                    // 如果勾选了邮件通知，发送邮件
                    if ($isEmailSent && !empty($user['email'])) {
                        $this->sendEmailNotification($user['email'], $title, $content);
                    }
                }
            } else {
                // 发送给指定用户
                $insertStmt = $this->db->prepare("
                    INSERT INTO qf_messages (user_id, title, content, is_email_sent, created_at)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $insertStmt->execute([$userId, $title, $content, $isEmailSent, date('Y-m-d H:i:s')]);

                // 如果勾选了邮件通知，发送邮件
                if ($isEmailSent) {
                    $userStmt = $this->db->prepare("SELECT email FROM qf_users WHERE id = ?");
                    $userStmt->execute([$userId]);
                    $user = $userStmt->fetch(\PDO::FETCH_ASSOC);
                    if ($user && !empty($user['email'])) {
                        $this->sendEmailNotification($user['email'], $title, $content);
                    }
                }
            }

            $this->db->commit();

            echo $this->success([], '消息发送成功');
            exit;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            echo $this->error('发送失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 编辑消息
     */
    public function editMessage()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));
        $title = $this->post('title', '');
        $content = $this->post('content', '');

        if ($id <= 0) {
            echo $this->error('消息ID无效');
            exit;
        }

        if (empty($title)) {
            echo $this->error('标题不能为空');
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE qf_messages SET title = ?, content = ? WHERE id = ?
            ");
            $stmt->execute([$title, $content, $id]);

            echo $this->success([], '消息更新成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('更新失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 删除消息
     */
    public function deleteMessage()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));

        if ($id <= 0) {
            echo $this->error('消息ID无效');
            exit;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM qf_messages WHERE id = ?");
            $stmt->execute([$id]);

            echo $this->success([], '消息删除成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('删除失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 开发者管理页面
     */
    public function developers()
    {
        $this->requireAdminLogin();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;

        $applications = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("
                SELECT a.*, u.username, u.email
                FROM qf_developer_applications a
                LEFT JOIN qf_users u ON a.user_id = u.id
                ORDER BY a.created_at DESC
                LIMIT ?, ?
            ");
            $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(2, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $applications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $total = $this->db->query("SELECT COUNT(*) FROM qf_developer_applications")->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderAdmin('admin/developers', [
            'applications' => $applications,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'developers', '开发者管理');
    }

    /**
     * 审核开发者申请
     */
    public function reviewDeveloper()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));
        $action = $this->post('action', '');
        $rejectReason = $this->post('reject_reason', '');

        if ($id <= 0) {
            echo $this->error('申请ID无效');
            exit;
        }

        if (!in_array($action, ['approve', 'reject'])) {
            echo $this->error('无效的操作');
            exit;
        }

        if ($action === 'reject' && empty($rejectReason)) {
            echo $this->error('驳回原因不能为空');
            exit;
        }

        try {
            $this->db->beginTransaction();

            // 获取申请信息
            $stmt = $this->db->prepare("SELECT * FROM qf_developer_applications WHERE id = ?");
            $stmt->execute([$id]);
            $application = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$application) {
                echo $this->error('申请不存在');
                exit;
            }

            if ($action === 'approve') {
                // 通过：更新qf_users的is_developer=1, developer_status='approved'
                $stmt = $this->db->prepare("UPDATE qf_users SET is_developer = 1, developer_status = 'approved' WHERE id = ?");
                $stmt->execute([$application['user_id']]);

                // 更新qf_developer_applications的status='approved'
                $stmt = $this->db->prepare("UPDATE qf_developer_applications SET status = 'approved' WHERE id = ?");
                $stmt->execute([$id]);
            } else {
                // 驳回：更新status='rejected'，记录驳回原因
                $stmt = $this->db->prepare("UPDATE qf_developer_applications SET status = 'rejected', reject_reason = ? WHERE id = ?");
                $stmt->execute([$rejectReason, $id]);

                // 给用户发消息通知（不发送邮件）
                $stmt = $this->db->prepare("
                    INSERT INTO qf_messages (user_id, title, content, is_email_sent, created_at)
                    VALUES (?, ?, ?, 0, ?)
                ");
                $stmt->execute([
                    $application['user_id'],
                    '开发者申请被驳回',
                    '您的开发者申请已被驳回。原因：' . $rejectReason,
                    date('Y-m-d H:i:s')
                ]);
            }

            $this->db->commit();

            echo $this->success([], $action === 'approve' ? '开发者申请已通过' : '开发者申请已驳回');
            exit;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            echo $this->error('操作失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 插件管理页面
     */
    public function plugins()
    {
        $this->requireAdminLogin();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;

        $plugins = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("
                SELECT p.*, u.username
                FROM qf_plugins p
                LEFT JOIN qf_users u ON p.user_id = u.id
                ORDER BY p.created_at DESC
                LIMIT ?, ?
            ");
            $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(2, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $plugins = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $total = $this->db->query("SELECT COUNT(*) FROM qf_plugins")->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderAdmin('admin/plugins', [
            'plugins' => $plugins,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'plugins', '插件管理');
    }

    /**
     * 审核插件
     */
    public function reviewPlugin()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));
        $action = $this->post('action', '');
        $rejectReason = $this->post('reject_reason', '');

        if ($id <= 0) {
            echo $this->error('插件ID无效');
            exit;
        }

        if (!in_array($action, ['approve', 'reject'])) {
            echo $this->error('无效的操作');
            exit;
        }

        if ($action === 'reject' && empty($rejectReason)) {
            echo $this->error('驳回原因不能为空');
            exit;
        }

        try {
            if ($action === 'approve') {
                $stmt = $this->db->prepare("UPDATE qf_plugins SET status = 'approved' WHERE id = ?");
                $stmt->execute([$id]);
            } else {
                $stmt = $this->db->prepare("UPDATE qf_plugins SET status = 'rejected', reject_reason = ? WHERE id = ?");
                $stmt->execute([$rejectReason, $id]);
            }

            echo $this->success([], $action === 'approve' ? '插件已通过审核' : '插件已驳回');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('操作失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 编辑插件信息
     */
    public function editPlugin()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));
        $name = $this->post('name', '');
        $description = $this->post('description', '');
        $price = floatval($this->post('price', 0));

        if ($id <= 0) {
            echo $this->error('插件ID无效');
            exit;
        }

        if (empty($name)) {
            echo $this->error('插件名称不能为空');
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE qf_plugins SET name = ?, description = ?, price = ? WHERE id = ?
            ");
            $stmt->execute([$name, $description, $price, $id]);

            echo $this->success([], '插件信息更新成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('更新失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 删除插件
     */
    public function deletePlugin()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));

        if ($id <= 0) {
            echo $this->error('插件ID无效');
            exit;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM qf_plugins WHERE id = ?");
            $stmt->execute([$id]);

            echo $this->success([], '插件删除成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('删除失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 反馈管理页面
     */
    public function feedbackList()
    {
        $this->requireAdminLogin();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;

        $feedbacks = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("
                SELECT f.*, u.username
                FROM qf_feedback f
                LEFT JOIN qf_users u ON f.user_id = u.id
                ORDER BY f.created_at DESC
                LIMIT ?, ?
            ");
            $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(2, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $feedbacks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $total = $this->db->query("SELECT COUNT(*) FROM qf_feedback")->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderAdmin('admin/feedback', [
            'feedbacks' => $feedbacks,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'feedback', '反馈管理');
    }

    /**
     * 回复反馈
     */
    public function replyFeedback()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));
        $reply = $this->post('reply', '');

        if ($id <= 0) {
            echo $this->error('反馈ID无效');
            exit;
        }

        if (empty($reply)) {
            echo $this->error('回复内容不能为空');
            exit;
        }

        try {
            $stmt = $this->db->prepare("UPDATE qf_feedback SET reply = ?, status = 'processing' WHERE id = ?");
            $stmt->execute([$reply, $id]);

            echo $this->success([], '回复成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('回复失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 标记反馈已处理
     */
    public function processFeedback()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));

        if ($id <= 0) {
            echo $this->error('反馈ID无效');
            exit;
        }

        try {
            $stmt = $this->db->prepare("UPDATE qf_feedback SET status = 'processed' WHERE id = ?");
            $stmt->execute([$id]);

            echo $this->success([], '反馈已标记为已处理');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('操作失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 驳回反馈
     */
    public function rejectFeedback()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));
        $rejectReason = $this->post('reject_reason', '');

        if ($id <= 0) {
            echo $this->error('反馈ID无效');
            exit;
        }

        if (empty($rejectReason)) {
            echo $this->error('驳回原因不能为空');
            exit;
        }

        try {
            $stmt = $this->db->prepare("UPDATE qf_feedback SET status = 'rejected', reject_reason = ? WHERE id = ?");
            $stmt->execute([$rejectReason, $id]);

            echo $this->success([], '反馈已驳回');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('操作失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 邮箱池配置页面
     */
    public function emailPool()
    {
        $this->requireAdminLogin();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;

        $emails = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("
                SELECT * FROM qf_email_pool
                ORDER BY id ASC
                LIMIT ?, ?
            ");
            $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(2, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $emails = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $total = $this->db->query("SELECT COUNT(*) FROM qf_email_pool")->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderAdmin('admin/email-pool', [
            'emails' => $emails,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'email_pool', '邮箱池配置');
    }

    /**
     * 添加邮箱到池
     */
    public function addEmailPool()
    {
        $this->requireAdminLogin();

        $email = $this->post('email', '');
        $smtpHost = $this->post('smtp_host', '');
        $smtpPort = intval($this->post('smtp_port', 465));
        $smtpUser = $this->post('smtp_user', '');
        $smtpPass = $this->post('smtp_pass', '');
        $smtpEncryption = $this->post('smtp_encryption', 'ssl');

        if (empty($email) || empty($smtpHost) || empty($smtpUser) || empty($smtpPass)) {
            echo $this->error('请填写完整的邮箱配置信息');
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO qf_email_pool (email, smtp_host, smtp_port, smtp_user, smtp_pass, smtp_encryption, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, 1, ?)
            ");
            $stmt->execute([$email, $smtpHost, $smtpPort, $smtpUser, $smtpPass, $smtpEncryption, date('Y-m-d H:i:s')]);

            echo $this->success([], '邮箱添加成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('添加失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 编辑邮箱配置
     */
    public function editEmailPool()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));
        $email = $this->post('email', '');
        $smtpHost = $this->post('smtp_host', '');
        $smtpPort = intval($this->post('smtp_port', 465));
        $smtpUser = $this->post('smtp_user', '');
        $smtpPass = $this->post('smtp_pass', '');
        $smtpEncryption = $this->post('smtp_encryption', 'ssl');
        $status = intval($this->post('status', 1));

        if ($id <= 0) {
            echo $this->error('邮箱ID无效');
            exit;
        }

        if (empty($email) || empty($smtpHost) || empty($smtpUser)) {
            echo $this->error('请填写完整的邮箱配置信息');
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE qf_email_pool SET email = ?, smtp_host = ?, smtp_port = ?, smtp_user = ?, smtp_pass = ?, smtp_encryption = ?, status = ?
                WHERE id = ?
            ");
            $stmt->execute([$email, $smtpHost, $smtpPort, $smtpUser, $smtpPass, $smtpEncryption, $status, $id]);

            echo $this->success([], '邮箱配置更新成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('更新失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 删除邮箱
     */
    public function deleteEmailPool()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));

        if ($id <= 0) {
            echo $this->error('邮箱ID无效');
            exit;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM qf_email_pool WHERE id = ?");
            $stmt->execute([$id]);

            echo $this->success([], '邮箱删除成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('删除失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 测试发送邮件
     */
    public function testEmail()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));
        $testEmail = $this->post('test_email', '');

        if ($id <= 0) {
            echo $this->error('请选择邮箱');
            exit;
        }

        if (empty($testEmail)) {
            echo $this->error('请填写测试接收邮箱');
            exit;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM qf_email_pool WHERE id = ?");
            $stmt->execute([$id]);
            $emailConfig = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$emailConfig) {
                echo $this->error('邮箱配置不存在');
                exit;
            }

            $result = $this->sendTestEmail($emailConfig, $testEmail);

            if ($result) {
                echo $this->success([], '测试邮件发送成功');
            } else {
                echo $this->error('测试邮件发送失败');
            }
            exit;
        } catch (\PDOException $e) {
            echo $this->error('发送失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 邮件模板管理页面
     */
    public function emailTemplates()
    {
        $this->requireAdminLogin();

        $templates = [];
        try {
            $stmt = $this->db->query("SELECT * FROM qf_email_templates ORDER BY id ASC");
            $templates = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
        }

        return $this->renderAdmin('admin/email-templates', [
            'templates' => $templates,
        ], 'email_templates', '邮件模板管理');
    }

    /**
     * 保存邮件模板
     */
    public function saveEmailTemplate()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));
        $name = $this->post('name', '');
        $subject = $this->post('subject', '');
        $content = $this->post('content', '');
        $code = $this->post('code', '');

        if (empty($name) || empty($code)) {
            echo $this->error('模板名称和标识不能为空');
            exit;
        }

        try {
            if ($id > 0) {
                $stmt = $this->db->prepare("
                    UPDATE qf_email_templates SET name = ?, subject = ?, content = ?, code = ? WHERE id = ?
                ");
                $stmt->execute([$name, $subject, $content, $code, $id]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO qf_email_templates (name, subject, content, code, created_at)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$name, $subject, $content, $code, date('Y-m-d H:i:s')]);
            }

            echo $this->success([], '模板保存成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('保存失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 支付通道管理页面
     */
    public function paymentChannels()
    {
        $this->requireAdminLogin();

        $channels = [];
        try {
            $stmt = $this->db->query("SELECT * FROM qf_payment_channels ORDER BY id ASC");
            $channels = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
        }

        return $this->renderAdmin('admin/payment-channels', [
            'channels' => $channels,
        ], 'payment_channels', '支付通道管理');
    }

    /**
     * 保存支付通道
     */
    public function savePaymentChannel()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));
        $name = $this->post('name', '');
        $channelCode = $this->post('channel_code', '');
        $apiUrl = $this->post('api_url', '');
        $merchantId = $this->post('merchant_id', '');
        $merchantKey = $this->post('merchant_key', '');
        $feeRate = floatval($this->post('fee_rate', 0));
        $status = intval($this->post('status', 1));

        if (empty($name) || empty($channelCode)) {
            echo $this->error('通道名称和通道代码不能为空');
            exit;
        }

        try {
            if ($id > 0) {
                $stmt = $this->db->prepare("
                    UPDATE qf_payment_channels SET name = ?, channel_code = ?, api_url = ?, merchant_id = ?, merchant_key = ?, fee_rate = ?, status = ?
                    WHERE id = ?
                ");
                $stmt->execute([$name, $channelCode, $apiUrl, $merchantId, $merchantKey, $feeRate, $status, $id]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO qf_payment_channels (name, channel_code, api_url, merchant_id, merchant_key, fee_rate, status, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$name, $channelCode, $apiUrl, $merchantId, $merchantKey, $feeRate, $status, date('Y-m-d H:i:s')]);
            }

            echo $this->success([], '支付通道保存成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('保存失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 删除支付通道
     */
    public function deletePaymentChannel()
    {
        $this->requireAdminLogin();

        $id = intval($this->post('id', 0));

        if ($id <= 0) {
            echo $this->error('通道ID无效');
            exit;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM qf_payment_channels WHERE id = ?");
            $stmt->execute([$id]);

            echo $this->success([], '支付通道删除成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('删除失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 上传文件管理页面
     */
    public function uploadFiles()
    {
        $this->requireAdminLogin();

        $page = max(1, intval($this->get('page', 1)));
        $pageSize = 15;
        $offset = ($page - 1) * $pageSize;

        $files = [];
        $total = 0;

        try {
            $stmt = $this->db->prepare("
                SELECT * FROM qf_upload_files
                ORDER BY created_at DESC
                LIMIT ?, ?
            ");
            $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
            $stmt->bindValue(2, $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            $files = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $total = $this->db->query("SELECT COUNT(*) FROM qf_upload_files")->fetchColumn();
        } catch (\PDOException $e) {
        }

        $totalPages = ceil($total / $pageSize);

        return $this->renderAdmin('admin/upload-files', [
            'files' => $files,
            'page' => $page,
            'totalPages' => $totalPages,
        ], 'upload_files', '上传文件管理');
    }

    /**
     * 上传网站Logo
     */
    public function uploadSiteLogo()
    {
        $this->requireAdminLogin();

        if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
            echo $this->error('文件上传失败');
            exit;
        }

        $file = $_FILES['logo'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];

        if (!in_array($ext, $allowedExts)) {
            echo $this->error('仅支持图片格式: jpg, jpeg, png, gif, svg, webp');
            exit;
        }

        $uploadDir = STORAGE_PATH . 'site/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = 'logo_' . time() . '.' . $ext;
        $destPath = $uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            echo $this->error('文件保存失败');
            exit;
        }

        try {
            $this->saveSetting('site_logo', $fileName);

            echo $this->success(['file' => $fileName], 'Logo上传成功');
            exit;
        } catch (\PDOException $e) {
            unlink($destPath);
            echo $this->error('保存失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 上传网站缩略图（Favicon）
     */
    public function uploadSiteFavicon()
    {
        $this->requireAdminLogin();

        if (!isset($_FILES['favicon']) || $_FILES['favicon']['error'] !== UPLOAD_ERR_OK) {
            echo $this->error('文件上传失败');
            exit;
        }

        $file = $_FILES['favicon'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = ['ico', 'png', 'jpg', 'jpeg', 'svg'];

        if (!in_array($ext, $allowedExts)) {
            echo $this->error('仅支持格式: ico, png, jpg, jpeg, svg');
            exit;
        }

        $uploadDir = STORAGE_PATH . 'site/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileName = 'favicon_' . time() . '.' . $ext;
        $destPath = $uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            echo $this->error('文件保存失败');
            exit;
        }

        try {
            $this->saveSetting('site_favicon', $fileName);

            echo $this->success(['file' => $fileName], 'Favicon上传成功');
            exit;
        } catch (\PDOException $e) {
            unlink($destPath);
            echo $this->error('保存失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 保存邮箱配置（随机/顺序选择等）
     */
    public function saveEmailConfig()
    {
        $this->requireAdminLogin();

        $selectMode = $this->post('select_mode', 'random'); // random 或 sequence
        $dailyLimit = intval($this->post('daily_limit', 0));
        $hourlyLimit = intval($this->post('hourly_limit', 0));
        $retryTimes = intval($this->post('retry_times', 3));
        $timeout = intval($this->post('timeout', 30));

        try {
            $this->saveSetting('email_select_mode', $selectMode);
            $this->saveSetting('email_daily_limit', $dailyLimit);
            $this->saveSetting('email_hourly_limit', $hourlyLimit);
            $this->saveSetting('email_retry_times', $retryTimes);
            $this->saveSetting('email_timeout', $timeout);

            echo $this->success([], '邮箱配置保存成功');
            exit;
        } catch (\PDOException $e) {
            echo $this->error('保存失败: ' . $e->getMessage());
            exit;
        }
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        session_destroy();
        $this->redirect('/admin/login');
    }

    /**
     * 保存单个设置项
     */
    private function saveSetting($key, $value)
    {
        $stmt = $this->db->prepare("SELECT id FROM qf_settings WHERE `key` = ?");
        $stmt->execute([$key]);
        if ($stmt->fetch()) {
            $stmt = $this->db->prepare("UPDATE qf_settings SET `value` = ? WHERE `key` = ?");
            $stmt->execute([$value, $key]);
        } else {
            $stmt = $this->db->prepare("INSERT INTO qf_settings (`key`, `value`) VALUES (?, ?)");
            $stmt->execute([$key, $value]);
        }
    }

    /**
     * 发送邮件通知（用于消息通知）
     */
    private function sendEmailNotification($to, $subject, $content)
    {
        try {
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: " . $this->getSiteSetting('site_name') . " <" . $this->getSiteSetting('smtp_from_email') . ">\r\n";
            mail($to, $subject, $content, $headers);
        } catch (\Exception $e) {
            // 邮件发送失败不影响主流程
        }
    }

    /**
     * 发送测试邮件
     */
    private function sendTestEmail($config, $to)
    {
        try {
            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "From: " . $config['email'] . " <" . $config['smtp_user'] . ">\r\n";
            $subject = '测试邮件 - ' . $this->getSiteSetting('site_name');
            $content = '<p>这是一封测试邮件，如果您收到此邮件，说明邮箱配置正确。</p>';

            return mail($to, $subject, $content, $headers);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 获取单个站点设置
     */
    private function getSiteSetting($key, $default = '')
    {
        try {
            $stmt = $this->db->prepare("SELECT `value` FROM qf_settings WHERE `key` = ?");
            $stmt->execute([$key]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $result ? $result['value'] : $default;
        } catch (\PDOException $e) {
            return $default;
        }
    }
}