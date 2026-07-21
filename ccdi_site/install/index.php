<?php
/**
 * CCDI CMS v8.0.0 安装向导
 * 中央纪委国家监委网站风格 CMS 安装程序
 */

define('SKIP_INSTALL_CHECK', true);
define('SYSTEM_INIT', true);

require_once __DIR__ . '/../includes/config.php';

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$errors = [];
$success = '';
$db_created = false;

// 环境检查
$php_version = PHP_VERSION;
$php_version_ok = version_compare($php_version, '7.0.0', '>=');
$pdo_ok = extension_loaded('pdo');
$pdo_sqlite_ok = extension_loaded('pdo_sqlite');
$gd_ok = extension_loaded('gd');
$mbstring_ok = extension_loaded('mbstring');
$json_ok = extension_loaded('json');
$session_ok = extension_loaded('session');
$curl_ok = extension_loaded('curl');
$data_writable = is_writable(dirname(DB_PATH));
$uploads_writable = is_writable(UPLOADS_PATH);

$env_all_ok = $php_version_ok && $pdo_ok && $pdo_sqlite_ok && $mbstring_ok && $json_ok && $session_ok && $data_writable && $uploads_writable;

// 检查数据库文件是否已存在
if (file_exists(DB_PATH) && $step < 5) {
    $db_created = true;
}

// 检查是否已完成安装（使用 try/catch + PDO，不调用任何 db_* 函数）
$install_complete = false;
if (file_exists(DB_PATH)) {
    try {
        $check_db = new PDO(DB_DSN);
        $check_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $check_db->query("SELECT COUNT(*) FROM users WHERE role = 'super_admin'");
        $install_complete = $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        $install_complete = false;
    }
}

// 如果已完成安装，直接跳转到完成页
if ($install_complete && $step < 5) {
    $step = 5;
}

