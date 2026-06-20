<?php
/**
 * 语云科技官网安装程序
 */

if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    die('PHP 版本需要 >= 7.4.0');
}

$root = __DIR__;
$installedLock = $root . '/data/.installed';

if (file_exists($installedLock) && file_exists($root . '/config.php')) {
    require_once $root . '/config.php';
    if (defined('INSTALLED') && INSTALLED) {
        die('网站已安装，如需重新安装请删除 data/.installed 文件。');
    }
}

$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$error = '';
$success = '';

// 检测扩展
$hasPdo = class_exists('PDO');
$hasSqlite = extension_loaded('pdo_sqlite');
$hasMysql = extension_loaded('pdo_mysql');
$hasJson = true;

// 目录可写
$writable = [
    'data' => is_writable($root . '/data') || @mkdir($root . '/data', 0755, true),
    'uploads' => is_writable($root . '/uploads') || @mkdir($root . '/uploads', 0755, true),
    'config.php' => is_writable($root) || is_writable($root . '/config.php'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 2) {
        $dbType = $_POST['db_type'] ?? 'sqlite';
        $dbHost = trim($_POST['db_host'] ?? '');
        $dbPort = trim($_POST['db_port'] ?? '3306');
        $dbName = trim($_POST['db_name'] ?? '');
        $dbUser = trim($_POST['db_user'] ?? '');
        $dbPass = $_POST['db_pass'] ?? '';

        // 测试连接
        if ($dbType === 'sqlite') {
            $dbFile = $root . '/data/yuyun.db';
            try {
                $pdo = new PDO('sqlite:' . $dbFile);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $e) {
                $error = 'SQLite 连接失败：' . $e->getMessage();
            }
        } elseif ($dbType === 'mysql') {
            try {
                $dsn = 'mysql:host=' . $dbHost . ';port=' . $dbPort . ';dbname=' . $dbName . ';charset=utf8mb4';
                $pdo = new PDO($dsn, $dbUser, $dbPass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (Exception $e) {
                $error = 'MySQL 连接失败：' . $e->getMessage();
            }
        } elseif ($dbType === 'json') {
            $jsonDir = $root . '/data/json';
            if (!is_dir($jsonDir) && !@mkdir($jsonDir, 0755, true)) {
                $error = '无法创建 JSON 数据目录';
            }
        } else {
            $error = '未知的数据库类型';
        }

        if (!$error) {
            // 写入配置文件
            $configContent = "<?php\n";
            $configContent .= "if (!defined('YUYUN_ROOT')) {\n";
            $configContent .= "    define('YUYUN_ROOT', __DIR__);\n";
            $configContent .= "}\n\n";
            $configContent .= "define('DB_TYPE', " . var_export($dbType, true) . ");\n";
            $configContent .= "define('DB_HOST', " . var_export($dbHost, true) . ");\n";
            $configContent .= "define('DB_PORT', " . var_export($dbPort, true) . ");\n";
            if ($dbType === 'sqlite') {
                $configContent .= "define('DB_NAME', YUYUN_ROOT . '/data/yuyun.db');\n";
            } else {
                $configContent .= "define('DB_NAME', " . var_export($dbName, true) . ");\n";
            }
            $configContent .= "define('DB_USER', " . var_export($dbUser, true) . ");\n";
            $configContent .= "define('DB_PASS', " . var_export($dbPass, true) . ");\n";
            $configContent .= "define('DB_CHARSET', 'utf8mb4');\n\n";
            $configContent .= "define('INSTALLED', false);\n";

            if (!@file_put_contents($root . '/config.php', $configContent)) {
                $error = '无法写入 config.php，请检查目录权限';
            } else {
                header('Location: install.php?step=3');
                exit;
            }
        }
    } elseif ($step === 3) {
        $adminUser = trim($_POST['admin_user'] ?? '');
        $adminPass = $_POST['admin_pass'] ?? '';
        $adminPass2 = $_POST['admin_pass2'] ?? '';

        if (empty($adminUser) || empty($adminPass)) {
            $error = '请填写管理员账号和密码';
        } elseif ($adminPass !== $adminPass2) {
            $error = '两次输入的密码不一致';
        } elseif (strlen($adminPass) < 6) {
            $error = '密码长度至少 6 位';
        } else {
            require_once $root . '/includes/db.php';
            $db = YuyunDB::getInstance();
            $db->initTables();

            // 创建管理员
            $hash = password_hash($adminPass, PASSWORD_DEFAULT);
            if ($db->getType() === 'json') {
                $db->jsonInsert('admins', ['username' => $adminUser, 'password' => $hash]);
            } else {
                $db->execute("INSERT INTO admins (username, password) VALUES (?, ?)", [$adminUser, $hash]);
            }

            // 初始化默认配置
            seedDefaults($db);

            // 标记安装完成
            $configContent = file_get_contents($root . '/config.php');
            $configContent = str_replace("define('INSTALLED', false);", "define('INSTALLED', true);", $configContent);
            file_put_contents($root . '/config.php', $configContent);
            file_put_contents($installedLock, date('Y-m-d H:i:s'));

            $success = '安装成功！请删除或重命名 install.php 以保证安全。后台地址：/admin/ 账号：' . htmlspecialchars($adminUser);
        }
    }
}

function seedDefaults($db) {
    $defaults = [
        ['site_title', '语云科技 - 中国企业官网'],
        ['site_keywords', '语云科技,云服务器,企业官网,云计算,数据中心'],
        ['site_description', '语云科技中国企业官网，提供云计算、服务器、数据中心等企业级服务。'],
        ['company_name', '语云科技美国有限公司'],
        ['company_address', '中国北京市朝阳区建国路88号SOHO现代城'],
        ['company_intro', '语云科技（YuYun Technology）是全球领先的云计算与数据中心服务提供商，致力于为企业客户提供安全、稳定、高效的云服务解决方案。'],
        ['sales_phone', '400-800-8451'],
        ['service_phone', '400-800-8451'],
        ['company_email', 'contact@yuyun.cloud'],
        ['group_chat', '#'],
        ['icp', '京ICP备12345678号-1'],
        ['icp_gongan', '京公网安备11010502030405号'],
        ['license', '增值电信业务经营许可证 B1-20240001'],
        ['footer_text', '语云科技®等是我们（语云科技美国有限公司）在中国的注册授权'],
        ['map_type', 'baidu'],
        ['map_key', ''],
        ['map_lat', '39.9042'],
        ['map_lng', '116.4074'],
        ['current_template', 'default'],
        ['popup_enabled', '1'],
        ['popup_title', '欢迎访问语云科技'],
        ['popup_content', '我们为您提供全球领先的云计算与数据中心服务，立即咨询获取专属方案。'],
        ['international_url', 'https://cloud.loveym.cloud'],
    ];

    foreach ($defaults as $item) {
        if ($db->getType() === 'json') {
            $rows = $db->jsonAll('settings', 'id', 'ASC');
            $found = false;
            foreach ($rows as &$row) {
                if ($row['s_key'] === $item[0]) {
                    $row['s_value'] = $item[1];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $rows[] = ['id' => 0, 's_key' => $item[0], 's_value' => $item[1], 'updated_at' => date('Y-m-d H:i:s')];
            }
            file_put_contents(__DIR__ . '/data/json/settings.json', json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
        } else {
            $existing = $db->queryOne("SELECT id FROM settings WHERE s_key = ?", [$item[0]]);
            if ($existing) {
                $db->execute("UPDATE settings SET s_value = ? WHERE s_key = ?", [$item[1], $item[0]]);
            } else {
                $db->execute("INSERT INTO settings (s_key, s_value) VALUES (?, ?)", [$item[0], $item[1]]);
            }
        }
    }

    // 默认轮播图
    $slides = [
        ['title' => '语云科技 智领云端', 'subtitle' => '全球分布式云计算基础设施，助力企业数字化转型', 'image' => '', 'link' => '?page=products', 'btn_text' => '了解产品', 'sort_order' => 1, 'is_active' => 1],
        ['title' => '安全稳定 值得信赖', 'subtitle' => '多节点冗余、DDoS 防护、7×24 小时运维保障', 'image' => '', 'link' => '?page=contact', 'btn_text' => '立即咨询', 'sort_order' => 2, 'is_active' => 1],
    ];
    foreach ($slides as $s) {
        if ($db->getType() === 'json') {
            $db->jsonInsert('slides', $s);
        } else {
            $db->execute("INSERT INTO slides (title, subtitle, image, link, btn_text, sort_order, is_active) VALUES (?,?,?,?,?,?,?)", array_values($s));
        }
    }

    // 默认产品
    $products = [
        ['icon' => 'fa-server', 'title' => '云服务器', 'summary' => '弹性可扩展的云服务器，支持多种实例规格。', 'detail' => '语云科技云服务器提供高性能计算资源，支持按需扩展，适用于 Web 应用、大数据、AI 训练等场景。', 'image' => '', 'sort_order' => 1, 'is_active' => 1],
        ['icon' => 'fa-database', 'title' => '云数据库', 'summary' => '高可用、自动备份的关系型数据库服务。', 'detail' => '支持 MySQL、PostgreSQL、Redis 等多种数据库引擎，提供自动备份、监控告警、读写分离能力。', 'image' => '', 'sort_order' => 2, 'is_active' => 1],
        ['icon' => 'fa-shield-halved', 'title' => '云安全', 'summary' => 'DDoS 防护、WAF、漏洞扫描一体化安全方案。', 'detail' => '构建纵深防御体系，提供流量清洗、Web 应用防火墙、主机安全、数据加密等全方位保护。', 'image' => '', 'sort_order' => 3, 'is_active' => 1],
        ['icon' => 'fa-network-wired', 'title' => 'CDN 加速', 'summary' => '全球节点分发，降低延迟，提升访问体验。', 'detail' => '覆盖全球的 CDN 节点，智能调度，支持静态加速、动态加速、HTTPS、视频点播等场景。', 'image' => '', 'sort_order' => 4, 'is_active' => 1],
        ['icon' => 'fa-building', 'title' => '数据中心', 'summary' => 'T3+ 标准数据中心，提供机柜托管与专线接入。', 'detail' => '自建及合作运营多个 Tier3+ 数据中心，提供机柜租用、带宽租赁、专线互联、混合云组网服务。', 'image' => '', 'sort_order' => 5, 'is_active' => 1],
        ['icon' => 'fa-headset', 'title' => '企业运维', 'summary' => '7×24 小时专业技术支持与运维服务。', 'detail' => '资深运维团队提供全天候技术支持、故障响应、系统优化、灾备演练等企业级服务。', 'image' => '', 'sort_order' => 6, 'is_active' => 1],
    ];
    foreach ($products as $p) {
        if ($db->getType() === 'json') {
            $db->jsonInsert('products', $p);
        } else {
            $db->execute("INSERT INTO products (icon, title, summary, detail, image, sort_order, is_active) VALUES (?,?,?,?,?,?,?)", array_values($p));
        }
    }

    // 默认合作伙伴
    $partners = [
        ['name' => '腾讯云', 'logo' => '', 'link' => '#', 'sort_order' => 1, 'is_active' => 1],
        ['name' => 'Cloudflare', 'logo' => '', 'link' => '#', 'sort_order' => 2, 'is_active' => 1],
        ['name' => '华为云', 'logo' => '', 'link' => '#', 'sort_order' => 3, 'is_active' => 1],
        ['name' => '阿里云', 'logo' => '', 'link' => '#', 'sort_order' => 4, 'is_active' => 1],
        ['name' => '魔方财务', 'logo' => '', 'link' => '#', 'sort_order' => 5, 'is_active' => 1],
        ['name' => '中国电信', 'logo' => '', 'link' => '#', 'sort_order' => 6, 'is_active' => 1],
    ];
    foreach ($partners as $p) {
        if ($db->getType() === 'json') {
            $db->jsonInsert('partners', $p);
        } else {
            $db->execute("INSERT INTO partners (name, logo, link, sort_order, is_active) VALUES (?,?,?,?,?)", array_values($p));
        }
    }

    // 默认证书
    $certs = [
        ['name' => '营业执照', 'image' => '', 'description' => '语云科技美国有限公司营业执照', 'sort_order' => 1],
        ['name' => '增值电信业务经营许可证', 'image' => '', 'description' => '中华人民共和国增值电信业务经营许可证', 'sort_order' => 2],
    ];
    foreach ($certs as $c) {
        if ($db->getType() === 'json') {
            $db->jsonInsert('certificates', $c);
        } else {
            $db->execute("INSERT INTO certificates (name, image, description, sort_order) VALUES (?,?,?,?)", array_values($c));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>语云科技官网安装程序</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "PingFang SC", "Microsoft YaHei", sans-serif; background: #f5f7fa; color: #333; line-height: 1.6; }
        .container { max-width: 720px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 40px; }
        h1 { font-size: 24px; margin-bottom: 10px; color: #0066FF; }
        .subtitle { color: #86909C; margin-bottom: 30px; }
        .step { display: flex; margin-bottom: 30px; }
        .step-item { flex: 1; text-align: center; padding: 10px; border-bottom: 2px solid #e5e6eb; color: #86909C; font-size: 14px; }
        .step-item.active { border-color: #0066FF; color: #0066FF; font-weight: 600; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 14px; }
        input, select, textarea { width: 100%; padding: 10px 12px; border: 1px solid #d9d9d9; border-radius: 6px; font-size: 14px; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #0066FF; }
        .hint { font-size: 12px; color: #86909C; margin-top: 4px; }
        .btn { display: inline-block; padding: 12px 32px; background: #0066FF; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 15px; }
        .btn:hover { background: #0052cc; }
        .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; }
        .alert-danger { background: #fff2f0; color: #cf1322; border: 1px solid #ffccc7; }
        .alert-success { background: #f6ffed; color: #389e0d; border: 1px solid #b7eb8f; }
        .check-list { margin: 20px 0; }
        .check-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0; }
        .check-item.ok { color: #389e0d; }
        .check-item.fail { color: #cf1322; }
        .hidden { display: none; }
    </style>
</head>
<body>
<div class="container">
    <h1>语云科技官网安装向导</h1>
    <p class="subtitle">请在继续前确保服务器满足以下条件</p>

    <div class="step">
        <div class="step-item <?php echo $step == 1 ? 'active' : ($step > 1 ? 'active' : ''); ?>">1. 环境检测</div>
        <div class="step-item <?php echo $step == 2 ? 'active' : ($step > 2 ? 'active' : ''); ?>">2. 数据库配置</div>
        <div class="step-item <?php echo $step == 3 ? 'active' : ($step > 3 ? 'active' : ''); ?>">3. 创建管理员</div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($step === 1): ?>
        <div class="check-list">
            <div class="check-item <?php echo version_compare(PHP_VERSION, '7.4.0', '>=') ? 'ok' : 'fail'; ?>">
                <span>PHP 版本 (>= 7.4.0)</span>
                <span><?php echo PHP_VERSION; ?></span>
            </div>
            <div class="check-item <?php echo $hasPdo ? 'ok' : 'fail'; ?>">
                <span>PDO 扩展</span>
                <span><?php echo $hasPdo ? '已安装' : '未安装'; ?></span>
            </div>
            <div class="check-item <?php echo $hasSqlite ? 'ok' : 'fail'; ?>">
                <span>PDO SQLite 扩展</span>
                <span><?php echo $hasSqlite ? '已安装' : '未安装'; ?></span>
            </div>
            <div class="check-item <?php echo $hasMysql ? 'ok' : 'fail'; ?>">
                <span>PDO MySQL 扩展</span>
                <span><?php echo $hasMysql ? '已安装' : '未安装'; ?></span>
            </div>
            <?php foreach ($writable as $k => $v): ?>
                <div class="check-item <?php echo $v ? 'ok' : 'fail'; ?>">
                    <span><?php echo $k; ?> 可写</span>
                    <span><?php echo $v ? '是' : '否'; ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <form method="get" action="">
            <input type="hidden" name="step" value="2">
            <button type="submit" class="btn" <?php echo (array_sum($writable) < count($writable)) ? 'disabled style="background:#999;cursor:not-allowed;"' : ''; ?>>下一步：配置数据库</button>
        </form>
    <?php elseif ($step === 2): ?>
        <form method="post" action="?step=2" id="dbForm">
            <div class="form-group">
                <label>数据存储方式</label>
                <select name="db_type" id="dbType" onchange="toggleDb()">
                    <option value="sqlite" <?php echo $hasSqlite ? 'selected' : 'disabled'; ?>>SQLite（推荐，虚拟主机常用）</option>
                    <option value="mysql" <?php echo !$hasSqlite && $hasMysql ? 'selected' : ''; ?>>MySQL</option>
                    <option value="json">JSON 文件（无需数据库扩展）</option>
                </select>
                <div class="hint">SQLite 为单文件数据库，无需额外配置，推荐普通虚拟主机使用。</div>
            </div>
            <div id="mysqlFields" class="hidden">
                <div class="form-group">
                    <label>数据库主机</label>
                    <input type="text" name="db_host" value="localhost">
                </div>
                <div class="form-group">
                    <label>数据库端口</label>
                    <input type="text" name="db_port" value="3306">
                </div>
                <div class="form-group">
                    <label>数据库名</label>
                    <input type="text" name="db_name" value="yuyun">
                </div>
                <div class="form-group">
                    <label>数据库用户名</label>
                    <input type="text" name="db_user" value="">
                </div>
                <div class="form-group">
                    <label>数据库密码</label>
                    <input type="password" name="db_pass" value="">
                </div>
            </div>
            <button type="submit" class="btn">下一步：创建管理员</button>
        </form>
        <script>
            function toggleDb() {
                document.getElementById('mysqlFields').style.display = document.getElementById('dbType').value === 'mysql' ? 'block' : 'none';
            }
            toggleDb();
        </script>
    <?php elseif ($step === 3): ?>
        <form method="post" action="?step=3">
            <div class="form-group">
                <label>管理员账号</label>
                <input type="text" name="admin_user" value="admin" required>
            </div>
            <div class="form-group">
                <label>管理员密码</label>
                <input type="password" name="admin_pass" required>
            </div>
            <div class="form-group">
                <label>确认密码</label>
                <input type="password" name="admin_pass2" required>
            </div>
            <button type="submit" class="btn">完成安装</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
