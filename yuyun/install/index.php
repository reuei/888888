<?php
if (!defined('YUYUN_ROOT')) {
    define('YUYUN_ROOT', dirname(__DIR__));
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$installed = file_exists(YUYUN_ROOT . '/data/installed.lock');
if ($installed) {
    die('<p style="text-align:center;padding:40px;font-family:sans-serif">网站已安装，如需重新安装请删除 data/installed.lock 文件。</p>');
}
$step = max(1, min(4, intval($_GET['step'] ?? 1)));
$errors = [];
$success = '';

$checks = [
    'php_version' => PHP_VERSION_ID >= 70400,
    'pdo_sqlite' => extension_loaded('pdo_sqlite'),
    'pdo_mysql' => extension_loaded('pdo_mysql'),
    'data_writable' => is_writable(YUYUN_ROOT . '/data') || (is_dir(YUYUN_ROOT . '/data') === false && is_writable(YUYUN_ROOT)),
    'uploads_writable' => is_writable(YUYUN_ROOT . '/uploads') || (is_dir(YUYUN_ROOT . '/uploads') === false && is_writable(YUYUN_ROOT)),
];

function createTables(PDO $pdo, string $type): void {
    if ($type === 'sqlite') {
        $sql = file_get_contents(__DIR__ . '/schema.sqlite.sql');
    } else {
        $sql = file_get_contents(__DIR__ . '/schema.mysql.sql');
    }
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
        if ($stmt !== '') $pdo->exec($stmt);
    }
}

