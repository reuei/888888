<section class="section">
    <div class="article-detail">
        <h1 class="article-title"><?= h($notice['title'] ?? '公告') ?></h1>
        <div class="article-meta">
            <span>发布于：<?= format_time($notice['create_time'] ?? null) ?></span>
        </div>
        <div class="article-content">
            <?= nl2br(h($notice['content'] ?? '')) ?>
        </div>
        <a href="/notice" class="btn btn-line">返回列表</a>
    </div>
</section>
