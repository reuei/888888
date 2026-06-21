<?php
/**
 * 语云科技企业官网 - 安装程序
 * 首次部署时运行此文件进行初始化配置
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 定义根目录
define('YUYUN_ROOT', dirname(__FILE__));
define('DATA_PATH', YUYUN_ROOT . '/data/');
define('UPLOADS_PATH', YUYUN_ROOT . '/uploads/');

// 检查是否已安装
if (file_exists(DATA_PATH . 'config.json') && file_get_contents(DATA_PATH . 'config.json')) {
    $config = json_decode(file_get_contents(DATA_PATH . 'config.json'), true);
    if (!empty($config['installed'])) {
        $installed = true;
    } else {
        $installed = false;
    }
} else {
    $installed = false;
}

$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$error = '';
$success = '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = isset($_POST['step']) ? intval($_POST['step']) : 1;

    switch ($step) {
        case 1:
            // 环境检查通过，进入下一步
            header('Location: install.php?step=2');
            exit;

        case 2:
            // 数据库配置
            $_SESSION['install_db'] = [
                'db_host' => trim($_POST['db_host'] ?? ''),
                'db_name' => trim($_POST['db_name'] ?? ''),
                'db_user' => trim($_POST['db_user'] ?? ''),
                'db_pass' => trim($_POST['db_pass'] ?? ''),
                'use_mysql' => !empty($_POST['use_mysql'])
            ];

            if ($_SESSION['install_db']['use_mysql']) {
                // 测试数据库连接
                try {
                    $pdo = new PDO(
                        "mysql:host={$_SESSION['install_db']['db_host']};charset=utf8mb4",
                        $_SESSION['install_db']['db_user'],
                        $_SESSION['install_db']['db_pass']
                    );

                    // 创建数据库
                    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$_SESSION['install_db']['db_name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $success = '数据库连接成功！';
                } catch (PDOException $e) {
                    $error = '数据库连接失败：' . $e->getMessage();
                    break;
                }
            }

            header('Location: install.php?step=3');
            exit;

        case 3:
            // 站点配置
            $siteConfig = [
                'site_name' => trim($_POST['site_name'] ?? '语云科技'),
                'site_url' => trim($_POST['site_url'] ?? ''),
                'admin_email' => trim($_POST['admin_email'] ?? ''),
                'admin_password' => trim($_POST['admin_password'] ?? ''),
                'template' => 'default',
                'installed' => true,
                'install_time' => date('Y-m-d H:i:s'),
                'version' => '1.0.0'
            ];

            // 邮件配置
            if (!empty($_POST['smtp_host'])) {
                $siteConfig['smtp_host'] = trim($_POST['smtp_host']);
                $siteConfig['smtp_port'] = intval($_POST['smtp_port'] ?? 465);
                $siteConfig['smtp_user'] = trim($_POST['smtp_user'] ?? '');
                $siteConfig['smtp_pass'] = trim($_POST['smtp_pass'] ?? '');
                $siteConfig['smtp_from'] = trim($_POST['smtp_from'] ?? '');
            }

            // 保存配置
            if (!is_dir(DATA_PATH)) {
                mkdir(DATA_PATH, 0755, true);
            }

            file_put_contents(DATA_PATH . 'config.json', json_encode($siteConfig, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            // 保存数据库配置
            $dbConfig = "<?php\n/**\n * 语云科技 - 数据库配置\n * 安装时自动生成\n */\n\n";
            $dbConfig .= "return [\n";

            if (!empty($_SESSION['install_db']['use_mysql'])) {
                $dbConfig .= "    'db_host' => '{$_SESSION['install_db']['db_host']}',\n";
                $dbConfig .= "    'db_name' => '{$_SESSION['install_db']['db_name']}',\n";
                $dbConfig .= "    'db_user' => '{$_SESSION['install_db']['db_user']}',\n";
                $dbConfig .= "    'db_pass' => '{$_SESSION['install_db']['db_pass']}',\n";
            } else {
                $dbConfig .= "    'db_host' => '',\n";
                $dbConfig .= "    'db_name' => '',\n";
                $dbConfig .= "    'db_user' => '',\n";
                $dbConfig .= "    'db_pass' => '',\n";
            }

            $dbConfig .= "];\n";
            file_put_contents(YUYUN_ROOT . '/config.php', $dbConfig);

            // 初始化数据文件
            init_data_files();

            // 如果使用MySQL，创建管理员账号和表结构
            if (!empty($_SESSION['install_db']['use_mysql'])) {
                require_once YUYUN_ROOT . '/core/Database.php';
                $db = Database::getInstance();
                $db->initTables();

                // 创建默认管理员
                $db->insert('users', [
                    'email' => $siteConfig['admin_email'],
                    'password' => password_hash($siteConfig['admin_password'], PASSWORD_DEFAULT),
                    'name' => '超级管理员',
                    'role' => 'admin',
                    'status' => 'active',
                    'email_verified' => 1
                ]);
            }

            // 创建必要目录
            $dirs = [
                UPLOADS_PATH . 'images/',
                UPLOADS_PATH . 'templates/',
                UPLOADS_PATH . 'patches/',
                DATA_PATH . 'logs/'
            ];
            foreach ($dirs as $dir) {
                if (!is_dir($dir)) {
                    mkdir($dir, 0755, true);
                }
            }

            // 尝试删除安装文件(可选)
            // @unlink(__FILE__);

            header('Location: install.php?step=4');
            exit;
    }
}

