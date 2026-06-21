<?php
/**
 * 语云科技企业官网系统 - 配置文件
 * YuYun Tech Enterprise Website System - Configuration
 */

// 基础配置
define('SITE_NAME', '语云科技');
define('SITE_NAME_EN', 'YuYun Tech');
define('SITE_SLOGAN', '全球云服务专家');
define('INTERNATIONAL_URL', 'https://cloud.loveym.cloud');
define('SALES_PHONE', '400-800-8541');
define('SALES_PHONE_DISPLAY', '400-800-8451');
define('COMPANY_ADDRESS', '中国·青岛市市南区语云大厦');
define('COMPANY_EMAIL', 'sales@yuyun-tech.com');

// 数据库配置 (支持可选MySQL
define('DB_ENABLED', false);
define('DB_HOST', 'localhost');
define('DB_NAME', 'yuyun_tech');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// 数据存储文件 (当MySQL不可用时使用)
define('DATA_DIR', __DIR__ . '/data');
define('DATA_FILE', DATA_DIR . '/site_data.json');

// 后台配置
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

// 时区
date_default_timezone_set('Asia/Shanghai');

// 会话
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 自动加载数据
function load_site_data() {
    if (DB_ENABLED) {
        // MySQL模式 - 简化实现，使用数据库
        return load_from_db();
    }
    // JSON文件模式
    if (!file_exists(DATA_FILE)) {
        return get_default_data();
    }
    $data = json_decode(file_get_contents(DATA_FILE), true);
    return $data ?: get_default_data();
}

