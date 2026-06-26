<?php
require __DIR__ . '/includes/config.php';
if (template_include('index.php')) exit;
$pageTitle = __('home');
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

// 公司分布节点（城市,纬度,经度）
$nodes = [
    ['北京',39.9042,116.4074],['青岛',36.0671,120.3826],['莫斯科',55.7558,37.6173],
    ['圣彼得堡',59.9343,30.3351],['首尔',37.5665,126.9780],['新加坡',1.3521,103.8198],
    ['悉尼',-33.8688,151.2093],['纽约',40.7128,-74.0060],['华盛顿',38.9072,-77.0369],
    ['旧金山',37.7749,-122.4194],['伦敦',51.5074,-0.1278],['迪拜',25.2048,55.2708],
];
// 经纬度→SVG坐标（使用Equirectangular投影，viewBox 0 0 1000 500）
function project($lat,$lon){
    $x = (($lon + 180) / 360) * 1000;
    $y = ((90 - $lat) / 180) * 500;
    return [$x,$y];
}
?>

<!-- Hero with 3D flip carousel -->
<section class="hero" id="hero">
    <div class="hero-3d-stage" id="heroStage">
        <?php foreach ($slides as $i => $slide): ?>
        <div class="hero-slide-3d <?php echo $i===0?'active':'' ?>" data-index="<?php echo $i ?>">
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
    </div>
    <!-- Progress bar (长方条状白底) -->
    <div class="hero-progress-wrap" id="heroProgressWrap">
        <?php foreach ($slides as $i => $s): ?>
        <div class="hero-progress-bar <?php echo $i===0?'active':'' ?>" data-index="<?php echo $i ?>">
            <div class="hero-progress-fill"></div>
        </div>
        <?php endforeach; ?>
    </div>
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
            <h2><?php echo __('products_title') ?></h2>
            <p><?php echo __('products_sub') ?></p>
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
            <h2 style="font-size:24px"><?php echo __('partners_title') ?></h2>
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