/**
 * 初始化默认数据文件
 */
function init_data_files() {
    // 轮播图数据
    $banners = [
        ['id' => 1, 'title' => '语云科技 - 全球领先的云计算服务提供商', 'subtitle' => '为企业提供安全、稳定、高效的云服务解决方案', 'image' => '/assets/img/banner/banner1.jpg', 'link' => '', 'sort_order' => 1, 'status' => 1],
        ['id' => 2, 'title' => '全球节点覆盖', 'subtitle' => '中东、欧洲、亚洲、北美、澳洲等多地区数据中心', 'image' => '/assets/img/banner/banner2.jpg', 'link' => '/products.php', 'sort_order' => 2, 'status' => 1],
        ['id' => 3, 'title' => '7x24小时技术支持', 'subtitle' => '专业团队为您提供全天候技术服务与支持', 'image' => '/assets/img/banner/banner3.jpg', 'link' => '/contact.php', 'sort_order' => 3, 'status' => 1],
        ['id' => 4, 'title' => '企业级安全防护', 'subtitle' => '多层安全架构，DDoS防护，数据加密存储', 'image' => '/assets/img/banner/banner4.jpg', 'link' => '/products.php', 'sort_order' => 4, 'status' => 1]
    ];
    file_put_contents(DATA_PATH . 'banners.json', json_encode($banners, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    // 产品数据
    $products = [
        ['id' => 1, 'name' => '云服务器 ECS', 'description' => '高性能、安全稳定的弹性云计算服务，按需付费，弹性伸缩', 'icon' => 'fa-server', 'price' => 0, 'features' => json_encode(['弹性配置', '快速部署', '安全隔离', '多种操作系统']), 'status' => 'active', 'sort_order' => 1],
        ['id' => 2, 'name' => '云数据库 RDS', 'description' => '即开即用、稳定可靠的云端数据库服务，支持MySQL、PostgreSQL等', 'icon' => 'fa-database', 'price' => 0, 'features' => json_encode(['自动备份', '高可用', '读写分离', '监控告警']), 'status' => 'active', 'sort_order' => 2],
        ['id' => 3, 'name' => 'CDN加速服务', 'description' => '全球加速网络，智能调度，让您的网站访问速度提升300%', 'icon' => 'fa-globe', 'price' => 0, 'features' => json_encode(['全球节点', '智能DNS', 'HTTPS支持', '实时统计']), 'status' => 'active', 'sort_order' => 3],
        ['id' => 4, 'name' => '对象存储 OSS', 'description' => '海量、安全、低成本的云端存储服务，适用于各类场景', 'icon' => 'fa-cloud-upload-alt', 'price' => 0, 'features' => json_encode(['无限容量', '99.999%可用性', '多地域容灾', 'API接口']), 'status' => 'active', 'sort_order' => 4],
        ['id' => 5, 'name' => '域名注册服务', 'description' => '提供全球主流域名后缀的注册与管理服务，价格透明', 'icon' => 'fa-globe-asia', 'price' => 0, 'features' => json_encode(['批量管理', 'DNS解析', 'SSL证书', '域名转出']), 'status' => 'active', 'sort_order' => 5],
        ['id' => 6, 'name' => 'DDoS防护', 'description' => '企业级DDoS攻击防护服务，Tbps级防御能力', 'icon' => 'fa-shield-alt', 'price' => 0, 'features' => json_encode(['Tbps级防御', 'AI智能清洗', '7x24监控', '专家支持']), 'status' => 'active', 'sort_order' => 6]
    ];
    file_put_contents(DATA_PATH . 'products.json', json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    // 合作伙伴数据
    $partners = [
        ['id' => 1, 'name' => '腾讯云', 'logo_url' => '/assets/img/partner/tencent.png', 'link_url' => 'https://cloud.tencent.com', 'sort_order' => 1, 'status' => 1],
        ['id' => 2, 'name' => '阿里云', 'logo_url' => '/assets/img/partner/alibaba.png', 'link_url' => 'https://www.aliyun.com', 'sort_order' => 2, 'status' => 1],
        ['id' => 3, 'name' => '华为云', 'logo_url' => '/assets/img/partner/huawei.png', 'link_url' => 'https://www.huaweicloud.com', 'sort_order' => 3, 'status' => 1],
        ['id' => 4, 'name' => 'Cloudflare', 'logo_url' => '/assets/img/partner/cloudflare.png', 'link_url' => 'https://www.cloudflare.com', 'sort_order' => 4, 'status' => 1],
        ['id' => 5, 'name' => 'AWS', 'logo_url' => '/assets/img/partner/aws.png', 'link_url' => 'https://aws.amazon.com', 'sort_order' => 5, 'status' => 1],
        ['id' => 6, 'name' => 'Google Cloud', 'logo_url' => '/assets/img/partner/google.png', 'link_url' => 'https://cloud.google.com', 'sort_order' => 6, 'status' => 1],
        ['id' => 7, 'name' => 'Microsoft Azure', 'logo_url' => '/assets/img/partner/azure.png', 'link_url' => 'https://azure.microsoft.com', 'sort_order' => 7, 'status' => 1],
        ['id' => 8, 'name' => '百度智能云', 'logo_url' => '/assets/img/partner/baidu.png', 'link_url' => 'https://cloud.baidu.com', 'sort_order' => 8, 'status' => 1]
    ];
    file_put_contents(DATA_PATH . 'partners.json', json_encode($partners, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    // 员工数据
    $staff = [
        ['id' => 1, 'name' => '张明远', 'position' => '首席执行官 CEO', 'avatar' => '/assets/img/staff/staff1.png', 'bio' => '拥有15年互联网行业经验，曾任职于多家世界500强企业', 'social_link' => '', 'sort_order' => 1, 'status' => 1],
        ['id' => 2, 'name' => '李思琪', 'position' => '首席技术官 CTO', 'avatar' => '/assets/img/staff/staff2.png', 'bio' => '资深云计算架构师，主导多个大型分布式系统设计', 'social_link' => '', 'sort_order' => 2, 'status' => 1],
        ['id' => 3, 'name' => '王浩然', 'position' => '产品总监', 'avatar' => '/assets/img/staff/staff3.png', 'bio' => '专注于企业级SaaS产品设计，用户体验专家', 'social_link' => '', 'sort_order' => 3, 'status' => 1],
        ['id' => 4, 'name' => '陈雨晴', 'position' => '运营总监 COO', 'avatar' => '/assets/img/staff/staff4.png', 'bio' => '精通国际市场拓展，成功带领团队开拓海外业务', 'social_link' => '', 'sort_order' => 4, 'status' => 1]
    ];
    file_put_contents(DATA_PATH . 'staff.json', json_encode($staff, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    // 工单数据(空)
    file_put_contents(DATA_PATH . 'tickets.json', json_encode([], JSON_PRETTY_PRINT));
    file_put_contents(DATA_PATH . 'ticket_replies.json', json_encode([], JSON_PRETTY_PRINT));
    file_put_contents(DATA_PATH . 'users.json', json_encode([], JSON_PRETTY_PRINT));
    file_put_contents(DATA_PATH . 'feedback.json', json_encode([], JSON_PRETTY_PRINT));

    // 友情链接
    $links = [
        ['id' => 1, 'name' => '腾讯云', 'url' => 'https://cloud.tencent.com', 'sort_order' => 1, 'status' => 1],
        ['id' => 2, 'name' => '阿里云', 'url' => 'https://www.aliyun.com', 'sort_order' => 2, 'status' => 1],
        ['id' => 3, 'name' => 'Cloudflare', 'url' => 'https://www.cloudflare.com', 'sort_order' => 3, 'status' => 1]
    ];
    file_put_contents(DATA_PATH . 'links.json', json_encode($links, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    // 资质证书
    $certificates = [
        ['id' => 1, 'name' => '营业执照', 'image' => '/assets/img/certificate/license.jpg', 'sort_order' => 1, 'status' => 1],
        ['id' => 2, 'name' => '增值电信业务经营许可证', 'image' => '/assets/img/certificate/icp.jpg', 'sort_order' => 2, 'status' => 1],
        ['id' => 3, 'name' => 'ISO27001信息安全认证', 'image' => '/assets/img/certificate/iso27001.jpg', 'sort_order' => 3, 'status' => 1]
    ];
    file_put_contents(DATA_PATH . 'certificates.json', json_encode($certificates, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

/**
 * 环境检查函数
 */
function check_env() {
    $results = [];

    // PHP版本
    $phpVersion = PHP_VERSION;
    $results['php_version'] = [
        'name' => 'PHP版本',
        'value' => $phpVersion,
        'required' => '>= 7.4.0',
        'pass' => version_compare($phpVersion, '7.4.0', '>=')
    ];

    // PDO扩展
    $results['pdo'] = [
        'name' => 'PDO扩展',
        'value' => extension_loaded('pdo') && extension_loaded('pdo_mysql') ? '已安装' : '未安装',
        'required' => '需要安装',
        'pass' => extension_loaded('pdo')
    ];

    // GD库
    $results['gd'] = [
        'name' => 'GD库',
        'value' => extension_loaded('gd') ? '已安装' : '未安装',
        'required' => '建议安装(图片处理)',
        'pass' => true // 非必须
    ];

    // MBString
    $results['mbstring'] = [
        'name' => 'MBString扩展',
        'value' => extension_loaded('mbstring') ? '已安装' : '未安装',
        'required' => '需要安装',
        'pass' => extension_loaded('mbstring')
    ];

    // JSON扩展
    $results['json'] = [
        'name' => 'JSON扩展',
        'value' => extension_loaded('json') ? '已安装' : '未安装',
        'required' => '需要安装',
        'pass' => extension_loaded('json')
    ];

    // Session
    $results['session'] = [
        'name' => 'Session支持',
        'value' => function_exists('session_start') ? '正常' : '异常',
        'required' => '需要支持',
        'pass' => function_exists('session_start')
    ];

    // 目录权限
    $dirs = [
        'data/' => DATA_PATH,
        'uploads/' => UPLOADS_PATH,
        'core/' => YUYUN_ROOT . '/core/'
    ];

    foreach ($dirs as $name => $path) {
        $writable = is_dir($path) ? is_writable($path) : @mkdir($path, 0755, true);
        $results['dir_' . str_replace('/', '_', $name)] = [
            'name' => $name . ' 目录写入权限',
            'value' => $writable ? '可写' : '不可写',
            'required' => '需要可写权限',
            'pass' => $writable
        ];
    }

    return $results;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>语云科技 - 安装向导</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Noto Sans SC', sans-serif;
            background: linear-gradient(135deg, #0a1628 0%, #0066CC 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .install-container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 800px;
            overflow: hidden;
        }
        .install-header {
            background: linear-gradient(135deg, #0066CC, #00A8E8);
            color: #fff;
            padding: 40px;
            text-align: center;
        }
        .install-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .install-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        .install-body {
            padding: 40px;
        }
        .steps {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
        }
        .step-item {
            display: flex;
            align-items: center;
            color: #ccc;
            font-size: 14px;
        }
        .step-num {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            font-weight: 600;
            font-size: 13px;
        }
        .step-item.active { color: #0066CC; }
        .step-item.active .step-num {
            border-color: #0066CC;
            background: #0066CC;
            color: #fff;
        }
        .step-item.done { color: #22c55e; }
        .step-item.done .step-num {
            border-color: #22c55e;
            background: #22c55e;
            color: #fff;
        }
        .step-line {
            width: 60px;
            height: 2px;
            background: #ddd;
            margin: 0 12px;
        }
        .step-item.done + .step-line,
        .step-line.done {
            background: #22c55e;
        }
        .form-group {
            margin-bottom: 24px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #374151;
            font-size: 14px;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            outline: none;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #0066CC;
            box-shadow: 0 0 0 3px rgba(0,102,204,0.1);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 32px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #0066CC, #00A8E8);
            color: #fff;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,102,204,0.35);
        }
        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }
        .btn-secondary:hover {
            background: #e5e7eb;
        }
        .env-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .env-table th,
        .env-table td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }
        .env-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        .status-pass {
            color: #22c55e;
            font-weight: 600;
        }
        .status-fail {
            color: #ef4444;
            font-weight: 600;
        }
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }
        .alert-error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        .alert-success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .success-icon {
            text-align: center;
            padding: 40px 0;
        }
        .success-icon .icon {
            width: 80px;
            height: 80px;
            background: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: #fff;
        }
        .success-icon h2 {
            color: #1f2937;
            margin-bottom: 10px;
        }
        .success-icon p {
            color: #6b7280;
            margin-bottom: 30px;
        }
        @media (max-width: 640px) {
            .row { grid-template-columns: 1fr; }
            .install-header { padding: 30px 20px; }
            .install-body { padding: 30px 20px; }
        }
    </style>
</head>
<body>
<div class="install-container">
    <div class="install-header">
        <h1>语云科技 官网安装向导</h1>
        <p>Yuyun Technology Official Website Installer</p>
    </div>

    <div class="install-body">
        <!-- 步骤指示器 -->
        <div class="steps">
            <div class="step-item <?php echo $step >= 1 ? ($step > 1 ? 'done' : 'active') : ''; ?>">
                <span class="step-num">1</span>环境检测
            </div>
            <div class="step-line <?php echo $step > 1 ? 'done' : ''; ?>"></div>
            <div class="step-item <?php echo $step >= 2 ? ($step > 2 ? 'done' : 'active') : ''; ?>">
                <span class="step-num">2</span>数据库
            </div>
            <div class="step-line <?php echo $step > 2 ? 'done' : ''; ?>"></div>
            <div class="step-item <?php echo $step >= 3 ? ($step > 3 ? 'done' : 'active') : ''; ?>">
                <span class="step-num">3</span>站点配置
            </div>
            <div class="step-line <?php echo $step > 3 ? 'done' : ''; ?>"></div>
            <div class="step-item <?php echo $step >= 4 ? 'active' : ''; ?>">
                <span class="step-num">4</span>完成
            </div>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($installed): ?>
        <div class="success-icon">
            <div class="icon">&#10003;</div>
            <h2>系统已安装</h2>
            <p>语云科技企业官网已完成安装配置。</p>
            <a href="index.php" class="btn btn-primary">访问首页</a>
            <a href="admin/index.php" style="margin-left:12px;" class="btn btn-secondary">进入后台</a>
        </div>

        <?php elseif ($step == 1): ?>
        <!-- 步骤1：环境检测 -->
        <h3 style="margin-bottom:20px;color:#1f2937;">服务器环境检测</h3>
        <table class="env-table">
            <thead>
                <tr>
                    <th>检测项目</th>
                    <th>当前值</th>
                    <th>要求</th>
                    <th>状态</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (check_env() as $check): ?>
                <tr>
                    <td><?php echo htmlspecialchars($check['name']); ?></td>
                    <td><?php echo htmlspecialchars($check['value']); ?></td>
                    <td><?php echo htmlspecialchars($check['required']); ?></td>
                    <td class="<?php echo $check['pass'] ? 'status-pass' : 'status-fail'; ?>">
                        <?php echo $check['pass'] ? '&#10003; 通过' : '&#10007; 不通过'; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form method="post">
            <input type="hidden" name="step" value="1">
            <button type="submit" class="btn btn-primary">下一步：数据库配置</button>
        </form>

        <?php elseif ($step == 2): ?>
        <!-- 步骤2：数据库配置 -->
        <h3 style="margin-bottom:20px;color:#1f2937;">数据库配置（可选）</h3>
        <p style="color:#6b7280;margin-bottom:24px;font-size:14px;">
            如不配置MySQL，系统将使用JSON文件存储数据。推荐生产环境使用MySQL。
        </p>
        <form method="post">
            <input type="hidden" name="step" value="2">
            <div class="checkbox-group">
                <input type="checkbox" name="use_mysql" id="use_mysql" checked>
                <label for="use_mysql">使用 MySQL 数据库</label>
            </div>
            <div id="db-fields">
                <div class="row">
                    <div class="form-group">
                        <label>数据库主机</label>
                        <input type="text" name="db_host" value="localhost" placeholder="localhost">
                    </div>
                    <div class="form-group">
                        <label>数据库名称</label>
                        <input type="text" name="db_name" value="yuyun_website" placeholder="yuyun_website">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label>数据库用户名</label>
                        <input type="text" name="db_user" value="root" placeholder="root">
                    </div>
                    <div class="form-group">
                        <label>数据库密码</label>
                        <input type="password" name="db_pass" placeholder="请输入密码">
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:12px;margin-top:24px;">
                <a href="?step=1" class="btn btn-secondary">上一步</a>
                <button type="submit" class="btn btn-primary">下一步：站点配置</button>
            </div>
        </form>

        <?php elseif ($step == 3): ?>
        <!-- 步骤3：站点配置 -->
        <h3 style="margin-bottom:20px;color:#1f2937;">基本配置</h3>
        <form method="post">
            <input type="hidden" name="step" value="3">
            <div class="form-group">
                <label>网站名称</label>
                <input type="text" name="site_name" value="语云科技" required placeholder="语云科技">
            </div>
            <div class="form-group">
                <label>网站URL地址</label>
                <input type="url" name="site_url" value="<?php echo 'http://' . ($_SERVER['HTTP_HOST'] ?? ''); ?>" placeholder="http://yourdomain.com">
            </div>
            <div class="row">
                <div class="form-group">
                    <label>管理员邮箱</label>
                    <input type="email" name="admin_email" required placeholder="admin@example.com">
                </div>
                <div class="form-group">
                    <label>管理员密码</label>
                    <input type="password" name="admin_password" required minlength="6" placeholder="至少6位密码">
                </div>
            </div>

            <hr style="border:none;border-top:1px solid #e5e7eb;margin:30px 0;">
            <h4 style="margin-bottom:16px;color:#374151;">邮件配置（可选）</h4>
            <p style="color:#6b7280;margin-bottom:20px;font-size:14px;">用于发送验证码和通知邮件</p>
            <div class="row">
                <div class="form-group">
                    <label>SMTP主机</label>
                    <input type="text" name="smtp_host" placeholder="smtp.qq.com">
                </div>
                <div class="form-group">
                    <label>SMTP端口</label>
                    <input type="number" name="smtp_port" value="465" placeholder="465">
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label>SMTP用户名</label>
                    <input type="text" name="smtp_user" placeholder="your@email.com">
                </div>
                <div class="form-group">
                    <label>SMTP密码/授权码</label>
                    <input type="password" name="smtp_pass" placeholder="SMTP授权码">
                </div>
            </div>
            <div class="form-group">
                <label>发件人邮箱</label>
                <input type="email" name="smtp_from" placeholder="noreply@yuyun.com">
            </div>

            <div style="display:flex;gap:12px;margin-top:24px;">
                <a href="?step=2" class="btn btn-secondary">上一步</a>
                <button type="submit" class="btn btn-primary">开始安装</button>
            </div>
        </form>

        <?php elseif ($step == 4): ?>
        <!-- 步骤4：完成 -->
        <div class="success-icon">
            <div class="icon">&#10003;</div>
            <h2>安装完成！</h2>
            <p>语云科技企业官网已成功安装，您可以开始使用了。</p>
            <div style="background:#f9fafb;padding:24px;border-radius:8px;text-align:left;max-width:400px;margin:0 auto 30px;">
                <p style="font-size:14px;color:#6b7280;"><strong>登录信息：</strong></p>
                <p style="font-size:14px;color:#374151;margin-top:8px;">后台地址：<code style="background:#e5e7eb;padding:2px 8px;border-radius:4px;">/admin/index.php</code></p>
                <p style="font-size:14px;color:#374151;">前台地址：<code style="background:#e5e7eb;padding:2px 8px;border-radius:4px;">/index.php</code></p>
            </div>
            <a href="index.php" class="btn btn-primary">访问首页</a>
            <a href="admin/index.php" style="margin-left:12px;" class="btn btn-secondary">进入后台管理</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('use_mysql')?.addEventListener('change', function() {
    document.getElementById('db-fields').style.display = this.checked ? 'block' : 'none';
});
</script>
</body>
</html>
