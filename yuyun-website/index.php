<?php
/**
 * 语云科技企业官网 - 首页
 * 包含: 导航栏、轮播图、合作伙伴、产品服务、全球分布地图、员工卡片、资质证书、页脚
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
$banners = get_content('banners');
$products = get_content('products');
$partners = get_content('partners');
$staff = get_content('staff');
$certificates = get_content('certificates');
$links = get_content('links');

// 过滤启用的数据
$banners = array_filter($banners, fn($b) => ($b['status'] ?? 1) == 1);
$products = array_filter($products, fn($p) => ($p['status'] ?? 'active') === 'active');
$partners = array_filter($partners, fn($p) => ($p['status'] ?? 1) == 1);
$staff = array_filter($staff, fn($s) => ($s['status'] ?? 1) == 1);
$certificates = array_filter($certificates, fn($c) => ($c['status'] ?? 1) == 1);

// 排序
usort($banners, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
usort($products, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
usort($staff, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));

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
    <meta name="description" content="<?php echo e($config['seo_description'] ?? $siteName . ' - 全球领先的云计算服务提供商，提供云服务器、CDN加速、域名注册等企业级服务'); ?>">
    <meta name="keywords" content="<?php e($config['seo_keywords'] ?? '语云科技,云计算,云服务器,CDN,域名注册,DDoS防护'); ?>">
    <title><?php echo e($siteName); ?> - 全球领先云计算服务</title>

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
            <li><a href="/" class="nav-link active">首页</a></li>
            <li><a href="/about.php" class="nav-link">关于我们</a></li>
            <li><a href="/intro.php" class="nav-link">公司简介</a></li>
            <li><a href="/products.php" class="nav-link">产品服务</a></li>
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
    <a href="/" class="nav-link active">首页</a>
    <a href="/about.php" class="nav-link">关于我们</a>
    <a href="/intro.php" class="nav-link">公司简介</a>
    <a href="/products.php" class="nav-link">产品服务</a>
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
     Hero 轮播区域 (腾讯云同款)
     ============================================ -->
<section class="hero-carousel" id="heroCarousel">
    <div class="carousel-slides" id="carouselSlides">
        <?php if (!empty($banners)):
            foreach ($banners as $index => $banner):
                $bgStyle = !empty($banner['image'])
                    ? "background-image:url('{$banner['image']}')"
                    : "background:linear-gradient(135deg,#004080 0%,#0066CC 50%,#00A8E8 100%)";
        ?>
        <div class="carousel-slide">
            <div class="carousel-slide-bg" style="<?php echo $bgStyle; ?>"></div>
            <div class="carousel-content">
                <h1 class="carousel-title"><?php echo e($banner['title']); ?></h1>
                <p class="carousel-subtitle"><?php echo e($banner['subtitle']); ?></p>
                <div class="carousel-actions">
                    <?php if (!empty($banner['link'])): ?>
                    <a href="<?php echo e($banner['link']); ?>" class="btn btn-primary btn-lg">
                        了解更多 <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <?php endif; ?>
                    <a href="/contact.php" class="btn btn-outline btn-lg">
                        <i class="fa-solid fa-headset"></i> 联系我们
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach;
        else: /* 默认轮播 */ ?>
        <div class="carousel-slide">
            <div class="carousel-slide-bg" style="background:linear-gradient(135deg,#0a1628 0%,#0066CC 50%,#00A8E8 100%);"></div>
            <div class="carousel-content">
                <h1 class="carousel-title"><?php echo e($siteName); ?> - 全球云计算服务</h1>
                <p class="carousel-subtitle">为企业提供安全、稳定、高效的云计算解决方案，助力数字化转型</p>
                <div class="carousel-actions">
                    <a href="/products.php" class="btn btn-primary btn-lg">
                        浏览产品 <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="/contact.php" class="btn btn-outline btn-lg">
                        <i class="fa-solid fa-headset"></i> 免费咨询
                    </a>
                </div>
            </div>
        </div>
        <div class="carousel-slide">
            <div class="carousel-slide-bg" style="background:linear-gradient(135deg,#004080 0%,#001a33 100%);"></div>
            <div class="carousel-content">
                <h1 class="carousel-title">全球节点覆盖 · 极速互联</h1>
                <p class="carousel-subtitle">中东、欧洲、亚洲、北美、澳洲等多地区数据中心，全球网络加速</p>
                <div class="carousel-actions">
                    <a href="#map-section" class="btn btn-primary btn-lg">
                        查看节点 <i class="fa-solid fa-map-location-dot"></i>
                    </a>
                    <a href="/about.php" class="btn btn-outline btn-lg">
                        了解更多
                    </a>
                </div>
            </div>
        </div>
        <div class="carousel-slide">
            <div class="carousel-slide-bg" style="background:linear-gradient(135deg,#001a33 0%,#003366 100%);"></div>
            <div class="carousel-content">
                <h1 class="carousel-title">7×24小时专业技术支持</h1>
                <p class="carousel-subtitle">专业团队全天候为您服务，响应迅速，解决及时</p>
                <div class="carousel-actions">
                    <a href="/contact.php" class="btn btn-accent btn-lg">
                        <i class="fa-solid fa-phone"></i> 立即联系
                    </a>
                    <a href="/user/register.php" class="btn btn-outline btn-lg">
                        免费试用
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- 轮播控制 -->
    <button class="carousel-btn carousel-btn-prev" aria-label="上一张">
        <i class="fa-solid fa-chevron-left"></i>
    </button>
    <button class="carousel-btn carousel-btn-next" aria-label="下一张">
        <i class="fa-solid fa-chevron-right"></i>
    </button>

    <!-- 轮播指示器 -->
    <div class="carousel-indicators" id="carouselIndicators">
        <?php
        $totalSlides = max(count($banners), 3);
        for ($i = 0; $i < $totalSlides; $i++):
            $activeClass = $i === 0 ? ' active' : '';
        ?>
        <button class="carousel-indicator<?php echo $activeClass; ?>" data-index="<?php echo $i; ?>" aria-label="第<?php echo $i+1; ?>张"></button>
        <?php endfor; ?>
    </div>