<!-- Map (真实世界地图) -->
<section class="map-section">
    <div class="container">
        <div class="section-title">
            <?php echo sectionIcon('map') ?>
            <h2><?php echo __('map_title') ?></h2>
            <p><?php echo __('map_sub') ?></p>
        </div>
        <div class="map-wrap">
            <svg class="map-svg" viewBox="0 0 1000 500" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
                <!-- 真实世界大陆轮廓（简化版Equirectangular投影） -->
                <!-- 北美 -->
                <path class="map-continent" d="M148,90 C165,80 190,78 215,85 C245,82 275,90 300,105 C320,100 340,108 350,125 C355,140 345,155 330,160 C315,170 295,175 275,170 C260,185 240,190 220,185 C205,195 185,190 170,180 C155,185 140,175 135,160 C130,145 135,125 145,110 C142,100 145,93 148,90 Z"/>
                <!-- 中美洲 -->
                <path class="map-continent" d="M235,190 C250,195 260,205 255,220 C250,230 240,235 230,230 C222,222 225,205 235,190 Z"/>
                <!-- 南美 -->
                <path class="map-continent" d="M290,220 C305,215 320,225 325,245 C330,270 325,300 315,330 C310,355 300,380 285,395 C275,400 265,395 262,380 C258,360 265,340 270,320 C265,300 270,275 280,255 C283,240 285,228 290,220 Z"/>
                <!-- 欧洲 -->
                <path class="map-continent" d="M470,100 C485,95 505,95 520,105 C535,100 550,108 555,125 C560,135 555,148 545,155 C530,160 515,158 500,152 C485,155 470,148 465,135 C462,122 465,108 470,100 Z"/>
                <!-- 非洲 -->
                <path class="map-continent" d="M495,175 C515,170 535,175 545,195 C555,215 550,245 545,270 C540,295 530,320 515,340 C505,350 490,345 485,330 C480,310 485,290 480,270 C475,245 480,220 485,200 C488,188 490,180 495,175 Z"/>
                <!-- 亚洲（大陆主体） -->
                <path class="map-continent" d="M560,95 C590,85 630,82 670,88 C710,82 750,88 780,100 C810,95 840,105 860,125 C875,140 870,160 855,170 C840,175 820,170 800,175 C780,185 760,180 740,175 C720,185 700,180 680,175 C660,185 640,180 620,175 C600,170 580,160 570,145 C562,130 558,112 560,95 Z"/>
                <!-- 中东/阿拉伯半岛 -->
                <path class="map-continent" d="M580,170 C600,168 615,175 620,195 C615,215 600,225 585,220 C575,210 572,190 580,170 Z"/>
                <!-- 印度次大陆 -->
                <path class="map-continent" d="M670,170 C685,168 695,180 695,200 C690,215 680,220 670,215 C662,205 663,185 670,170 Z"/>
                <!-- 东南亚 -->
                <path class="map-continent" d="M730,190 C745,188 755,198 752,212 C745,222 735,220 728,212 C722,202 725,193 730,190 Z"/>
                <!-- 日本 -->
                <path class="map-continent" d="M855,135 C862,133 868,140 865,150 C860,155 853,150 852,142 C851,138 853,136 855,135 Z"/>
                <!-- 澳大利亚 -->
                <path class="map-continent" d="M800,300 C820,295 845,300 860,315 C870,330 865,350 850,360 C830,365 810,360 795,348 C785,335 790,318 800,300 Z"/>
                <!-- 新西兰 -->
                <path class="map-continent" d="M895,370 C902,368 908,375 905,383 C900,388 893,385 891,378 C890,374 892,371 895,370 Z"/>
                <!-- 英国 -->
                <path class="map-continent" d="M468,108 C475,106 480,112 478,120 C474,124 468,121 466,115 C465,112 466,109 468,108 Z"/>

                <!-- 连线 -->
                <line class="map-line" x1="<?php echo project(39.9042,116.4074)[0] ?>" y1="<?php echo project(39.9042,116.4074)[1] ?>" x2="<?php echo project(55.7558,37.6173)[0] ?>" y2="<?php echo project(55.7558,37.6173)[1] ?>"/>
                <line class="map-line" x1="<?php echo project(39.9042,116.4074)[0] ?>" y1="<?php echo project(39.9042,116.4074)[1] ?>" x2="<?php echo project(1.3521,103.8198)[0] ?>" y2="<?php echo project(1.3521,103.8198)[1] ?>"/>
                <line class="map-line" x1="<?php echo project(39.9042,116.4074)[0] ?>" y1="<?php echo project(39.9042,116.4074)[1] ?>" x2="<?php echo project(40.7128,-74.0060)[0] ?>" y2="<?php echo project(40.7128,-74.0060)[1] ?>"/>
                <line class="map-line" x1="<?php echo project(51.5074,-0.1278)[0] ?>" y1="<?php echo project(51.5074,-0.1278)[1] ?>" x2="<?php echo project(40.7128,-74.0060)[0] ?>" y2="<?php echo project(40.7128,-74.0060)[1] ?>"/>
                <line class="map-line" x1="<?php echo project(51.5074,-0.1278)[0] ?>" y1="<?php echo project(51.5074,-0.1278)[1] ?>" x2="<?php echo project(55.7558,37.6173)[0] ?>" y2="<?php echo project(55.7558,37.6173)[1] ?>"/>
                <line class="map-line" x1="<?php echo project(25.2048,55.2708)[0] ?>" y1="<?php echo project(25.2048,55.2708)[1] ?>" x2="<?php echo project(1.3521,103.8198)[0] ?>" y2="<?php echo project(1.3521,103.8198)[1] ?>"/>

                <!-- 节点 -->
                <?php foreach ($nodes as $n): $p = project($n[1],$n[2]); ?>
                <g class="map-node-g" data-city="<?php echo e($n[0]) ?>">
                    <circle class="map-node-pulse" cx="<?php echo $p[0] ?>" cy="<?php echo $p[1] ?>" r="8"/>
                    <circle class="map-node-s" cx="<?php echo $p[0] ?>" cy="<?php echo $p[1] ?>" r="4.5"/>
                    <text class="map-label" x="<?php echo $p[0] ?>" y="<?php echo $p[1] - 10 ?>"><?php echo e($n[0]) ?></text>
                </g>
                <?php endforeach; ?>
            </svg>
        </div>
    </div>
</section>