function save_site_data($data) {
    if (!is_dir(DATA_DIR)) {
        mkdir(DATA_DIR, 0755, true);
    }
    file_put_contents(DATA_FILE, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function get_default_data() {
    return [
        'site' => [
            'name' => '语云科技',
            'slogan' => '全球云服务专家',
            'logo' => 'assets/images/logo.svg',
            'favicon' => 'assets/images/favicon.ico',
            'theme' => 'default',
            'copyright' => '© 2024 语云科技美国有限公司 版权所有',
        ],
        'contact' => [
            'phone' => '400-800-8541',
            'phone_display' => '400-800-8451',
            'email' => 'sales@yuyun-tech.com',
            'address' => '中国·青岛市市南区语云科技大厦',
            'wechat' => 'yuyun_tech',
            'qq' => '800888888',
            'qq_group' => '123456789',
        ],
        'icp' => [
            'icp_number' => '鲁ICP备2024000000号-1',
            'police_number' => '鲁公网安备 37020000000000号',
            'license_number' => 'B1-20240000',
        ],
        'slides' => [
            ['title' => '语云科技', 'subtitle' => '全球领先的云服务提供商', 'desc' => '为您提供安全、稳定、高效的云基础设施', 'image' => 'assets/images/slide1.jpg', 'link' => '#', 'color' => '#1a73e8'],
            ['title' => '智能云服务', 'subtitle' => '云计算 · 大数据 · AI智能', 'desc' => '一站式云解决方案，助力企业数字化转型', 'image' => 'assets/images/slide2.jpg', 'link' => '#', 'color' => '#ff6b35'],
            ['title' => '全球节点覆盖', 'subtitle' => '遍布全球的数据中心', 'desc' => '中东、欧洲、亚太美洲', 'image' => 'assets/images/slide3.jpg', 'link' => '#', 'color' => '#00a86b'],
        ],
        'products' => [
            ['name' => '云服务器 ECS', 'desc' => '弹性可扩展的计算服务，按需付费，灵活部署', 'icon' => 'fa-server', 'price' => '¥99/月起', 'link' => 'products.php', 'color' => '#1a73e8'],
            ['name' => '云数据库 RDS', 'desc' => '高可用、高可靠的关系型数据库服务', 'icon' => 'fa-database', 'price' => '¥199/月起', 'link' => 'products.php', 'color' => '#ff6b35'],
            ['name' => 'CDN加速', 'desc' => '全球节点加速，提升网站访问速度', 'icon' => 'fa-bolt', 'price' => '¥0.1/GB', 'link' => 'products.php', 'color' => '#00a86b'],
            ['name' => '对象存储 OSS', 'desc' => '海量、安全、低成本的对象存储服务', 'icon' => 'fa-cloud', 'price' => '¥0.15/GB', 'link' => 'products.php', 'color' => '#9b59b6'],
            ['name' => 'SSL证书', 'desc' => '一站式SSL/TLS证书，保障数据传输安全', 'icon' => 'fa-lock', 'price' => '¥199/年起', 'link' => 'products.php', 'color' => '#e74c3c'],
            ['name' => '企业邮箱', 'desc' => '专业企业级邮箱服务，安全可靠', 'icon' => 'fa-envelope', 'price' => '¥10/账号/月', 'link' => 'products.php', 'color' => '#2ecc71'],
        ],
        'partners' => [
            ['name' => '腾讯云', 'logo' => 'assets/images/partner-tencent.svg'],
            ['name' => '阿里云', 'logo' => 'assets/images/partner-aliyun.svg'],
            ['name' => '华为云', 'logo' => 'assets/images/partner-huawei.svg'],
            ['name' => 'Cloudflare', 'logo' => 'assets/images/partner-cloudflare.svg'],
            ['name' => '亚马逊AWS', 'logo' => 'assets/images/partner-aws.svg'],
            ['name' => 'Microsoft Azure', 'logo' => 'assets/images/partner-azure.svg'],
            ['name' => 'Google Cloud', 'logo' => 'assets/images/partner-google.svg'],
            ['name' => '百度云', 'logo' => 'assets/images/partner-baidu.svg'],
            ['name' => '京东云', 'logo' => 'assets/images/partner-jd.svg'],
            ['name' => '中国移动', 'logo' => 'assets/images/partner-chinamobile.svg'],
        ],
        'locations' => [
            ['region' => '中东地区', 'cities' => ['迪拜', '利雅得', '多哈']],
            ['region' => '欧洲地区', 'cities' => ['伦敦', '巴黎', '法兰克福', '阿姆斯特丹']],
            ['region' => '中国', 'cities' => ['北京', '青岛', '上海', '深圳']],
            ['region' => '俄罗斯', 'cities' => ['莫斯科', '圣彼得堡']],
            ['region' => '韩国', 'cities' => ['首尔']],
            ['region' => '东南亚', 'cities' => ['新加坡', '吉隆坡', '雅加达']],
            ['region' => '澳大利亚', 'cities' => ['悉尼', '墨尔本']],
            ['region' => '北美地区', 'cities' => ['纽约', '华盛顿', '旧金山', '洛杉矶']],
        ],
        'news' => [
            ['title' => '语云科技与腾讯云达成战略合作', 'date' => '2024-01-15', 'desc' => '双方将在云计算、人工智能等领域展开深度合作'],
            ['title' => '语云科技欧洲数据中心正式启用', 'date' => '2024-02-20', 'desc' => '覆盖欧洲主要城市，提供低延迟服务'],
            ['title' => '荣获"年度最佳云服务提供商', 'date' => '2024-03-10', 'desc' => '在行业评选中获得高度认可'],
        ],
        'certificates' => [
            ['name' => '营业执照', 'image' => 'assets/images/cert-license.jpg'],
            ['name' => '增值电信业务经营许可证', 'image' => 'assets/images/cert-license2.jpg'],
            ['name' => 'ISO27001信息安全认证', 'image' => 'assets/images/cert-iso.jpg'],
            ['name' => '高新技术企业证书', 'image' => 'assets/images/cert-hightech.jpg'],
        ],
        'employees' => [
            ['name' => '张经理', 'title' => 'CEO · 创始人', 'avatar' => 'assets/images/employee1.jpg', 'desc' => '20年云计算行业经验，曾任多家知名企业高管'],
            ['name' => '李总监', 'title' => '技术总监', 'avatar' => 'assets/images/employee2.jpg', 'desc' => '精通分布式系统设计，云架构专家'],
            ['name' => '王女士', 'title' => '销售总监', 'avatar' => 'assets/images/employee3.jpg', 'desc' => '资深销售专家，客户关系管理专家'],
        ],
        'testimonials' => [
            ['name' => '某科技公司', 'avatar' => 'assets/images/customer1.jpg', 'content' => '语云科技的云服务非常稳定，技术支持响应迅速，是我们数字化转型的最佳伙伴。', 'role' => '技术总监'],
            ['name' => '某电商平台', 'avatar' => 'assets/images/customer2.jpg', 'content' => '使用语云科技CDN后，网站访问速度提升了60%，用户体验大幅改善。', 'role' => '运营经理'],
            ['name' => '某金融机构', 'avatar' => 'assets/images/customer3.jpg', 'content' => '安全可靠的云基础设施，完善的安全体系让我们放心使用。', 'role' => 'IT负责人'],
        ],
        'friendlinks' => [
            ['name' => '腾讯云', 'url' => 'https://cloud.tencent.com'],
            ['name' => 'Cloudflare', 'url' => 'https://www.cloudflare.com'],
            ['name' => '阿里云', 'url' => 'https://www.aliyun.com'],
            ['name' => '语云国际版', 'url' => 'https://cloud.loveym.cloud'],
        ],
    ];
}

// 数据库连接 (可选)
function db_connect() {
    if (!DB_ENABLED) return null;
    static $conn = null;
    if ($conn) return $conn;
    try {
        $conn = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        return null;
    }
}

function load_from_db() {
    $conn = db_connect();
    if (!$conn) return get_default_data();
    // 简化实现 - 在MySQL模式下也使用JSON文件
    if (!file_exists(DATA_FILE)) return get_default_data();
    return json_decode(file_get_contents(DATA_FILE), true) ?: get_default_data();
}

// 安全转义函数
function e($text) {
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

function ddump($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

// 获取当前页面
$site_data = load_site_data();
