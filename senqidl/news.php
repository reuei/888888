<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$currentPage = 'news';
$category = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 5;

$conditions = ['status' => 1];
if ($category) {
    $conditions['category'] = $category;
}

$total = DB::count('news', $conditions);
$totalPages = max(1, ceil($total / $perPage));
$offset = ($page - 1) * $perPage;

$newsList = DB::getList('news', $conditions, 'date', 'DESC', $perPage, $offset);
$allNews = DB::getAll('news');

$categories = [];
foreach ($allNews as $n) {
    if (!empty($n['category']) && !in_array($n['category'], $categories)) {
        $categories[] = $n['category'];
    }
}

$latestNews = DB::getList('news', ['status' => 1], 'date', 'DESC', 5);
$allCategories = $categories;

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="page-hero" style="background: linear-gradient(135deg, var(--bg-darker) 0%, var(--primary-dark) 50%, var(--primary) 100%); padding: 6rem 0 4rem; margin-top: var(--header-height); color: var(--white); position: relative; overflow: hidden;">
    <div style="position: absolute; inset: 0; background: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); opacity: 0.5;"></div>
    <div class="container" style="position: relative; z-index: 1;">
        <div style="text-align: center;">
            <h1 style="font-size: clamp(2rem, 5vw, 3rem); font-weight: 800; margin-bottom: 1rem; color: var(--white);">新闻资讯</h1>
            <p style="font-size: 1.1rem; color: rgba(255,255,255,0.9); margin-bottom: 1.5rem;">了解行业最新动态和公司资讯</p>
            <div class="breadcrumbs" style="background: transparent; border: none; padding: 0;">
                <ul class="breadcrumbs-list" style="justify-content: center;">
                    <li><a href="<?php echo siteUrl(); ?>" style="color: rgba(255,255,255,0.8);">首页</a></li>
                    <li><span class="current" style="color: var(--white);">新闻资讯</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- News Listing Section -->
