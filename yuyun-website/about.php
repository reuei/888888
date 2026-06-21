<?php
/**
 * 语云科技企业官网 - 关于我们
 * 包含: 公司信息卡片、公司介绍、核心优势、地图位置、联系方式
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

// 过滤启用的数据
$staff = array_filter($staff, fn($s) => ($s['status'] ?? 1) == 1);
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
    <meta name="description" content="<?php echo e($siteName); ?> - 了解语云科技的使命、愿景与价值观，全球领先的云计算服务提供商">
    <meta name="keywords" content="<?php echo e($siteName); ?>,关于我们,公司介绍,云计算,云服务">
    <title>关于我们 - <?php echo e($siteName); ?></title>

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
            <li><a href="/" class="nav-link">首页</a></li>
            <li><a href="/about.php" class="nav-link active">关于我们</a></li>
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
    <a href="/" class="nav-link">首页</a>
    <a href="/about.php" class="nav-link active">关于我们</a>
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
     页面Hero区域 (蓝色渐变背景)
     ============================================ -->
<section class="page-hero" style="background:linear-gradient(135deg,#004080 0%,#0066CC 50%,#00A8E8 100%);padding:120px 0 80px;text-align:center;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;);"></div>
    <div class="container" style="position:relative;z-index:1;">
        <h1 style="font-size:clamp(32px,5vw,48px);font-weight:900;color:#fff;margin-bottom:16px;">关于语云科技</h1>
        <p style="font-size:18px;color:rgba(255,255,255,0.9);max-width:600px;margin:0 auto;">了解我们的使命、愿景与价值观</p>

        <!-- 面包屑导航 -->
        <div style="margin-top:24px;display:flex;justify-content:center;align-items:center;gap:8px;color:rgba(255,255,255,0.75);font-size:14px;">
            <a href="/" style="color:rgba(255,255,255,0.85);text-decoration:none;"><i class="fa-solid fa-house"></i> 首页</a>
            <i class="fa-solid fa-chevron-right" style="font-size:11px;"></i>
            <span style="color:#fff;font-weight:500;">关于我们</span>
        </div>
    </div>
</section>

<!-- ============================================
     公司信息卡片区域
     ============================================ -->
<section class="section" style="background:#f8fafc;padding:60px 0;" data-animate>
    <div class="container">
        <div class="company-info-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:24px;margin-bottom:20px;">
            <div class="info-card" style="background:#fff;border-radius:12px;padding:28px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,0.06);transition:transform 0.3s,box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,102,204,0.15)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">
                <i class="fa-solid fa-building" style="font-size:36px;color:#0066CC;margin-bottom:12px;display:block;"></i>
                <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">公司全称</div>
                <div style="font-size:16px;font-weight:700;color:#1f2937;">语云科技<br><span style="font-size:14px;color:#0066CC;">Yuyun Technology</span></div>
            </div>

            <div class="info-card" style="background:#fff;border-radius:12px;padding:28px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,0.06);transition:transform 0.3s,box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,102,204,0.15)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">
                <i class="fa-solid fa-calendar-check" style="font-size:36px;color:#00A8E8;margin-bottom:12px;display:block;"></i>
                <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">成立时间</div>
                <div style="font-size:16px;font-weight:700;color:#1f2937;">2018年</div>
            </div>

            <div class="info-card" style="background:#fff;border-radius:12px;padding:28px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,0.06);transition:transform 0.3s,box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,102,204,0.15)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">
                <i class="fa-solid fa-location-dot" style="font-size:36px;color:#0066CC;margin-bottom:12px;display:block;"></i>
                <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">总部位置</div>
                <div style="font-size:16px;font-weight:700;color:#1f2937;">中国·北京 | 美国·旧金山</div>
            </div>

            <div class="info-card" style="background:#fff;border-radius:12px;padding:28px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,0.06);transition:transform 0.3s,box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,102,204,0.15)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">
                <i class="fa-solid fa-users" style="font-size:36px;color:#00A8E8;margin-bottom:12px;display:block;"></i>
                <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">员工人数</div>
                <div style="font-size:16px;font-weight:700;color:#1f2937;">200+</div>
            </div>

            <div class="info-card" style="background:#fff;border-radius:12px;padding:28px;text-align:center;box-shadow:0 2px 12px rgba(0,0,0,0.06);transition:transform 0.3s,box-shadow 0.3s;" onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,102,204,0.15)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 2px 12px rgba(0,0,0,0.06)'">
                <i class="fa-solid fa-handshake" style="font-size:36px;color:#0066CC;margin-bottom:12px;display:block;"></i>
                <div style="font-size:13px;color:#6b7280;margin-bottom:6px;">服务客户</div>
                <div style="font-size:16px;font-weight:700;color:#1f2937;">50000+ 企业</div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     公司介绍长文本 (3段)
     ============================================ -->
<section class="section" style="padding:80px 0;" data-animate>
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title">公司<span class="highlight">介绍</span></h2>
            <p class="section-subtitle">深耕云计算领域，助力全球企业数字化转型</p>
        </div>

        <div style="max-width:900px;margin:0 auto;line-height:1.9;color:#374151;font-size:16px;">
            <p style="margin-bottom:24px;text-indent:2em;">
                语云科技是美国Yuyun Technology USA Inc.在中国的注册授权运营方，是一家专注于云计算服务的高新技术企业。自2018年成立以来，我们始终秉承"技术驱动、服务至上"的理念，致力于为全球企业提供安全、稳定、高效的云计算解决方案。我们的核心团队由来自世界知名互联网公司的资深工程师组成，拥有丰富的云计算架构设计和运维经验。
            </p>
            <p style="margin-bottom:24px;text-indent:2em;">
                我们致力于为全球企业提供安全、稳定、高效的云计算服务，涵盖云服务器、云数据库、CDN加速、对象存储、域名注册、DDoS防护等全方位产品线。通过持续的技术创新和严格的质量管控，我们已经建立了覆盖中东、欧洲、亚洲、北美、澳洲等多个地区的全球化服务网络。无论您的业务位于何处，我们都能够提供低延迟、高可用的云端基础设施支持。
            </p>
            <p style="text-indent:2em;">
                在中东、欧洲、亚洲、北美、澳洲等地拥有多个数据中心，构建了完善的全球网络节点体系。我们采用业界领先的技术架构和安全标准，确保客户数据的安全性和隐私保护。同时，我们提供7×24小时专业技术支持，拥有快速响应机制和完善的服务保障体系，让每一位客户都能享受到专业、贴心的服务体验。
            </p>
        </div>
    </div>
</section>

<!-- ============================================
     核心优势 (4个图标卡片)
     ============================================ -->
<section class="section" style="background:linear-gradient(135deg,#f0f7ff 0%,#e0efff 100%);padding:80px 0;" data-animate>
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title">核心<span class="highlight">优势</span></h2>
            <p class="section-subtitle">四大核心优势，铸就卓越品质</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:28px;margin-top:40px;">
            <div style="background:#fff;border-radius:16px;padding:36px 28px;text-align:center;box-shadow:0 4px 20px rgba(0,102,204,0.08);transition:all 0.3s;" onmouseover="this.style.transform='translateY(-8px)';this.style.boxShadow='0 12px 32px rgba(0,102,204,0.18)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 20px rgba(0,102,204,0.08)'">
                <div style="width:72px;height:72px;background:linear-gradient(135deg,#0066CC,#00A8E8);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <i class="fa-solid fa-earth-americas" style="font-size:32px;color:#fff;"></i>
                </div>
                <h3 style="font-size:20px;font-weight:700;color:#1f2937;margin-bottom:12px;">全球覆盖</h3>
                <p style="color:#6b7280;line-height:1.7;font-size:14px;">在中东、欧洲、亚洲、北美、澳洲等地拥有多个数据中心，实现真正的全球化服务网络覆盖。</p>
            </div>

            <div style="background:#fff;border-radius:16px;padding:36px 28px;text-align:center;box-shadow:0 4px 20px rgba(0,102,204,0.08);transition:all 0.3s;" onmouseover="this.style.transform='translateY(-8px)';this.style.boxShadow='0 12px 32px rgba(0,102,204,0.18)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 20px rgba(0,102,204,0.08)'">
                <div style="width:72px;height:72px;background:linear-gradient(135deg,#00A8E8,#00d4ff);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <i class="fa-solid fa-shield-halved" style="font-size:32px;color:#fff;"></i>
                </div>
                <h3 style="font-size:20px;font-weight:700;color:#1f2937;margin-bottom:12px;">安全可靠</h3>
                <p style="color:#6b7280;line-height:1.7;font-size:14px;">ISO27001信息安全认证，多重数据备份，Tbps级DDoS防护，确保您的数据安全无忧。</p>
            </div>

            <div style="background:#fff;border-radius:16px;padding:36px 28px;text-align:center;box-shadow:0 4px 20px rgba(0,102,204,0.08);transition:all 0.3s;" onmouseover="this.style.transform='translateY(-8px)';this.style.boxShadow='0 12px 32px rgba(0,102,204,0.18)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 20px rgba(0,102,204,0.08)'">
                <div style="width:72px;height:72px;background:linear-gradient(135deg,#0066CC,#004080);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <i class="fa-solid fa-microchip" style="font-size:32px;color:#fff;"></i>
                </div>
                <h3 style="font-size:20px;font-weight:700;color:#1f2937;margin-bottom:12px;">技术领先</h3>
                <p style="color:#6b7280;line-height:1.7;font-size:14px;">采用最新的云计算技术和容器化架构，持续技术创新，保持行业领先地位。</p>
            </div>

            <div style="background:#fff;border-radius:16px;padding:36px 28px;text-align:center;box-shadow:0 4px 20px rgba(0,102,204,0.08);transition:all 0.3s;" onmouseover="this.style.transform='translateY(-8px)';this.style.boxShadow='0 12px 32px rgba(0,102,204,0.18)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 20px rgba(0,102,204,0.08)'">
                <div style="width:72px;height:72px;background:linear-gradient(135deg,#ff6b35,#ff8c42);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <i class="fa-solid fa-headset" style="font-size:32px;color:#fff;"></i>
                </div>
                <h3 style="font-size:20px;font-weight:700;color:#1f2937;margin-bottom:12px;">服务优质</h3>
                <p style="color:#6b7280;line-height:1.7;font-size:14px;">7×24小时专业技术支持，响应时间<15分钟，99.99%服务可用性保障。</p>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     地图嵌入区域 + 地址信息
     ============================================ -->
<section class="section" style="padding:80px 0;background:#fff;" data-animate>
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title">公司<span class="highlight">位置</span></h2>
            <p class="section-subtitle">欢迎莅临参观或洽谈合作</p>
        </div>

        <div style="display:grid;grid-template-columns:1fr 380px;gap:32px;align-items:start;margin-top:40px;">
            <!-- 地图占位 -->
            <div style="background:linear-gradient(135deg,#e8f4fd 0%,#d0e8f7 100%);border-radius:16px;height:400px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;">
                <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=116.3%2C39.9%2C116.5%2C40.0&layer=mapnik" width="100%" height="100%" style="border:none;border-radius:16px;" loading="lazy"></iframe>
            </div>

            <!-- 地址信息卡片 -->
            <div style="background:#f8fafc;border-radius:16px;padding:32px;">
                <h3 style="font-size:20px;font-weight:700;color:#1f2937;margin-bottom:24px;display:flex;align-items:center;gap:10px;">
                    <i class="fa-solid fa-location-dot" style="color:#0066CC;"></i> 联系地址
                </h3>

                <div style="space-y:20px;">
                    <div style="margin-bottom:20px;">
                        <div style="font-size:13px;color:#6b7280;margin-bottom:6px;font-weight:500;">中国总部</div>
                        <div style="color:#1f2937;line-height:1.6;">北京市海淀区中关村软件园二期<br>互联网创新中心大厦 18层</div>
                    </div>

                    <div style="margin-bottom:20px;">
                        <div style="font-size:13px;color:#6b7280;margin-bottom:6px;font-weight:500;">美国总部</div>
                        <div style="color:#1f2937;line-height:1.6;">San Francisco, CA 94105<br>United States</div>
                    </div>

                    <hr style="border:none;border-top:1px solid #e5e7eb;margin:20px 0;">

                    <div style="margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                        <i class="fa-solid fa-phone" style="color:#ff6b35;width:20px;text-align:center;"></i>
                        <div>
                            <div style="font-size:13px;color:#6b7280;">销售电话</div>
                            <div style="font-size:18px;font-weight:700;color:#ff6b35;">400-800-8541</div>
                        </div>
                    </div>

                    <div style="margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                        <i class="fa-solid fa-envelope" style="color:#0066CC;width:20px;text-align:center;"></i>
                        <div>
                            <div style="font-size:13px;color:#6b7280;">邮箱地址</div>
                            <div style="color:#1f2937;"><?php echo e($config['admin_email'] ?? 'support@yuyun.com'); ?></div>
                        </div>
                    </div>

                    <div style="display:flex;align-items:center;gap:10px;">
                        <i class="fa-brands fa-qq" style="color:#12B7F5;width:20px;text-align:center;"></i>
                        <div>
                            <div style="font-size:13px;color:#6b7280;">QQ群</div>
                            <div style="color:#1f2937;"><?php echo $qqGroup ? e($qqGroup) : '点击右侧工具栏加入'; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     联系方式区块
     ============================================ -->
<section class="section" style="background:linear-gradient(135deg,#1f2937 0%,#111827 100%);padding:80px 0;color:#fff;" data-animate>
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title" style="color:#fff;">联系方式</h2>
            <p class="section-subtitle" style="color:rgba(255,255,255,0.7);">随时联系我们，获取专业的技术咨询与服务支持</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:28px;margin-top:40px;">
            <div style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:32px;text-align:center;backdrop-filter:blur(10px);">
                <div style="width:64px;height:64px;background:linear-gradient(135deg,#ff6b35,#ff8c42);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <i class="fa-solid fa-phone-volume" style="font-size:28px;color:#fff;"></i>
                </div>
                <h3 style="font-size:22px;font-weight:700;margin-bottom:8px;color:#ff6b35;">400-800-8541</h3>
                <p style="color:rgba(255,255,255,0.65);font-size:14px;">销售咨询热线</p>
                <p style="color:rgba(255,255,255,0.55);font-size:13px;margin-top:8px;">工作日 9:00-18:00</p>
            </div>

            <div style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:32px;text-align:center;backdrop-filter:blur(10px);">
                <div style="width:64px;height:64px;background:linear-gradient(135deg,#0066CC,#00A8E8);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <i class="fa-solid fa-envelope-circle-check" style="font-size:28px;color:#fff;"></i>
                </div>
                <h3 style="font-size:18px;font-weight:700;margin-bottom:8px;"><?php echo e($config['admin_email'] ?? 'support@yuyun.com'); ?></h3>
                <p style="color:rgba(255,255,255,0.65);font-size:14px;">商务合作邮箱</p>
                <p style="color:rgba(255,255,255,0.55);font-size:13px;margin-top:8px;">24小时内回复</p>
            </div>

            <div style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:32px;text-align:center;backdrop-filter:blur(10px);">
                <div style="width:64px;height:64px;background:linear-gradient(135deg,#12B7F5,#00d4ff);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <i class="fa-brands fa-qq" style="font-size:28px;color:#fff;"></i>
                </div>
                <h3 style="font-size:18px;font-weight:700;margin-bottom:8px;">官方QQ群</h3>
                <p style="color:rgba(255,255,255,0.65);font-size:14px;">技术交流群</p>
                <p style="color:rgba(255,255,255,0.55);font-size:13px;margin-top:8px;"><?php if ($qqGroup): ?><a href="<?php echo e($qqGroup); ?>" target="_blank" style="color:#00A8E8;text-decoration:none;">点击加入</a><?php else: ?>点击右侧工具栏加入<?php endif; ?></p>
            </div>

            <div style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:16px;padding:32px;text-align:center;backdrop-filter:blur(10px);">
                <div style="width:64px;height:64px;background:linear-gradient(135deg,#10b981,#34d399);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                    <i class="fa-solid fa-clock" style="font-size:28px;color:#fff;"></i>
                </div>
                <h3 style="font-size:18px;font-weight:700;margin-bottom:8px;">工作时间</h3>
                <p style="color:rgba(255,255,255,0.65);font-size:14px;">周一至周五</p>
                <p style="color:rgba(255,255,255,0.55);font-size:13px;margin-top:8px;">9:00 - 18:00 (北京时间)</p>
            </div>
        </div>

        <!-- 官方群聊入口 -->
        <div style="text-align:center;margin-top:48px;padding:32px;background:rgba(255,107,53,0.1);border:1px dashed rgba(255,107,53,0.3);border-radius:12px;">
            <p style="color:rgba(255,255,255,0.8);font-size:16px;margin-bottom:16px;"><i class="fa-solid fa-comments" style="color:#ff6b35;margin-right:8px;"></i> 加入官方群聊，获取最新优惠信息和技术支持</p>
            <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
                <?php if ($qqGroup): ?>
                <a href="<?php echo e($qqGroup); ?>" target="_blank" class="btn" style="background:#12B7F5;color:#fff;border:none;padding:12px 28px;border-radius:8px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all 0.3s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(18,183,245,0.4)'" onmouseout="this.style.transform='none';this.style.boxShadow='none'"><i class="fa-brands fa-qq"></i> 加入QQ群</a>
                <?php endif; ?>
                <a href="mailto:<?php echo e($config['admin_email'] ?? 'support@yuyun.com'); ?>" class="btn" style="background:#0066CC;color:#fff;border:none;padding:12px 28px;border-radius:8px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all 0.3s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,102,204,0.4)'" onmouseout="this.style.transform='none';this.style.boxShadow='none'"><i class="fa-solid fa-envelope"></i> 发送邮件</a>
                <a href="tel:4008008541" class="btn" style="background:#ff6b35;color:#fff;border:none;padding:12px 28px;border-radius:8px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:8px;transition:all 0.3s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(255,107,53,0.4)'" onmouseout="this.style.transform='none';this.style.boxShadow='none'"><i class="fa-solid fa-phone"></i> 电话咨询</a>
            </div>
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
