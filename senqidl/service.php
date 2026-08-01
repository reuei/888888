<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$currentPage = 'service';
$services = DB::getList('services', ['status' => 1], 'sort', 'ASC');

$process = [
    ['step' => '01', 'title' => '需求分析', 'desc' => '深入了解客户业务现状、痛点和目标，进行全面的行业调研和竞品分析。'],
    ['step' => '02', 'title' => '策略制定', 'desc' => '基于分析结果，制定详细的品牌数字化策略和可执行的落地方案。'],
    ['step' => '03', 'title' => '设计开发', 'desc' => '专业的设计和技术团队，高品质交付，确保每个细节都达到预期标准。'],
    ['step' => '04', 'title' => '测试上线', 'desc' => '全面的测试和优化，确保产品在各种环境下稳定运行，顺利上线。'],
    ['step' => '05', 'title' => '运营维护', 'desc' => '持续的运营支持和数据分析，帮助客户实现长期业务增长。']
];

$whyUs = [
    ['icon' => '🏆', 'title' => '10年+行业经验', 'desc' => '深耕品牌数字化领域，服务超过5000家企业客户'],
    ['icon' => '👨‍💻', 'title' => '200+专业团队', 'desc' => '汇聚设计、开发、运营各领域资深专家'],
    ['icon' => '⚡', 'title' => '快速响应交付', 'desc' => '敏捷开发流程，高效项目管理，确保按时交付'],
    ['icon' => '🔒', 'title' => '品质保证', 'desc' => '严格的质量管控体系，多重测试验证，确保交付品质'],
    ['icon' => '🎯', 'title' => '量身定制', 'desc' => '根据客户实际需求提供定制化解决方案'],
    ['icon' => '📞', 'title' => '7x24服务', 'desc' => '全天候技术支持和客户服务，随时响应您的需求']
];

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="page-hero" style="background: linear-gradient(135deg, var(--bg-darker) 0%, var(--primary-dark) 50%, var(--primary) 100%); padding: 6rem 0 4rem; margin-top: var(--header-height); color: var(--white); position: relative; overflow: hidden;">
    <div style="position: absolute; inset: 0; background: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); opacity: 0.5;"></div>
    <div class="container" style="position: relative; z-index: 1;">
        <div style="text-align: center;">
            <h1 style="font-size: clamp(2rem, 5vw, 3rem); font-weight: 800; margin-bottom: 1rem; color: var(--white);">我们的服务</h1>
            <p style="font-size: 1.1rem; color: rgba(255,255,255,0.9); margin-bottom: 1.5rem;">全链路品牌数字化解决方案，一站式服务</p>
            <div class="breadcrumbs" style="background: transparent; border: none; padding: 0;">
                <ul class="breadcrumbs-list" style="justify-content: center;">
                    <li><a href="<?php echo siteUrl(); ?>" style="color: rgba(255,255,255,0.8);">首页</a></li>
                    <li><span class="current" style="color: var(--white);">服务项目</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- All Services Grid -->
<section class="section" style="background: var(--bg);">
    <div class="container">
        <div class="grid grid-3" style="gap: 2rem;">
            <?php foreach ($services as $svc): ?>
            <div class="service-card reveal" style="background: var(--surface); border: 1px solid var(--border-light); border-radius: var(--radius-lg); padding: 2.5rem 2rem; transition: var(--transition); position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--primary), var(--accent)); transform: scaleX(0); transform-origin: left; transition: transform 0.4s ease;"></div>
                <div class="service-icon" style="width: 72px; height: 72px; background: linear-gradient(135deg, rgba(26,95,255,0.1), rgba(0,212,255,0.1)); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; font-size: 1.75rem;"><?php echo h($svc['icon']); ?></div>
                <h3 class="service-title" style="font-size: 1.375rem; font-weight: 700; color: var(--heading); margin-bottom: 0.75rem;"><?php echo h($svc['title']); ?></h3>
                <p class="service-desc" style="font-size: 0.95rem; color: var(--text-light); line-height: 1.7; margin-bottom: 1.25rem;"><?php echo h($svc['desc']); ?></p>
                <a href="<?php echo siteUrl('contact.php'); ?>" class="service-link" style="color: var(--primary); font-weight: 600; font-size: 0.9rem;">了解更多 →</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Process / Workflow Section -->
<section class="section" style="background: var(--bg-alt);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">服务流程</h2>
            <p class="section-subtitle">专业的项目管理流程，确保每个项目高效落地</p>
            <div class="section-divider"></div>
        </div>
        <div class="process-steps" style="display: flex; justify-content: space-between; gap: 1rem; flex-wrap: wrap; position: relative;">
            <div style="position: absolute; top: 40px; left: 10%; right: 10%; height: 3px; background: linear-gradient(90deg, var(--primary), var(--accent)); border-radius: 3px; opacity: 0.3; z-index: 0;"></div>
            <?php foreach ($process as $step): ?>
            <div class="process-step reveal" style="flex: 1; min-width: 180px; text-align: center; position: relative; z-index: 1; padding: 0 0.5rem;">
                <div style="width: 80px; height: 80px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: white; font-weight: 800; font-size: 1.5rem; box-shadow: 0 8px 30px rgba(26,95,255,0.35);"><?php echo h($step['step']); ?></div>
                <h4 style="font-size: 1.25rem; font-weight: 700; color: var(--heading); margin-bottom: 0.75rem;"><?php echo h($step['title']); ?></h4>
                <p style="color: var(--text-light); font-size: 0.9rem; line-height: 1.7; margin: 0;"><?php echo h($step['desc']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="section" style="background: var(--bg);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">为什么选择我们</h2>
            <p class="section-subtitle">六大核心优势，助力您的品牌腾飞</p>
            <div class="section-divider"></div>
        </div>
        <div class="grid grid-3" style="gap: 2rem;">
            <?php foreach ($whyUs as $item): ?>
            <div class="why-card reveal" style="padding: 2rem; background: var(--surface); border-radius: var(--radius-lg); border: 1px solid var(--border-light); transition: var(--transition); display: flex; gap: 1.25rem;">
                <div style="width: 64px; height: 64px; background: linear-gradient(135deg, rgba(26,95,255,0.1), rgba(0,212,255,0.1)); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1.75rem;"><?php echo h($item['icon']); ?></div>
                <div>
                    <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--heading); margin-bottom: 0.5rem;"><?php echo h($item['title']); ?></h4>
                    <p style="color: var(--text-light); font-size: 0.9rem; line-height: 1.7; margin: 0;"><?php echo h($item['desc']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section" style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--accent) 100%); padding: 4rem 0; color: var(--white);">
    <div class="container">
        <div class="cta-content" style="text-align: center;">
            <h2 class="cta-title" style="color: var(--white); font-size: clamp(1.75rem, 4vw, 2.5rem); margin-bottom: 1rem;">需要定制化解决方案？</h2>
            <p class="cta-desc" style="color: rgba(255,255,255,0.9); font-size: 1.1rem; margin-bottom: 2rem;">联系我们的专家团队，获取专属服务方案</p>
            <a href="<?php echo siteUrl('contact.php'); ?>" class="btn btn-white btn-lg">立即咨询 →</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>