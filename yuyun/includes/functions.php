<?php
function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function setting(string $key, ?string $default = null): ?string {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            $db = getDb();
            $stmt = $db->query('SELECT config_key, config_value FROM settings');
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $cache[$row['config_key']] = $row['config_value'];
            }
        } catch (Throwable $e) {
            // ignore before install
        }
    }
    return $cache[$key] ?? $default;
}

function setSetting(string $key, ?string $value): void {
    $db = getDb();
    if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
        $stmt = $db->prepare('INSERT INTO settings (config_key, config_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE config_value=:v');
    } else {
        $stmt = $db->prepare('INSERT INTO settings (config_key, config_value) VALUES (:k, :v) ON CONFLICT(config_key) DO UPDATE SET config_value=:v');
    }
    $stmt->execute([':k' => $key, ':v' => $value]);
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token = null): void {
    $token = $token ?? ($_POST['csrf_token'] ?? $_GET['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('CSRF 验证失败，请刷新页面重试。');
    }
}

function flash(string $type, string $message): void {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flash(): array {
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

function render_flash(): string {
    $html = '';
    foreach (get_flash() as $f) {
        $type = $f['type'] === 'success' ? 'success' : ($f['type'] === 'error' ? 'error' : 'info');
        $html .= '<div class="flash-message flash-' . $type . '" data-type="' . $type . '">' . e($f['message']) . '</div>';
    }
    return $html;
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function current_user(): ?array {
    if (!is_logged_in()) return null;
    try {
        $db = getDb();
        $stmt = $db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    } catch (Throwable $e) {
        return null;
    }
}

function is_admin(): bool {
    $user = current_user();
    return $user && !empty($user['is_admin']);
}

function require_login(): void {
    if (!is_logged_in()) {
        flash('error', '请先登录');
        redirect(YUYUN_URL . '/login.php');
    }
}

function require_admin(): void {
    require_login();
    if (!is_admin()) {
        flash('error', '权限不足');
        redirect(YUYUN_URL . '/');
    }
}

function upload_file(array $file, string $subdir = '', array $allowed = ['image/jpeg','image/png','image/webp','image/gif','image/svg+xml'], int $max = 5242880, array $allowedExts = ['jpg','jpeg','png','webp','gif','svg','ico']): string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('文件上传失败，错误码：' . $file['error']);
    }
    if ($file['size'] > $max) {
        throw new Exception('文件超过限制大小');
    }
    if (!in_array($file['type'], $allowed, true)) {
        throw new Exception('不支持的文件类型：' . $file['type']);
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExts, true)) {
        throw new Exception('不支持的文件扩展名：' . $ext);
    }
    $dir = YUYUN_ROOT . '/uploads' . ($subdir ? '/' . trim($subdir, '/') : '');
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    $filename = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . ($ext ? '.' . $ext : '');
    $path = $dir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $path)) {
        throw new Exception('保存文件失败');
    }
    return 'uploads' . ($subdir ? '/' . trim($subdir, '/') : '') . '/' . $filename;
}

function send_code(string $email, string $code): bool {
    $subject = '【' . e(setting('site_name', L('nav.home', '语云科技'))) . '】' . L('mail.code_subject', '您的登录验证码');
    $message = str_replace('{code}', $code, L('mail.code_body', "您好，\n\n您的验证码是：{$code}，5分钟内有效。\n\n如非本人操作，请忽略此邮件。"));
    $from = setting('smtp_from') ?: setting('site_email', 'noreply@loveym.cloud');
    $fromName = setting('site_name', L('nav.home', '语云科技'));
    if (setting('smtp_host') && setting('smtp_user') && setting('smtp_pass')) {
        return smtp_send($email, $subject, $message, $from, $fromName);
    }
    $headers = "From: " . $fromName . " <" . $from . ">\r\n";
    return mail($email, $subject, $message, $headers);
}

function generate_code(): string {
    return str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
}

