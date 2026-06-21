<?php
$slides = dbActive('slides', 'sort_order', 'ASC');
$products = dbActive('products', 'sort_order', 'ASC');
$partners = dbActive('partners', 'sort_order', 'ASC');
$certs = dbAll('certificates', 'sort_order', 'ASC');
$testimonials = dbActive('testimonials', 'sort_order', 'ASC');
$popupEnabled = getSetting('popup_enabled', '0');
?>

<!-- Hero Slider -->
<section class="hero-slider">
    <?php if (empty($slides)): ?>
        <div class="hero-slide active" style="background: linear-gradient(135deg, #0066FF 0%, #003d99 100%);">
            <div class="hero-content">
                <div class="hero-badge"><i class="fa-solid fa-award"></i> 企业级云计算服务商</div>
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
            <?php
            $slideStyle = !empty($slide['image']) ? 'background-image:url(' . yy_e($slide['image']) . ')' : '';
            $slideClass = $i === 0 ? 'active' : '';
            ?>
            <div class="hero-slide <?php echo $slideClass; ?>"<?php if ($slideStyle): ?> style="<?php echo $slideStyle; ?>"<?php endif; ?>>
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    <div class="hero-badge"><i class="fa-solid fa-bolt"></i> 语云科技</div>
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
                <?php $dotClass = $i === 0 ? 'active' : ''; ?>
                <span class="hero-dot <?php echo $dotClass; ?>"></span>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Stats -->
<section class="section stats-section">
    <div class="container">
        <div class="stats-grid reveal">
            <div class="stat-box">
                <div class="stat-number" data-count="15">0</div>
                <div class="stat-label">全球节点</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" data-count="500">0</div>
                <div class="stat-label">服务客户</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" data-count="99">0</div>
                <div class="stat-label">在线率 %</div>
            </div>
            <div class="stat-box">
                <div class="stat-number" data-count="24">0</div>
                <div class="stat-label">小时支持</div>
            </div>
        </div>
    </div>
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
                    <span class="card-more">了解详情 <i class="fa-solid fa-arrow-right"></i></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features -->
<section class="section section-gray">
    <div class="container">
        <div class="section-header reveal">
            <h2>为什么选择语云科技</h2>
            <p>稳定、安全、高效的云计算服务，助力业务持续增长</p>
            <div class="section-title-line"></div>
        </div>
        <div class="features-grid reveal">
            <div class="feature-item">
                <i class="fa-solid fa-shield-halved"></i>
                <h4>安全可靠</h4>
                <p>多重安全防护，数据加密传输，保障业务连续性</p>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-globe"></i>
                <h4>全球部署</h4>
                <p>覆盖中东、欧洲、亚太、北美等核心区域节点</p>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-headset"></i>
                <h4>专业服务</h4>
                <p>7×24 小时技术支持，快速响应客户需求</p>
            </div>
            <div class="feature-item">
                <i class="fa-solid fa-chart-line"></i>
                <h4>弹性扩展</h4>
                <p>按需扩展资源，灵活应对业务高峰</p>
            </div>
        </div>
    </div>
</section>

<!-- Certificates -->
<section class="section">
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
<section class="section section-gray">
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

<!-- Testimonials -->
<section class="section testimonials-section">
    <div class="container">
        <div class="section-header reveal">
            <h2>客户评价</h2>
            <p>来自各行业客户的真实反馈</p>
            <div class="section-title-line"></div>
        </div>
        <div class="testimonials-grid reveal">
            <?php
            $defaultTestimonials = [
                ['content' => '语云科技的云服务器稳定性非常出色，帮助我们顺利度过了多次业务高峰。', 'author' => '某金融科技公司 CTO', 'company' => '金融科技', 'stars' => 5],
                ['content' => '技术支持响应很快，全球化节点布局让我们的海外业务访问速度大幅提升。', 'author' => '某跨境电商平台 运维总监', 'company' => '跨境电商', 'stars' => 5],
                ['content' => '从咨询到部署全流程专业高效，是企业数字化转型的可靠合作伙伴。', 'author' => '某制造业集团 信息中心主任', 'company' => '智能制造', 'stars' => 5],
            ];
            $showTestimonials = !empty($testimonials) ? $testimonials : $defaultTestimonials;
            foreach ($showTestimonials as $t):
                $stars = intval($t['stars'] ?? 5);
            ?>
                <div class="testimonial-card">
                    <div class="stars"><?php echo str_repeat('<i class="fa-solid fa-star"></i>', $stars); ?></div>
                    <p>"<?php echo yy_e($t['content']); ?>"</p>
                    <div class="testimonial-author">— <?php echo yy_e($t['author']); ?><?php if (!empty($t['company'])): ?> <span style="color:var(--text-muted);">/ <?php echo yy_e($t['company']); ?></span><?php endif; ?></div>
                </div>
            <?php endforeach; ?>
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

<!-- CTA -->
<section class="section cta-section">
    <div class="container">
        <div class="cta-box reveal">
            <h2>开启您的云端之旅</h2>
            <p>立即联系语云科技，获取专属云计算解决方案与优惠报价</p>
            <div class="btns">
                <a href="tel:<?php echo yy_e(getSetting('sales_phone', '400-800-8451')); ?>" class="btn btn-primary btn-lg"><i class="fa-solid fa-phone"></i> <?php echo yy_e(getSetting('sales_phone', '400-800-8451')); ?></a>
                <a href="?page=contact" class="btn btn-outline btn-lg">在线留言</a>
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
