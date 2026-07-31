<div class="filter-bar">
    <div class="filter-tabs">
        <a href="/user/messages" class="filter-tab<?= !isset($filter) || $filter === 'all' ? ' active' : '' ?>">全部</a>
        <a href="/user/messages?filter=unread" class="filter-tab<?= ($filter ?? '') === 'unread' ? ' active' : '' ?>">未读</a>
        <a href="/user/messages?action=read-all" class="filter-tab" style="margin-left: auto;">
            <svg width="14" height="14" style="vertical-align: middle; margin-right: 4px;"><use href="#i-check"/></svg>
            全部已读
        </a>
    </div>
</div>

<div class="card">
    <?php if (!empty($messages)): ?>
    <div class="message-list">
        <?php foreach ($messages as $msg): ?>
        <div class="message-item" onclick="toggleMessage(this, <?= $msg['id'] ?? 0 ?>)">
            <div class="message-item-row">
                <div class="message-dot <?= ($msg['is_read'] ?? 0) == 0 ? '' : 'read' ?>"></div>
                <div style="width: 36px; height: 36px; border-radius: 10px; background: <?= ($msg['is_read'] ?? 0) == 0 ? 'var(--primary-50)' : 'var(--bg-tertiary)' ?>; color: <?= ($msg['is_read'] ?? 0) == 0 ? 'var(--primary)' : 'var(--text-muted)' ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="18" height="18"><use href="#i-bell"/></svg>
                </div>
                <div class="message-content">
                    <div class="message-title" style="<?= ($msg['is_read'] ?? 0) == 0 ? 'font-weight: 600;' : '' ?>">
                        <?= htmlspecialchars($msg['title'] ?? '') ?>
                    </div>
                    <div class="message-preview">
                        <?= htmlspecialchars(mb_substr(strip_tags($msg['content'] ?? ''), 0, 100)) ?><?= mb_strlen(strip_tags($msg['content'] ?? '')) > 100 ? '...' : '' ?>
                    </div>
                </div>
                <div class="message-time">
                    <svg width="14" height="14" style="vertical-align: middle; margin-right: 4px;"><use href="#i-clock"/></svg>
                    <?= htmlspecialchars($msg['created_at'] ?? '') ?>
                </div>
            </div>
            <div class="message-full-content" style="display: none;">
                <?= nl2br(htmlspecialchars($msg['content'] ?? '')) ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (($totalPages ?? 1) > 1): ?>
    <div class="pagination" style="display: flex; justify-content: center; gap: 8px; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-light);">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" style="padding: 8px 14px; border: 1px solid var(--border); border-radius: var(--radius); text-decoration: none; color: var(--text); font-size: 13px; transition: all 0.15s; <?= ($page ?? 1) == $i ? 'background: var(--primary); color: #fff; border-color: var(--primary);' : 'background: var(--bg-card);' ?>">
            <?= $i ?>
        </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg width="32" height="32"><use href="#i-message"/></svg>
        </div>
        <div class="empty-state-text">暂无消息</div>
    </div>
    <?php endif; ?>
</div>

<script>
    function toggleMessage(el, msgId) {
        var full = el.querySelector('.message-full-content');
        var preview = el.querySelector('.message-preview');
        if (full && preview) {
            if (full.style.display === 'none' || full.style.display === '') {
                full.style.display = 'block';
                preview.style.display = 'none';
                fetch('/user/readMessage?id=' + msgId, { method: 'POST' });
                var dot = el.querySelector('.message-dot');
                if (dot) dot.classList.add('read');
                var title = el.querySelector('.message-title');
                if (title) title.style.fontWeight = '400';
            } else {
                full.style.display = 'none';
                preview.style.display = '';
            }
        }
    }
</script>