function send_verify_email(string $email, string $code): bool {
    $subject = '【' . e(setting('site_name', L('nav.home', '语云科技'))) . '】' . L('mail.verify_subject', '邮箱验证');
    $message = str_replace('{code}', $code, L('mail.verify_body', "您好，\n\n您的邮箱验证码是：{code}，5 分钟内有效。\n\n如非本人操作，请忽略此邮件。"));
    $from = setting('smtp_from') ?: setting('site_email', 'noreply@loveym.cloud');
    $fromName = setting('site_name', L('nav.home', '语云科技'));
    if (setting('smtp_host') && setting('smtp_user') && setting('smtp_pass')) {
        return smtp_send($email, $subject, $message, $from, $fromName);
    }
    $headers = "From: " . $fromName . " <" . $from . ">\r\n";
    return mail($email, $subject, $message, $headers);
}

function slugify(string $s): string {
    return preg_replace('/[^a-z0-9_-]+/i', '-', strtolower(trim($s)));
}

function template_include(string $page): bool {
    $tpl = preg_replace('/[^a-z0-9_-]/i', '', setting('template', 'default'));
    if ($tpl === 'default' || $tpl === '') return false;
    $file = YUYUN_ROOT . '/templates/' . $tpl . '/' . $page;
    if (is_file($file)) {
        require $file;
        return true;
    }
    return false;
}

function init_patch2_tables(): void {
    try {
        $db = getDb();
        $isMysql = defined('DB_TYPE') && DB_TYPE === 'mysql';
        if ($isMysql) {
            $db->exec("CREATE TABLE IF NOT EXISTS notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                content TEXT,
                is_read TINYINT DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } else {
            $db->exec("CREATE TABLE IF NOT EXISTS notifications (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                content TEXT,
                is_read INTEGER DEFAULT 0,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )");
        }
    } catch (Throwable $e) {
        // ignore before install
    }
}

function notify_user(int $user_id, string $title, string $content = ''): void {
    try {
        $db = getDb();
        $stmt = $db->prepare('INSERT INTO notifications (user_id, title, content, created_at) VALUES (:u, :t, :c, :d)');
        $stmt->execute([':u' => $user_id, ':t' => $title, ':c' => $content, ':d' => date('Y-m-d H:i:s')]);
    } catch (Throwable $e) {
        // ignore
    }
}

function get_notification_count(int $user_id): int {
    try {
        $db = getDb();
        $stmt = $db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id=:u AND is_read=0');
        $stmt->execute([':u' => $user_id]);
        return (int)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

function get_notifications(int $user_id, int $limit = 50): array {
    try {
        $db = getDb();
        $stmt = $db->prepare('SELECT * FROM notifications WHERE user_id=:u ORDER BY created_at DESC LIMIT :l');
        $stmt->bindValue(':u', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':l', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
}

function mark_notification_read(int $id, int $user_id): void {
    try {
        $db = getDb();
        $db->prepare('UPDATE notifications SET is_read=1 WHERE id=:id AND user_id=:u')->execute([':id' => $id, ':u' => $user_id]);
    } catch (Throwable $e) {
    }
}

function map_fa_to_iconfont(string $icon): string {
    $map = [
        'fa-cube' => 'icon-cubes',
        'fa-server' => 'icon-store',
        'fa-shield-halved' => 'icon-shield',
        'fa-network-wired' => 'icon-cloud',
        'fa-globe' => 'icon-map',
        'fa-database' => 'icon-store',
        'fa-lock' => 'icon-lock',
        'fa-envelope' => 'icon-envelope',
        'fa-microchip' => 'icon-store',
        'fa-file-invoice-dollar' => 'icon-certificate',
    ];
    if (strpos($icon, 'fa-') === 0) {
        return $map[$icon] ?? 'icon-cubes';
    }
    return $icon ?: 'icon-cubes';
}

function admin_icon_preview(string $icon): string {
    $cls = map_fa_to_iconfont($icon);
    return '<span class="admin-icon-preview"><i class="iconfont ' . e($cls) . '"></i></span>';
}
