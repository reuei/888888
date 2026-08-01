<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$currentPage = 'case';
$category = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 6;

$conditions = ['status' => 1];
if ($category) {
    $conditions['category'] = $category;
}

$total = DB::count('cases', $conditions);
$totalPages = max(1, ceil($total / $perPage));
$offset = ($page - 1) * $perPage;

$cases = DB::getList('cases', $conditions, 'sort', 'ASC', $perPage, $offset);
$allCategories = DB::getAll('cases');

$categories = [];
foreach ($allCategories as $c) {
    if (!empty($c['category']) && !in_array($c['category'], $categories)) {
        $categories[] = $c['category'];
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="page-hero" style="background: linear-gradient(135deg, var(--bg-darker) 0%, var(--primary-dark) 50%, var(--primary) 100%); padding: 6rem 0 4rem; margin-top: var(--header-height); color: var(--white); position: relative; overflow: hidden;">
    <div style="position: absolute; inset: 0; background: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); opacity: 0.5;"></div>
    <div class="container" style="position: relative; z-index: 1;">
        <div style="text-align: center;">
            <h1 style="font-size: clamp(2rem, 5vw, 3rem); font-weight: 800; margin-bottom: 1rem; color: var(--white);">客户案例</h1>
            <p style="font-size: 1.1rem; color: rgba(255,255,255,0.9); margin-bottom: 1.5rem;">我们为客户创造的价值</p>
            <div class="breadcrumbs" style="background: transparent; border: none; padding: 0;">
                <ul class="breadcrumbs-list" style="justify-content: center;">
                    <li><a href="<?php echo siteUrl(); ?>" style="color: rgba(255,255,255,0.8);">首页</a></li>
                    <li><span class="current" style="color: var(--white);">客户案例</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Cases Section -->
<section class="section" style="background: var(--bg);">
    <div class="container">
        <!-- Category Filter Tabs -->
        <div class="case-filter" style="display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 2.5rem; flex-wrap: wrap;">
            <a href="<?php echo siteUrl('case.php'); ?>" class="case-filter-item <?php echo !$category ? 'active' : ''; ?>" style="padding: 0.5rem 1.25rem; border-radius: var(--radius-full); font-size: 0.9rem; font-weight: 500; color: <?php echo !$category ? 'var(--white)' : 'var(--text-light)'; ?>; background: <?php echo !$category ? 'var(--primary)' : 'var(--bg-alt)'; ?>; cursor: pointer; text-decoration: none; transition: var(--transition-fast);">全部</a>
            <?php foreach ($categories as $cat): ?>
            <a href="<?php echo siteUrl('case.php?cat=' . urlencode($cat)); ?>" class="case-filter-item <?php echo $category === $cat ? 'active' : ''; ?>" style="padding: 0.5rem 1.25rem; border-radius: var(--radius-full); font-size: 0.9rem; font-weight: 500; color: <?php echo $category === $cat ? 'var(--white)' : 'var(--text-light)'; ?>; background: <?php echo $category === $cat ? 'var(--primary)' : 'var(--bg-alt)'; ?>; cursor: pointer; text-decoration: none; transition: var(--transition-fast);"><?php echo h($cat); ?></a>
            <?php endforeach; ?>
        </div>

        <!-- Case Cards Grid -->
        <?php if (count($cases) > 0): ?>
        <div class="grid grid-3" style="gap: 2rem;">
            <?php foreach ($cases as $case): ?>
            <div class="case-card reveal" style="background: var(--surface); border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); transition: var(--transition);">
                <a href="<?php echo siteUrl('case_detail.php?id=' . $case['id']); ?>" style="text-decoration: none; color: inherit;">
                    <div class="case-image" style="position: relative; aspect-ratio: 4/3; overflow: hidden;">
                        <img src="<?php echo h($case['image']); ?>" alt="<?php echo h($case['title']); ?>" onerror="this.src='/assets/images/placeholder.jpg';" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease;">
                        <div class="case-overlay" style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(26,95,255,0.9) 0%, rgba(26,95,255,0.3) 60%, transparent 100%); display: flex; align-items: flex-end; padding: 1.5rem; opacity: 0; transition: opacity 0.4s ease;">
                            <span style="color: var(--white); font-weight: 600; font-size: 0.85rem;"><?php echo h($case['category']); ?></span>
                        </div>
                    </div>
                    <div class="case-info" style="padding: 1.5rem;">
                        <span class="case-category" style="display: inline-block; font-size: 0.8rem; font-weight: 600; color: var(--primary); margin-bottom: 0.375rem;"><?php echo h($case['category']); ?></span>
                        <h3 class="case-title" style="font-size: 1.25rem; font-weight: 700; color: var(--heading); margin-bottom: 0.5rem;"><?php echo h($case['title']); ?></h3>
                        <p class="case-desc" style="font-size: 0.9rem; color: var(--text-light); margin: 0; line-height: 1.7;"><?php echo h(truncate($case['desc'], 80)); ?></p>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination" style="display: flex; align-items: center; justify-content: center; gap: 0.375rem; margin: 3rem 0;">
            <?php if ($page > 1): ?>
            <a href="<?php echo siteUrl('case.php' . ($category ? '?cat=' . urlencode($category) : '') . '&page=' . ($page - 1)); ?>" class="pagination-item" style="min-width: 40px; height: 40px; padding: 0 0.875rem; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius); background: var(--surface); border: 1px solid var(--border); color: var(--text); font-weight: 500; font-size: 0.9rem; text-decoration: none;">‹</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="<?php echo siteUrl('case.php' . ($category ? '?cat=' . urlencode($category) : '') . '&page=' . $i); ?>" class="pagination-item <?php echo $i === $page ? 'active' : ''; ?>" style="min-width: 40px; height: 40px; padding: 0 0.875rem; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius); background: <?php echo $i === $page ? 'var(--primary)' : 'var(--surface)'; ?>; border: 1px solid <?php echo $i === $page ? 'var(--primary)' : 'var(--border)'; ?>; color: <?php echo $i === $page ? 'var(--white)' : 'var(--text)'; ?>; font-weight: 500; font-size: 0.9rem; text-decoration: none;"><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
            <a href="<?php echo siteUrl('case.php' . ($category ? '?cat=' . urlencode($category) : '') . '&page=' . ($page + 1)); ?>" class="pagination-item" style="min-width: 40px; height: 40px; padding: 0 0.875rem; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius); background: var(--surface); border: 1px solid var(--border); color: var(--text); font-weight: 500; font-size: 0.9rem; text-decoration: none;">›</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div style="text-align: center; padding: 4rem 0; color: var(--text-light);">
            <p style="font-size: 1.1rem;">暂无相关案例</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section" style="background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 50%, var(--accent) 100%); padding: 4rem 0; color: var(--white);">
    <div class="container">
        <div class="cta-content" style="text-align: center;">
            <h2 class="cta-title" style="color: var(--white); font-size: clamp(1.75rem, 4vw, 2.5rem); margin-bottom: 1rem;">想成为我们的下一个成功案例？</h2>
            <p class="cta-desc" style="color: rgba(255,255,255,0.9); font-size: 1.1rem; margin-bottom: 2rem;">联系我们的专家团队，开始您的数字化之旅</p>
            <a href="<?php echo siteUrl('contact.php'); ?>" class="btn btn-white btn-lg">立即咨询 →</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>