<section class="section" style="background: var(--bg);">
    <div class="container">
        <div class="news-layout" style="display: grid; grid-template-columns: 1fr 320px; gap: 3rem; align-items: start;">
            <!-- Main Content -->
            <div class="news-main">
                <!-- Category Filter -->
                <div class="case-filter" style="display: flex; gap: 0.5rem; margin-bottom: 2rem; flex-wrap: wrap;">
                    <a href="<?php echo siteUrl('news.php'); ?>" style="padding: 0.5rem 1.25rem; border-radius: var(--radius-full); font-size: 0.9rem; font-weight: 500; color: <?php echo !$category ? 'var(--white)' : 'var(--text-light)'; ?>; background: <?php echo !$category ? 'var(--primary)' : 'var(--bg-alt)'; ?>; text-decoration: none; transition: var(--transition-fast);">全部</a>
                    <?php foreach ($allCategories as $cat): ?>
                    <a href="<?php echo siteUrl('news.php?cat=' . urlencode($cat)); ?>" style="padding: 0.5rem 1.25rem; border-radius: var(--radius-full); font-size: 0.9rem; font-weight: 500; color: <?php echo $category === $cat ? 'var(--white)' : 'var(--text-light)'; ?>; background: <?php echo $category === $cat ? 'var(--primary)' : 'var(--bg-alt)'; ?>; text-decoration: none; transition: var(--transition-fast);"><?php echo h($cat); ?></a>
                    <?php endforeach; ?>
                </div>

                <!-- News Cards List -->
                <?php if (count($newsList) > 0): ?>
                <div class="news-list" style="display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php foreach ($newsList as $item): ?>
                    <article class="news-card reveal" style="display: flex; gap: 1.5rem; background: var(--surface); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); transition: var(--transition); padding: 1.5rem; border: 1px solid var(--border-light);">
                        <a href="<?php echo siteUrl('news_detail.php?id=' . $item['id']); ?>" class="news-thumb" style="flex-shrink: 0; width: 280px; aspect-ratio: 16/9; overflow: hidden; border-radius: var(--radius-md); display: block;">
                            <img src="<?php echo h($item['image']); ?>" alt="<?php echo h($item['title']); ?>" onerror="this.src='/assets/images/placeholder.jpg';" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                        </a>
                        <div class="news-body" style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
                            <div class="news-meta" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem; font-size: 0.85rem; color: var(--text-muted);">
                                <span class="news-category" style="color: var(--primary); font-weight: 600;"><?php echo h($item['category']); ?></span>
                                <span class="news-date"><?php echo h(formatDate($item['date'])); ?></span>
                            </div>
                            <h3 class="news-title" style="font-size: 1.25rem; font-weight: 700; color: var(--heading); margin-bottom: 0.75rem; line-height: 1.4;"><a href="<?php echo siteUrl('news_detail.php?id=' . $item['id']); ?>" style="color: inherit; text-decoration: none; transition: color 0.3s ease;"><?php echo h($item['title']); ?></a></h3>
                            <p class="news-excerpt" style="font-size: 0.95rem; color: var(--text-light); line-height: 1.7; margin-bottom: 1rem;"><?php echo h(truncate(strip_tags($item['content']), 120)); ?></p>
                            <a href="<?php echo siteUrl('news_detail.php?id=' . $item['id']); ?>" class="news-link" style="color: var(--primary); font-weight: 600; font-size: 0.9rem; text-decoration: none;">阅读全文 →</a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination" style="display: flex; align-items: center; justify-content: center; gap: 0.375rem; margin: 3rem 0;">
                    <?php if ($page > 1): ?>
                    <a href="<?php echo siteUrl('news.php' . ($category ? '?cat=' . urlencode($category) : '') . '&page=' . ($page - 1)); ?>" style="min-width: 40px; height: 40px; padding: 0 0.875rem; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius); background: var(--surface); border: 1px solid var(--border); color: var(--text); font-weight: 500; font-size: 0.9rem; text-decoration: none;">‹</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?php echo siteUrl('news.php' . ($category ? '?cat=' . urlencode($category) : '') . '&page=' . $i); ?>" style="min-width: 40px; height: 40px; padding: 0 0.875rem; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius); background: <?php echo $i === $page ? 'var(--primary)' : 'var(--surface)'; ?>; border: 1px solid <?php echo $i === $page ? 'var(--primary)' : 'var(--border)'; ?>; color: <?php echo $i === $page ? 'var(--white)' : 'var(--text)'; ?>; font-weight: 500; font-size: 0.9rem; text-decoration: none;"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                    <a href="<?php echo siteUrl('news.php' . ($category ? '?cat=' . urlencode($category) : '') . '&page=' . ($page + 1)); ?>" style="min-width: 40px; height: 40px; padding: 0 0.875rem; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius); background: var(--surface); border: 1px solid var(--border); color: var(--text); font-weight: 500; font-size: 0.9rem; text-decoration: none;">›</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div style="text-align: center; padding: 4rem 0; color: var(--text-light);">
                    <p style="font-size: 1.1rem;">暂无相关资讯</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="news-sidebar" style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- Categories Widget -->
                <div class="sidebar-widget" style="background: var(--surface); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm); border: 1px solid var(--border-light);">
                    <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--heading); margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 2px solid var(--border-light);">文章分类</h4>
                    <ul class="sidebar-categories" style="list-style: none; padding: 0; margin: 0;">
                        <li style="margin-bottom: 0.5rem;"><a href="<?php echo siteUrl('news.php'); ?>" style="display: flex; justify-content: space-between; padding: 0.5rem 0; color: var(--text-light); text-decoration: none; border-bottom: 1px solid var(--border-light); transition: var(--transition-fast);"><span>全部分类</span><span style="color: var(--text-muted);"><?php echo DB::count('news', ['status' => 1]); ?></span></a></li>
                        <?php foreach ($categories as $cat): ?>
                        <li style="margin-bottom: 0.5rem;"><a href="<?php echo siteUrl('news.php?cat=' . urlencode($cat)); ?>" style="display: flex; justify-content: space-between; padding: 0.5rem 0; color: <?php echo $category === $cat ? 'var(--primary)' : 'var(--text-light)'; ?>; font-weight: <?php echo $category === $cat ? '600' : '400'; ?>; text-decoration: none; border-bottom: 1px solid var(--border-light); transition: var(--transition-fast);"><span><?php echo h($cat); ?></span><span style="color: var(--text-muted);"><?php echo DB::count('news', ['status' => 1, 'category' => $cat]); ?></span></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Latest News Widget -->
                <div class="sidebar-widget" style="background: var(--surface); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm); border: 1px solid var(--border-light);">
                    <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--heading); margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 2px solid var(--border-light);">最新资讯</h4>
                    <ul class="sidebar-latest" style="list-style: none; padding: 0; margin: 0;">
                        <?php foreach ($latestNews as $lNews): ?>
                        <li style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-light);">
                            <a href="<?php echo siteUrl('news_detail.php?id=' . $lNews['id']); ?>" style="display: flex; gap: 0.75rem; text-decoration: none; transition: var(--transition-fast);">
                                <img src="<?php echo h($lNews['image']); ?>" alt="<?php echo h($lNews['title']); ?>" onerror="this.src='/assets/images/placeholder.jpg';" style="width: 64px; height: 64px; object-fit: cover; border-radius: var(--radius-sm); flex-shrink: 0;">
                                <div style="flex: 1; min-width: 0;">
                                    <h5 style="font-size: 0.9rem; font-weight: 600; color: var(--heading); line-height: 1.4; margin: 0 0 0.375rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo h($lNews['title']); ?></h5>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo h(formatDate($lNews['date'])); ?></span>
                                </div>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>