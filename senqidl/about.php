<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$currentPage = 'about';
$settings = DB::getSetting();

$stats = $settings['home_stats'] ?? [];
$services = DB::getList('services', ['status' => 1], 'sort', 'ASC');

$teamMembers = [
    ['name' => '张总', 'title' => 'CEO / 创始人', 'image' => '/assets/images/team1.jpg', 'desc' => '10年+品牌数字化行业经验'],
    ['name' => '李总', 'title' => 'CTO / 技术总监', 'image' => '/assets/images/team2.jpg', 'desc' => '深耕技术架构与创新研发'],
    ['name' => '王总', 'title' => 'COO / 运营总监', 'image' => '/assets/images/team3.jpg', 'desc' => '擅长品牌运营与市场策略'],
    ['name' => '赵总', 'title' => '设计总监', 'image' => '/assets/images/team4.jpg', 'desc' => '创意设计与用户体验专家']
];

$values = [
    ['icon' => '🎯', 'title' => '客户至上', 'desc' => '始终以客户成功为我们的成功标准'],
    ['icon' => '💡', 'title' => '创新驱动', 'desc' => '不断探索新技术、新方法、新模式'],
    ['icon' => '🛡️', 'title' => '诚信务实', 'desc' => '以诚待人，脚踏实地做好每一个项目'],
    ['icon' => '🤝', 'title' => '团队协作', 'desc' => '相信团队的力量，共同成长进步']
];

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="page-hero" style="background: linear-gradient(135deg, var(--bg-darker) 0%, var(--primary-dark) 50%, var(--primary) 100%); padding: 6rem 0 4rem; margin-top: var(--header-height); color: var(--white); position: relative; overflow: hidden;">
    <div style="position: absolute; inset: 0; background: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); opacity: 0.5;"></div>
    <div class="container" style="position: relative; z-index: 1;">
        <div style="text-align: center;">
            <h1 style="font-size: clamp(2rem, 5vw, 3rem); font-weight: 800; margin-bottom: 1rem; color: var(--white);">关于森企动力</h1>
            <p style="font-size: 1.1rem; color: rgba(255,255,255,0.9); margin-bottom: 1.5rem;">让数字化更有价值，助力中国品牌全球化</p>
            <div class="breadcrumbs" style="background: transparent; border: none; padding: 0;">
                <ul class="breadcrumbs-list" style="justify-content: center;">
                    <li><a href="<?php echo siteUrl(); ?>" style="color: rgba(255,255,255,0.8);">首页</a></li>
                    <li><span class="current" style="color: var(--white);">关于我们</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Company Intro Section -->
<section class="section" style="background: var(--bg);">
    <div class="container">
        <div class="about-grid">
            <div class="about-content">
                <div class="section-header left" style="text-align: left;">
                    <span style="display: inline-block; padding: 0.25rem 1rem; background: rgba(26,95,255,0.1); color: var(--primary); border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-bottom: 1rem;">公司介绍</span>
                    <h2 class="section-title"><?php echo h($settings['about_title'] ?? '关于森企动力'); ?></h2>
                    <div class="section-divider" style="margin: 1rem 0 0; background: linear-gradient(90deg, var(--primary), var(--accent)); width: 60px; height: 4px; border-radius: 4px;"></div>
                </div>
                <p class="about-text" style="font-size: 1.05rem; line-height: 1.9; color: var(--text-light); margin-top: 1.5rem;"><?php echo h($settings['about_content'] ?? ''); ?></p>
                <div class="about-features" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 2rem;">
                    <div class="about-feature" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 0;">
                        <div style="width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">✓</div>
                        <span style="font-weight: 500; color: var(--text);">10年+行业经验</span>
                    </div>
                    <div class="about-feature" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 0;">
                        <div style="width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">✓</div>
                        <span style="font-weight: 500; color: var(--text);">5000+成功案例</span>
                    </div>
                    <div class="about-feature" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 0;">
                        <div style="width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">✓</div>
                        <span style="font-weight: 500; color: var(--text);">200+专业团队</span>
                    </div>
                    <div class="about-feature" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 0;">
                        <div style="width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">✓</div>
                        <span style="font-weight: 500; color: var(--text);">7x24贴心服务</span>
                    </div>
                </div>
            </div>
            <div class="about-image" style="position: relative; border-radius: var(--radius-xl); overflow: hidden; box-shadow: var(--shadow-lg);">
                <img src="/assets/images/about.jpg" alt="关于森企动力" onerror="this.style.display='none';this.parentNode.classList.add('no-image');" style="width: 100%; height: 100%; object-fit: cover; min-height: 400px;">
                <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(26,95,255,0.1), transparent 60%); pointer-events: none;"></div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Counters Section -->
