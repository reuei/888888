<?php
function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function ensure_settings_table(): void {
    static $ensured = false;
    if ($ensured) return;
    $ensured = true;
    try {
        $db = getDb();
        $type = defined('DB_TYPE') && DB_TYPE === 'mysql' ? 'mysql' : 'sqlite';
        if ($type === 'mysql') {
            $db->exec("CREATE TABLE IF NOT EXISTS settings (
                config_key VARCHAR(100) PRIMARY KEY,
                config_value TEXT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        } else {
            $db->exec("CREATE TABLE IF NOT EXISTS settings (
                config_key TEXT PRIMARY KEY,
                config_value TEXT
            )");
        }
    } catch (Throwable $e) { /* ignore */ }
}

function setting(string $key, ?string $default = null): ?string {
    static $cache = null;
    if ($cache === null || !empty($GLOBALS['__yy_settings_dirty'])) {
        $cache = [];
        $GLOBALS['__yy_settings_dirty'] = false;
        try {
            ensure_settings_table();
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
    ensure_settings_table();
    $db = getDb();
    if (defined('DB_TYPE') && DB_TYPE === 'mysql') {
        $stmt = $db->prepare('INSERT INTO settings (config_key, config_value) VALUES (:k, :v) ON DUPLICATE KEY UPDATE config_value=VALUES(config_value)');
    } else {
        $stmt = $db->prepare('INSERT INTO settings (config_key, config_value) VALUES (:k, :v) ON CONFLICT(config_key) DO UPDATE SET config_value=:v');
    }
    $stmt->execute([':k' => $key, ':v' => $value]);
    $GLOBALS['__yy_settings_dirty'] = true; // 下次 setting() 调用时重新加载
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
        $html .= '<div class="flash-data" data-type="' . e($type) . '" data-message="' . e($f['message']) . '" style="display:none"></div>';
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
    $subject = '【语云科技】您的登录验证码';
    $message = "您好，\n\n您的验证码是：{$code}，5分钟内有效。\n\n如非本人操作，请忽略此邮件。";
    $headers = "From: " . (setting('site_email', 'noreply@loveym.cloud')) . "\r\n";
    return mail($email, $subject, $message, $headers);
}

function generate_code(): string {
    return str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
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

function ensure_notifications_table(): void {
    $db = getDb();
    $type = defined('DB_TYPE') && DB_TYPE === 'mysql' ? 'mysql' : 'sqlite';
    if ($type === 'mysql') {
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
}

function ensure_user_columns(): void {
    $db = getDb();
    $type = defined('DB_TYPE') && DB_TYPE === 'mysql' ? 'mysql' : 'sqlite';
    $cols = ['email_verified', 'verify_code', 'code_expire'];
    foreach ($cols as $col) {
        try {
            $db->query("SELECT {$col} FROM users LIMIT 1");
        } catch (PDOException $e) {
            if ($type === 'mysql') {
                $def = $col === 'email_verified' ? 'TINYINT DEFAULT 0' : ($col === 'code_expire' ? 'INT' : 'VARCHAR(20)');
                $db->exec("ALTER TABLE users ADD COLUMN {$col} {$def}");
            } else {
                $def = $col === 'email_verified' ? 'INTEGER DEFAULT 0' : ($col === 'code_expire' ? 'INTEGER' : 'TEXT');
                $db->exec("ALTER TABLE users ADD COLUMN {$col} {$def}");
            }
        }
    }
}

function notify_user(int $user_id, string $title, string $content): void {
    ensure_notifications_table();
    $db = getDb();
    $stmt = $db->prepare('INSERT INTO notifications (user_id, title, content, created_at) VALUES (:u, :t, :c, :d)');
    $stmt->execute([':u' => $user_id, ':t' => $title, ':c' => $content, ':d' => date('Y-m-d H:i:s')]);
}

function unread_notification_count(int $user_id): int {
    try {
        ensure_notifications_table();
        $db = getDb();
        $stmt = $db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id=:u AND is_read=0');
        $stmt->execute([':u' => $user_id]);
        return (int)$stmt->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}