// 处理 POST 请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 3 && !$db_created) {
        // 创建数据库及所有表
        try {
            $db = new PDO(DB_DSN);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->exec("PRAGMA journal_mode=WAL");
            $db->exec("PRAGMA foreign_keys=ON");

            $db->exec("
                CREATE TABLE IF NOT EXISTS site_config (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    config_key TEXT UNIQUE NOT NULL,
                    config_value TEXT,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username TEXT UNIQUE NOT NULL,
                    password TEXT NOT NULL,
                    email TEXT DEFAULT '',
                    role TEXT DEFAULT 'subscriber',
                    status TEXT DEFAULT 'active',
                    remember_token TEXT DEFAULT NULL,
                    reg_time DATETIME DEFAULT CURRENT_TIMESTAMP,
                    last_login DATETIME DEFAULT NULL
                );

                CREATE TABLE IF NOT EXISTS categories (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    parent_id INTEGER DEFAULT 0,
                    name TEXT NOT NULL,
                    slug TEXT UNIQUE NOT NULL,
                    description TEXT DEFAULT '',
                    icon TEXT DEFAULT '',
                    sort_order INTEGER DEFAULT 0,
                    status INTEGER DEFAULT 1,
                    is_nav INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS articles (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    category_id INTEGER DEFAULT 0,
                    title TEXT NOT NULL,
                    slug TEXT UNIQUE,
                    content TEXT,
                    summary TEXT DEFAULT '',
                    cover_image TEXT DEFAULT '',
                    author TEXT DEFAULT '',
                    source TEXT DEFAULT '',
                    keywords TEXT DEFAULT '',
                    is_top INTEGER DEFAULT 0,
                    is_recommend INTEGER DEFAULT 0,
                    status TEXT DEFAULT 'draft',
                    view_count INTEGER DEFAULT 0,
                    publish_time DATETIME DEFAULT CURRENT_TIMESTAMP,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS carousel (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT DEFAULT '',
                    type TEXT DEFAULT 'image',
                    image TEXT DEFAULT '',
                    video_url TEXT DEFAULT '',
                    link TEXT DEFAULT '',
                    description TEXT DEFAULT '',
                    sort_order INTEGER DEFAULT 0,
                    status INTEGER DEFAULT 1,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS videos (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL,
                    cover TEXT DEFAULT '',
                    video_url TEXT DEFAULT '',
                    video_file TEXT DEFAULT '',
                    description TEXT DEFAULT '',
                    category_id INTEGER DEFAULT 0,
                    view_count INTEGER DEFAULT 0,
                    sort_order INTEGER DEFAULT 0,
                    status INTEGER DEFAULT 1,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS staff (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    title TEXT DEFAULT '',
                    avatar TEXT DEFAULT '',
                    department TEXT DEFAULT '',
                    bio TEXT DEFAULT '',
                    sort_order INTEGER DEFAULT 0,
                    status INTEGER DEFAULT 1,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS footer_carousel (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT DEFAULT '',
                    type TEXT DEFAULT 'image',
                    image TEXT DEFAULT '',
                    link TEXT DEFAULT '',
                    sort_order INTEGER DEFAULT 0,
                    status INTEGER DEFAULT 1,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS popups (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT DEFAULT '',
                    content TEXT,
                    image TEXT DEFAULT '',
                    link TEXT DEFAULT '',
                    start_time DATETIME DEFAULT NULL,
                    end_time DATETIME DEFAULT NULL,
                    status INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS messages (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER DEFAULT 0,
                    name TEXT DEFAULT '',
                    email TEXT DEFAULT '',
                    phone TEXT DEFAULT '',
                    title TEXT DEFAULT '',
                    content TEXT NOT NULL,
                    type TEXT DEFAULT 'message',
                    status TEXT DEFAULT 'unread',
                    reply TEXT DEFAULT NULL,
                    replied_at DATETIME DEFAULT NULL,
                    ip_address TEXT DEFAULT '',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS reports (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER DEFAULT 0,
                    name TEXT DEFAULT '',
                    email TEXT DEFAULT '',
                    phone TEXT DEFAULT '',
                    title TEXT NOT NULL,
                    content TEXT NOT NULL,
                    attachment TEXT DEFAULT '',
                    report_type TEXT DEFAULT 'report',
                    status TEXT DEFAULT 'pending',
                    reply TEXT DEFAULT NULL,
                    handle_remark TEXT DEFAULT '',
                    handled_at DATETIME DEFAULT NULL,
                    ip_address TEXT DEFAULT '',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS system_logs (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER DEFAULT 0,
                    action TEXT DEFAULT '',
                    description TEXT DEFAULT '',
                    ip_address TEXT DEFAULT '',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS nav_menu (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    parent_id INTEGER DEFAULT 0,
                    name TEXT NOT NULL,
                    url TEXT DEFAULT '',
                    target TEXT DEFAULT '_self',
                    sort_order INTEGER DEFAULT 0,
                    status INTEGER DEFAULT 1,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE INDEX IF NOT EXISTS idx_articles_category ON articles(category_id);
                CREATE INDEX IF NOT EXISTS idx_articles_status ON articles(status);
                CREATE INDEX IF NOT EXISTS idx_articles_publish ON articles(publish_time);
                CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
                CREATE INDEX IF NOT EXISTS idx_messages_status ON messages(status);
                CREATE INDEX IF NOT EXISTS idx_reports_status ON reports(status);
            ");

            // 插入默认配置
            $db->exec("
                INSERT OR IGNORE INTO site_config (config_key, config_value) VALUES
                ('site_name', '中央纪委国家监委网站'),
                ('site_description', '中央纪委国家监委网站——中国共产党中央纪律检查委员会、中华人民共和国国家监察委员会官方网站'),
                ('site_keywords', '中央纪委,国家监委,反腐败,纪检监察,巡视巡察,党风廉政'),
                ('security_key', '" . bin2hex(random_bytes(32)) . "'),
                ('footer_text', '版权所有 © 中央纪委国家监委'),
                ('footer_image', ''),
                ('banner_image', ''),
                ('preloader_image', ''),
                ('preloader_enabled', '0'),
                ('popup_enabled', '0'),
                ('icp_number', ''),
                ('contact_email', ''),
                ('report_email', '');
            ");

            // 插入默认分类
            $db->exec("
                INSERT OR IGNORE INTO categories (id, parent_id, name, slug, description, sort_order, is_nav) VALUES
                (1, 0, '要闻', 'yaowen', '重要新闻', 1, 1),
                (2, 0, '审查调查', 'shenchadiaocha', '审查调查信息', 2, 1),
                (3, 0, '巡视巡察', 'xunshixuncha', '巡视巡察工作', 3, 1),
                (4, 0, '党纪法规', 'dangjifagui', '党纪法规库', 4, 1),
                (5, 0, '监督举报', 'jiandujubao', '监督举报通道', 5, 1),
                (6, 0, '工作动态', 'gongzuodongtai', '工作动态信息', 6, 1),
                (7, 0, '纪法百科', 'jifabaike', '纪检监察知识百科', 7, 1),
                (8, 0, '视频', 'shipin', '视频中心', 8, 1),
                (9, 0, '文化之约', 'wenhuazhiyue', '廉洁文化', 9, 1),
                (10, 0, '国际追逃', 'guojizhuitao', '国际追逃追赃', 10, 1);
            ");

            // 插入默认导航菜单
            $db->exec("
                INSERT OR IGNORE INTO nav_menu (id, parent_id, name, url, sort_order) VALUES
                (1, 0, '要闻', '/category/yaowen', 1),
                (2, 0, '审查调查', '/category/shenchadiaocha', 2),
                (3, 0, '巡视巡察', '/category/xunshixuncha', 3),
                (4, 0, '党纪法规', '/category/dangjifagui', 4),
                (5, 0, '监督举报', '/report.php', 5),
                (6, 0, '纪法百科', '/category/jifabaike', 6),
                (7, 0, '视频', '/category/shipin', 7),
                (8, 0, '文化之约', '/category/wenhuazhiyue', 8),
                (9, 0, '国际追逃', '/category/guojizhuitao', 9);
            ");

            $db_created = true;
            $success = '数据库创建成功！';
        } catch (PDOException $e) {
            $errors[] = '数据库创建失败：' . $e->getMessage();
        }
    }

    if ($step === 4) {
        // 创建管理员账户
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $email = trim($_POST['email'] ?? '');

        if (empty($username)) {
            $errors[] = '请输入管理员用户名';
        } elseif (!preg_match('/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]{3,20}$/u', $username)) {
            $errors[] = '用户名格式不正确（3-20位，字母、数字、下划线、中文）';
        }

        if (empty($password)) {
            $errors[] = '请输入管理员密码';
        } elseif (strlen($password) < 6) {
            $errors[] = '密码长度不能少于6位';
        }

        if ($password !== $password2) {
            $errors[] = '两次输入的密码不一致';
        }

        if (empty($errors)) {
            try {
                $db = new PDO(DB_DSN);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $stmt = $db->prepare("INSERT INTO users (username, password, email, role, status, reg_time) VALUES (?, ?, ?, 'super_admin', 'active', datetime('now','localtime'))");
                $stmt->execute([$username, $hashed, $email]);

                // 更新站点名称
                $site_name = trim($_POST['site_name'] ?? '');
                if ($site_name) {
                    $stmt = $db->prepare("UPDATE site_config SET config_value = ? WHERE config_key = 'site_name'");
                    $stmt->execute([$site_name]);
                }

                $success = '安装完成！管理员账户创建成功。';
                $install_complete = true;
            } catch (PDOException $e) {
                $errors[] = '创建管理员失败：' . $e->getMessage();
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统安装 - CCDI CMS v8.0.0</title>
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Microsoft YaHei", "PingFang SC", "Helvetica Neue", Arial, sans-serif;
            background: #f5f6fa;
            color: #2c3e50;
            line-height: 1.6;
            min-height: 100vh;
        }

        .install-wrapper {
            max-width: 740px;
            margin: 0 auto;
            padding: 30px 16px;
        }

        .install-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        /* 头部 */
        .install-header {
            background: linear-gradient(135deg, #c41230 0%, #9b0e26 100%);
            color: #fff;
            padding: 36px 32px;
            text-align: center;
        }
        .install-header h1 {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .install-header .version {
            font-size: 13px;
            opacity: 0.85;
            letter-spacing: 0.5px;
        }

        /* 步骤条 */
        .steps-bar {
            display: flex;
            background: #fafbfc;
            border-bottom: 2px solid #eef0f4;
        }
        .steps-bar .step-item {
            flex: 1;
            text-align: center;
            padding: 16px 8px;
            font-size: 13px;
            color: #b0b8c1;
            position: relative;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .steps-bar .step-item.active {
            color: #c41230;
            font-weight: 700;
        }
        .steps-bar .step-item.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 20%;
            right: 20%;
            height: 3px;
            background: #c41230;
            border-radius: 3px 3px 0 0;
        }
        .steps-bar .step-item.done {
            color: #52c41a;
        }

        /* 主体 */
        .install-body {
            padding: 32px;
        }
        .install-body h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 24px;
            color: #1a1a2e;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f5;
        }

        /* 表单 */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 7px;
            color: #444;
        }
        .form-group label .required {
            color: #c41230;
        }
        .form-group input {
            width: 100%;
            padding: 11px 15px;
            border: 1.5px solid #dde1e6;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #fafbfc;
            color: #333;
        }
        .form-group input:focus {
            border-color: #c41230;
            outline: none;
            box-shadow: 0 0 0 3px rgba(196,18,48,0.08);
            background: #fff;
        }

        /* 按钮 */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 28px;
            font-size: 15px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            line-height: 1.4;
        }
        .btn-primary {
            background: #c41230;
            color: #fff;
        }
        .btn-primary:hover {
            background: #a00e28;
            box-shadow: 0 4px 12px rgba(196,18,48,0.3);
        }
        .btn-success {
            background: #52c41a;
            color: #fff;
        }
        .btn-success:hover {
            background: #45a815;
        }
        .btn-outline {
            background: #f5f6fa;
            color: #555;
            border: 1.5px solid #dde1e6;
        }
        .btn-outline:hover {
            background: #eef0f4;
        }
        .btn + .btn {
            margin-left: 10px;
        }

        /* 提示消息 */
        .alert {
            padding: 14px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.7;
        }
        .alert-success {
            background: #f0fff4;
            border: 1px solid #b7eb8f;
            color: #389e0d;
        }
        .alert-error {
            background: #fff2f0;
            border: 1px solid #ffccc7;
            color: #cf1322;
        }
        .alert-info {
            background: #e6f7ff;
            border: 1px solid #91d5ff;
            color: #0050b3;
        }

        /* 检查列表 */
        .check-list {
            list-style: none;
        }
        .check-list li {
            padding: 11px 0;
            border-bottom: 1px solid #f5f5f5;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }
        .check-list li:last-child {
            border-bottom: none;
        }
        .check-list .check-label {
            flex: 1;
            min-width: 0;
        }

        /* 徽章 */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-success {
            background: #f0fff4;
            color: #52c41a;
        }
        .badge-error {
            background: #fff2f0;
            color: #ff4d4f;
        }
        .badge-warning {
            background: #fffbe6;
            color: #faad14;
        }

        /* 许可协议 */
        .license-box {
            background: #fafbfc;
            border: 1px solid #e8ecf0;
            border-radius: 8px;
            padding: 20px 24px;
            max-height: 280px;
            overflow-y: auto;
            font-size: 13px;
            line-height: 2;
            margin-bottom: 24px;
            color: #555;
        }
        .license-box strong {
            color: #333;
        }

        /* 完成页 */
        .complete-icon {
            text-align: center;
            font-size: 56px;
            margin-bottom: 16px;
        }
        .complete-actions {
            margin-top: 24px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* 底部 */
        .install-footer {
            text-align: center;
            padding: 20px;
            color: #999;
            font-size: 12px;
        }

        /* 响应式 */
        @media (max-width: 600px) {
            .install-wrapper {
                padding: 0;
            }
            .install-card {
                border-radius: 0;
            }
            .install-header {
                padding: 24px 20px;
            }
            .install-header h1 {
                font-size: 20px;
            }
            .install-body {
                padding: 20px;
            }
            .steps-bar .step-item {
                font-size: 11px;
                padding: 12px 4px;
            }
            .check-list li {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }
            .complete-actions {
                flex-direction: column;
            }
            .complete-actions .btn {
                width: 100%;
                margin-left: 0 !important;
            }
            .btn + .btn {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
<div class="install-wrapper">
    <div class="install-card">
        <div class="install-header">
            <h1>CCDI CMS 安装向导</h1>
            <div class="version">v8.0.0 · 中央纪委国家监委网站内容管理系统</div>
        </div>

        <div class="steps-bar">
            <div class="step-item <?php echo $step == 1 ? 'active' : ($step > 1 ? 'done' : ''); ?>">1. 环境检查</div>
            <div class="step-item <?php echo $step == 2 ? 'active' : ($step > 2 ? 'done' : ''); ?>">2. 许可协议</div>
            <div class="step-item <?php echo $step == 3 ? 'active' : ($step > 3 ? 'done' : ''); ?>">3. 数据库安装</div>
            <div class="step-item <?php echo $step == 4 ? 'active' : ($step > 4 ? 'done' : ''); ?>">4. 管理员设置</div>
            <div class="step-item <?php echo $step == 5 ? 'active' : ''; ?>">5. 安装完成</div>
        </div>

        <div class="install-body">
            <?php if ($errors): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if ($step === 1): ?>
                <h3>服务器环境检查</h3>
                <ul class="check-list">
                    <li>
                        <span class="check-label">PHP 版本（要求 >= 7.0）</span>
                        <span><?php echo htmlspecialchars($php_version); ?> <span class="badge <?php echo $php_version_ok ? 'badge-success' : 'badge-error'; ?>"><?php echo $php_version_ok ? '✓ 通过' : '✗ 失败'; ?></span></span>
                    </li>
                    <li>
                        <span class="check-label">PDO 扩展</span>
                        <span class="badge <?php echo $pdo_ok ? 'badge-success' : 'badge-error'; ?>"><?php echo $pdo_ok ? '✓ 通过' : '✗ 失败'; ?></span>
                    </li>
                    <li>
                        <span class="check-label">PDO_SQLite 扩展</span>
                        <span class="badge <?php echo $pdo_sqlite_ok ? 'badge-success' : 'badge-error'; ?>"><?php echo $pdo_sqlite_ok ? '✓ 通过' : '✗ 失败'; ?></span>
                    </li>
                    <li>
                        <span class="check-label">GD 图像处理</span>
                        <span class="badge <?php echo $gd_ok ? 'badge-success' : 'badge-warning'; ?>"><?php echo $gd_ok ? '✓ 通过' : '⚠ 建议安装'; ?></span>
                    </li>
                    <li>
                        <span class="check-label">MBString 多字节字符串</span>
                        <span class="badge <?php echo $mbstring_ok ? 'badge-success' : 'badge-error'; ?>"><?php echo $mbstring_ok ? '✓ 通过' : '✗ 失败'; ?></span>
                    </li>
                    <li>
                        <span class="check-label">JSON 扩展</span>
                        <span class="badge <?php echo $json_ok ? 'badge-success' : 'badge-error'; ?>"><?php echo $json_ok ? '✓ 通过' : '✗ 失败'; ?></span>
                    </li>
                    <li>
                        <span class="check-label">Session 扩展</span>
                        <span class="badge <?php echo $session_ok ? 'badge-success' : 'badge-error'; ?>"><?php echo $session_ok ? '✓ 通过' : '✗ 失败'; ?></span>
                    </li>
                    <li>
                        <span class="check-label">cURL 扩展</span>
                        <span class="badge <?php echo $curl_ok ? 'badge-success' : 'badge-warning'; ?>"><?php echo $curl_ok ? '✓ 通过' : '⚠ 建议安装'; ?></span>
                    </li>
                    <li>
                        <span class="check-label">data/ 目录可写</span>
                        <span class="badge <?php echo $data_writable ? 'badge-success' : 'badge-error'; ?>"><?php echo $data_writable ? '✓ 通过' : '✗ 失败'; ?></span>
                    </li>
                    <li>
                        <span class="check-label">uploads/ 目录可写</span>
                        <span class="badge <?php echo $uploads_writable ? 'badge-success' : 'badge-error'; ?>"><?php echo $uploads_writable ? '✓ 通过' : '✗ 失败'; ?></span>
                    </li>
                </ul>

                <?php if (!$data_writable || !$uploads_writable): ?>
                    <div class="alert alert-error" style="margin-top:16px;">请确保 data/ 和 uploads/ 目录具有写入权限（执行 chmod 755 或 775）</div>
                <?php endif; ?>

                <div style="margin-top:24px;">
                    <?php if ($env_all_ok): ?>
                        <a href="?step=2" class="btn btn-primary">下一步 →</a>
                    <?php else: ?>
                        <div class="alert alert-error">请先解决上述环境问题后再继续安装</div>
                    <?php endif; ?>
                </div>

            <?php elseif ($step === 2): ?>
                <h3>许可协议</h3>
                <div class="license-box">
                    <p><strong>CCDI CMS v8.0.0 —— 中央纪委国家监委网站内容管理系统</strong></p>
                    <p>本系统仅供学习研究使用，不得用于任何违法活动。</p>
                    <p>1. 您可以在遵守法律法规的前提下自由使用、修改本系统。</p>
                    <p>2. 本系统采用 PHP + SQLite 架构，无需 MySQL 数据库，开箱即用。</p>
                    <p>3. 系统使用 bcrypt 算法加密存储用户密码，保障数据安全。</p>
                    <p>4. 请妥善保管管理员账户密码，定期备份 data/ 目录下的数据库文件。</p>
                    <p>5. 禁止利用本系统传播违法和不良信息。</p>
                    <p>6. 安装完成后建议删除 install/ 目录或限制其访问权限。</p>
                    <p>7. 本系统作者不对因使用本系统造成的任何问题承担责任。</p>
                </div>
                <a href="?step=3" class="btn btn-primary">同意并继续 →</a>
                <a href="?step=1" class="btn btn-outline">← 返回</a>

            <?php elseif ($step === 3): ?>
                <h3>数据库安装</h3>
                <?php if ($db_created): ?>
                    <div class="alert alert-info">数据库文件已存在，无需重复安装。</div>
                    <a href="?step=4" class="btn btn-primary">下一步 →</a>
                <?php else: ?>
                    <div class="alert alert-info">
                        <strong>数据库类型：</strong>SQLite 3<br>
                        <strong>存储路径：</strong><?php echo htmlspecialchars(DB_PATH); ?><br>
                        <strong>说明：</strong>SQLite 是单文件数据库，无需额外配置，适合虚拟主机环境。系统将自动创建所需全部数据表及默认数据。
                    </div>
                    <form method="post">
                        <button type="submit" class="btn btn-primary">开始安装数据库</button>
                        <a href="?step=2" class="btn btn-outline">← 返回</a>
                    </form>
                <?php endif; ?>

            <?php elseif ($step === 4): ?>
                <h3>设置管理员账户</h3>
                <form method="post">
                    <div class="form-group">
                        <label>网站名称</label>
                        <input type="text" name="site_name" value="中央纪委国家监委网站" placeholder="您的网站名称">
                    </div>
                    <div class="form-group">
                        <label>管理员用户名 <span class="required">*</span></label>
                        <input type="text" name="username" required placeholder="3-20位，支持字母、数字、下划线、中文" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>管理员密码 <span class="required">*</span></label>
                        <input type="password" name="password" required placeholder="至少6位密码" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label>确认密码 <span class="required">*</span></label>
                        <input type="password" name="password2" required placeholder="再次输入密码" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label>管理员邮箱</label>
                        <input type="email" name="email" placeholder="可选，用于密码找回">
                    </div>
                    <button type="submit" class="btn btn-primary">完成安装</button>
                    <a href="?step=3" class="btn btn-outline">← 返回</a>
                </form>

            <?php elseif ($step === 5): ?>
                <h3>安装完成</h3>
                <div class="complete-icon">✅</div>
                <div class="alert alert-success">
                    <strong>CCDI CMS v8.0.0 安装成功！</strong><br>
                    系统已准备就绪，请妥善保管您的管理员账户信息。
                </div>
                <div class="alert alert-info">
                    <strong>安全提示：</strong><br>
                    1. 建议立即删除 install/ 目录或限制其访问权限<br>
                    2. 定期备份 data/ccdi_site.db 数据库文件<br>
                    3. 将 data/ 目录移出 Web 根目录可进一步增强安全性
                </div>
                <div class="complete-actions">
                    <a href="<?php echo htmlspecialchars(SITE_FULL_URL); ?>/admin/" class="btn btn-primary">进入后台管理</a>
                    <a href="<?php echo htmlspecialchars(SITE_FULL_URL); ?>/" class="btn btn-success">访问网站首页</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="install-footer">
        CCDI CMS v8.0.0 &copy; <?php echo date('Y'); ?> 中央纪委国家监委网站
    </div>
</div>
</body>
</html>