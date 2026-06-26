<?php
require __DIR__ . '/includes/config.php';
if (template_include('index.php')) exit;
$pageTitle = '首页';
require __DIR__ . '/includes/header.php';

$db = getDb();
$slides = $db->query('SELECT * FROM slides WHERE is_active=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
$products = $db->query('SELECT * FROM products WHERE is_active=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
$partners = $db->query('SELECT * FROM partners WHERE is_active=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
$staff = $db->query('SELECT * FROM staff ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
$gradients = ['linear-gradient(135deg,#0f2027,#203a43,#2c5364)','linear-gradient(135deg,#1a2980,#26d0ce)','linear-gradient(135deg,#ff512f,#dd2476)'];
$iconMap = ['fa-cube'=>'icon-cubes','fa-server'=>'icon-store','fa-shield-halved'=>'icon-shield','fa-network-wired'=>'icon-cloud','fa-globe'=>'icon-map','fa-database'=>'icon-store','fa-lock'=>'icon-lock'];

function sectionIcon($name) {
    $icons = [
        'products' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="8" width="14" height="14" rx="2"/><rect x="26" y="8" width="14" height="14" rx="2"/><rect x="8" y="26" width="14" height="14" rx="2"/><rect x="26" y="26" width="14" height="14" rx="2"/></svg>',
        'map' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="24" cy="20" r="8"/><path d="M12 40c0-8 6-14 12-14s12 6 12 14"/></svg>',
        'cert' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="24" cy="18" r="10"/><path d="M14 28l-2 14 12-6 12 6-2-14"/></svg>',
        'team' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="24" cy="14" r="8"/><path d="M6 44c0-12 8-18 18-18s18 6 18 18"/></svg>',
    ];
    return '<div class="ip-illustration">' . ($icons[$name] ?? '') . '</div>';
}

$nodes = [
    ['北京',39.9042,116.4074],
    ['青岛',36.0671,120.3826],
    ['莫斯科',55.7558,37.6173],
    ['圣彼得堡',59.9343,30.3351],
    ['首尔',37.5665,126.9780],
    ['新加坡',1.3521,103.8198],
    ['悉尼',-33.8688,151.2093],
    ['纽约',40.7128,-74.0060],
    ['华盛顿',38.9072,-77.0369],
    ['旧金山',37.7749,-122.4194],
    ['伦敦',51.5074,-0.1278],
    ['迪拜',25.2048,55.2708],
];
function project($lat,$lon){
    $x = (($lon + 180) / 360) * 1000;
    $y = ((90 - $lat) / 180) * 500;
    return [$x,$y];
}
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
                    <a href="<?php echo e($slide['link']) ?>" class="btn btn-primary"><?php echo __('learn_more') ?> <i class="iconfont icon-arrow-right"></i></a>
                <?php else: ?>
                    <a href="<?php echo YUYUN_URL ?>/products.php" class="btn btn-primary"><?php echo __('view_products') ?> <i class="iconfont icon-arrow-right"></i></a>
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
            <?php echo sectionIcon('products') ?>
            <h2>业务与产品</h2>
            <p>覆盖云计算、网络安全、企业服务的全栈解决方案</p>
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
                <span class="more"><?php echo __('detail') ?> <i class="iconfont icon-chevron-right"></i></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Partners -->
<section class="partner-section">
    <div class="container" style="margin-bottom:20px">
        <div class="section-title" style="margin-bottom:0">
            <h2 style="font-size:24px">我们与以下企业 / 组织携手共进</h2>
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
            <?php echo sectionIcon('map') ?>
            <h2>公司分布</h2>
            <p>全球节点布局，保障业务稳定低延迟</p>
        </div>
        <div class="map-wrap">
            <svg class="map-svg" viewBox="0 0 1000 500" xmlns="http://www.w3.org/2000/svg">
                <path class="map-continent" d="M150,130 C220,80 320,80 360,120 C400,90 480,80 560,100 C680,70 820,90 900,130 C920,180 880,260 820,300 C780,320 720,300 680,280 C640,300 580,310 520,290 C480,330 420,340 360,320 C300,340 220,330 150,300 C120,250 120,180 150,130 Z"/>
                <path class="map-continent" d="M330,250 C360,230 390,240 410,260 C420,320 410,390 380,420 C350,410 330,380 320,340 C310,300 315,270 330,250 Z"/>
                <path class="map-continent" d="M430,190 C480,180 540,190 570,220 C590,290 580,360 550,390 C500,380 460,360 440,320 C425,280 420,230 430,190 Z"/>
                <path class="map-continent" d="M750,300 C800,290 870,300 890,330 C900,370 880,410 840,420 C790,410 760,380 750,350 C740,330 740,310 750,300 Z"/>
                <?php foreach ($nodes as $n): $p = project($n[1],$n[2]); ?>
                <circle class="map-node-s" cx="<?php echo $p[0] ?>" cy="<?php echo $p[1] ?>" r="5" data-city="<?php echo e($n[0]) ?>">
                    <title><?php echo e($n[0]) ?></title>
                </circle>
                <text class="map-label" x="<?php echo $p[0] ?>" y="<?php echo $p[1] - 10 ?>"><?php echo e($n[0]) ?></text>
                <?php endforeach; ?>
                <line class="map-line" x1="<?php echo project(39.9042,116.4074)[0] ?>" y1="<?php echo project(39.9042,116.4074)[1] ?>" x2="<?php echo project(55.7558,37.6173)[0] ?>" y2="<?php echo project(55.7558,37.6173)[1] ?>"/>
                <line class="map-line" x1="<?php echo project(39.9042,116.4074)[0] ?>" y1="<?php echo project(39.9042,116.4074)[1] ?>" x2="<?php echo project(1.3521,103.8198)[0] ?>" y2="<?php echo project(1.3521,103.8198)[1] ?>"/>
                <line class="map-line" x1="<?php echo project(39.9042,116.4074)[0] ?>" y1="<?php echo project(39.9042,116.4074)[1] ?>" x2="<?php echo project(40.7128,-74.0060)[0] ?>" y2="<?php echo project(40.7128,-74.0060)[1] ?>"/>
                <line class="map-line" x1="<?php echo project(51.5074,-0.1278)[0] ?>" y1="<?php echo project(51.5074,-0.1278)[1] ?>" x2="<?php echo project(40.7128,-74.0060)[0] ?>" y2="<?php echo project(40.7128,-74.0060)[1] ?>"/>
            </svg>
        </div>
    </div>
</section>

<!-- Certificates -->
<section class="section bg-white">
    <div class="container">
        <div class="section-title">
            <?php echo sectionIcon('cert') ?>
            <h2>资质证照</h2>
            <p>合规经营，值得信赖</p>
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
<section class="section staff-section" style="background-color:<?php echo e(setting('staff_bg_color','#f5f7fa')) ?>;<?php if (setting('staff_bg_image')): ?>background-image:url('<?php echo e(setting('staff_bg_image')) ?>')<?php endif; ?>">
    <div class="container">
        <div class="section-title">
            <?php echo sectionIcon('team') ?>
            <h2>核心团队</h2>
            <p>来自全球顶尖科技与互联网企业</p>
        </div>
        <div class="staff-scroll">
            <?php foreach ($staff as $s): ?>
            <div class="staff-card">
                <?php if ($s['avatar']): ?>
                    <img src="<?php echo e($s['avatar']) ?>" alt="<?php echo e($s['name']) ?>">
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
            <h3 id="modalProductTitle">产品详情</h3>
            <button class="modal-close"><i class="iconfont icon-close"></i></button>
        </div>
        <div class="modal-body" id="modalProductBody"></div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal-overlay" id="imageModal">
    <div class="modal" style="max-width:720px">
        <div class="modal-header">
            <h3 id="modalImageTitle">证照预览</h3>
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
            <div class="ip-illustration" style="width:100px;height:100px;margin-bottom:16px"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="8" width="36" height="28" rx="2"/><path d="M6 14l18 12 18-12"/><path d="M16 36l-6 6v-6"/></svg></div>
            <h3 style="margin:0 0 10px;color:var(--dark)">欢迎来到 <?php echo e(setting('site_name','语云科技')) ?></h3>
            <p style="color:var(--text-2);margin:0 0 22px"><?php echo e(setting('site_slogan','企业与开发者信赖的云计算与数字化服务伙伴')) ?></p>
            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
                <a href="tel:<?php echo e(setting('sales_phone','400-800-8451')) ?>" class="btn btn-primary"><i class="iconfont icon-phone"></i> 电话咨询</a>
                <button class="btn btn-outline" onclick="document.getElementById('welcomeModal').classList.remove('active')">稍后再说</button>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
