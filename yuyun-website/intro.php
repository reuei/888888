<?php
/**
 * 语云科技企业官网 - 公司简介
 * 包含: 发展历程时间线、企业文化、团队介绍、荣誉资质
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
$staff = get_content('staff');
$certificates = get_content('certificates');

// 过滤启用的数据
$staff = array_filter($staff, fn($s) => ($s['status'] ?? 1) == 1);
$certificates = array_filter($certificates, fn($c) => ($c['status'] ?? 1) == 1);
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
    <meta name="description" content="<?php echo e($siteName); ?> - 公司简介，了解语云科技的发展历程、企业文化和荣誉资质">
    <meta name="keywords" content="<?php echo e($siteName); ?>,公司简介,发展历程,企业文化,云计算">
    <title>公司简介 - <?php echo e($siteName); ?></title>

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

    <!-- 时间线样式 -->
    <style>
        .timeline { position: relative; padding: 20px 0; }
        .timeline::before { content: ''; position: absolute; left: 50%; transform: translateX(-50%); width: 3px; height: 100%; background: linear-gradient(180deg, #0066CC 0%, #00A8E8 100%); border-radius: 3px; }
        .timeline-item { position: relative; margin-bottom: 48px; display: flex; align-items: flex-start; }
        .timeline-item:nth-child(odd) { flex-direction: row-reverse; text-align: right; padding-right: calc(50% + 40px); }
        .timeline-item:nth-child(even) { padding-left: calc(50% + 40px); }
        .timeline-dot { position: absolute; left: 50%; transform: translateX(-50%); width: 18px; height: 18px; background: #0066CC; border: 4px solid #fff; border-radius: 50%; box-shadow: 0 0 0 3px #0066CC33; z-index: 2; top: 4px; }
        .timeline-content { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 16px rgba(0,102,204,0.08); max-width: 420px; transition: all 0.3s; }
        .timeline-content:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,102,204,0.15); }
        .timeline-year { font-size: 28px; font-weight: 900; color: #0066CC; margin-bottom: 8px; }
        .timeline-title { font-size: 17px; font-weight: 700; color: #1f2937; margin-bottom: 8px; }
        .timeline-desc { font-size: 14px; color: #6b7280; line-height: 1.7; }

        @media (max-width: 768px) {
            .timeline::before { left: 20px; }
            .timeline-item { flex-direction: row !important; padding-left: 55px !important; padding-right: 0 !important; text-align: left !important; }
            .timeline-dot { left: 20px; }
            .timeline-content { max-width: 100%; }
        }

        .culture-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 28px; }
        .culture-card { background: #fff; border-radius: 16px; padding: 36px 28px; text-align: center; box-shadow: 0 4px 20px rgba(0,102,204,0.08); transition: all 0.3s; border-top: 4px solid transparent; }
        .culture-card:hover { transform: translateY(-8px); box-shadow: 0 12px 32px rgba(0,102,204,0.18); }
        .culture-card.mission { border-top-color: #0066CC; }
        .culture-card.vision { border-top-color: #00A8E8; }
        .culture-card.values { border-top-color: #ff6b35; }
        .culture-card.concept { border-top-color: #10b981; }
        .culture-icon { width: 72px; height: 72px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 32px; color: #fff; }
        .culture-card.mission .culture-icon { background: linear-gradient(135deg, #0066CC, #00A8E8); }
        .culture-card.vision .culture-icon { background: linear-gradient(135deg, #00A8E8, #00d4ff); }
        .culture-card.values .culture-icon { background: linear-gradient(135deg, #ff6b35, #ff8c42); }
        .culture-card.concept .culture-icon { background: linear-gradient(135deg, #10b981, #34d399); }
        .culture-card h3 { font-size: 20px; font-weight: 700; color: #1f2937; margin-bottom: 12px; }
        .culture-card p { color: #6b7280; line-height: 1.7; font-size: 14px; }
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
            <li><a href="/intro.php" class="nav-link active">公司简介</a></li>
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
    <a href="/" class="nav-link">首页</a>
    <a href="/about.php" class="nav-link">关于我们</a>
    <a href="/intro.php" class="nav-link active">公司简介</a>
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
     页面Hero区域
     ============================================ -->
<section class="page-hero" style="background:linear-gradient(135deg,#004080 0%,#0066CC 50%,#00A8E8 100%);padding:120px 0 80px;text-align:center;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;);"></div>
    <div class="container" style="position:relative;z-index:1;">
        <h1 style="font-size:clamp(32px,5vw,48px);font-weight:900;color:#fff;margin-bottom:16px;">公司简介</h1>
        <p style="font-size:18px;color:rgba(255,255,255,0.9);max-width:600px;margin:0 auto;">探索语云科技的成长之路与企业文化</p>

        <!-- 面包屑导航 -->
        <div style="margin-top:24px;display:flex;justify-content:center;align-items:center;gap:8px;color:rgba(255,255,255,0.75);font-size:14px;">
            <a href="/" style="color:rgba(255,255,255,0.85);text-decoration:none;"><i class="fa-solid fa-house"></i> 首页</a>
            <i class="fa-solid fa-chevron-right" style="font-size:11px;"></i>
            <span style="color:#fff;font-weight:500;">公司简介</span>
        </div>
    </div>
</section>

<!-- ============================================
     发展历程时间线 (2018-2025年)
     ============================================ -->
<section class="section" style="padding:80px 0;background:#f8fafc;" data-animate>
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title">发展<span class="highlight">历程</span></h2>
            <p class="section-subtitle">从创立到卓越，每一步都坚实有力</p>
        </div>

        <div class="timeline" style="margin-top:40px;">
            <div class="timeline-item" data-animate="fade-up">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-year">2018</div>
                    <div class="timeline-title"><i class="fa-solid fa-rocket" style="color:#0066CC;margin-right:6px;"></i>公司成立</div>
                    <div class="timeline-desc">语云科技在美国旧金山正式成立，开始布局全球云计算服务市场，确立以技术创新为核心的发展战略。</div>
                </div>
            </div>

            <div class="timeline-item" data-animate="fade-up delay-1">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-year">2019</div>
                    <div class="timeline-title"><i class="fa-solid fa-globe" style="color:#00A8E8;margin-right:6px;"></i>进军中国市场</div>
                    <div class="timeline-desc">在中国北京设立运营中心，获得相关经营许可，开始为国内企业提供优质的云计算服务。</div>
                </div>
            </div>

            <div class="timeline-item" data-animate="fade-up delay-2">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-year">2020</div>
                    <div class="timeline-title"><i class="fa-solid fa-server" style="color:#0066CC;margin-right:6px;"></i>产品线扩展</div>
                    <div class="timeline-desc">推出云服务器、云数据库、CDN加速等核心产品，完成中东、亚洲多个数据中心的建设部署。</div>
                </div>
            </div>

            <div class="timeline-item" data-animate="fade-up">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-year">2021</div>
                    <div class="timeline-title"><i class="fa-solid fa-users" style="color:#00A8E8;margin-right:6px;"></i>客户突破万级</div>
                    <div class="timeline-desc">服务企业客户突破10000家，获得ISO27001信息安全认证，建立完善的安全管理体系。</div>
                </div>
            </div>

            <div class="timeline-item" data-animate="fade-up delay-1">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-year">2022</div>
                    <div class="timeline-title"><i class="fa-solid fa-shield-halved" style="color:#0066CC;margin-right:6px;"></i>安全能力升级</div>
                    <div class="timeline-desc">发布Tbps级DDoS防护服务，欧洲数据中心上线，实现跨洲际网络加速覆盖。</div>
                </div>
            </div>

            <div class="timeline-item" data-animate="fade-up delay-2">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-year">2023</div>
                    <div class="timeline-title"><i class="fa-solid fa-chart-line" style="color:#00A8E8;margin-right:6px;"></i>业务高速增长</div>
                    <div class="timeline-desc">客户规模突破30000家，北美和澳洲数据中心相继投入运营，全球化服务网络初步形成。</div>
                </div>
            </div>

            <div class="timeline-item" data-animate="fade-up">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-year">2024</div>
                    <div class="timeline-title"><i class="fa-solid fa-trophy" style="color:#ff6b35;margin-right:6px;"></i>行业认可</div>
                    <div class="timeline-desc">荣获多项行业大奖，技术团队扩展至150人，推出AI智能运维平台，服务可用性达到99.99%。</div>
                </div>
            </div>

            <div class="timeline-item" data-animate="fade-up delay-1">
                <div class="timeline-dot"></div>
                <div class="timeline-content">
                    <div class="timeline-year">2025</div>
                    <div class="timeline-title"><i class="fa-solid fa-star" style="color:#10b981;margin-right:6px;"></i>全新征程</div>
                    <div class="timeline-desc">服务客户超过50000家企业，员工人数突破200人，持续引领行业创新，致力于成为全球领先的云计算服务商。</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     企业文化板块 (使命/愿景/价值观/理念)
     ============================================ -->
<section class="section" style="padding:80px 0;" data-animate>
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title">企业<span class="highlight">文化</span></h2>
            <p class="section-subtitle">我们的核心价值观指引着每一个决策和行动</p>
        </div>

        <div class="culture-grid" style="margin-top:40px;">
            <div class="culture-card mission" data-animate="fade-up">
                <div class="culture-icon">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <h3>使命 Mission</h3>
                <p>让云计算更简单、更安全、更高效。通过技术创新和服务优化，降低企业数字化转型的门槛，助力每一位客户实现业务增长。</p>
            </div>

            <div class="culture-card vision" data-animate="fade-up delay-1">
                <div class="culture-icon">
                    <i class="fa-solid fa-eye"></i>
                </div>
                <h3>愿景 Vision</h3>
                <p>成为全球最受信赖的云计算服务提供商，构建连接世界的数字基础设施，让优质的技术资源惠及每一个企业和个人。</p>
            </div>

            <div class="culture-card values" data-animate="fade-up delay-2">
                <div class="culture-icon">
                    <i class="fa-solid fa-gem"></i>
                </div>
                <h3>价值观 Values</h3>
                <p>客户至上 · 技术驱动 · 诚信为本 · 协作共赢 · 持续创新 · 追求卓越。我们坚信，只有为客户创造真正的价值，才能实现自身的长远发展。</p>
            </div>

            <div class="culture-card concept" data-animate="fade-up">
                <div class="culture-icon">
                    <i class="fa-solid fa-lightbulb"></i>
                </div>
                <h3>理念 Concept</h3>
                <p>"技术驱动，服务至上"。我们始终将技术创新作为核心竞争力，同时坚持以客户为中心的服务理念，提供超越期望的用户体验。</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     团队介绍 (复用员工数据)
     ============================================ -->
<section class="staff-section section" id="team-section" style="background:linear-gradient(135deg,#f0f7ff 0%,#e0efff 100%);">
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
     荣誉资质展示
     ============================================ -->
<section class="certificates-section section" style="padding:80px 0;">
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title">荣誉<span class="highlight">资质</span></h2>
            <p class="section-subtitle">正规企业资质，值得信赖的合作伙伴</p>
        </div>

        <div class="certificates-grid">
            <?php
            $defaultCerts = [
                ['name'=>'营业执照','image'=>''],
                ['name'=>'增值电信业务经营许可证','image'=>''],
                ['name'=>'ISO27001信息安全认证','image'=>''],
                ['name'=>'国家高新技术企业证书','image'=>''],
                ['name'=>'可信云服务认证','image'=>''],
                ['name=>'网络安全等级保护三级','image'=>'']
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
        <h2 style="font-size:clamp(28px,4vw,42px);font-weight:800;color:#fff;margin-bottom:16px;">加入语云科技，共创美好未来</h2>
        <p style="font-size:18px;color:rgba(255,255,255,0.85);max-width:560px;margin:0 auto 36px;">我们正在寻找志同道合的伙伴，一起推动云计算行业的创新发展</p>
        <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
            <a href="/contact.php" class="btn btn-lg" style="background:#fff;color:#0066CC;font-weight:700;padding:16px 36px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all 0.3s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.2)'" onmouseout="this.style.transform='none';this.style.boxShadow='none'"><i class="fa-solid fa-handshake"></i> 商务合作</a>
            <a href="/about.php" class="btn btn-lg" style="background:transparent;color:#fff;border:2px solid #fff;font-weight:700;padding:14px 34px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all 0.3s;" onmouseover="this.style.background='#fff';this.style.color='#0066CC'" onmouseout="this.style.background='transparent';this.style.color='#fff'"><i class="fa-solid fa-circle-info"></i> 了解更多</a>
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
