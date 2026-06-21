<?php
/**
 * 语云科技企业官网 - 产品与服务
 * 包含: 产品标签筛选栏、产品网格展示、CTA区域
 */

session_start();
define('YUYUN_ROOT', __DIR__);
require_once YUYUN_ROOT . '/core/Functions.php';

// 检查安装状态
if (!is_installed()) {
    header('Location: install.php');
    exit;
}

// 加载配置和数据
$config = get_config();
$products = get_content('products');

// 过滤启用的数据
$products = array_filter($products, fn($p) => ($p['status'] ?? 'active') === 'active');
usort($products, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));

// 站点名称
$siteName = $config['site_name'] ?? '语云科技';
$siteUrl = $config['site_url'] ?? '';
$logoUrl = $config['logo_url'] ?? '';
$icpNumber = $config['icp_number'] ?? '京ICP备XXXXXXXX号-X';
$icpLicense = $config['icp_license'] ?? '增值电信业务经营许可证：京B2-XXXXXXXX';
$policeNumber = $config['police_number'] ?? '京公网安备 XXXXXXXXXXXXXX号';
$qqGroup = $config['qq_group'] ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e($siteName); ?> - 全方位云计算产品服务，包括云服务器、CDN加速、域名注册等企业级解决方案">
    <meta name="keywords" content="<?php echo e($siteName); ?>,云计算,云服务器,CDN,域名注册,DDoS防护,产品服务">
    <title>产品与服务 - <?php echo e($siteName); ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@300;400;500;700;900&display=swap" rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- 主样式 -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/animations.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">

    <!-- 产品筛选样式 -->
    <style>
        .filter-bar { display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-bottom:40px; padding:16px; background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.06); }
        .filter-btn { padding:10px 24px; border:2px solid #e5e7eb; background:#fff; border-radius:8px; font-size:14px; font-weight:600; color:#6b7280; cursor:pointer; transition:all 0.3s; display:inline-flex; align-items:center; gap:6px; }
        .filter-btn:hover, .filter-btn.active { border-color:#0066CC; color:#0066CC; background:#f0f7ff; }
        .filter-btn.active { background:#0066CC; color:#fff; border-color:#0066CC; }

        .product-grid-enhanced { display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:28px; }
        .product-card-enhanced { background:#fff; border-radius:16px; padding:32px 28px; box-shadow:0 4px 20px rgba(0,102,204,0.08); transition:all 0.3s; position:relative; overflow:hidden; border-top:4px solid transparent; }
        .product-card-enhanced:hover { transform:translateY(-8px); box-shadow:0 12px 32px rgba(0,102,204,0.18); }
        .product-card-enhanced::before { content:''; position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg,#0066CC,#00A8E8); opacity:0; transition:opacity 0.3s; }
        .product-card-enhanced:hover::before { opacity:1; }

        .product-icon-lg { width:72px; height:72px; background:linear-gradient(135deg,#f0f7ff,#e0efff); border-radius:16px; display:flex; align-items:center; justify-content:center; margin-bottom:20px; font-size:32px; color:#0066CC; transition:all 0.3s; }
        .product-card-enhanced:hover .product-icon-lg { background:linear-gradient(135deg,#0066CC,#00A8E8); color:#fff; }

        .product-name-lg { font-size:22px; font-weight:700; color:#1f2937; margin-bottom:10px; }
        .product-desc-lg { color:#6b7280; line-height:1.7; font-size:14px; margin-bottom:18px; min-height:48px; }

        .product-features-list { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:20px; }
        .feature-tag { padding:6px 14px; background:#f0f7ff; color:#0066CC; border-radius:20px; font-size:12px; font-weight:600; }

        .product-price { font-size:20px; font-weight:800; color:#ff6b35; margin-bottom:20px; }
        .product-price small { font-size:13px; font-weight:500; color:#9ca3af; }

        @media (max-width: 768px) {
            .product-grid-enhanced { grid-template-columns:1fr; }
            .filter-bar { gap:8px; }
            .filter-btn { padding:8px 16px; font-size:13px; }
        }
    </style>
</head>
<body>

<!-- ============================================
     导航栏
     ============================================ -->
<nav class="navbar" id="navbar">
    <div class="container navbar-inner">
        <!-- Logo -->
        <a href="/" class="nav-logo">
            <?php if ($logoUrl): ?>
            <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($siteName); ?>">
            <?php else: ?>
            <div class="nav-logo-text"><?php echo e($siteName); ?><span>®</span></div>
            <?php endif; ?>
        </a>

        <!-- 导航菜单 -->
        <ul class="nav-menu">
            <li><a href="/" class="nav-link">首页</a></li>
            <li><a href="/about.php" class="nav-link">关于我们</a></li>
            <li><a href="/intro.php" class="nav-link">公司简介</a></li>
            <li><a href="/products.php" class="nav-link active">产品服务</a></li>
            <li><a href="/partners.php" class="nav-link">合作伙伴</a></li>
            <li><a href="/contact.php" class="nav-link">联系我们</a></li>
            <li>
                <a href="https://cloud.loveym.cloud" target="_blank" rel="noopener" class="nav-link">
                    <i class="fa-solid fa-globe"></i> 国际版
                </a>
            </li>
        </ul>

        <!-- 右侧操作区 -->
        <div class="nav-actions">
            <?php if (is_logged_in()): ?>
            <a href="/user/dashboard.php" class="nav-btn-primary"><i class="fa-solid fa-user"></i> 用户中心</a>
            <?php else: ?>
            <a href="/user/login.php" class="btn btn-outline-dark btn-sm"><i class="fa-solid fa-right-to-bracket"></i> 登录</a>
            <a href="/user/register.php" class="nav-btn-primary">免费注册</a>
            <?php endif; ?>
            <button class="hamburger" id="hamburger" aria-label="菜单">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</nav>

<!-- 移动端菜单遮罩 -->
<div class="mobile-menu-overlay" id="menuOverlay"></div>

<!-- 移动端侧边菜单 -->
<div class="mobile-menu" id="mobileMenu">
    <a href="/" class="nav-link">首页</a>
    <a href="/about.php" class="nav-link">关于我们</a>
    <a href="/intro.php" class="nav-link">公司简介</a>
    <a href="/products.php" class="nav-link active">产品服务</a>
    <a href="/partners.php" class="nav-link">合作伙伴</a>
    <a href="/contact.php" class="nav-link">联系我们</a>
    <a href="https://cloud.loveym.cloud" target="_blank" rel="noopener" class="nav-link">
        <i class="fa-solid fa-globe"></i> 国际版官网
    </a>
    <hr style="border:none;border-top:1px solid #eee;margin:16px 0;">
    <?php if (is_logged_in()): ?>
    <a href="/user/dashboard.php" class="nav-link"><i class="fa-solid fa-user"></i> 用户中心</a>
    <a href="/user/logout.php" class="nav-link"><i class="fa-solid fa-right-from-bracket"></i> 退出登录</a>
    <?php else: ?>
    <a href="/user/login.php" class="nav-link"><i class="fa-solid fa-right-to-bracket"></i> 登录</a>
    <a href="/user/register.php" class="nav-link"><i class="fa-solid fa-user-plus"></i> 注册</a>
    <?php endif; ?>
</div>

<!-- ============================================
     页面Hero区域
     ============================================ -->
<section class="page-hero" style="background:linear-gradient(135deg,#004080 0%,#0066CC 50%,#00A8E8 100%);padding:120px 0 80px;text-align:center;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;);"></div>
    <div class="container" style="position:relative;z-index:1;">
        <h1 style="font-size:clamp(32px,5vw,48px);font-weight:900;color:#fff;margin-bottom:16px;">产品与服务</h1>
        <p style="font-size:18px;color:rgba(255,255,255,0.9);max-width:600px;margin:0 auto;">全方位云计算解决方案，满足各类业务需求</p>

        <!-- 面包屑导航 -->
        <div style="margin-top:24px;display:flex;justify-content:center;align-items:center;gap:8px;color:rgba(255,255,255,0.75);font-size:14px;">
            <a href="/" style="color:rgba(255,255,255,0.85);text-decoration:none;"><i class="fa-solid fa-house"></i> 首页</a>
            <i class="fa-solid fa-chevron-right" style="font-size:11px;"></i>
            <span style="color:#fff;font-weight:500;">产品服务</span>
        </div>
    </div>
</section>

<!-- ============================================
     产品标签筛选栏
     ============================================ -->
<section class="section" style="padding:60px 0 40px;" data-animate>
    <div class="container">
        <div class="filter-bar" id="filterBar">
            <button class="filter-btn active" data-filter="all"><i class="fa-solid fa-border-all"></i> 全部</button>
            <button class="filter-btn" data-filter="compute"><i class="fa-solid fa-microchip"></i> 计算</button>
            <button class="filter-btn" data-filter="storage"><i class="fa-solid fa-database"></i> 存储</button>
            <button class="filter-btn" data-filter="network"><i class="fa-solid fa-network-wired"></i> 网络</button>
            <button class="filter-btn" data-filter="security"><i class="fa-solid fa-shield-halved"></i> 安全</button>
            <button class="filter-btn" data-filter="domain"><i class="fa-solid fa-globe"></i> 域名</button>
        </div>
    </div>
</section>

<!-- ============================================
     产品网格展示 (6个产品卡片)
     ============================================ -->
<section class="section" style="padding:0 0 80px;" data-animate>
    <div class="container">
        <div class="product-grid-enhanced" id="productGrid">
            <?php
            $defaultProducts = [
                [
                    'icon' => 'fa-server',
                    'name' => '云服务器 ECS',
                    'desc' => '高性能、安全稳定的弹性云计算服务，支持按需付费和包年包月，弹性伸缩满足各种业务场景需求',
                    'category' => 'compute',
                    'features' => ['弹性配置', '快速部署', '安全隔离', '多地域'],
                    'price' => '¥50/月起'
                ],
                [
                    'icon' => 'fa-database',
                    'name' => '云数据库 RDS',
                    'desc' => '即开即用、稳定可靠的云端数据库服务，支持MySQL、PostgreSQL等多种数据库引擎',
                    'category' => 'storage',
                    'features' => ['自动备份', '高可用', '读写分离', '容灾恢复'],
                    'price' => '¥100/月起'
                ],
                [
                    'icon' => 'fa-globe',
                    'name' => 'CDN 加速服务',
                    'desc' => '全球加速网络，智能调度，覆盖全球主要地区节点，访问速度提升300%',
                    'category' => 'network',
                    'features' => ['全球节点', '智能DNS', 'HTTPS', '实时监控'],
                    'price' => '¥0.15/GB起'
                ],
                [
                    'icon' => 'fa-cloud-upload-alt',
                    'name' => '对象存储 OSS',
                    'desc' => '海量、安全、低成本的云端存储服务，适用于图片、视频、文档等各类数据存储',
                    'category' => 'storage',
                    'features' => ['无限容量', '多地域容灾', 'API接口', 'CDN加速'],
                    'price' => '¥0.12/GB/月'
                ],
                [
                    'icon' => 'fa-globe-asia',
                    'name' => '域名注册',
                    'desc' => '全球主流域名后缀注册与管理服务，提供完善的DNS解析和SSL证书管理',
                    'category' => 'domain',
                    'features' => ['批量管理', 'DNS解析', 'SSL证书', 'WHOIS保护'],
                    'price' => '¥1/年起'
                ],
                [
                    'icon' => 'fa-shield-alt',
                    'name' => 'DDoS 防护',
                    'desc' => '企业级DDoS攻击防护服务，Tbps级防御能力，AI智能清洗，保障业务连续性',
                    'category' => 'security',
                    'features' => ['Tbps防御', 'AI智能清洗', '7x24监控', '即时响应'],
                    'price' => '¥3000/月起'
                ]
            ];

            $displayProducts = !empty($products) ? array_values($products) : $defaultProducts;

            foreach ($displayProducts as $idx => $product):
                $icon = $product['icon'] ?? 'fa-cube';
                $name = $product['name'] ?? '';
                $desc = $product['description'] ?? $product['desc'] ?? '';
                $category = $product['category'] ?? 'compute';
                $features = isset($product['features']) && is_string($product['features'])
                    ? json_decode($product['features'], true) ?: []
                    : ($product['features'] ?? []);
                $price = $product['price'] ?? '联系咨询';
            ?>
            <div class="product-card-enhanced" data-category="<?php echo e($category); ?>" data-animate="fade-up" style="transition-delay:<?php echo $idx * 0.08; ?>s;">
                <div class="product-icon-lg">
                    <i class="fa-solid <?php echo e($icon); ?>"></i>
                </div>
                <h3 class="product-name-lg"><?php echo e($name); ?></h3>
                <p class="product-desc-lg"><?php echo e($desc); ?></p>

                <?php if (!empty($features)): ?>
                <div class="product-features-list">
                    <?php foreach (array_slice((array)$features, 0, 4) as $feature): ?>
                    <span class="feature-tag"><?php echo e($feature); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="product-price">
                    <?php echo e($price); ?>
                    <?php if (strpos($price, '起') !== false || strpos($price, '/') !== false): ?>
                    <small>（价格仅供参考）</small>
                    <?php endif; ?>
                </div>

                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <a href="/contact.php" class="btn btn-primary" style="flex:1;text-align:center;padding:12px 20px;border-radius:8px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;gap:6px;">
                        <i class="fa-solid fa-paper-plane"></i> 立即咨询
                    </a>
                    <a href="#details-<?php echo $idx; ?>" class="btn btn-outline-dark" style="padding:12px 20px;border-radius:8px;font-weight:600;text-decoration:none;">
                        详情
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================
     CTA 底部区域
     ============================================ -->
<section style="background:linear-gradient(135deg,#0066CC 0%,#004080 100%);padding:80px 0;text-align:center;position:relative;overflow:hidden;" data-animate>
    <div style="position:absolute;inset:0;background:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;);"></div>
    <div class="container" style="position:relative;z-index:1;">
        <h2 style="font-size:clamp(28px,4vw,42px);font-weight:800;color:#fff;margin-bottom:16px;">找不到合适的产品？</h2>
        <p style="font-size:18px;color:rgba(255,255,255,0.85);max-width:560px;margin:0 auto 36px;">我们的专业团队可以根据您的具体需求，为您定制专属的云计算解决方案</p>
        <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
            <a href="/contact.php" class="btn btn-lg" style="background:#fff;color:#0066CC;font-weight:700;padding:16px 36px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all 0.3s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.2)'" onmouseout="this.style.transform='none';this.style.boxShadow='none'"><i class="fa-solid fa-comments"></i> 定制方案咨询</a>
            <a href="tel:4008008541" class="btn btn-lg" style="background:transparent;color:#fff;border:2px solid #fff;font-weight:700;padding:14px 34px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center:gap:8px;transition:all 0.3s;" onmouseover="this.style.background='#fff';this.style.color='#0066CC'" onmouseout="this.style.background='transparent';this.style.color='#fff'"><i class="fa-solid fa-phone"></i> 电话咨询：400-800-8541</a>
        </div>
    </div>
</section>

<!-- ============================================
     页脚 (Cloudflare黑色风格 + 橙色电话)
     ============================================ -->
<footer class="footer" id="footer">
    <div class="container">
        <div class="footer-main">
            <!-- Logo & 电话 -->
            <div class="footer-brand">
                <div class="footer-logo">
                    <?php if ($logoUrl): ?>
                    <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($siteName); ?>" style="height:36px;filter:brightness(0) invert(1);">
                    <?php else: ?>
                    <div class="footer-logo-text"><?php echo e($siteName); ?><span>®</span></div>
                    <?php endif; ?>
                </div>
                <div class="footer-phone-label">销售电话</div>
                <div class="footer-phone">400-800-8541</div>
                <p class="footer-desc" style="margin-top:12px;">
                    <?php echo e($config['footer_desc'] ?? '语云科技美国有限公司在中国的注册授权运营方，致力于为全球企业提供优质的云计算服务。'); ?>
                </p>
            </div>

            <!-- 产品服务 -->
            <div class="footer-column">
                <h4>产品服务</h4>
                <ul class="footer-links">
                    <li><a href="/products.php">云服务器 ECS</a></li>
                    <li><a href="/products.php">云数据库 RDS</a></li>
                    <li><a href="/products.php">CDN 加速</a></li>
                    <li><a href="/products.php">对象存储 OSS</a></li>
                    <li><a href="/products.php">域名注册</a></li>
                    <li><a href="/products.php">DDoS 防护</a></li>
                </ul>
            </div>

            <!-- 关于我们 -->
            <div class="footer-column">
                <h4>关于我们</h4>
                <ul class="footer-links">
                    <li><a href="/about.php">公司介绍</a></li>
                    <li><a href="/intro.php">发展历程</a></li>
                    <li><a href="/partners.php">合作伙伴</a></li>
                    <li><a href="/contact.php">联系我们</a></li>
                    <li><a href="https://cloud.loveym.cloud" target="_blank" rel="noopener">国际版官网</a></li>
                </ul>
            </div>

            <!-- 帮助支持 -->
            <div class="footer-column">
                <h4>帮助支持</h4>
                <ul class="footer-links">
                    <li><a href="/user/login.php">用户登录</a></li>
                    <li><a href="/user/register.php">免费注册</a></li>
                    <li><a href="/contact.php">提交工单</a></li>
                    <li><a href="#">帮助文档</a></li>
                    <li><a href="#">API 文档</a></li>
                </ul>
            </div>
        </div>

        <!-- 底部备案信息 -->
        <div class="footer-bottom">
            <div class="footer-copyright">
                &copy; <?php echo date('Y'); ?> <?php echo e($siteName); ?> 版权所有
            </div>
            <div class="footer-beian">
                <a href="https://beian.miit.gov.cn/" target="_blank" rel="nofollow noopener noreferrer">
                    <i class="fa-solid fa-shield-halved" style="margin-right:4px;"></i><?php echo e($icpNumber); ?>
                </a>
                <a href="#" target="_blank" rel="nofollow noopener noreferrer">
                    <i class="fa-solid fa-file-contract" style="margin-right:4px;"></i><?php echo e($icpLicense); ?>
                </a>
                <a href="#" target="_blank" rel="nofollow noopener noreferrer">
                    <i class="fa-solid fa-shield" style="margin-right:4px;"></i><?php echo e($policeNumber); ?>
                </a>
            </div>
        </div>

        <!-- 免责声明 -->
        <div class="footer-disclaimer">
            语云科技® 是我们（语云科技美国有限公司）在中国的注册授权 | Yuyun Technology USA Inc.
        </div>
    </div>
</footer>

<!-- ============================================
     右侧悬浮工具栏 (魔方财务同款)
     ============================================ -->
<aside class="side-toolbar" id="sideToolbar">
    <div class="toolbar-item accent" onclick="showContactModal()">
        <i class="fa-solid fa-headset"></i>
        客服
        <div class="toolbar-tooltip">在线客服</div>
    </div>
    <div class="toolbar-item" onclick="window.location.href='tel:4008008541'">
        <i class="fa-solid fa-phone"></i>
        电话
        <div class="toolbar-tooltip">400-800-8541</div>
    </div>
    <div class="toolbar-item" onclick="window.location.href='mailto:<?php echo e($config['admin_email'] ?? 'support@yuyun.com'); ?>'">
        <i class="fa-solid fa-envelope"></i>
        邮件
        <div class="toolbar-tooltip">发送邮件</div>
    </div>
    <?php if ($qqGroup): ?>
    <div class="toolbar-item" onclick="window.open('<?php echo e($qqGroup); ?>')">
        <i class="fa-brands fa-qq"></i>
        QQ群
        <div class="toolbar-tooltip">加入QQ群</div>
    </div>
    <?php endif; ?>
    <div class="toolbar-item" onclick="window.scrollTo({top:0,behavior:'smooth'})">
        <i class="fa-solid fa-arrow-up"></i>
        顶部
        <div class="toolbar-tooltip">返回顶部</div>
    </div>
</aside>

<!-- Toast容器 -->
<div class="toast-container" id="toastContainer"></div>

<!-- JavaScript -->
<script>
// 全局站点配置
window.siteConfig = <?php echo json_encode($config, JSON_UNESCAPED_UNICODE); ?>;
</script>
<script src="/assets/js/main.js"></script>
<script src="/assets/js/carousel.js"></script>
<script src="/assets/js/modal.js"></script>
<script src="/assets/js/map.js"></script>

<!-- 产品筛选脚本 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const productCards = document.querySelectorAll('.product-card-enhanced');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // 更新按钮状态
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;

            // 筛选产品卡片
            productCards.forEach(card => {
                if (filter === 'all' || card.dataset.category === filter) {
                    card.style.display = '';
                    card.style.animation = 'fadeInUp 0.5s ease forwards';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>

</body>
</html>
