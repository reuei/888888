<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$currentPage = 'case';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$case = $id ? DB::getRow('cases', ['id' => $id]) : null;

if (!$case) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="zh-CN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>页面未找到 - <?php echo h(SITE_NAME); ?></title>
        <link rel="stylesheet" href="/assets/css/style.css">
        <style>
            body { background: linear-gradient(135deg, var(--bg-darker) 0%, var(--primary-dark) 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
            .error-box { text-align: center; color: var(--white); }
            .error-code { font-size: clamp(6rem, 20vw, 10rem); font-weight: 900; line-height: 1; background: linear-gradient(135deg, var(--accent), var(--primary-light)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; margin-bottom: 1rem; }
            .error-title { font-size: 1.5rem; margin-bottom: 1rem; }
            .error-desc { color: rgba(255,255,255,0.8); margin-bottom: 2rem; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <div class="error-code">404</div>
            <h1 class="error-title">案例未找到</h1>
            <p class="error-desc">抱歉，您访问的案例不存在或已被删除。</p>
            <a href="<?php echo siteUrl('case.php'); ?>" class="btn btn-white btn-lg">返回案例列表</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$relatedCases = DB::getList('cases', ['status' => 1, 'category' => $case['category']], 'sort', 'ASC', 3);
$relatedCases = array_filter($relatedCases, function($r) use ($id) {
    return $r['id'] != $id;
});
if (count($relatedCases) < 3) {
    $moreCases = DB::getList('cases', ['status' => 1], 'sort', 'ASC', 3);
    foreach ($moreCases as $r) {
        if ($r['id'] != $id && count($relatedCases) < 3 && !in_array($r['id'], array_column($relatedCases, 'id'))) {
            $relatedCases[] = $r;
        }
    }
}
$relatedCases = array_slice($relatedCases, 0, 3);

include __DIR__ . '/includes/header.php';
?>

<!-- Case Hero Section -->
<section class="case-hero" style="position: relative; overflow: hidden; margin-top: var(--header-height); background: var(--bg-alt);">
    <div class="case-hero-image" style="position: absolute; inset: 0;">
        <img src="<?php echo h($case['image']); ?>" alt="<?php echo h($case['title']); ?>" onerror="this.src='/assets/images/placeholder.jpg';" style="width: 100%; height: 100%; object-fit: cover;">
        <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(5,13,40,0.85) 0%, rgba(13,71,194,0.7) 50%, rgba(26,95,255,0.6) 100%);"></div>
    </div>
    <div class="container" style="position: relative; z-index: 1; padding: 6rem 1.5rem 4rem;">
        <div style="max-width: 800px; color: var(--white);">
            <div class="breadcrumbs" style="background: transparent; border: none; padding: 0; margin-bottom: 1.5rem;">
                <ul class="breadcrumbs-list">
                    <li><a href="<?php echo siteUrl(); ?>" style="color: rgba(255,255,255,0.8);">首页</a></li>
                    <li><a href="<?php echo siteUrl('case.php'); ?>" style="color: rgba(255,255,255,0.8);">客户案例</a></li>
                    <li><span class="current" style="color: var(--white);"><?php echo h($case['title']); ?></span></li>
                </ul>
            </div>
            <span style="display: inline-block; padding: 0.375rem 1rem; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); border-radius: var(--radius-full); font-size: 0.85rem; color: var(--white); margin-bottom: 1.5rem;"><?php echo h($case['category']); ?></span>
            <h1 style="font-size: clamp(2rem, 5vw, 3rem); font-weight: 800; margin-bottom: 1rem; color: var(--white); line-height: 1.2;"><?php echo h($case['title']); ?></h1>
            <div style="display: flex; gap: 2rem; flex-wrap: wrap; color: rgba(255,255,255,0.9); font-size: 0.95rem;">
                <span>👤 客户：<?php echo h($case['client'] ?? ''); ?></span>
                <span>📅 日期：<?php echo h(formatDate($case['date'] ?? '')); ?></span>
                <span>🏷️ 分类：<?php echo h($case['category']); ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Case Content Section -->
<section class="section" style="background: var(--bg);">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <div style="background: var(--surface); border-radius: var(--radius-lg); padding: 3rem; box-shadow: var(--shadow-sm); border: 1px solid var(--border-light);">
                <div class="case-content" style="font-size: 1.05rem; line-height: 1.9; color: var(--text);">
                    <?php echo nl2br(h($case['content'])); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Cases Section -->
<?php if (count($relatedCases) > 0): ?>
<section class="section" style="background: var(--bg-alt);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">相关案例</h2>
            <div class="section-divider"></div>
        </div>
        <div class="grid grid-3" style="gap: 2rem;">
            <?php foreach ($relatedCases as $rCase): ?>
            <div class="case-card reveal" style="background: var(--surface); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); transition: var(--transition);">
                <a href="<?php echo siteUrl('case_detail.php?id=' . $rCase['id']); ?>" style="text-decoration: none; color: inherit;">
                    <div class="case-image" style="position: relative; aspect-ratio: 4/3; overflow: hidden;">
                        <img src="<?php echo h($rCase['image']); ?>" alt="<?php echo h($rCase['title']); ?>" onerror="this.src='/assets/images/placeholder.jpg';" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease;">
                    </div>
                    <div class="case-info" style="padding: 1.5rem;">
                        <span class="case-category" style="display: inline-block; font-size: 0.8rem; font-weight: 600; color: var(--primary); margin-bottom: 0.375rem;"><?php echo h($rCase['category']); ?></span>
                        <h3 class="case-title" style="font-size: 1.125rem; font-weight: 700; color: var(--heading); margin-bottom: 0.5rem;"><?php echo h($rCase['title']); ?></h3>
                        <p class="case-desc" style="font-size: 0.9rem; color: var(--text-light); margin: 0; line-height: 1.7;"><?php echo h(truncate($rCase['desc'], 60)); ?></p>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contact CTA Section -->
<section class="cta-section" style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--accent) 100%); padding: 4rem 0; color: var(--white);">
    <div class="container">
        <div class="cta-content" style="text-align: center;">
            <h2 class="cta-title" style="color: var(--white); font-size: clamp(1.75rem, 4vw, 2.5rem); margin-bottom: 1rem;">有类似的项目需求？</h2>
            <p class="cta-desc" style="color: rgba(255,255,255,0.9); font-size: 1.1rem; margin-bottom: 2rem;">联系我们的专家团队，获取专属解决方案</p>
            <a href="<?php echo siteUrl('contact.php'); ?>" class="btn btn-white btn-lg">立即咨询 →</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>