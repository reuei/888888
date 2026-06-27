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
        'stats' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M6 36h36"/><path d="M6 30l10-14 8 10 10-18 8 12"/></svg>',
        'features' => '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polygon points="24,4 29,19 44,19 32,28 36,43 24,34 12,43 16,28 4,19 19,19"/></svg>',
        'quote' => '<svg viewBox="0 0 48 48" fill="currentColor"><path d="M12 24c-3.3 0-6-2.7-6-6V12c0-3.3 2.7-6 6-6h6c3.3 0 6 2.7 6 6v6c0 8.3-6.7 15-15 15v-6c5 0 9-4 9-9h-6zm18 0c-3.3 0-6-2.7-6-6V12c0-3.3 2.7-6 6-6h6c3.3 0 6 2.7 6 6v6c0 8.3-6.7 15-15 15v-6c5 0 9-4 9-9h-6z"/></svg>',
    ];
    return '<div class="ip-illustration">' . ($icons[$name] ?? '') . '</div>';
}

// 全球节点（城市,纬度,经度,区域）
$nodes = [
    ['北京',39.9042,116.4074,'Asia'],['青岛',36.0671,120.3826,'Asia'],
    ['莫斯科',55.7558,37.6173,'Europe'],['圣彼得堡',59.9343,30.3351,'Europe'],
    ['首尔',37.5665,126.9780,'Asia'],['新加坡',1.3521,103.8198,'Asia'],
    ['悉尼',-33.8688,151.2093,'Oceania'],['纽约',40.7128,-74.0060,'North America'],
    ['华盛顿',38.9072,-77.0369,'North America'],['旧金山',37.7749,-122.4194,'North America'],
    ['伦敦',51.5074,-0.1278,'Europe'],['迪拜',25.2048,55.2708,'Asia'],
];
function project($lat,$lon){
    $x = (($lon + 180) / 360) * 1000;
    $y = ((90 - $lat) / 180) * 500;
    return [$x,$y];
}
$statItems = [
    ['icon'=>'icon-users','num'=>'5000','suffix'=>'+','label'=>__('stats_clients')],
    ['icon'=>'icon-shield-check','num'=>'99.99','suffix'=>'%','label'=>__('stats_uptime')],
    ['icon'=>'icon-map','num'=>count($nodes),'suffix'=>'','label'=>__('stats_nodes')],
    ['icon'=>'icon-headset','num'=>'7×24','suffix'=>'','label'=>__('stats_support')],
];
$featureItems = [
    ['icon'=>'icon-cloud','title'=>__('feature_cloud_title'),'desc'=>__('feature_cloud_desc')],
    ['icon'=>'icon-shield','title'=>__('feature_security_title'),'desc'=>__('feature_security_desc')],
    ['icon'=>'icon-global','title'=>__('feature_network_title'),'desc'=>__('feature_network_desc')],
    ['icon'=>'icon-service','title'=>__('feature_service_title'),'desc'=>__('feature_service_desc')],
];
$testimonials = [
    ['name'=>'张经理','company'=>'某跨境电商','content'=>'语云科技的全球节点让我们的海外访问速度提升了300%，服务非常稳定。'],
    ['name'=>'李总监','company'=>'某金融科技公司','content'=>'DDoS防护能力出色，多次帮我们抵御了大流量攻击，技术团队响应很及时。'],
    ['name'=>'王CTO','company'=>'某SaaS企业','content'=>'弹性云计算方案大大降低了我们的IT成本，部署简单，管理方便。'],
];
?>