</section>

<!-- ============================================
     数据统计 (数字动画)
     ============================================ -->
<section class="stats-section section" data-animate>
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item" data-animate="fade-up">
                <div class="stat-number" data-count="15" data-suffix="+">0</div>
                <div class="stat-label">全球数据中心</div>
            </div>
            <div class="stat-item" data-animate="fade-up delay-1">
                <div class="stat-number" data-count="50000" data-suffix="+">0</div>
                <div class="stat-label">企业客户</div>
            </div>
            <div class="stat-item" data-animate="fade-up delay-2">
                <div class="stat-number" data-count="99" data-suffix=".99%">0</div>
                <div class="stat-label">服务可用性</div>
            </div>
            <div class="stat-item" data-animate="fade-up delay-3">
                <div class="stat-number" data-count="7" data-suffix="x24">0</div>
                <div class="stat-label">技术支持</div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     合作伙伴横滚 (Cloudflare同款)
     ============================================ -->
<section class="partners-section" data-animate>
    <div class="container" style="text-align:center;margin-bottom:30px;">
        <h2 style="font-size:26px;font-weight:700;color:#1f2937;">我们与以下<span class="highlight">企业/组织</span>协手共进</h2>
        <p style="color:#6b7280;margin-top:8px;">携手行业领袖，共建数字化未来</p>
    </div>
    <div style="overflow:hidden;">
        <div class="partners-track" id="partnersTrack">
            <?php
            // 双份实现无缝滚动
            $allPartners = array_merge($partners, $partners);

            $defaultLogos = [
                ['name' => '腾讯云', 'url' => '/assets/img/partner/tencent.png'],
                ['name' => '阿里云', 'url' => '/assets/img/partner/alibaba.png'],
                ['name' => '华为云', 'url' => '/assets/img/partner/huawei.png'],
                ['name' => 'Cloudflare', 'url' => '/assets/img/partner/cloudflare.png'],
                ['name' => 'AWS', 'url' => '/assets/img/partner/aws.png'],
                ['name' => 'Google Cloud', 'url' => '/assets/img/partner/google.png'],
                ['name' => 'Microsoft Azure', 'url' => '/assets/img/partner/azure.png'],
                ['name' => '百度智能云', 'url' => '/assets/img/partner/baidu.png']
            ];

            $displayPartners = !empty($allPartners) ? $allPartners : array_merge($defaultLogos, $defaultLogos);

            foreach ($displayPartners as $partner):
                $logoUrl = !empty($partner['logo_url']) ? $partner['logo_url'] : ($partner['url'] ?? '');
                $linkUrl = !empty($partner['link_url']) ? $partner['link_url'] : '#';
                $name = $partner['name'] ?? 'Partner';
            ?>
            <a href="<?php echo e($linkUrl); ?>" target="<?php echo $linkUrl !== '#' ? '_blank' : '_self'; ?>" rel="noopener" title="<?php echo e($name); ?>" style="flex-shrink:0;">
                <img src="<?php echo e($logoUrl); ?>"
                     alt="<?php echo e($name); ?>"
                     class="partner-logo"
                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22180%22 height=%2248%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22180%22 height=%2248%22 rx=%228%22/%3E%3Ctext x=%2290%22 y=%2229%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2214%22%3E'.encodeURIComponent($name).'%3C/text%3E%3C/svg%3E'"
                     loading="lazy">
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================
     产品服务展示 (魔方财务同款卡片)
     ============================================ -->
