<section class="section">
    <div class="section-head">
        <h2 class="section-title">站点公告</h2>
        <p class="section-sub">了解平台最新动态</p>
    </div>
    <div class="notice-list">
        <?php foreach ($notices as $n): ?>
        <a href="/notice/<?= (int) $n['id'] ?>" class="notice-item">
            <span class="notice-dot"></span>
            <span class="notice-title"><?= h($n['title']) ?></span>
            <span class="notice-time"><?= format_time($n['create_time'] ?? null, 'Y-m-d') ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</section>
