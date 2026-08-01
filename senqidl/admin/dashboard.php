<?php
$pageTitle = '仪表盘';
require_once __DIR__ . '/header.php';

$caseCount = DB::count('cases');
$newsCount = DB::count('news');
$serviceCount = DB::count('services');
$slideCount = DB::count('slides');

$recentCases = DB::getList('cases', [], 'id', 'DESC', 5);
$recentNews = DB::getList('news', [], 'id', 'DESC', 5);
$recentSlides = DB::getList('slides', [], 'id', 'DESC', 3);
?>
<div class="admin-breadcrumbs">
    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php">首页</a>
    <span class="sep">/</span>
    <span class="current">仪表盘</span>
</div>

<div class="admin-page-header">
    <div>
        <h2 class="admin-page-title">欢迎回来，<?php echo h($adminUser['username']); ?>！</h2>
        <div class="admin-page-subtitle">这是您的管理后台概览</div>
    </div>
    <div class="admin-quick-actions">
        <a href="<?php echo SITE_URL; ?>/admin/pages/slides.php?action=add" class="btn btn-primary btn-sm">+ 新幻灯片</a>
        <a href="<?php echo SITE_URL; ?>/admin/pages/services.php?action=add" class="btn btn-outline btn-sm">+ 新服务</a>
        <a href="<?php echo SITE_URL; ?>/admin/pages/cases.php?action=add" class="btn btn-outline btn-sm">+ 新案例</a>
        <a href="<?php echo SITE_URL; ?>/admin/pages/news.php?action=add" class="btn btn-outline btn-sm">+ 新闻</a>
    </div>
</div>

<div class="admin-stats">
    <div class="admin-stat-card">
        <div class="admin-stat-icon blue">📁</div>
        <div class="admin-stat-info">
            <div class="admin-stat-value"><?php echo $caseCount; ?></div>
            <div class="admin-stat-label">案例总数</div>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon green">📰</div>
        <div class="admin-stat-info">
            <div class="admin-stat-value"><?php echo $newsCount; ?></div>
            <div class="admin-stat-label">新闻总数</div>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon orange">🛠️</div>
        <div class="admin-stat-info">
            <div class="admin-stat-value"><?php echo $serviceCount; ?></div>
            <div class="admin-stat-label">服务项目</div>
        </div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-icon purple">🖼️</div>
        <div class="admin-stat-info">
            <div class="admin-stat-value"><?php echo $slideCount; ?></div>
            <div class="admin-stat-label">幻灯片</div>
        </div>
    </div>
</div>

<div class="admin-dashboard-grid">
    <div>
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>最新案例</h3>
                <a href="<?php echo SITE_URL; ?>/admin/pages/cases.php" class="admin-action-btn primary" title="查看全部">→</a>
            </div>
            <div class="admin-card-body">
                <?php if (count($recentCases) > 0): ?>
                <ul class="admin-recent-list">
                    <?php foreach ($recentCases as $item): ?>
                    <li class="admin-recent-item">
                        <div class="admin-recent-icon blue">📁</div>
                        <div class="admin-recent-info">
                            <div class="admin-recent-title"><?php echo h($item['title']); ?></div>
                            <div class="admin-recent-meta"><?php echo h($item['category']); ?> · <?php echo h($item['date']); ?></div>
                        </div>
                        <a href="<?php echo SITE_URL; ?>/admin/pages/cases.php?action=edit&id=<?php echo $item['id']; ?>" class="admin-action-btn primary">✎</a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="admin-table-empty">
                    <div class="admin-table-empty-icon">📁</div>
                    暂无案例
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h3>最新新闻</h3>
                <a href="<?php echo SITE_URL; ?>/admin/pages/news.php" class="admin-action-btn primary" title="查看全部">→</a>
            </div>
            <div class="admin-card-body">
                <?php if (count($recentNews) > 0): ?>
                <ul class="admin-recent-list">
                    <?php foreach ($recentNews as $item): ?>
                    <li class="admin-recent-item">
                        <div class="admin-recent-icon green">📰</div>
                        <div class="admin-recent-info">
                            <div class="admin-recent-title"><?php echo h($item['title']); ?></div>
                            <div class="admin-recent-meta"><?php echo h($item['category']); ?> · <?php echo h($item['date']); ?></div>
                        </div>
                        <a href="<?php echo SITE_URL; ?>/admin/pages/news.php?action=edit&id=<?php echo $item['id']; ?>" class="admin-action-btn primary">✎</a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="admin-table-empty">
                    <div class="admin-table-empty-icon">📰</div>
                    暂无新闻
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div>
        <div class="admin-card">
            <div class="admin-card-header">
                <h3>快捷操作</h3>
            </div>
            <div class="admin-card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                    <a href="<?php echo SITE_URL; ?>/admin/pages/slides.php" class="btn btn-primary btn-block" style="padding:0.75rem;font-size:0.85rem;">🖼️ 幻灯片</a>
                    <a href="<?php echo SITE_URL; ?>/admin/pages/services.php" class="btn btn-outline btn-block" style="padding:0.75rem;font-size:0.85rem;">🛠️ 服务</a>
                    <a href="<?php echo SITE_URL; ?>/admin/pages/cases.php" class="btn btn-outline btn-block" style="padding:0.75rem;font-size:0.85rem;">📁 案例</a>
                    <a href="<?php echo SITE_URL; ?>/admin/pages/news.php" class="btn btn-outline btn-block" style="padding:0.75rem;font-size:0.85rem;">📰 新闻</a>
                    <a href="<?php echo SITE_URL; ?>/admin/settings.php" class="btn btn-outline btn-block" style="padding:0.75rem;font-size:0.85rem;">⚙️ 设置</a>
                    <a href="<?php echo siteUrl(); ?>" target="_blank" class="btn btn-outline btn-block" style="padding:0.75rem;font-size:0.85rem;">🌐 预览</a>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h3>最新幻灯片</h3>
            </div>
            <div class="admin-card-body">
                <?php if (count($recentSlides) > 0): ?>
                <ul class="admin-recent-list">
                    <?php foreach ($recentSlides as $item): ?>
                    <li class="admin-recent-item">
                        <div class="admin-recent-icon purple">🖼️</div>
                        <div class="admin-recent-info">
                            <div class="admin-recent-title"><?php echo h($item['title']); ?></div>
                            <div class="admin-recent-meta">排序: <?php echo h($item['sort']); ?></div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="admin-table-empty">
                    <div class="admin-table-empty-icon">🖼️</div>
                    暂无幻灯片
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>