<!-- Hero with cinematic carousel -->
<section class="hero" id="hero">
    <div class="hero-3d-stage" id="heroStage">
        <?php foreach ($slides as $i => $slide): ?>
        <div class="hero-slide-3d <?php echo $i===0?'active':'' ?>" data-index="<?php echo $i ?>">
            <div class="hero-bg" style="<?php echo $slide['image'] ? 'background-image: url(\''.e($slide['image']).'\'),' : 'background-image:' ?> <?php echo $gradients[$i % count($gradients)] ?>"></div>
            <div class="hero-particles" aria-hidden="true"></div>
            <div class="hero-content">
                <div class="container">
                    <span class="hero-badge"><?php echo e(setting('site_short','语云')) ?> CLOUD</span>
                    <h1><?php echo e($slide['title']) ?></h1>
                    <p><?php echo e($slide['subtitle']) ?></p>
                    <?php if ($slide['link']): ?>
                        <a href="<?php echo e($slide['link']) ?>" class="btn btn-primary btn-lg"><?php echo __('learn_more') ?> <i class="iconfont icon-arrow-right"></i></a>
                    <?php else: ?>
                        <a href="<?php echo YUYUN_URL ?>/products.php" class="btn btn-primary btn-lg"><?php echo __('view_products') ?> <i class="iconfont icon-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="hero-progress-wrap" id="heroProgressWrap">
        <?php foreach ($slides as $i => $s): ?>
        <div class="hero-progress-bar <?php echo $i===0?'active':'' ?>" data-index="<?php echo $i ?>">
            <div class="hero-progress-fill"></div>
            <span class="hero-progress-title"><?php echo e($s['title']) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
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

<!-- Stats -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <?php foreach ($statItems as $s): ?>
            <div class="stat-card">
                <i class="iconfont <?php echo e($s['icon']) ?>"></i>
                <div class="stat-num" data-target="<?php echo e($s['num']) ?>"><?php echo e($s['num']) ?></div>
                <div class="stat-label"><?php echo e($s['label']) ?><?php if($s['suffix']): ?><span class="stat-suffix"><?php echo e($s['suffix']) ?></span><?php endif; ?></div>
            </div>
            <?php endforeach; ?>
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

