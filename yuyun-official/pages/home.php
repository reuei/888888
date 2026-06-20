<?php
$slides = dbActive('slides', 'sort_order', 'ASC');
$products = dbActive('products', 'sort_order', 'ASC');
$partners = dbActive('partners', 'sort_order', 'ASC');
$certs = dbAll('certificates', 'sort_order', 'ASC');
$popupEnabled = getSetting('popup_enabled', '0');
?>

<!-- Hero Slider -->
<section class="hero-slider">
    <?php if (empty($slides)): ?>
        <div class="hero-slide active" style="background: linear-gradient(135deg, #0066FF 0%, #003d99 100%);">
            <div class="hero-content">
                <h1>语云科技 智领云端</h1>
                <p>全球分布式云计算基础设施，助力企业数字化转型</p>
                <div class="btns">
                    <a href="?page=products" class="btn btn-primary btn-lg">了解产品</a>
                    <a href="?page=contact" class="btn btn-outline btn-lg" style="color:#fff;border-color:#fff;">立即咨询</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($slides as $i => $slide): ?>
            <div class="hero-slide <?php echo $i === 0 ? 'active' : ''; ?>" style="<?php echo $slide['image'] ? 'background-image:url(' . yy_e($slide['image']) . ')' : 'background: linear-gradient(135deg, #0066FF 0%, #003d99 100%)'; ?>;">
                <div class="hero-content">
                    <h1><?php echo yy_e($slide['title']); ?></h1>
                    <p><?php echo yy_e($slide['subtitle']); ?></p>
                    <div class="btns">
                        <a href="<?php echo yy_e($slide['link'] ?: '?page=products'); ?>" class="btn btn-primary btn-lg"><?php echo yy_e($slide['btn_text'] ?: '了解更多'); ?></a>
                        <a href="?page=contact" class="btn btn-outline btn-lg" style="color:#fff;border-color:#fff;">立即咨询</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (count($slides) > 1): ?>
        <button class="hero-nav hero-prev"><i class="fa-solid fa-chevron-left"></i></button>
        <button class="hero-nav hero-next"><i class="fa-solid fa-chevron-right"></i></button>
        <div class="hero-dots">
            <?php foreach ($slides as $i => $slide): ?>
                <span class="hero-dot <?php echo $i === 0 ? 'active' : ''; ?>"></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Products -->
<section class="section">
    <div class="container">
        <div class="section-header reveal">
            <h2>核心业务与产品</h2>
            <p>为企业提供全栈式云计算与数字化解决方案</p>
            <div class="section-title-line"></div>
        </div>
        <div class="card-grid">
            <?php foreach ($products as $p): ?>
                <div class="card reveal" data-product-detail data-title="<?php echo yy_e($p['title']); ?>" data-detail="<?php echo yy_e($p['detail'] ?: $p['summary']); ?>" data-image="<?php echo yy_e($p['image']); ?>">
                    <div class="card-icon"><i class="fa-solid <?php echo yy_e($p['icon'] ?: 'fa-cube'); ?>"></i></div>
                    <h3><?php echo yy_e($p['title']); ?></h3>
                    <p><?php echo yy_e($p['summary']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Certificates -->
<section class="section section-gray">
    <div class="container">
        <div class="section-header reveal">
            <h2>资质证书</h2>
            <p>合规经营，值得信赖的企业级服务商</p>
            <div class="section-title-line"></div>
        </div>
        <div class="cert-scroll">
            <?php foreach ($certs as $c): ?>
                <div class="cert-item reveal" data-cert data-title="<?php echo yy_e($c['name']); ?>" data-image="<?php echo yy_e($c['image']); ?>" data-desc="<?php echo yy_e($c['description']); ?>">
                    <div class="cert-img">
                        <?php if ($c['image'] && file_exists(YUYUN_ROOT . '/' . $c['image'])): ?>
                            <img src="<?php echo yy_e($c['image']); ?>" alt="<?php echo yy_e($c['name']); ?>">
                        <?php else: ?>
                            <i class="fa-solid fa-certificate"></i>
                        <?php endif; ?>
                    </div>
                    <div class="cert-info">
                        <h4><?php echo yy_e($c['name']); ?></h4>
                        <p><?php echo yy_e($c['description']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Partners -->
<section class="section">
    <div class="container">
        <div class="section-header reveal">
            <h2>我们与以下企业/组织携手共进</h2>
            <p>携手全球领先科技企业，共建云端生态</p>
            <div class="section-title-line"></div>
        </div>
        <div class="partner-marquee reveal">
            <div class="partner-track">
                <?php
                $partnerList = array_merge($partners, $partners);
                foreach ($partnerList as $p):
                ?>
                    <div class="partner-item">
                        <?php if ($p['logo'] && file_exists(YUYUN_ROOT . '/' . $p['logo'])): ?>
                            <img src="<?php echo yy_e($p['logo']); ?>" alt="<?php echo yy_e($p['name']); ?>">
                        <?php else: ?>
                            <span><?php echo yy_e($p['name']); ?></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Global Map -->
<section class="section section-gray map-section">
    <div class="container">
        <div class="section-header reveal">
            <h2>公司分布</h2>
            <p>全球布局，就近服务，构建稳定的云计算网络</p>
            <div class="section-title-line"></div>
        </div>
        <div class="map-container reveal">
            <div class="map-bg"></div>
            <div class="map-dots">
                <?php
                $locations = [
                    ['北京', '76%', '32%'], ['青岛', '78%', '35%'],
                    ['莫斯科', '58%', '22%'], ['圣彼得堡', '56%', '18%'],
                    ['首尔', '84%', '34%'], ['新加坡', '78%', '58%'],
                    ['悉尼', '88%', '78%'], ['东京', '88%', '36%'],
                    ['迪拜（中东）', '50%', '45%'], ['法兰克福（欧洲）', '46%', '28%'],
                    ['伦敦（欧洲）', '43%', '25%'], ['纽约', '24%', '32%'],
                    ['华盛顿', '23%', '35%'], ['旧金山', '16%', '36%'],
                ];
                foreach ($locations as $loc):
                ?>
                    <div class="map-dot" style="left:<?php echo $loc[1]; ?>;top:<?php echo $loc[2]; ?>;" data-label="<?php echo yy_e($loc[0]); ?>"></div>
                <?php endforeach; ?>
            </div>
            <div class="map-legend">
                <span><i class="fa-solid fa-location-dot" style="color:#FF6A00;"></i> 中国：北京、青岛</span>
                <span>俄罗斯：莫斯科、圣彼得堡</span>
                <span>东南亚：新加坡</span>
                <span>东亚：首尔、东京</span>
                <span>中东：迪拜</span>
                <span>欧洲：法兰克福、伦敦</span>
                <span>澳洲：悉尼</span>
                <span>美国：纽约、华盛顿、旧金山</span>
            </div>
        </div>
    </div>
</section>

<?php if ($popupEnabled == '1'): ?>
<div class="modal-overlay" id="homePopup">
    <div class="modal">
        <div class="modal-header">
            <h3><?php echo yy_e(getSetting('popup_title', '公告')); ?></h3>
            <button class="modal-close popup-close">&times;</button>
        </div>
        <div class="modal-body">
            <p><?php echo nl2br(yy_e(getSetting('popup_content', ''))); ?></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary popup-close">我知道了</button>
        </div>
    </div>
</div>
<?php endif; ?>