<!-- Certificates -->
<section class="section bg-white">
    <div class="container">
        <div class="section-title">
            <?php echo sectionIcon('cert') ?>
            <h2><?php echo __('cert_title') ?></h2>
            <p><?php echo __('cert_sub') ?></p>
        </div>
        <div class="cert-grid">
            <?php $licenseImg = setting('site_license_image'); ?>
            <div class="cert-card" onclick="openImageModal('<?php echo e($licenseImg ?: '') ?>','<?php echo e(setting('site_license',__('business_license'))) ?>')">
                <?php if ($licenseImg): ?><img src="<?php echo e($licenseImg) ?>" alt="<?php echo e(setting('site_license',__('business_license'))) ?>"><?php else: ?><i class="iconfont icon-certificate icon-3xl"></i><?php endif; ?>
                <h4><?php echo e(setting('site_license',__('business_license'))) ?></h4>
                <p><?php echo __('issued_by') ?></p>
            </div>
            <?php $evImg = setting('site_ev_license_image'); ?>
            <div class="cert-card" onclick="openImageModal('<?php echo e($evImg ?: '') ?>','<?php echo e(setting('site_ev_license',__('ev_license'))) ?>')">
                <?php if ($evImg): ?><img src="<?php echo e($evImg) ?>" alt="<?php echo e(setting('site_ev_license',__('ev_license'))) ?>"><?php else: ?><i class="iconfont icon-shield icon-3xl"></i><?php endif; ?>
                <h4><?php echo e(setting('site_ev_license',__('ev_license'))) ?></h4>
                <p><?php echo __('telecom_license') ?></p>
            </div>
            <?php $securityImg = setting('site_security_image'); ?>
            <div class="cert-card" onclick="openImageModal('<?php echo e($securityImg ?: '') ?>','<?php echo __('security_cert') ?>')">
                <?php if ($securityImg): ?><img src="<?php echo e($securityImg) ?>" alt="<?php echo __('security_cert') ?>"><?php else: ?><i class="iconfont icon-lock icon-3xl"></i><?php endif; ?>
                <h4><?php echo __('security_cert') ?></h4>
                <p><?php echo __('level3_cert') ?></p>
            </div>
            <?php $trustImg = setting('site_trust_image'); ?>
            <div class="cert-card" onclick="openImageModal('<?php echo e($trustImg ?: '') ?>','<?php echo __('trust_cert') ?>')">
                <?php if ($trustImg): ?><img src="<?php echo e($trustImg) ?>" alt="<?php echo __('trust_cert') ?>"><?php else: ?><i class="iconfont icon-cloud icon-3xl"></i><?php endif; ?>
                <h4><?php echo __('trust_cert') ?></h4>
                <p><?php echo __('cloud_eval') ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Staff -->
<section class="section staff-section" style="background-color:<?php echo e(setting('staff_bg_color','#f5f7fa')) ?>;<?php if (setting('staff_bg_image')): ?>background-image:url('<?php echo e(setting('staff_bg_image')) ?>')<?php endif; ?>">
    <div class="container">
        <div class="section-title">
            <?php echo sectionIcon('team') ?>
            <h2><?php echo __('team_title') ?></h2>
            <p><?php echo __('team_sub') ?></p>
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
            <h3 id="modalProductTitle"><?php echo __('detail') ?></h3>
            <button class="modal-close"><i class="iconfont icon-close"></i></button>
        </div>
        <div class="modal-body" id="modalProductBody"></div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal-overlay" id="imageModal">
    <div class="modal" style="max-width:720px">
        <div class="modal-header">
            <h3 id="modalImageTitle"><?php echo __('cert_preview') ?></h3>
            <button class="modal-close" onclick="closeImageModal()"><i class="iconfont icon-close"></i></button>
        </div>
        <div class="modal-body text-center">
            <img id="modalImageSrc" src="" alt="" style="max-width:100%;border-radius:8px;box-shadow:var(--shadow)">
            <p id="modalImageTip" style="margin-top:14px;color:var(--text-2)"><?php echo __('click_mask_close') ?></p>
        </div>
    </div>
</div>

<!-- Welcome popup (closable to side) -->
<div class="modal-overlay" id="welcomeModal">
    <div class="modal welcome-modal" style="max-width:480px;text-align:center">
        <div class="modal-body" style="padding:34px 28px">
            <div class="ip-illustration" style="width:100px;height:100px;margin-bottom:16px"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="8" width="36" height="28" rx="2"/><path d="M6 14l18 12 18-12"/><path d="M16 36l-6 6v-6"/></svg></div>
            <h3 style="margin:0 0 10px;color:var(--dark)"><?php echo __('welcome_to') ?> <?php echo e(setting('site_name','语云科技')) ?></h3>
            <p style="color:var(--text-2);margin:0 0 22px"><?php echo e(setting('site_slogan','企业与开发者信赖的云计算与数字化服务伙伴')) ?></p>
            <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
                <a href="tel:<?php echo e(setting('sales_phone','400-800-8451')) ?>" class="btn btn-primary"><i class="iconfont icon-phone"></i> <?php echo __('phone_consult') ?></a>
                <button class="btn btn-outline" id="welcomeCloseBtn"><?php echo __('later') ?></button>
            </div>
        </div>
    </div>
</div>
<!-- Side collapsed announcement tab -->
<div class="welcome-side-tab" id="welcomeSideTab" title="<?php echo __('system_notice') ?>">
    <i class="iconfont icon-megaphone"></i>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
