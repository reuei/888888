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
     * 退出登录
     */
    public function logout()
    {
        session_destroy();
        $this->redirect('/admin/login');
    }
}