<section class="products-section section" id="products-section">
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title">我们的<span class="highlight">产品与服务</span></h2>
            <p class="section-subtitle">全方位云计算解决方案，满足各类业务需求</p>
        </div>

        <div class="product-grid">
            <?php
            $defaultProducts = [
                ['icon' => 'fa-server', 'name' => '云服务器 ECS', 'desc' => '高性能、安全稳定的弹性云计算服务，按需付费，弹性伸缩', 'features' => ['弹性配置','快速部署','安全隔离']],
                ['icon' => 'fa-database', 'name' => '云数据库 RDS', 'desc' => '即开即用、稳定可靠的云端数据库服务', 'features' => ['自动备份','高可用','读写分离']],
                ['icon' => 'fa-globe', 'name' => 'CDN加速服务', 'desc' => '全球加速网络，智能调度，访问速度提升300%', 'features' => ['全球节点','智能DNS','HTTPS']],
                ['icon' => 'fa-cloud-upload-alt', 'name' => '对象存储 OSS', 'desc' => '海量、安全、低成本的云端存储服务', 'features' => ['无限容量','多地域容灾','API接口']],
                ['icon' => 'fa-globe-asia', 'name' => '域名注册', 'desc' => '全球主流域名后缀注册与管理服务', 'features' => ['批量管理','DNS解析','SSL证书']],
                ['icon' => 'fa-shield-alt', 'name' => 'DDoS防护', 'desc' => '企业级DDoS攻击防护服务，Tbps级防御能力', 'features' => ['Tbps防御','AI智能清洗','7x24监控']]
            ];

            $displayProducts = !empty($products) ? array_values($products) : $defaultProducts;

            foreach ($displayProducts as $idx => $product):
                $icon = $product['icon'] ?? 'fa-cube';
                $features = isset($product['features']) && is_string($product['features'])
                    ? json_decode($product['features'], true) ?: []
                    : ($product['features'] ?? []);
            ?>
            <div class="product-card" data-animate="fade-up" style="transition-delay:<?php echo $idx * 0.08; ?>s;">
                <div class="product-icon">
                    <i class="fa-solid <?php echo e($icon); ?>"></i>
                </div>
                <h3 class="product-name"><?php echo e($product['name']); ?></h3>
                <p class="product-desc"><?php echo e($product['description'] ?? $product['desc']); ?></p>
                <?php if (!empty($features)): ?>
                <div class="product-features">
                    <?php foreach (array_slice($features, 0, 4) as $feature): ?>
                    <span class="product-feature-tag"><?php echo e($feature); ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div style="margin-top:20px;">
                    <a href="/products.php" class="btn btn-outline-dark btn-sm">
                        了解详情 <i class="fa-solid fa-arrow-right" style="font-size:12px;"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center;margin-top:48px;" data-animate="fade-up">
            <a href="/products.php" class="btn btn-primary btn-lg">
                查看全部产品 <i class="fa-solid fa-th-large"></i>
            </a>
        </div>
    </div>
</section>

<!-- ============================================
     全球分布地图 (魔方财务/腾讯云/Cloudflare同款)
     ============================================ -->
<section class="map-section section" id="map-section">
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title" style="color:#fff;">全球网络<span style="color:#00A8E8;">分布</span></h2>
            <p class="section-subtitle" style="color:rgba(255,255,255,0.65);">覆盖全球主要地区的数据中心与网络节点</p>
        </div>

        <div id="world-map" data-animate="scale"></div>
    </div>
</section>

<!-- ============================================
     员工团队卡片
     ============================================ -->