<section class="section" style="background: var(--bg-alt); padding: 4rem 0;">
    <div class="container">
        <div class="grid grid-4" style="text-align: center; gap: 2rem;">
            <?php foreach ($stats as $stat): ?>
            <div class="stat-item reveal">
                <div class="stat-number" data-count="<?php echo preg_replace('/[^0-9.]/', '', $stat['num']); ?>"><?php echo h($stat['num']); ?></div>
                <div class="stat-label"><?php echo h($stat['label']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Services Showcase Section -->
<section class="section" style="background: var(--bg);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">我们的服务</h2>
            <p class="section-subtitle">全链路品牌数字化解决方案，一站式服务</p>
            <div class="section-divider"></div>
        </div>
        <div class="grid grid-3" style="gap: 2rem;">
            <?php foreach ($services as $svc): ?>
            <div class="service-card reveal" style="background: var(--surface); border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 2.5rem 2rem; transition: var(--transition); position: relative; overflow: hidden;">
                <div class="service-icon" style="width: 72px; height: 72px; background: linear-gradient(135deg, rgba(26,95,255,0.1), rgba(0,212,255,0.1)); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; font-size: 1.75rem;"><?php echo h($svc['icon']); ?></div>
                <h3 class="service-title" style="font-size: 1.375rem; font-weight: 700; color: var(--heading); margin-bottom: 0.75rem;"><?php echo h($svc['title']); ?></h3>
                <p class="service-desc" style="font-size: 0.95rem; color: var(--text-light); line-height: 1.7; margin-bottom: 1.25rem;"><?php echo h($svc['desc']); ?></p>
                <a href="<?php echo siteUrl('service.php'); ?>" class="service-link" style="color: var(--primary); font-weight: 600; font-size: 0.9rem;">了解更多 →</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Team Section -->
<section id="team" class="section" style="background: var(--bg-alt);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">核心团队</h2>
            <p class="section-subtitle">汇聚行业精英，共创卓越未来</p>
            <div class="section-divider"></div>
        </div>
        <div class="grid grid-4" style="gap: 2rem;">
            <?php foreach ($teamMembers as $member): ?>
            <div class="team-card reveal" style="background: var(--surface); border-radius: var(--radius-lg); overflow: hidden; text-align: center; box-shadow: var(--shadow-sm); transition: var(--transition);">
                <div class="team-avatar" style="aspect-ratio: 1; overflow: hidden; background: var(--bg-alt);">
                    <img src="<?php echo h($member['image']); ?>" alt="<?php echo h($member['name']); ?>" onerror="this.src='/assets/images/placeholder.jpg';" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="team-info" style="padding: 1.5rem;">
                    <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--heading); margin-bottom: 0.25rem;"><?php echo h($member['name']); ?></h4>
                    <p style="color: var(--primary); font-weight: 600; font-size: 0.9rem; margin-bottom: 0.75rem;"><?php echo h($member['title']); ?></p>
                    <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;"><?php echo h($member['desc']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Culture / Values Section -->
<section id="culture" class="section" style="background: var(--bg);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">企业文化</h2>
            <p class="section-subtitle">我们的价值观，驱动我们不断前行</p>
            <div class="section-divider"></div>
        </div>
        <div class="grid grid-4" style="gap: 2rem;">
            <?php foreach ($values as $val): ?>
            <div class="value-card reveal" style="text-align: center; padding: 2.5rem 1.5rem; background: var(--surface); border-radius: var(--radius-lg); border: 1px solid var(--border-light); transition: var(--transition);">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, rgba(26,95,255,0.1), rgba(0,212,255,0.1)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 2rem;"><?php echo h($val['icon']); ?></div>
                <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--heading); margin-bottom: 0.75rem;"><?php echo h($val['title']); ?></h4>
                <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.7; margin: 0;"><?php echo h($val['desc']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section" style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--accent) 100%); padding: 4rem 0; color: var(--white);">
    <div class="container">
        <div class="cta-content" style="text-align: center;">
            <h2 class="cta-title" style="color: var(--white); font-size: clamp(1.75rem, 4vw, 2.5rem); margin-bottom: 1rem;">准备好开启您的品牌数字化之旅了吗？</h2>
            <p class="cta-desc" style="color: rgba(255,255,255,0.9); font-size: 1.1rem; margin-bottom: 2rem;">联系我们的专家团队，获取专属解决方案</p>
            <a href="<?php echo siteUrl('contact.php'); ?>" class="btn btn-white btn-lg">立即咨询 →</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>