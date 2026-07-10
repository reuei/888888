<div class="page-head">
    <h1 class="page-title">消息中心</h1>
</div>
<div class="panel">
    <?php if (empty($messages)): ?>
        <div class="empty-state">
            <div class="empty-icon"></div>
            <p>暂无消息</p>
        </div>
    <?php else: ?>
    <div class="msg-list">
        <?php foreach ($messages as $m): ?>
        <a href="/user/messages/read/<?= (int) $m['id'] ?>" class="msg-item <?= empty($m['is_read']) ? 'unread' : '' ?>">
            <span class="msg-type tag-<?= h($m['type'] ?? 'system') ?>"><?= h($m['type'] ?? '系统') ?></span>
            <span class="msg-title"><?= h($m['title']) ?></span>
            <span class="msg-time"><?= format_time($m['create_time'] ?? null) ?></span>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
