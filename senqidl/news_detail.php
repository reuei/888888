<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$currentPage = 'news';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$news = $id ? DB::getRow('news', ['id' => $id]) : null;

if (!$news) {
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
            <h1 class="error-title">资讯未找到</h1>
            <p class="error-desc">抱歉，您访问的资讯不存在或已被删除。</p>
            <a href="<?php echo siteUrl('news.php'); ?>" class="btn btn-white btn-lg">返回新闻列表</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$relatedNews = DB::getList('news', ['status' => 1, 'category' => $news['category']], 'date', 'DESC', 3);
$relatedNews = array_filter($relatedNews, function($r) use ($id) {
    return $r['id'] != $id;
});
if (count($relatedNews) < 3) {
    $moreNews = DB::getList('news', ['status' => 1], 'date', 'DESC', 3);
    foreach ($moreNews as $r) {
        if ($r['id'] != $id && count($relatedNews) < 3 && !in_array($r['id'], array_column($relatedNews, 'id'))) {
            $relatedNews[] = $r;
        }
    }
}
$relatedNews = array_slice($relatedNews, 0, 3);

$allNews = DB::getList('news', ['status' => 1], 'date', 'DESC');
$currentIndex = -1;
foreach ($allNews as $i => $n) {
    if ($n['id'] == $id) {
        $currentIndex = $i;
        break;
    }
}
$prevNews = $currentIndex > 0 ? $allNews[$currentIndex - 1] : null;
$nextNews = $currentIndex < count($allNews) - 1 ? $allNews[$currentIndex + 1] : null;

include __DIR__ . '/includes/header.php';
?>

<!-- Article Header -->
<section class="article-header" style="background: var(--bg-alt); padding: 4rem 0 2rem; margin-top: var(--header-height);">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <div class="breadcrumbs" style="margin-bottom: 1.5rem;">
                <ul class="breadcrumbs-list">
                    <li><a href="<?php echo siteUrl(); ?>">首页</a></li>
                    <li><a href="<?php echo siteUrl('news.php'); ?>">新闻资讯</a></li>
                    <li><span class="current"><?php echo h($news['title']); ?></span></li>
                </ul>
            </div>
            <div class="article-meta" style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1.5rem; font-size: 0.9rem; color: var(--text-light);">
                <span class="news-category" style="color: var(--primary); font-weight: 600;"><?php echo h($news['category']); ?></span>
                <span>📅 <?php echo h(formatDate($news['date'])); ?></span>
            </div>
            <h1 class="article-title" style="font-size: clamp(1.75rem, 4vw, 2.5rem); font-weight: 800; color: var(--heading); line-height: 1.3; margin-bottom: 1.5rem;"><?php echo h($news['title']); ?></h1>
        </div>
    </div>
</section>

<!-- Article Image -->
<?php if (!empty($news['image'])): ?>
<section style="background: var(--bg); padding-bottom: 2rem;">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <img src="<?php echo h($news['image']); ?>" alt="<?php echo h($news['title']); ?>" onerror="this.src='/assets/images/placeholder.jpg';" style="width: 100%; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Article Content -->
<section class="section" style="background: var(--bg); padding-top: 2rem;">
    <div class="container">
        <div style="max-width: 900px; margin: 0 auto;">
            <div class="article-content" style="font-size: 1.05rem; line-height: 1.9; color: var(--text); background: var(--surface); padding: 2.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-light);">
                <?php echo nl2br(h($news['content'])); ?>
            </div>

            <!-- Previous/Next Navigation -->
            <div class="article-nav" style="display: flex; justify-content: space-between; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-light);">
                <?php if ($prevNews): ?>
                <a href="<?php echo siteUrl('news_detail.php?id=' . $prevNews['id']); ?>" style="flex: 1; padding: 1rem 1.25rem; background: var(--surface); border: 1px solid var(--border-light); border-radius: var(--radius-lg); text-decoration: none; color: inherit; transition: var(--transition); display: block;">
                    <span style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.375rem;">← 上一篇</span>
                    <span style="font-weight: 600; color: var(--heading); display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;"><?php echo h($prevNews['title']); ?></span>
                </a>
                <?php else: ?>
                <div style="flex: 1;"></div>
                <?php endif; ?>
                <?php if ($nextNews): ?>
                <a href="<?php echo siteUrl('news_detail.php?id=' . $nextNews['id']); ?>" style="flex: 1; padding: 1rem 1.25rem; background: var(--surface); border: 1px solid var(--border-light); border-radius: var(--radius-lg); text-decoration: none; color: inherit; transition: var(--transition); display: block; text-align: right;">
                    <span style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.375rem;">下一篇 →</span>
                    <span style="font-weight: 600; color: var(--heading); display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;"><?php echo h($nextNews['title']); ?></span>
                </a>
                <?php else: ?>
                <div style="flex: 1;"></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Related News Section -->
<?php if (count($relatedNews) > 0): ?>
<section class="section" style="background: var(--bg-alt);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">相关资讯</h2>
            <div class="section-divider"></div>
        </div>
        <div class="grid grid-3" style="gap: 2rem;">
            <?php foreach ($relatedNews as $rNews): ?>
            <div class="news-card reveal" style="background: var(--surface); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); transition: var(--transition);">
                <a href="<?php echo siteUrl('news_detail.php?id=' . $rNews['id']); ?>" style="text-decoration: none; color: inherit;">
                    <div class="news-image" style="aspect-ratio: 16/9; overflow: hidden;">
                        <img src="<?php echo h($rNews['image']); ?>" alt="<?php echo h($rNews['title']); ?>" onerror="this.src='/assets/images/placeholder.jpg';" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="news-body" style="padding: 1.5rem;">
                        <div class="news-meta" style="display: flex; gap: 1rem; margin-bottom: 0.75rem; font-size: 0.85rem; color: var(--text-muted);">
                            <span class="news-category" style="color: var(--primary); font-weight: 600;"><?php echo h($rNews['category']); ?></span>
                            <span><?php echo h(formatDate($rNews['date'])); ?></span>
                        </div>
                        <h3 class="news-title" style="font-size: 1.125rem; font-weight: 700; color: var(--heading); margin-bottom: 0.5rem;"><?php echo h($rNews['title']); ?></h3>
                        <p class="news-excerpt" style="font-size: 0.9rem; color: var(--text-light); margin: 0;"><?php echo h(truncate(strip_tags($rNews['content']), 60)); ?></p>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>