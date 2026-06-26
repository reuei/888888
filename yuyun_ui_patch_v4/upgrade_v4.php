<?php
/**
 * 语云科技官网 v4 补丁升级脚本
 * 使用方法：上传到网站根目录后访问 upgrade_v4.php
 * 升级完成后请删除此文件
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('YUYUN_ROOT', __DIR__);
require_once YUYUN_ROOT . '/includes/config.php';

$db = getDb();
$type = defined('DB_TYPE') && DB_TYPE === 'mysql' ? 'mysql' : 'sqlite';

$messages = [];

// 1. 确保 settings 表存在
try {
    ensure_settings_table();
    $messages[] = '✓ settings 表已就绪';
} catch (Exception $e) {
    $messages[] = '✗ settings 表创建失败: ' . $e->getMessage();
}

// 2. 确保用户表有必要的列
try {
    ensure_user_columns();
    $messages[] = '✓ users 表列已就绪';
} catch (Exception $e) {
    $messages[] = '✗ users 表升级失败: ' . $e->getMessage();
}

// 3. 确保 notifications 表存在
try {
    ensure_notifications_table();
    $messages[] = '✓ notifications 表已就绪';
} catch (Exception $e) {
    $messages[] = '✗ notifications 表创建失败: ' . $e->getMessage();
}

// 4. 确保 feedback 表有 reply 和 replied_at 列
try {
    $db->query('SELECT reply FROM feedback LIMIT 1');
} catch (PDOException $e) {
    $def = $type === 'mysql' ? 'TEXT' : 'TEXT';
    try {
        $db->exec("ALTER TABLE feedback ADD COLUMN reply {$def}");
        $messages[] = '✓ feedback.reply 列已添加';
    } catch (PDOException $e2) {
        $messages[] = '✗ feedback.reply 列添加失败: ' . $e2->getMessage();
    }
}

try {
    $db->query('SELECT replied_at FROM feedback LIMIT 1');
} catch (PDOException $e) {
    $def = $type === 'mysql' ? 'DATETIME NULL' : 'TEXT';
    try {
        $db->exec("ALTER TABLE feedback ADD COLUMN replied_at {$def}");
        $messages[] = '✓ feedback.replied_at 列已添加';
    } catch (PDOException $e2) {
        $messages[] = '✗ feedback.replied_at 列添加失败: ' . $e2->getMessage();
    }
}

// 5. 确保 slides 表存在
try {
    $db->query('SELECT id FROM slides LIMIT 1');
    $messages[] = '✓ slides 表已存在';
} catch (PDOException $e) {
    if ($type === 'mysql') {
        $sql = "CREATE TABLE IF NOT EXISTS slides (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            subtitle VARCHAR(500),
            image VARCHAR(500),
            link VARCHAR(500),
            is_active TINYINT DEFAULT 1,
            sort_order INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    } else {
        $sql = "CREATE TABLE IF NOT EXISTS slides (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            subtitle TEXT,
            image TEXT,
            link TEXT,
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0
        )";
    }
    try {
        $db->exec($sql);
        $messages[] = '✓ slides 表已创建';
    } catch (PDOException $e2) {
        $messages[] = '✗ slides 表创建失败: ' . $e2->getMessage();
    }
}

// 6. 确保 products 表存在
try {
    $db->query('SELECT id FROM products LIMIT 1');
    $messages[] = '✓ products 表已存在';
} catch (PDOException $e) {
    if ($type === 'mysql') {
        $sql = "CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            summary TEXT,
            detail TEXT,
            icon VARCHAR(100),
            is_active TINYINT DEFAULT 1,
            sort_order INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    } else {
        $sql = "CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            summary TEXT,
            detail TEXT,
            icon TEXT,
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0
        )";
    }
    try {
        $db->exec($sql);
        $messages[] = '✓ products 表已创建';
    } catch (PDOException $e2) {
        $messages[] = '✗ products 表创建失败: ' . $e2->getMessage();
    }
}

// 7. 确保 partners 表存在
try {
    $db->query('SELECT id FROM partners LIMIT 1');
    $messages[] = '✓ partners 表已存在';
} catch (PDOException $e) {
    if ($type === 'mysql') {
        $sql = "CREATE TABLE IF NOT EXISTS partners (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            logo VARCHAR(500),
            link VARCHAR(500),
            is_active TINYINT DEFAULT 1,
            sort_order INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    } else {
        $sql = "CREATE TABLE IF NOT EXISTS partners (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            logo TEXT,
            link TEXT,
            is_active INTEGER DEFAULT 1,
            sort_order INTEGER DEFAULT 0
        )";
    }
    try {
        $db->exec($sql);
        $messages[] = '✓ partners 表已创建';
    } catch (PDOException $e2) {
        $messages[] = '✗ partners 表创建失败: ' . $e2->getMessage();
    }
}

// 8. 确保 staff 表存在
try {
    $db->query('SELECT id FROM staff LIMIT 1');
    $messages[] = '✓ staff 表已存在';
} catch (PDOException $e) {
    if ($type === 'mysql') {
        $sql = "CREATE TABLE IF NOT EXISTS staff (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            position VARCHAR(100),
            bio TEXT,
            avatar VARCHAR(500),
            sort_order INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    } else {
        $sql = "CREATE TABLE IF NOT EXISTS staff (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            position TEXT,
            bio TEXT,
            avatar TEXT,
            sort_order INTEGER DEFAULT 0
        )";
    }
    try {
        $db->exec($sql);
        $messages[] = '✓ staff 表已创建';
    } catch (PDOException $e2) {
        $messages[] = '✗ staff 表创建失败: ' . $e2->getMessage();
    }
}

// 9. 确保 feedback 表存在
try {
    $db->query('SELECT id FROM feedback LIMIT 1');
    $messages[] = '✓ feedback 表已存在';
} catch (PDOException $e) {
    if ($type === 'mysql') {
        $sql = "CREATE TABLE IF NOT EXISTS feedback (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            type VARCHAR(50) DEFAULT 'suggestion',
            content TEXT NOT NULL,
            contact VARCHAR(255),
            reply TEXT,
            replied_at DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    } else {
        $sql = "CREATE TABLE IF NOT EXISTS feedback (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            type TEXT DEFAULT 'suggestion',
            content TEXT NOT NULL,
            contact TEXT,
            reply TEXT,
            replied_at TEXT,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        )";
    }
    try {
        $db->exec($sql);
        $messages[] = '✓ feedback 表已创建';
    } catch (PDOException $e2) {
        $messages[] = '✗ feedback 表创建失败: ' . $e2->getMessage();
    }
}

// 10. 确保 tickets 表存在
try {
    $db->query('SELECT id FROM tickets LIMIT 1');
    $messages[] = '✓ tickets 表已存在';
} catch (PDOException $e) {
    if ($type === 'mysql') {
        $sql = "CREATE TABLE IF NOT EXISTS tickets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            category VARCHAR(50) DEFAULT 'technical',
            content TEXT NOT NULL,
            status TINYINT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    } else {
        $sql = "CREATE TABLE IF NOT EXISTS tickets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            title TEXT NOT NULL,
            category TEXT DEFAULT 'technical',
            content TEXT NOT NULL,
            status INTEGER DEFAULT 0,
            created_at TEXT DEFAULT CURRENT_TIMESTAMP
        )";
    }
    try {
        $db->exec($sql);
        $messages[] = '✓ tickets 表已创建';
    } catch (PDOException $e2) {
        $messages[] = '✗ tickets 表创建失败: ' . $e2->getMessage();
    }
}

// 11. 初始化一些默认设置（如果不存在）
$defaultSettings = [
    'site_name' => '语云科技',
    'site_short' => '语云',
    'site_slogan' => '企业与开发者信赖的云计算与数字化服务伙伴',
    'sales_phone' => '400-800-8451',
    'banner_enabled' => '1',
    'banner_text' => '欢迎来到语云科技官网！',
    'banner_bg_color' => '#0a0a0a',
    'banner_icon' => 'megaphone',
    'email_verify_enabled' => '0',
    'staff_bg_color' => '#f5f7fa',
];

foreach ($defaultSettings as $key => $value) {
    $existing = setting($key, null);
    if ($existing === null) {
        setSetting($key, $value);
        $messages[] = "✓ 已初始化设置: {$key}";
    }
}

$messages[] = '';
$messages[] = '🎉 升级完成！请删除此文件。';

// 输出结果
echo '<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>语云科技 v4 补丁升级</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f7fa; }
        .card { background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        h1 { color: #1f2329; margin: 0 0 20px; font-size: 24px; }
        .msg { padding: 8px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        .msg:last-child { border-bottom: none; }
        .success { color: #52c41a; }
        .error { color: #ff4d4f; }
        .final { margin-top: 20px; padding: 15px; background: #f6ffed; border-radius: 8px; color: #52c41a; font-weight: 600; }
        .brand { color: #ff6a00; }
    </style>
</head>
<body>
    <div class="card">
        <h1><span class="brand">语云科技</span> v4 补丁升级</h1>
        <div class="messages">';
        foreach ($messages as $msg) {
            if (empty($msg)) {
                echo '<div class="msg">&nbsp;</div>';
            } elseif (strpos($msg, '✓') === 0) {
                echo '<div class="msg success">' . htmlspecialchars($msg) . '</div>';
            } elseif (strpos($msg, '✗') === 0) {
                echo '<div class="msg error">' . htmlspecialchars($msg) . '</div>';
            } elseif (strpos($msg, '🎉') === 0) {
                echo '<div class="final">' . htmlspecialchars($msg) . '</div>';
            } else {
                echo '<div class="msg">' . htmlspecialchars($msg) . '</div>';
            }
        }
        echo '</div>
    </div>
</body>
</html>';
