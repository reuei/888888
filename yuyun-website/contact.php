<?php
/**
 * 语云科技企业官网 - 联系我们
 * 包含: 联系表单、联系信息卡片、地图、社交媒体链接
 */

session_start();
define('YUYUN_ROOT', __DIR__);
require_once YUYUN_ROOT . '/core/Functions.php';

// 检查安装状态
if (!is_installed()) {
    header('Location: install.php');
    exit;
}

// 加载配置
$config = get_config();

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
    <meta name="description" content="<?php echo e($siteName); ?> - 联系我们，获取专业的云计算技术咨询与商务合作支持">
    <meta name="keywords" content="<?php echo e($siteName); ?>,联系我们,商务合作,技术支持,云计算咨询">
    <title>联系我们 - <?php echo e($siteName); ?></title>

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

    <!-- 联系表单样式 -->
    <style>
        .contact-layout { display:grid; grid-template-columns:1fr 420px; gap:40px; align-items:start; }
        .contact-form-wrapper { background:#fff; border-radius:16px; padding:40px; box-shadow:0 4px 24px rgba(0,102,204,0.1); }
        .form-group { margin-bottom:24px; }
        .form-label { display:block; font-size:14px; font-weight:600; color:#374151; margin-bottom:8px; }
        .form-label .required { color:#ef4444; margin-left:4px; }
        .form-input, .form-textarea, .form-select {
            width:100%; padding:14px 16px; border:2px solid #e5e7eb; border-radius:10px;
            font-size:15px; color:#1f2937; background:#f9fafb; transition:all 0.3s;
            font-family:'Noto Sans SC', sans-serif;
        }
        .form-input:focus, .form-textarea:focus, .form-select:focus {
            outline:none; border-color:#0066CC; background:#fff; box-shadow:0 0 0 3px rgba(0,102,204,0.1);
        }
        .form-textarea { min-height:140px; resize:vertical; }
        .submit-btn {
            width:100%; padding:16px; background:linear-gradient(135deg,#0066CC,#00A8E8); color:#fff;
            border:none; border-radius:10px; font-size:16px; font-weight:700; cursor:pointer;
            transition:all 0.3s; display:flex; align-items:center; justify-content:center; gap:10px;
        }
        .submit-btn:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(0,102,204,0.35); }

        .contact-info-sidebar { position:sticky; top:100px; }
        .info-card { background:#fff; border-radius:16px; padding:32px; box-shadow:0 4px 24px rgba(0,102,204,0.1); margin-bottom:24px; }
        .info-item { display:flex; align-items:flex-start; gap:16px; margin-bottom:24px; }
        .info-item:last-child { margin-bottom:0; }
        .info-icon { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:20px; color:#fff; }
        .info-icon.phone { background:linear-gradient(135deg,#ff6b35,#ff8c42); }
        .info-icon.email { background:linear-gradient(135deg,#0066CC,#00A8E8); }
        .info-icon.location { background:linear-gradient(135deg,#10b981,#34d399); }
        .info-icon.qq { background:linear-gradient(135deg,#12B7F5,#00d4ff); }
        .info-icon.time { background:linear-gradient(135deg,#8b5cf6,#a78bfa); }
        .info-content h4 { font-size:15px; font-weight:700; color:#1f2937; margin-bottom:4px; }
        .info-content p { font-size:14px; color:#6b7280; line-height:1.6; }
        .info-content a { color:#0066CC; text-decoration:none; font-weight:600; }
        .info-content a:hover { text-decoration:underline; }

        .map-card { height:280px; border-radius:16px; overflow:hidden; background:linear-gradient(135deg,#e8f4fd 0%,#d0e8f7 100%); display:flex; align-items:center; justify-content:center; }

        .social-links { display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:28px; }
        .social-link { width:52px; height:52px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:22px; color:#fff; text-decoration:none; transition:all 0.3s; }
        .social-link:hover { transform:translateY(-4px) scale(1.1); }
        .social-link.wechat { background:#07c160; }
        .social-link.weibo { background:#e6162d; }
        .social-link.qq { background:#12b7f5; }
        .social-link.twitter { background:#1da1f2; }
        .social-link.linkedin { background:#0077b5; }
        .social-link.github { background:#333; }

        @media (max-width: 1024px) {
            .contact-layout { grid-template-columns:1fr; }
            .contact-info-sidebar { position:relative; top:0; }
        }
        @media (max-width: 768px) {
            .contact-form-wrapper { padding:24px; }
            .info-card { padding:24px; }
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
            <li><a href="/products.php" class="nav-link">产品服务</a></li>
            <li><a href="/partners.php" class="nav-link">合作伙伴</a></li>
            <li><a href="/contact.php" class="nav-link active">联系我们</a></li>
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
    <a href="/products.php" class="nav-link">产品服务</a>
    <a href="/partners.php" class="nav-link">合作伙伴</a>
    <a href="/contact.php" class="nav-link active">联系我们</a>
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
        <h1 style="font-size:clamp(32px,5vw,48px);font-weight:900;color:#fff;margin-bottom:16px;">联系我们</h1>
        <p style="font-size:18px;color:rgba(255,255,255,0.9);max-width:600px;margin:0 auto;">随时与我们取得联系，获取专业的技术咨询服务</p>

        <!-- 面包屑导航 -->
        <div style="margin-top:24px;display:flex;justify-content:center;align-items:center;gap:8px;color:rgba(255,255,255,0.75);font-size:14px;">
            <a href="/" style="color:rgba(255,255,255,0.85);text-decoration:none;"><i class="fa-solid fa-house"></i> 首页</a>
            <i class="fa-solid fa-chevron-right" style="font-size:11px;"></i>
            <span style="color:#fff;font-weight:500;">联系我们</span>
        </div>
    </div>
</section>

<!-- ============================================
     联系表单 + 信息卡片 (左右布局)
     ============================================ -->
<section class="section" style="padding:80px 0;" data-animate>
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title">获取<span class="highlight">专业支持</span></h2>
            <p class="section-subtitle">填写以下表单，我们的专业团队将在24小时内与您联系</p>
        </div>

        <div class="contact-layout" style="margin-top:40px;">
            <!-- 左侧：联系表单 -->
            <div class="contact-form-wrapper" data-animate="fade-right">
                <form id="contactForm" action="#" method="POST" onsubmit="return handleContactSubmit(event)">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                        <div class="form-group">
                            <label class="form-label">姓名 <span class="required">*</span></label>
                            <input type="text" name="name" class="form-input" placeholder="请输入您的姓名" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">电话 <span class="required">*</span></label>
                            <input type="tel" name="phone" class="form-input" placeholder="请输入您的联系电话" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">邮箱 <span class="required">*</span></label>
                        <input type="email" name="email" class="form-input" placeholder="请输入您的邮箱地址" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">咨询主题</label>
                        <select name="subject" class="form-select">
                            <option value="">请选择咨询主题</option>
                            <option value="product">产品咨询</option>
                            <option value="price">价格方案</option>
                            <option value="technical">技术支持</option>
                            <option value="business">商务合作</option>
                            <option value="complaint">投诉建议</option>
                            <option value="other">其他问题</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">详细描述 <span class="required">*</span></label>
                        <textarea name="message" class="form-textarea" placeholder="请详细描述您的需求或问题，我们将为您提供针对性的解决方案..." required></textarea>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fa-solid fa-paper-plane"></i> 提交咨询
                    </button>

                    <p style="text-align:center;margin-top:16px;font-size:13px;color:#9ca3af;">
                        <i class="fa-solid fa-shield-halved" style="margin-right:4px;color:#10b981;"></i>
                        您的信息将被严格保密，仅用于业务联系
                    </p>
                </form>
            </div>

            <!-- 右侧：联系信息卡片 + 地图 -->
            <div class="contact-info-sidebar" data-animate="fade-left">
                <!-- 联系信息卡片 -->
                <div class="info-card">
                    <h3 style="font-size:20px;font-weight:700;color:#1f2937;margin-bottom:28px;display:flex;align-items:center;gap:10px;">
                        <i class="fa-solid fa-address-book" style="color:#0066CC;"></i> 联系方式
                    </h3>

                    <div class="info-item">
                        <div class="info-icon phone">
                            <i class="fa-solid fa-phone-volume"></i>
                        </div>
                        <div class="info-content">
                            <h4>销售热线</h4>
                            <p><a href="tel:4008008541" style="font-size:18px;font-weight:700;color:#ff6b35;">400-800-8541</a></p>
                            <p style="font-size:13px;margin-top:4px;">工作日 9:00-18:00</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon email">
                            <i class="fa-solid fa-envelope-circle-check"></i>
                        </div>
                        <div class="info-content">
                            <h4>电子邮箱</h4>
                            <p><a href="mailto:<?php echo e($config['admin_email'] ?? 'support@yuyun.com'); ?>"><?php echo e($config['admin_email'] ?? 'support@yuyun.com'); ?></a></p>
                            <p style="font-size:13px;margin-top:4px;">24小时内回复</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon location">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div class="info-content">
                            <h4>公司地址</h4>
                            <p>北京市海淀区中关村软件园二期<br>互联网创新中心大厦 18层</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon qq">
                            <i class="fa-brands fa-qq"></i>
                        </div>
                        <div class="info-content">
                            <h4>官方QQ群</h4>
                            <p><?php if ($qqGroup): ?><a href="<?php echo e($qqGroup); ?>" target="_blank"><?php echo e($qqGroup); ?></a><?php else: ?>点击右侧工具栏加入<?php endif; ?></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon time">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="info-content">
                            <h4>工作时间</h4>
                            <p>周一至周五 9:00 - 18:00<br>（北京时间，节假日除外）</p>
                        </div>
                    </div>
                </div>

                <!-- 地图卡片 -->
                <div class="map-card">
                    <iframe src="https://www.openstreetmap.org/export/embed.html?bbox=116.3%2C39.9%2C116.5%2C40.0&layer=mapnik" width="100%" height="100%" style="border:none;" loading="lazy" title="公司位置地图"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================
     其他联系方式 (社交媒体图标)
     ============================================ -->
<section class="section" style="background:linear-gradient(135deg,#f0f7ff 0%,#e0efff 100%);padding:60px 0;" data-animate>
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title">关注<span class="highlight">我们</span></h2>
            <p class="section-subtitle">通过社交媒体获取最新动态和技术资讯</p>
        </div>

        <div class="social-links">
            <a href="#" class="social-link wechat" title="微信公众号" onclick="alert('敬请期待微信二维码')">
                <i class="fa-brands fa-weixin"></i>
            </a>
            <a href="#" class="social-link weibo" title="官方微博" target="_blank" rel="noopener">
                <i class="fa-brands fa-weibo"></i>
            </a>
            <?php if ($qqGroup): ?>
            <a href="<?php echo e($qqGroup); ?>" class="social-link qq" title="QQ群" target="_blank">
                <i class="fa-brands fa-qq"></i>
            </a>
            <?php endif; ?>
            <a href="#" class="social-link twitter" title="Twitter/X" target="_blank" rel="noopener">
                <i class="fa-brands fa-x-twitter"></i>
            </a>
            <a href="#" class="social-link linkedin" title="LinkedIn" target="_blank" rel="noopener">
                <i class="fa-brands fa-linkedin-in"></i>
            </a>
            <a href="#" class="social-link github" title="GitHub" target="_blank" rel="noopener">
                <i class="fa-brands fa-github"></i>
            </a>
        </div>
    </div>
</section>

<!-- ============================================
     CTA 快速响应承诺
     ============================================ -->
<section style="background:linear-gradient(135deg,#1f2937 0%,#111827 100%);padding:80px 0;text-align:center;position:relative;overflow:hidden;" data-animate>
    <div style="position:absolute;inset:0;background:url(&quot;data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E&quot;);"></div>
    <div class="container" style="position:relative;z-index:1;">
        <h2 style="font-size:clamp(26px,4vw,38px);font-weight:800;color:#fff;margin-bottom:24px;">我们的服务承诺</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:28px;max-width:900px;margin:0 auto;">
            <div style="text-align:center;padding:20px;">
                <div style="width:64px;height:64px;background:rgba(255,107,53,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fa-solid fa-bolt" style="font-size:28px;color:#ff6b35;"></i>
                </div>
                <h3 style="font-size:18px;font-weight:700;color:#fff;margin-bottom:8px;">快速响应</h3>
                <p style="color:rgba(255,255,255,0.65);font-size:14px;">平均响应时间 &lt; 15分钟<br>紧急问题即时处理</p>
            </div>
            <div style="text-align:center;padding:20px;">
                <div style="width:64px;height:64px;background:rgba(0,168,232,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fa-solid fa-headset" style="font-size:28px;color:#00A8E8;"></i>
                </div>
                <h3 style="font-size:18px;font-weight:700;color:#fff;margin-bottom:8px;">专业团队</h3>
                <p style="color:rgba(255,255,255,0.65);font-size:14px;">资深工程师一对一服务<br>提供定制化解决方案</p>
            </div>
            <div style="text-align:center;padding:20px;">
                <div style="width:64px;height:64px;background:rgba(16,185,129,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                    <i class="fa-solid fa-clock" style="font-size:28px;color:#10b981;"></i>
                </div>
                <h3 style="font-size:18px;font-weight:700;color:#fff;margin-bottom:8px;">全天候支持</h3>
                <p style="color:rgba(255,255,255,0.65);font-size:14px;">7×24小时在线支持<br>节假日不间断服务</p>
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

<!-- 表单提交处理脚本 -->
<script>
function handleContactSubmit(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    // 基本验证
    const name = formData.get('name').trim();
    const email = formData.get('email').trim();
    const phone = formData.get('phone').trim();
    const message = formData.get('message').trim();

    if (!name || !email || !phone || !message) {
        showToast('请填写所有必填项', 'error');
        return false;
    }

    // 邮箱格式验证
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showToast('请输入有效的邮箱地址', 'error');
        return false;
    }

    // 手机号格式验证
    const phoneRegex = /^1[3-9]\d{9}$/;
    if (!phoneRegex.test(phone.replace(/[-\s]/g, ''))) {
        showToast('请输入有效的手机号码', 'error');
        return false;
    }

    // 模拟提交（实际项目中应发送到后端API）
    const submitBtn = form.querySelector('.submit-btn');
    const originalText = submitBtn.innerHTML;

    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> 提交中...';
    submitBtn.disabled = true;

    setTimeout(() => {
        showToast('提交成功！我们的团队将在24小时内与您联系', 'success');
        form.reset();
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 1500);

    return false;
}

// Toast提示函数（如果main.js中未定义）
if (typeof showToast !== 'function') {
    window.showToast = function(message, type = 'info') {
        console.log(`[${type.toUpperCase()}] ${message}`);
        alert(message);
    };
}
</script>

</body>
</html>
