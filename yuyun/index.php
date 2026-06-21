<?php
require __DIR__ . '/includes/config.php';
if (template_include('index.php')) exit;
$pageTitle = L('nav.home', '首页');
require __DIR__ . '/includes/header.php';

$db = getDb();
$slides = $db->query('SELECT * FROM slides WHERE is_active=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
$products = $db->query('SELECT * FROM products WHERE is_active=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
$partners = $db->query('SELECT * FROM partners WHERE is_active=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
$staff = $db->query('SELECT * FROM staff ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
$gradients = ['linear-gradient(135deg,#0f2027,#203a43,#2c5364)','linear-gradient(135deg,#1a2980,#26d0ce)','linear-gradient(135deg,#ff512f,#dd2476)'];
$iconMap = ['fa-cube'=>'icon-cubes','fa-server'=>'icon-store','fa-shield-halved'=>'icon-shield','fa-network-wired'=>'icon-cloud','fa-globe'=>'icon-map','fa-database'=>'icon-store','fa-lock'=>'icon-lock'];
$staffBg = setting('site_staff_bg_image');
?>

<!-- Hero -->
<section class="hero" id="hero">
    <?php foreach ($slides as $i => $slide): ?>
    <div class="hero-slide <?php echo $i===0?'active':'' ?>">
        <div class="hero-bg" style="<?php echo $slide['image'] ? 'background-image: url(\''.e($slide['image']).'\'),' : 'background-image:' ?> <?php echo $gradients[$i % count($gradients)] ?>"></div>
        <div class="hero-content">
            <div class="container">
                <h1><?php echo e($slide['title']) ?></h1>
                <p><?php echo e($slide['subtitle']) ?></p>
                <?php if ($slide['link']): ?>
                    <a href="<?php echo e($slide['link']) ?>" class="btn btn-primary"><?php echo L('btn.learn_more', '立即了解') ?> <i class="iconfont icon-arrow-right"></i></a>
                <?php else: ?>
                    <a href="<?php echo YUYUN_URL ?>/products.php" class="btn btn-primary"><?php echo L('btn.view_products', '查看产品') ?> <i class="iconfont icon-arrow-right"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (count($slides) > 1): ?>
    <div class="hero-dots">
        <?php foreach ($slides as $i => $slide): ?>
            <span class="hero-dot <?php echo $i===0?'active':'' ?>"></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- 3D tracking mascot -->
    <div class="hero-mascot" id="heroMascot">
        <div class="mascot-wrap">
            <div class="m-head"></div>
            <div class="m-body"></div>
            <div class="m-arm left"></div>
            <div class="m-arm right"></div>
            <div class="m-shadow"></div>
        </div>
    </div>
</section>

<!-- Products -->
<section class="section bg-white">
    <div class="container">
        <div class="section-title">
            <h2><?php echo L('home.products_title', '业务与产品') ?></h2>
            <p><?php echo L('home.products_subtitle', '覆盖云计算、网络安全、企业服务的全栈解决方案') ?></p>
        </div>
        <div class="card-grid">
            <?php foreach ($products as $prod):
                $icon = $prod['icon'] ?: 'fa-cube';
                $iconClass = $iconMap[$icon] ?? 'icon-cubes';
            ?>
            <div class="product-card" onclick="openProductModal('<?php echo e($prod['name']) ?>','<?php echo e($prod['detail'] ?: $prod['summary']) ?>')">
                <div class="icon"><i class="iconfont <?php echo e($iconClass) ?> icon-2xl"></i></div>
                <h3><?php echo e($prod['name']) ?></h3>
                <p><?php echo e($prod['summary']) ?></p>
                <span class="more"><?php echo L('btn.learn_more', '了解详情') ?> <i class="iconfont icon-chevron-right"></i></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Partners -->
<section class="partner-section">
    <div class="container" style="margin-bottom:20px">
        <div class="section-title" style="margin-bottom:0">
            <h2 style="font-size:24px"><?php echo L('home.partners_title', '我们与以下企业 / 组织携手共进') ?></h2>
        </div>
    </div>
    <div class="partner-track">
        <?php foreach (array_merge($partners, $partners) as $p): ?>
            <div class="partner-item">
                <?php if ($p['logo']): ?>
                    <img src="<?php echo e($p['logo']) ?>" alt="<?php echo e($p['name']) ?>" style="max-height:40px;max-width:120px;object-fit:contain">
                <?php else: ?>
                    <?php echo e($p['name']) ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Map -->
<section class="map-section">
    <div class="container">
        <div class="section-title">
            <h2><?php echo L('home.map_title', '公司分布') ?></h2>
            <p><?php echo L('home.map_subtitle', '全球节点布局，保障业务稳定低延迟') ?></p>
        </div>
        <div class="map-wrap">
            <svg class="map-svg" viewBox="0 0 1000 500" xmlns="http://www.w3.org/2000/svg">
                <g class="map-grid">
                    <line x1="100" y1="0" x2="100" y2="500"/>
                    <line x1="300" y1="0" x2="300" y2="500"/>
                    <line x1="500" y1="0" x2="500" y2="500"/>
                    <line x1="700" y1="0" x2="700" y2="500"/>
                    <line x1="900" y1="0" x2="900" y2="500"/>
                    <line x1="0" y1="125" x2="1000" y2="125"/>
                    <line x1="0" y1="250" x2="1000" y2="250"/>
                    <line x1="0" y1="375" x2="1000" y2="375"/>
                </g>
                <path class="map-continent" d="M40,70 L100,40 L180,35 L250,60 L300,120 L280,180 L230,230 L160,240 L100,210 L60,150 Z"/>
                <path class="map-continent" d="M250,250 L300,240 L330,280 L340,360 L310,440 L260,450 L240,360 Z"/>
                <path class="map-continent" d="M460,90 L500,50 L600,40 L750,60 L880,110 L930,180 L900,260 L800,280 L700,260 L600,230 L520,180 L470,140 Z"/>
                <path class="map-continent" d="M450,100 L520,90 L580,130 L600,220 L560,340 L480,360 L440,240 Z"/>
                <path class="map-continent" d="M760,290 L830,280 L890,320 L870,380 L790,390 L750,340 Z"/>

                <g class="map-node-wrap"><circle class="map-city" cx="823" cy="139" r="5"/><text class="map-label" x="823" y="158">北京</text><title>中国 · 北京</title></g>
                <g class="map-node-wrap"><circle class="map-city" cx="835" cy="150" r="5"/><text class="map-label" x="835" y="169">青岛</text><title>中国 · 青岛</title></g>
                <g class="map-node-wrap"><circle class="map-city" cx="604" cy="106" r="5"/><text class="map-label" x="604" y="125">莫斯科</text><title>俄罗斯 · 莫斯科</title></g>
                <g class="map-node-wrap"><circle class="map-city" cx="584" cy="94" r="5"/><text class="map-label" x="584" y="85">圣彼得堡</text><title>俄罗斯 · 圣彼得堡</title></g>
                <g class="map-node-wrap"><circle class="map-city" cx="841" cy="145" r="5"/><text class="map-label" x="841" y="164">首尔</text><title>韩国 · 首尔</title></g>
                <g class="map-node-wrap"><circle class="map-city" cx="888" cy="151" r="5"/><text class="map-label" x="888" y="170">东京</text><title>日本 · 东京</title></g>
                <g class="map-node-wrap"><circle class="map-city" cx="777" cy="247" r="5"/><text class="map-label" x="777" y="266">新加坡</text><title>新加坡</title></g>
                <g class="map-node-wrap"><circle class="map-city" cx="920" cy="369" r="5"/><text class="map-label" x="920" y="388">悉尼</text><title>澳大利亚 · 悉尼</title></g>
                <g class="map-node-wrap"><circle class="map-city" cx="294" cy="138" r="5"/><text class="map-label" x="294" y="157">纽约</text><title>美国 · 纽约</title></g>
                <g class="map-node-wrap"><circle class="map-city" cx="286" cy="142" r="5"/><text class="map-label" x="286" y="161">华盛顿</text><title>美国 · 华盛顿</title></g>
                <g class="map-node-wrap"><circle class="map-city" cx="161" cy="145" r="5"/><text class="map-label" x="161" y="164">旧金山</text><title>美国 · 旧金山</title></g>
                <g class="map-node-wrap"><circle class="map-city" cx="500" cy="112" r="5"/><text class="map-label" x="500" y="131">伦敦</text><title>英国 · 伦敦</title></g>
                <g class="map-node-wrap"><circle class="map-city" cx="524" cy="114" r="5"/><text class="map-label" x="524" y="105">法兰克福</text><title>德国 · 法兰克福</title></g>
                <g class="map-node-wrap"><circle class="map-city" cx="654" cy="181" r="5"/><text class="map-label" x="654" y="200">迪拜</text><title>阿联酋 · 迪拜</title></g>
            </svg>
        </div>
    </div>
</section>

<!-- Certificates -->
<section class="section bg-white">
    <div class="container">
        <div class="section-title">
            <h2><?php echo L('home.certs_title', '资质证照') ?></h2>
            <p><?php echo L('home.certs_subtitle', '合规经营，值得信赖') ?></p>
        </div>
        <div class="cert-grid">
            <?php $licenseImg = setting('site_license_image'); ?>
            <div class="cert-card" onclick="openImageModal('<?php echo e($licenseImg ?: '') ?>','<?php echo e(setting('site_license','营业执照')) ?>')">
                <?php if ($licenseImg): ?><img src="<?php echo e($licenseImg) ?>" alt="<?php echo e(setting('site_license','营业执照')) ?>"><?php else: ?><i class="iconfont icon-certificate icon-3xl"></i><?php endif; ?>
                <h4><?php echo e(setting('site_license','营业执照')) ?></h4>
                <p>工商行政管理机关核发</p>
            </div>
            <?php $evImg = setting('site_ev_license_image'); ?>
            <div class="cert-card" onclick="openImageModal('<?php echo e($evImg ?: '') ?>','<?php echo e(setting('site_ev_license','电子增值服务产业证')) ?>')">
                <?php if ($evImg): ?><img src="<?php echo e($evImg) ?>" alt="<?php echo e(setting('site_ev_license','电子增值服务产业证')) ?>"><?php else: ?><i class="iconfont icon-shield icon-3xl"></i><?php endif; ?>
                <h4><?php echo e(setting('site_ev_license','电子增值服务产业证')) ?></h4>
                <p>电信与信息服务业务经营许可</p>
            </div>
            <?php $securityImg = setting('site_security_image'); ?>
            <div class="cert-card" onclick="openImageModal('<?php echo e($securityImg ?: '') ?>','信息安全等级保护')">
                <?php if ($securityImg): ?><img src="<?php echo e($securityImg) ?>" alt="信息安全等级保护"><?php else: ?><i class="iconfont icon-lock icon-3xl"></i><?php endif; ?>
                <h4>信息安全等级保护</h4>
                <p>三级等保认证</p>
            </div>
            <?php $trustImg = setting('site_trust_image'); ?>
            <div class="cert-card" onclick="openImageModal('<?php echo e($trustImg ?: '') ?>','可信云服务认证')">
                <?php if ($trustImg): ?><img src="<?php echo e($trustImg) ?>" alt="可信云服务认证"><?php else: ?><i class="iconfont icon-cloud icon-3xl"></i><?php endif; ?>
                <h4>可信云服务认证</h4>
                <p>云计算服务能力评估</p>
            </div>
        </div>
    </div>
</section>

<!-- Staff -->
<section class="section staff-section" <?php echo $staffBg ? 'style="background-image:url(\''.e($staffBg).'\')"' : '' ?>>
    <div class="container">
        <div class="section-title">
            <h2><?php echo L('home.staff_title', '核心团队') ?></h2>
            <p><?php echo L('home.staff_subtitle', '来自全球顶尖科技与互联网企业') ?></p>
        </div>
        <div class="staff-scroll">
            <?php foreach ($staff as $s): ?>
            <div class="staff-card">
                <?php if ($s['avatar']): ?>
                    <div class="avatar"><img src="<?php echo e($s['avatar']) ?>" alt="<?php echo e($s['name']) ?>"></div>
                <?php else: ?>
                    <div class="avatar"><i class="iconfont icon-user icon-2xl"></i></div>
                <?php endif; ?>
                <h4><?php echo e($s['name']) ?></h4>
                <div class="pos"><?php echo e($s['position']) ?></div>
                <p><?php echo e($s['bio']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Product Modal -->
<div class="modal-overlay" id="productModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalProductTitle"><?php echo L('home.products_title', '产品详情') ?></h3>
            <button class="modal-close"><i class="iconfont icon-close"></i></button>
        </div>
        <div class="modal-body" id="modalProductBody"></div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal-overlay" id="imageModal">
    <div class="modal" style="max-width:720px">
        <div class="modal-header">
            <h3 id="modalImageTitle"><?php echo L('home.certs_title', '证照预览') ?></h3>
            <button class="modal-close" onclick="closeImageModal()"><i class="iconfont icon-close"></i></button>
        </div>
        <div class="modal-body text-center">
            <img id="modalImageSrc" src="" alt="" style="max-width:100%;border-radius:8px;box-shadow:var(--shadow)">
            <p id="modalImageTip" style="margin-top:14px;color:var(--text-2)">点击遮罩关闭</p>
        </div>
    </div>
</div>

<!-- Welcome popup -->
<div class="modal-overlay" id="welcomeModal">
    <div class="modal" style="max-width:480px;text-align:center">
        <div class="modal-body" style="padding:34px 28px">
            <div class="illustration-3d" style="width:100px;height:100px;margin-bottom:16px">
                <div class="cube" style="width:50px;height:50px;left:25px;top:25px"><div class="face"></div><div class="face"></div><div class="face"></div><div class="face"></div><div class="face"></div><div class="face"></div></div>
            </div>
            <h3 style="margin:0 0 10px;color:var(--dark)"><?php echo L('home.welcome_title', '欢迎来到') ?> <?php echo e(setting('site_name','语云科技')) ?></h3>
            <p style="color:var(--text-2);margin:0 0 22px"><?php echo e(setting('site_slogan', L('home.welcome_subtitle', '企业与开发者信赖的云计算与数字化服务伙伴'))) ?></p>
            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
                <a href="tel:<?php echo e(setting('sales_phone','400-800-8451')) ?>" class="btn btn-primary"><i class="iconfont icon-phone"></i> <?php echo L('btn.phone', '电话咨询') ?></a>
                <button class="btn btn-outline" onclick="document.getElementById('welcomeModal').classList.remove('active')"><?php echo L('btn.later', '稍后再说') ?></button>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
