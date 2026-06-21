<?php
/**
 * 语云科技企业官网 - 合作伙伴
 */

session_start();
define('YUYUN_ROOT', __DIR__);
require_once YUYUN_ROOT . '/core/Functions.php';

if (!is_installed()) {
    header('Location: install.php');
    exit;
}

$config = get_config();
$partners = get_content('partners');
$siteName = $config['site_name'] ?? '语云科技';
$logoUrl = $config['logo_url'] ?? '';
$icpNumber = $config['icp_number'] ?? '';
$icpLicense = $config['icp_license'] ?? '';
$policeNumber = $config['police_number'] ?? '';

$partners = array_filter($partners, fn($p) => ($p['status'] ?? 1) == 1);
usort($partners, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>合作伙伴 - <?php echo e($siteName); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@300;400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/animations.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">
</head>
<body>

<!-- 导航栏 -->
<nav class="navbar" id="navbar">
    <div class="container navbar-inner">
        <a href="/" class="nav-logo">
            <?php if ($logoUrl): ?>
            <img src="<?php echo e($logoUrl); ?>" alt="<?php echo e($siteName); ?>">
            <?php else: ?>
            <div class="nav-logo-text"><?php echo e($siteName); ?><span>®</span></div>
            <?php endif; ?>
        </a>
        <ul class="nav-menu">
            <li><a href="/" class="nav-link">首页</a></li>
            <li><a href="/about.php" class="nav-link">关于我们</a></li>
            <li><a href="/intro.php" class="nav-link">公司简介</a></li>
            <li><a href="/products.php" class="nav-link">产品服务</a></li>
            <li><a href="/partners.php" class="nav-link active">合作伙伴</a></li>
            <li><a href="/contact.php" class="nav-link">联系我们</a></li>
            <li><a href="https://cloud.loveym.cloud" target="_blank" rel="noopener" class="nav-link"><i class="fa-solid fa-globe"></i> 国际版</a></li>
        </ul>
        <div class="nav-actions">
            <?php if (is_logged_in()): ?>
            <a href="/user/dashboard.php" class="nav-btn-primary"><i class="fa-solid fa-user"></i> 用户中心</a>
            <?php else: ?>
            <a href="/user/login.php" class="btn btn-outline-dark btn-sm"><i class="fa-solid fa-right-to-bracket"></i> 登录</a>
            <a href="/user/register.php" class="nav-btn-primary">免费注册</a>
            <?php endif; ?>
            <button class="hamburger" id="hamburger" aria-label="菜单"><span></span><span></span><span></span></button>
        </div>
    </div>
</nav>

<div class="mobile-menu-overlay" id="menuOverlay"></div>
<div class="mobile-menu" id="mobileMenu">
    <a href="/" class="nav-link">首页</a><a href="/about.php" class="nav-link">关于我们</a>
    <a href="/intro.php" class="nav-link">公司简介</a><a href="/products.php" class="nav-link">产品服务</a>
    <a href="/partners.php" class="nav-link active">合作伙伴</a><a href="/contact.php" class="nav-link">联系我们</a>
    <a href="https://cloud.loveym.cloud" target="_blank" rel="noopener" class="nav-link"><i class="fa-solid fa-globe"></i> 国际版官网</a>
    <hr style="border:none;border-top:1px solid #eee;margin:16px 0;">
    <?php if (is_logged_in()): ?>
    <a href="/user/dashboard.php" class="nav-link"><i class="fa-solid fa-user"></i> 用户中心</a>
    <a href="/user/logout.php" class="nav-link"><i class="fa-solid fa-right-from-bracket"></i> 退出登录</a>
    <?php else: ?>
    <a href="/user/login.php" class="nav-link"><i class="fa-solid fa-right-to-bracket"></i> 登录</a>
    <a href="/user/register.php" class="nav-link"><i class="fa-solid fa-user-plus"></i> 注册</a>
    <?php endif; ?>
</div>

<!-- Hero -->
<section class="page-hero">
    <div class="container" style="position:relative;">
        <div class="breadcrumb">
            <a href="/">首页</a><span class="breadcrumb-separator">/</span><span>合作伙伴</span>
        </div>
        <h1>合作伙伴</h1>
        <p>携手全球行业领袖，共建数字化生态体系</p>
    </div>
</section>

<!-- 合作伙伴展示 -->
<section class="section" style="background:var(--gray-50);">
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title">我们的<span class="highlight">战略合作伙伴</span></h2>
            <p class="section-subtitle">与全球领先的技术和服务提供商建立深度合作，为客户提供最优质的解决方案</p>
        </div>

        <!-- Logo墙 - 网格展示 -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:28px;margin-bottom:60px;" data-animate>
            <?php
            $defaultPartners = [
                ['name'=>'腾讯云','logo'=>'','url'=>'https://cloud.tencent.com','desc'=>'国内领先的云服务提供商'],
                ['name'=>'阿里云','logo'=>'','url'=>'https://www.aliyun.com','desc'=>'阿里巴巴集团云计算品牌'],
                ['name'=>'华为云','logo'=>'','url'=>'https://www.huaweicloud.com','desc'=>'华为公司云计算服务平台'],
                ['name'=>'Cloudflare','logo'=>'','url'=>'https://www.cloudflare.com','desc':'全球CDN与安全服务领导者'],
                ['name'=>'AWS亚马逊云','logo'=>'','url'=>'https://aws.amazon.com','desc':'Amazon Web Services'],
                ['name'=>'Google Cloud','logo'=>'','url'=>'https://cloud.google.com','desc'=>'谷歌云计算平台'],
                ['name'=>'Microsoft Azure','logo'=>'','url'=>'https://azure.microsoft.com','desc'=>'微软公有云服务平台'],
                ['name'=>'百度智能云','logo'=>'','url'=>'https://cloud.baidu.com','desc'=>'百度旗下智能云计算'],
                ['name'=>'京东云','logo'=>'','url'=>'https://cloud.jd.com','desc':'京东集团云计算服务'],
                ['name'=>'火山引擎','logo'=>'','url'=>'https://www.volcengine.com','desc':'字节跳动旗下云服务'],
                ['name'=>'UCloud优刻得','logo'=>'','url'=>'https://www.ucloud.cn','desc'=>'中立云计算服务商'],
                ['name'=>'金山云','logo'=>'','url'=>'https://www.ksyun.com','desc'=>'金山集团云计算品牌']
            ];
            $displayPartners = !empty($partners) ? array_values($partners) : $defaultPartners;

            foreach ($displayPartners as $partner):
                $pLogo = !empty($partner['logo_url']) ? $partner['logo_url'] : '';
                $pLink = !empty($partner['link_url']) ? $partner['link_url'] : '#';
                $pName = $partner['name'] ?? 'Partner';
                $pDesc = $partner['desc'] ?? '值得信赖的合作伙伴';
            ?>
            <a href="<?php echo e($pLink); ?>" target="<?php echo $pLink !== '#' ? '_blank' : '_self'; ?>" rel="noopener"
               class="partner-card" data-animate="fade-up"
               style="background:var(--white);border-radius:var(--radius-lg);padding:32px 20px;text-align:center;
                      border:1px solid var(--gray-200);transition:all var(--transition-normal);
                      text-decoration:none;display:block;">
                <div style="width:80px;height:80px;margin:0 auto 16px;background:var(--gray-50);border-radius:var(--radius-md);
                            display:flex;align-items:center;justify-content:center;">
                    <?php if ($pLogo): ?>
                    <img src="<?php echo e($pLogo); ?>" alt="<?php echo e($pName); ?>" style="max-width:64px;max-height:64px;object-fit:contain;"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div style="display:none;font-size:24px;font-weight:700;color:var(--primary);">
                        <?php echo mb_substr($pName, 0, 1); ?>
                    </div>
                    <?php else: ?>
                    <span style="font-size:24px;font-weight:700;color:var(--primary);"><?php echo mb_substr($pName, 0, 1); ?></span>
                    <?php endif; ?>
                </div>
                <h4 style="font-size:16px;font-weight:600;color:var(--gray-800);margin-bottom:6px;"><?php echo e($pName); ?></h4>
                <p style="font-size:13px;color:var(--gray-500);"><?php echo e($pDesc); ?></p>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- 横滚Logo条 -->
        <div style="background:var(--white);border-radius:var(--radius-xl);padding:40px;overflow:hidden;" data-animate="scale">
            <h3 style="text-align:center;margin-bottom:30px;font-size:18px;color:var(--gray-700);">
                <i class="fa-solid fa-handshake" style="color:var(--primary);margin-right:8px;"></i>
                我们与以下企业/组织协手共进
            </h3>
            <div style="overflow:hidden;">
                <div class="partners-track">
                    <?php
                    $scrollPartners = array_merge($displayPartners, $displayPartners, $displayPartners);
                    foreach ($scrollPartners as $sp):
                        $spLogo = !empty($sp['logo_url']) ? $sp['logo_url'] : '';
                    ?>
                    <span style="flex-shrink:0;padding:12px 24px;">
                        <span style="font-size:15px;color:var(--gray-400);font-weight:500;"><?php echo e($sp['name']); ?></span>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 合作优势 -->
<section class="section">
    <div class="container">
        <div class="section-header" data-animate>
            <h2 class="section-title">合作<span class="highlight">优势</span></h2>
            <p class="section-subtitle">选择语云科技作为您的技术合作伙伴</p>
        </div>
        <div class="product-grid">
            <div class="product-card" data-animate="fade-up">
                <div class="product-icon"><i class="fa-solid fa-globe"></i></div>
                <h3 class="product-name">全球覆盖</h3>
                <p class="product-desc">遍布全球的数据中心网络，为您的业务提供就近接入和低延迟访问体验。</p>
            </div>
            <div class="product-card" data-animate="fade-up" style="transition-delay:0.08s;">
                <div class="product-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <h3 class="product-name">安全可靠</h3>
                <p class="product-desc">多层安全防护体系，DDoS攻击防御，数据加密传输与存储。</p>
            </div>
            <div class="product-card" data-animate="fade-up" style="transition-delay:0.16s;">
                <div class="product-icon"><i class="fa-solid fa-headset"></i></div>
                <h3 class="product-name">专业支持</h3>
                <p class="product-desc">7x24小时专业技术支持团队，快速响应，贴心服务。</p>
            </div>
            <div class="product-card" data-animate="fade-up" style="transition-delay:0.24s;">
                <div class="product-icon"><i class="fa-solid fa-chart-line"></i></div>
                <h3 class="product-name">弹性扩展</h3>
                <p class="product-desc">按需付费，弹性伸缩，根据业务需求灵活调整资源配置。</p>
            </div>
            <div class="product-card" data-animate="fade-up" style="transition-delay:0.32s;">
                <div class="product-icon"><i class="fa-solid fa-code"></i></div>
                <h3 class="product-name">API开放</h3>
                <p class="product-desc">完善的API接口和开发者文档，轻松集成到您的现有系统。</p>
            </div>
            <div class="product-card" data-animate="fade-up" style="transition-delay:0.4s;">
                <div class="product-icon"><i class="fa-solid fa-handshake"></i></div>
                <h3 class="product-name">定制方案</h3>
                <p class="product-desc">根据不同行业和规模的企业需求，提供量身定制的解决方案。</p>
            </div>
        </div>
    </div>
</section>

<!-- 合作申请CTA -->
<section style="background:linear-gradient(135deg,#0066CC,#004080);padding:70px 0;text-align:center;" data-animate>
    <div class="container">
        <h2 style="color:#fff;font-size:clamp(24px,3vw,36px);font-weight:800;margin-bottom:16px;">成为我们的合作伙伴</h2>
        <p style="color:rgba(255,255,255,0.85);font-size:17px;max-width:520px;margin:0 auto 32px;">无论您是技术服务商、系统集成商还是渠道代理商，我们都期待与您携手共赢</p>
        <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
            <a href="/contact.php" class="btn btn-lg" style="background:#fff;color:#0066CC;font-weight:700;"><i class="fa-solid fa-paper-plane"></i> 申请合作</a>
            <a href="tel:4008008541" class="btn btn-outline btn-lg"><i class="fa-solid fa-phone"></i> 400-800-8541</a>
        </div>
    </div>
</section>

<!-- 页脚 -->
<footer class="footer">
    <div class="container">
        <div class="footer-main">
            <div class="footer-brand">
                <div class="footer-logo">
                    <?php if ($logoUrl): ?><img src="<?php echo e($logoUrl); ?>" alt="" style="height:36px;filter:brightness(0) invert(1);">
                    <?php else: ?><div class="footer-logo-text"><?php echo e($siteName); ?><span>®</span></div><?php endif; ?>
                </div>
                <div class="footer-phone-label">销售电话</div>
                <div class="footer-phone">400-800-8541</div>
            </div>
            <div class="footer-column"><h4>产品服务</h4><ul class="footer-links">
                <li><a href="/products.php">云服务器 ECS</a></li><li><a href="/products.php">云数据库 RDS</a></li>
                <li><a href="/products.php">CDN 加速</a></li><li><a href="/products.php">对象存储 OSS</a></li>
                <li><a href="/products.php">域名注册</a></li><li><a href="/products.php">DDoS 防护</a></li>
            </ul></div>
            <div class="footer-column"><h4>关于我们</h4><ul class="footer-links">
                <li><a href="/about.php">公司介绍</a></li><li><a href="/intro.php">发展历程</a></li>
                <li><a href="/partners.php">合作伙伴</a></li><li><a href="/contact.php">联系我们</a></li>
                <li><a href="https://cloud.loveym.cloud" target="_blank" rel="noopener">国际版官网</a></li>
            </ul></div>
            <div class="footer-column"><h4>帮助支持</h4><ul class="footer-links">
                <li><a href="/user/login.php">用户登录</a></li><li><a href="/user/register.php">免费注册</a></li>
                <li><a href="/contact.php">提交工单</a></li><li><a href="#">帮助文档</a></li>
            </ul></div>
        </div>
        <div class="footer-bottom">
            <div class="footer-copyright">&copy; <?php echo date('Y'); ?> <?php echo e($siteName); ?> 版权所有</div>
            <div class="footer-beian">
                <a href="https://beian.miit.gov.cn/" target="_blank" rel="nofollow noopener noreferrer"><i class="fa-solid fa-shield-halved" style="margin-right:4px;"></i><?php echo e($icpNumber); ?></a>
                <a href="#" target="_blank"><i class="fa-solid fa-file-contract" style="margin-right:4px;"></i><?php echo e($icpLicense); ?></a>
                <a href="#" target="_blank"><i class="fa-solid fa-shield" style="margin-right:4px;"></i><?php echo e($policeNumber); ?></a>
            </div>
        </div>
        <div class="footer-disclaimer">语云科技® 是我们（语云科技美国有限公司）在中国的注册授权 | Yuyun Technology USA Inc.</div>
    </div>
</footer>

<aside class="side-toolbar" id="sideToolbar">
    <div class="toolbar-item accent" onclick="showContactModal()"><i class="fa-solid fa-headset"></i>客服<div class="toolbar-tooltip">在线客服</div></div>
    <div class="toolbar-item" onclick="window.location.href='tel:4008008541'"><i class="fa-solid fa-phone"></i>电话<div class="toolbar-tooltip">400-800-8541</div></div>
    <div class="toolbar-item" onclick="window.scrollTo({top:0,behavior:'smooth'})"><i class="fa-solid fa-arrow-up"></i>顶部<div class="toolbar-tooltip">返回顶部</div></div>
</aside>

<div class="toast-container" id="toastContainer"></div>
<script>window.siteConfig=<?php echo json_encode($config, JSON_UNESCAPED_UNICODE); ?>;</script>
<script src="/assets/js/main.js"></script><script src="/assets/js/modal.js"></script>
</body>
</html>