function seedDefaults(PDO $pdo, string $type): void {
    $settings = [
        ['site_name','语云科技'],
        ['site_slogan','企业与开发者信赖的云计算与数字化服务伙伴'],
        ['site_logo',''],
        ['site_favicon',''],
        ['sales_phone','400-800-8451'],
        ['site_icp','京ICP备XXXXXXXX号'],
        ['site_police','京公网安备XXXXXXXX号'],
        ['site_license','营业执照'],
        ['site_ev_license','电子增值服务产业证'],
        ['company_name','语云科技（美国）有限公司'],
        ['company_short','语云科技'],
        ['company_address','中国北京市朝阳区科技园路88号语云大厦'],
        ['company_phone','400-800-8451'],
        ['company_group','https://t.me/yuyun_official'],
        ['company_intro','语云科技（YuYun Technology）专注于为全球企业与开发者提供安全、稳定、高效的云计算、网络加速与数字化解决方案。'],
        ['company_map_url','https://map.baidu.com/search/北京市朝阳区科技园路88号'],
        ['site_email','noreply@loveym.cloud'],
        ['site_email_from','语云科技 <noreply@loveym.cloud>'],
        ['international_url','https://cloud.loveym.cloud'],
        ['footer_statement','语云科技® 等是我们（语云科技美国有限公司）在中国的注册授权。'],
        ['template','default'],
        ['banner_enabled','1'],
        ['banner_text','欢迎来到语云科技官网！我们致力于为全球企业提供安全、稳定、高效的云计算与数字化服务。'],
        ['banner_bg_color','#0a0a0a'],
        ['banner_icon','bell'],
        ['staff_bg_image',''],
        ['staff_bg_color','#f5f7fa'],
        ['email_verify_enabled','0'],
        ['site_language','zh'],
    ];
    $insertSetting = $type === 'mysql'
        ? 'INSERT IGNORE INTO settings (config_key, config_value) VALUES (:k, :v)'
        : 'INSERT OR IGNORE INTO settings (config_key, config_value) VALUES (:k, :v)';
    $stmt = $pdo->prepare($insertSetting);
    foreach ($settings as [$k, $v]) {
        $stmt->execute([':k' => $k, ':v' => $v]);
    }

    $slides = [
        ['智能云计算平台','为全球业务提供低延迟、高可用的云基础设施','','',1],
        ['企业级网络安全','DDoS 防护 / Web 应用防火墙 / 零信任架构','','',2],
        ['一站式数字化服务','从域名、主机到财务系统的完整生态','','',3],
    ];
    $s = $pdo->prepare('INSERT INTO slides (title, subtitle, image, link, sort_order, is_active) VALUES (:t,:st,:i,:l,:o,1)');
    foreach ($slides as $slide) {
        $s->execute([':t'=>$slide[0],':st'=>$slide[1],':i'=>$slide[2],':l'=>$slide[3],':o'=>$slide[4]]);
    }

    $products = [
        ['云服务器 ECS','弹性计算，分钟级交付，支持多地域部署','云服务器（Elastic Compute Service）提供可扩展的计算能力，支持 Windows/Linux 多种镜像，适用于网站、应用、数据库等场景。','fa-server','',1],
        ['裸金属服务器','专属物理机，性能无虚拟化损耗','为用户提供独享的物理服务器资源，兼具云的弹性与物理机的高性能，适合高负载业务。','fa-microchip','',2],
        ['CDN 内容分发','全球节点，静态动态内容一键加速','通过全球分布的边缘节点将网站内容分发到离用户最近的位置，显著降低访问延迟。','fa-globe','',3],
        ['DDoS 高防 IP','T 级防护，保障业务持续在线','提供大流量 DDoS 攻击清洗能力，支持 SYN Flood、CC 攻击等多种攻击类型防护。','fa-shield-halved','',4],
        ['企业邮箱','安全稳定，树立专业企业形象','支持自定义域名、无限容量、反垃圾反病毒、多终端同步，助力企业高效沟通。','fa-envelope','',5],
        ['魔方财务授权','正版授权，一站式 IDC 财务系统','提供魔方财务（MofangFinance）正版授权与部署服务，帮助 IDC、云服务商快速搭建计费与财务体系。','fa-file-invoice-dollar','',6],
    ];
    $p = $pdo->prepare('INSERT INTO products (name, summary, detail, icon, image, sort_order, is_active) VALUES (:n,:s,:d,:i,:img,:o,1)');
    foreach ($products as $prod) {
        $p->execute([':n'=>$prod[0],':s'=>$prod[1],':d'=>$prod[2],':i'=>$prod[3],':img'=>$prod[4],':o'=>$prod[5]]);
    }

    $partners = [
        ['阿里云','','',1],
        ['腾讯云','','',2],
        ['华为云','','',3],
        ['Cloudflare','','',4],
        ['百度智能云','','',5],
        ['京东云','','',6],
    ];
    $pa = $pdo->prepare('INSERT INTO partners (name, logo, link, sort_order, is_active) VALUES (:n,:l,:lk,:o,1)');
    foreach ($partners as $par) {
        $pa->execute([':n'=>$par[0],':l'=>$par[1],':lk'=>$par[2],':o'=>$par[3]]);
    }

    $staff = [
        ['张宇','创始人 & CEO','','带领团队构建全球化云计算平台',1],
        ['李雯','首席技术官','','负责核心架构与技术创新',2],
        ['王强','运营总监','','保障客户服务与业务增长',3],
        ['陈思','市场总监','','推动品牌建设与全球合作',4],
    ];
    $st = $pdo->prepare('INSERT INTO staff (name, position, avatar, bio, sort_order) VALUES (:n,:p,:a,:b,:o)');
    foreach ($staff as $sf) {
        $st->execute([':n'=>$sf[0],':p'=>$sf[1],':a'=>$sf[2],':b'=>$sf[3],':o'=>$sf[4]]);
    }

    $pdo->prepare('INSERT INTO templates (name, folder, is_active) VALUES ("默认模板","default",1)')->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step === 2) {
        $dbType = $_POST['db_type'] ?? 'sqlite';
        $mysqlHost = trim($_POST['mysql_host'] ?? '');
        $mysqlDb = trim($_POST['mysql_db'] ?? '');
        $mysqlUser = trim($_POST['mysql_user'] ?? '');
        $mysqlPass = $_POST['mysql_pass'] ?? '';
        try {
            if ($dbType === 'sqlite') {
                $dbPath = YUYUN_ROOT . '/data/yuyun.db';
                if (!is_dir(YUYUN_ROOT . '/data')) mkdir(YUYUN_ROOT . '/data', 0775, true);
                $pdo = new PDO('sqlite:' . $dbPath, null, null, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
                $pdo->exec('PRAGMA foreign_keys = ON;');
                createTables($pdo, 'sqlite');
            $config = "<?php\ndefine('DB_TYPE','sqlite');\ndefine('DB_DSN','sqlite:" . str_replace("'","\\'",$dbPath) . "');\ndefine('DB_USER','');\ndefine('DB_PASS','');\n";
        } else {
            if (!$mysqlHost || !$mysqlDb || !$mysqlUser) throw new Exception('请填写完整的 MySQL 信息');
            $pdo = new PDO("mysql:host={$mysqlHost};dbname={$mysqlDb};charset=utf8mb4", $mysqlUser, $mysqlPass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
            createTables($pdo, 'mysql');
            $config = "<?php\ndefine('DB_TYPE','mysql');\ndefine('DB_DSN','mysql:host=" . addslashes($mysqlHost) . ";dbname=" . addslashes($mysqlDb) . ";charset=utf8mb4');\ndefine('DB_USER','" . addslashes($mysqlUser) . "');\ndefine('DB_PASS','" . addslashes($mysqlPass) . "');\n";
        }
        $check = $pdo->query("SELECT COUNT(*) FROM settings WHERE config_key='site_name'")->fetchColumn();
        if (!$check) seedDefaults($pdo, $dbType);
            file_put_contents(YUYUN_ROOT . '/data/config.php', $config);
            $_SESSION['install_db_ok'] = true;
            redirect('?step=3');
        } catch (Throwable $e) {
            $errors[] = '数据库初始化失败：' . $e->getMessage();
        }
    }
    if ($step === 3) {
        if (empty($_SESSION['install_db_ok'])) {
            redirect('?step=2');
        }
        $email = trim($_POST['admin_email'] ?? '');
        $password = $_POST['admin_password'] ?? '';
        $password2 = $_POST['admin_password2'] ?? '';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = '管理员邮箱格式不正确';
        if (strlen($password) < 6) $errors[] = '管理员密码至少 6 位';
        if ($password !== $password2) $errors[] = '两次输入的密码不一致';
        if (empty($errors)) {
            require YUYUN_ROOT . '/data/config.php';
            $pdo = new PDO(DB_DSN, defined('DB_USER') ? DB_USER : null, defined('DB_PASS') ? DB_PASS : null, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $now = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare('INSERT INTO users (email, password, nickname, is_admin, email_verified, created_at) VALUES (:e,:p,:n,1,1,:t)');
            $stmt->execute([':e'=>$email,':p'=>$hash,':n'=>'管理员',':t'=>$now]);
            file_put_contents(YUYUN_ROOT . '/data/installed.lock', date('Y-m-d H:i:s'));
            unset($_SESSION['install_db_ok']);
            redirect('?step=4');
        }
    }
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>语云科技官网安装向导</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:"PingFang SC","Microsoft YaHei",sans-serif;background:#f5f7fa;color:#333;line-height:1.6}
        .wrap{max-width:720px;margin:40px auto;background:#fff;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.08);overflow:hidden}
        .head{background:#000;color:#fff;padding:28px 32px;text-align:center}
        .head h1{font-size:24px;margin-bottom:6px}
        .head p{opacity:.8;font-size:14px}
        .body{padding:32px}
        .steps{display:flex;justify-content:center;margin-bottom:28px}
        .step{flex:1;text-align:center;color:#999;font-size:13px;position:relative;padding-bottom:10px;border-bottom:2px solid #eee}
        .step.active{color:#ff6a00;border-color:#ff6a00;font-weight:600}
        .form-group{margin-bottom:18px}
        label{display:block;margin-bottom:6px;font-weight:500;font-size:14px}
        input[type=text],input[type=email],input[type=password],select{width:100%;padding:10px 12px;border:1px solid #ddd;border-radius:6px;font-size:14px}
        input:focus,select:focus{outline:none;border-color:#ff6a00}
        .btn{display:inline-block;background:#ff6a00;color:#fff;border:none;padding:11px 24px;border-radius:6px;font-size:15px;cursor:pointer;text-decoration:none}
        .btn:hover{background:#e65c00}
        .btn-block{display:block;width:100%;text-align:center}
        .alert{padding:12px 14px;border-radius:6px;margin-bottom:16px;font-size:14px}
        .alert-error{background:#fff2f0;color:#c00;border:1px solid #ffccc7}
        .alert-success{background:#f6ffed;color:#389e0d;border:1px solid #b7eb8f}
        .check-list{list-style:none;margin-bottom:16px}
        .check-list li{padding:10px 0;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center}
        .check-list li:last-child{border-bottom:none}
        .status{font-size:13px;padding:4px 10px;border-radius:20px}
        .ok{background:#f6ffed;color:#389e0d}
        .fail{background:#fff2f0;color:#c00}
        .muted{color:#888;font-size:13px}
        .success{text-align:center;padding:20px 0}
        .success h2{color:#ff6a00;margin-bottom:12px}
        .code{background:#f5f5f5;padding:10px;border-radius:6px;font-family:monospace;font-size:13px;text-align:left;word-break:break-all}
    </style>
</head>
<body>
<div class="wrap">
    <div class="head">
        <h1>语云科技官网安装向导</h1>
        <p>YuYun Enterprise Website Installer</p>
    </div>
    <div class="body">
        <div class="steps">
            <div class="step <?php echo $step==1?'active':'' ?>">1. 环境检测</div>
            <div class="step <?php echo $step==2?'active':'' ?>">2. 数据库配置</div>
            <div class="step <?php echo $step==3?'active':'' ?>">3. 管理员账号</div>
            <div class="step <?php echo $step==4?'active':'' ?>">4. 安装完成</div>
        </div>

        <?php if ($errors): ?>
            <?php foreach ($errors as $er): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($er) ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($step === 1): ?>
            <ul class="check-list">
                <li><span>PHP 版本 >= 7.4（当前 <?php echo PHP_VERSION ?>）</span><span class="status <?php echo $checks['php_version']?'ok':'fail' ?>"><?php echo $checks['php_version']?'通过':'失败' ?></span></li>
                <li><span>PDO SQLite 扩展</span><span class="status <?php echo $checks['pdo_sqlite']?'ok':'fail' ?>"><?php echo $checks['pdo_sqlite']?'通过':'失败' ?></span></li>
                <li><span>PDO MySQL 扩展（可选）</span><span class="status <?php echo $checks['pdo_mysql']?'ok':'fail' ?>"><?php echo $checks['pdo_mysql']?'通过':'未安装' ?></span></li>
                <li><span>data/ 目录可写</span><span class="status <?php echo $checks['data_writable']?'ok':'fail' ?>"><?php echo $checks['data_writable']?'通过':'失败' ?></span></li>
                <li><span>uploads/ 目录可写</span><span class="status <?php echo $checks['uploads_writable']?'ok':'fail' ?>"><?php echo $checks['uploads_writable']?'通过':'失败' ?></span></li>
            </ul>
            <?php if ($checks['php_version'] && $checks['data_writable'] && $checks['uploads_writable']): ?>
                <a href="?step=2" class="btn btn-block">下一步：配置数据库</a>
            <?php else: ?>
                <div class="alert alert-error">请先修复环境后再继续。</div>
            <?php endif; ?>
        <?php elseif ($step === 2): ?>
            <form method="post">
                <div class="form-group">
                    <label>数据库类型</label>
                    <select name="db_type" id="db_type" onchange="toggleDb()">
                        <option value="sqlite">SQLite（推荐，无需 MySQL）</option>
                        <option value="mysql">MySQL</option>
                    </select>
                </div>
                <div id="mysql-fields" style="display:none">
                    <div class="form-group"><label>MySQL 主机</label><input type="text" name="mysql_host" value="localhost"></div>
                    <div class="form-group"><label>数据库名</label><input type="text" name="mysql_db" value="yuyun"></div>
                    <div class="form-group"><label>用户名</label><input type="text" name="mysql_user" value="root"></div>
                    <div class="form-group"><label>密码</label><input type="password" name="mysql_pass"></div>
                </div>
                <p class="muted">选择 SQLite 时，数据库文件将保存在 data/yuyun.db，无需额外配置。</p>
                <button type="submit" class="btn btn-block">初始化数据库</button>
            </form>
            <script>
                function toggleDb(){
                    document.getElementById('mysql-fields').style.display = document.getElementById('db_type').value === 'mysql' ? 'block' : 'none';
                }
            </script>
        <?php elseif ($step === 3): ?>
            <form method="post">
                <div class="form-group"><label>管理员邮箱</label><input type="email" name="admin_email" required></div>
                <div class="form-group"><label>管理员密码</label><input type="password" name="admin_password" required minlength="6"></div>
                <div class="form-group"><label>确认密码</label><input type="password" name="admin_password2" required minlength="6"></div>
                <button type="submit" class="btn btn-block">完成安装</button>
            </form>
        <?php elseif ($step === 4): ?>
            <div class="success">
                <h2>安装完成！</h2>
                <p style="margin-bottom:12px">为了安全，请立即删除或重命名以下目录：</p>
                <div class="code"><?php echo htmlspecialchars(YUYUN_ROOT . '/install/') ?></div>
                <p style="margin-top:16px"><a href="../admin/login.php" class="btn">进入后台登录</a> <a href="../index.php" class="btn" style="background:#333;margin-left:10px">查看网站首页</a></p>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