<!-- Features -->
<section class="section features-section">
    <div class="container">
        <div class="section-title">
            <?php echo sectionIcon('features') ?>
            <h2><?php echo __('features_title') ?></h2>
            <p><?php echo __('features_sub') ?></p>
        </div>
        <div class="features-grid">
            <?php foreach ($featureItems as $f): ?>
            <div class="feature-card">
                <div class="feature-icon"><i class="iconfont <?php echo e($f['icon']) ?> icon-2xl"></i></div>
                <h3><?php echo e($f['title']) ?></h3>
                <p><?php echo e($f['desc']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Map -->
<section class="map-section">
    <div class="container">
        <div class="section-title">
            <?php echo sectionIcon('map') ?>
            <h2><?php echo __('map_title') ?></h2>
            <p><?php echo __('map_sub') ?></p>
        </div>
        <div class="map-layout">
            <div class="map-wrap">
                <svg class="map-svg" viewBox="0 0 1000 500" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid meet">
                    <defs>
                        <radialGradient id="mapGlow" cx="50%" cy="50%" r="50%">
                            <stop offset="0%" stop-color="rgba(255,106,0,.15)"/>
                            <stop offset="100%" stop-color="rgba(255,106,0,0)"/>
                        </radialGradient>
                        <filter id="mapShadow" x="-20%" y="-20%" width="140%" height="140%">
                            <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="rgba(0,0,0,.5)"/>
                        </filter>
                    </defs>
                    <rect width="1000" height="500" fill="transparent"/>
                    <circle cx="500" cy="250" r="280" fill="url(#mapGlow)" class="map-glow"/>
                    <!-- Grid -->
                    <g class="map-grid" stroke="rgba(255,255,255,.06)" stroke-width="1">
                        <?php for($i=0;$i<=10;$i++): ?><line x1="<?php echo $i*100 ?>" y1="0" x2="<?php echo $i*100 ?>" y2="500"/><?php endfor; ?>
                        <?php for($i=0;$i<=5;$i++): ?><line x1="0" y1="<?php echo $i*100 ?>" x2="1000" y2="<?php echo $i*100 ?>"/><?php endfor; ?>
                    </g>
                    <!-- Continents -->
                    <path class="map-continent" d="M148,90 C165,80 190,78 215,85 C245,82 275,90 300,105 C320,100 340,108 350,125 C355,140 345,155 330,160 C315,170 295,175 275,170 C260,185 240,190 220,185 C205,195 185,190 170,180 C155,185 140,175 135,160 C130,145 135,125 145,110 C142,100 145,93 148,90 Z"/>
                    <path class="map-continent" d="M235,190 C250,195 260,205 255,220 C250,230 240,235 230,230 C222,222 225,205 235,190 Z"/>
                    <path class="map-continent" d="M290,220 C305,215 320,225 325,245 C330,270 325,300 315,330 C310,355 300,380 285,395 C275,400 265,395 262,380 C258,360 265,340 270,320 C265,300 270,275 280,255 C283,240 285,228 290,220 Z"/>
                    <path class="map-continent" d="M470,100 C485,95 505,95 520,105 C535,100 550,108 555,125 C560,135 555,148 545,155 C530,160 515,158 500,152 C485,155 470,148 465,135 C462,122 465,108 470,100 Z"/>
                    <path class="map-continent" d="M495,175 C515,170 535,175 545,195 C555,215 550,245 545,270 C540,295 530,320 515,340 C505,350 490,345 485,330 C480,310 485,290 480,270 C475,245 480,220 485,200 C488,188 490,180 495,175 Z"/>
                    <path class="map-continent" d="M560,95 C590,85 630,82 670,88 C710,82 750,88 780,100 C810,95 840,105 860,125 C875,140 870,160 855,170 C840,175 820,170 800,175 C780,185 760,180 740,175 C720,185 700,180 680,175 C660,185 640,180 620,175 C600,170 580,160 570,145 C562,130 558,112 560,95 Z"/>
                    <path class="map-continent" d="M580,170 C600,168 615,175 620,195 C615,215 600,225 585,220 C575,210 572,190 580,170 Z"/>
                    <path class="map-continent" d="M670,170 C685,168 695,180 695,200 C690,215 680,220 670,215 C662,205 663,185 670,170 Z"/>
                    <path class="map-continent" d="M730,190 C745,188 755,198 752,212 C745,222 735,220 728,212 C722,202 725,193 730,190 Z"/>
                    <path class="map-continent" d="M855,135 C862,133 868,140 865,150 C860,155 853,150 852,142 C851,138 853,136 855,135 Z"/>
                    <path class="map-continent" d="M800,300 C820,295 845,300 860,315 C870,330 865,350 850,360 C830,365 810,360 795,348 C785,335 790,318 800,300 Z"/>
                    <path class="map-continent" d="M895,370 C902,368 908,375 905,383 C900,388 893,385 891,378 C890,374 892,371 895,370 Z"/>
                    <path class="map-continent" d="M468,108 C475,106 480,112 478,120 C474,124 468,121 466,115 C465,112 466,109 468,108 Z"/>

                    <!-- Flight lines -->
                    <?php
                    $routes = [
                        [39.9042,116.4074,55.7558,37.6173],
                        [39.9042,116.4074,1.3521,103.8198],
                        [39.9042,116.4074,40.7128,-74.0060],
                        [51.5074,-0.1278,40.7128,-74.0060],
                        [51.5074,-0.1278,55.7558,37.6173],
                        [25.2048,55.2708,1.3521,103.8198],
                        [37.7749,-122.4194,1.3521,103.8198],
                    ];
                    foreach ($routes as $r):
                        $a = project($r[0],$r[1]); $b = project($r[2],$r[3]);
                    ?>
                    <line class="map-line" x1="<?php echo $a[0] ?>" y1="<?php echo $a[1] ?>" x2="<?php echo $b[0] ?>" y2="<?php echo $b[1] ?>"/>
                    <?php endforeach; ?>

                    <!-- Nodes -->
                    <?php foreach ($nodes as $n): $p = project($n[1],$n[2]); ?>
                    <g class="map-node-g" data-city="<?php echo e($n[0]) ?>" data-region="<?php echo e($n[3]) ?>">
                        <circle class="map-node-pulse" cx="<?php echo $p[0] ?>" cy="<?php echo $p[1] ?>" r="4"/>
                        <circle class="map-node-s" cx="<?php echo $p[0] ?>" cy="<?php echo $p[1] ?>" r="4.5"/>
                        <text class="map-label" x="<?php echo $p[0] ?>" y="<?php echo $p[1] - 10 ?>"><?php echo e($n[0]) ?></text>
                    </g>
                    <?php endforeach; ?>
                </svg>
                <div class="map-tooltip" id="mapTooltip"></div>
            </div>
            <div class="map-panel">
                <h4><?php echo __('map_title') ?></h4>
                <p><?php echo __('map_sub') ?></p>
                <ul class="map-legend">
                    <li><span class="legend-dot" style="background:#ff6a00"></span> <?php echo __('stats_nodes') ?> <strong><?php echo count($nodes) ?></strong></li>
                    <li><span class="legend-line"></span> <?php echo __('global_network') ?></li>
                </ul>
                <a href="<?php echo YUYUN_URL ?>/contact.php" class="btn btn-primary"><?php echo __('contact') ?></a>
            </div>
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

<!-- Testimonials -->
<section class="section testimonials-section bg-white">
    <div class="container">
        <div class="section-title">
            <?php echo sectionIcon('quote') ?>
            <h2><?php echo __('testimonials_title') ?></h2>
            <p><?php echo __('testimonials_sub') ?></p>
        </div>
        <div class="testimonials-grid">
            <?php foreach ($testimonials as $t): ?>
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p><?php echo e($t['content']) ?></p>
                <div class="author">
                    <div class="author-avatar"><?php echo e(mb_substr($t['name'],0,1)) ?></div>
                    <div>
                        <strong><?php echo e($t['name']) ?></strong>
                        <span><?php echo e($t['company']) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <h2><?php echo __('cta_title') ?></h2>
            <p><?php echo __('cta_sub') ?></p>
            <a href="<?php echo YUYUN_URL ?>/contact.php" class="btn btn-light btn-lg"><?php echo __('cta_contact') ?> <i class="iconfont icon-arrow-right"></i></a>
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

<!-- Welcome popup (whale-card pro style) -->
<div class="modal-overlay" id="welcomeModal">
    <div class="modal welcome-modal pro-welcome">
        <div class="welcome-visual">
            <div class="welcome-shine"></div>
            <div class="welcome-icon"><i class="iconfont icon-gift"></i></div>
            <h3><?php echo __('welcome_to') ?></h3>
            <p><?php echo e(setting('site_name','语云科技')) ?></p>
        </div>
        <div class="welcome-body">
            <div class="welcome-tags">
                <span class="tag"><?php echo e(setting('site_short','语云')) ?> CLOUD</span>
                <span class="tag tag-hot">HOT</span>
            </div>
            <h4><?php echo e(setting('site_slogan','企业与开发者信赖的云计算与数字化服务伙伴')) ?></h4>
            <ul class="welcome-perks">
                <li><i class="iconfont icon-check-circle"></i> 全球 <?php echo count($nodes) ?>+ 节点稳定覆盖</li>
                <li><i class="iconfont icon-check-circle"></i> 7×24 小时专业技术支持</li>
                <li><i class="iconfont icon-check-circle"></i> 新用户专属上云方案</li>
            </ul>
            <div class="welcome-actions">
                <a href="tel:<?php echo e(setting('sales_phone','400-800-8451')) ?>" class="btn btn-primary btn-lg"><i class="iconfont icon-phone"></i> <?php echo __('phone_consult') ?></a>
                <button class="btn btn-outline btn-lg" id="welcomeCloseBtn"><?php echo __('later') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="welcome-side-tab" id="welcomeSideTab" title="<?php echo __('system_notice') ?>">
    <i class="iconfont icon-megaphone"></i>
    <span class="side-tab-pulse"></span>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