<section class="staff-section section" id="team-section">
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title">核心<span class="highlight">团队成员</span></h2>
            <p class="section-subtitle">专业的技术与服务团队，为您提供优质体验</p>
        </div>

        <div class="staff-grid" id="staffGrid">
            <?php
            $defaultStaff = [
                ['name'=>'张明远','position'=>'首席执行官 CEO','avatar'=>'','bio'=>'15年互联网行业经验，曾任职世界500强'],
                ['name'=>'李思琪','position'=>'首席技术官 CTO','avatar'=>'','bio'=>'资深云计算架构师，分布式系统专家'],
                ['name'=>'王浩然','position'=>'产品总监','avatar'=>'','bio'=>'专注企业级SaaS产品设计，UX专家'],
                ['name'=>'陈雨晴','position'=>'运营总监 COO','avatar'=>'','bio'=>'精通国际市场拓展，海外业务负责人']
            ];
            $displayStaff = !empty($staff) ? array_values($staff) : $defaultStaff;

            foreach ($displayStaff as $member):
                $avatar = !empty($member['avatar']) ? $member['avatar'] : '';
                $initials = mb_substr($member['name'], 0, 1);
            ?>
            <div class="staff-card" data-animate="fade-up">
                <?php if ($avatar): ?>
                <img src="<?php echo e($avatar); ?>" alt="<?php echo e($member['name']); ?>" class="staff-avatar"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                <div class="staff-avatar" style="display:none;background:linear-gradient(135deg,#0066CC,#00A8E8);align-items:center;justify-content:center;font-size:32px;font-weight:700;color:white;"><?php echo e($initials); ?></div>
                <?php else: ?>
                <div class="staff-avatar" style="background:linear-gradient(135deg,#0066CC,#00A8E8);display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;color:white;"><?php echo e($initials); ?></div>
                <?php endif; ?>
                <h4 class="staff-name"><?php echo e($member['name']); ?></h4>
                <div class="staff-position"><?php echo e($member['position']); ?></div>
                <p class="staff-bio"><?php echo e($member['bio']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================
     资质证书展示
     ============================================ -->
<section class="certificates-section section">
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title">资质<span class="highlight">认证</span></h2>
            <p class="section-subtitle">正规企业资质，值得信赖的合作伙伴</p>
        </div>

        <div class="certificates-grid">
            <?php
            $defaultCerts = [
                ['name'=>'营业执照','image'=>''],
                ['name'=>'增值电信业务经营许可证','image'=>''],
                ['name'=>'ISO27001信息安全认证','image'=>'']
            ];
            $displayCerts = !empty($certificates) ? array_values($certificates) : $defaultCerts;

            foreach ($displayCerts as $cert):
                $certImg = !empty($cert['image']) ? $cert['image'] : '';
            ?>
            <div class="certificate-card" data-animate="fade-up">
                <?php if ($certImg): ?>
                <img src="<?php echo e($certImg); ?>" alt="<?php echo e($cert['name']); ?>" class="certificate-img"
                     onerror="this.parentElement.innerHTML='<div style=\'width:100%;height:150px;background:linear-gradient(135deg,#f0f7ff,#e0efff);border-radius:8px;display:flex;align-items:center;justify-content:center;\'><div style=\'text-align:center;\'><i class=\'fa-solid fa-certificate\' style=\'font-size:40px;color:#0066CC;margin-bottom:10px;display:block;\'></i><span style=color:#0066CC;font-weight:600;font-size:14px;>'.e($cert['name']).'</span></div></div>'">
                <?php else: ?>
                <div style="width:100%;height:150px;background:linear-gradient(135deg,#f0f7ff,#e0efff);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <div style="text-align:center;">
                        <i class="fa-solid fa-certificate" style="font-size:40px;color:#0066CC;margin-bottom:10px;display:block;"></i>
                        <span style="color:#0066CC;font-weight:600;font-size:13px;"><?php echo e($cert['name']); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                <div class="certificate-name"><?php echo e($cert['name']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================
     CTA 行动召唤区
     ============================================ -->
<section style="background:linear-gradient(135deg,#0066CC 0%,#004080 100%);padding:80px 0;text-align:center;position:relative;overflow:hidden;" data-animate>
    <div style="position:absolute;inset:0;background:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;);"></div>
    <div class="container" style="position:relative;z-index:1;">
        <h2 style="font-size:clamp(28px,4vw,42px);font-weight:800;color:#fff;margin-bottom:16px;">准备好开始您的云计算之旅了吗？</h2>
        <p style="font-size:18px;color:rgba(255,255,255,0.85);max-width:560px;margin:0 auto 36px;">立即注册，享受新用户专享优惠。我们的专业团队将全程协助您完成部署。</p>
        <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
            <a href="/user/register.php" class="btn btn-lg" style="background:#fff;color:#0066CC;font-weight:700;padding:16px 36px;">
                <i class="fa-solid fa-rocket"></i> 立即注册
            </a>
            <a href="/contact.php" class="btn btn-outline btn-lg">
                <i class="fa-solid fa-phone"></i> 联系销售
            </a>
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

</body>
</html>
