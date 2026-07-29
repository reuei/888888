<div class="user-breadcrumb">
    <span>用户中心</span> / <span>消息中心</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">消息中心</h1>

<div class="card">
    <div class="card-header" style="display: flex; align-items: center; justify-content: space-between;">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-message"/></svg>消息列表
        </h3>
        <?php if (!empty($messages)): ?>
        <a href="/user/messages?action=read-all" class="btn btn-primary btn-sm">全部已读</a>
        <?php endif; ?>
    </div>
    <?php if (!empty($messages)): ?>
    <div style="display: flex; flex-direction: column; gap: 0;">
        <?php foreach ($messages as $msg): ?>
        <div class="message-item" style="border-bottom: 1px solid var(--border-light); padding: 16px 0; cursor: pointer; <?= ($msg['is_read'] ?? 0) == 0 ? 'background: var(--primary-light); padding-left: 16px; padding-right: 16px; margin-left: -16px; margin-right: -16px;' : '' ?>" onclick="toggleMessage(this, <?= $msg['id'] ?? 0 ?>)">
            <div style="display: flex; align-items: flex-start; justify-content: space-between;">
                <div style="flex: 1; min-width: 0;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <?php if (($msg['is_read'] ?? 0) == 0): ?>
                        <span style="display: inline-block; width: 8px; height: 8px; background: var(--primary); border-radius: 50%; flex-shrink: 0;"></span>
                        <?php endif; ?>
                        <span style="font-weight: 500; font-size: 14px; color: var(--text);"><?= htmlspecialchars($msg['title'] ?? '') ?></span>
                    </div>
                    <div class="message-preview" style="font-size: 13px; color: var(--text-secondary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 500px;">
                        <?= htmlspecialchars(mb_substr(strip_tags($msg['content'] ?? ''), 0, 80)) ?><?= mb_strlen(strip_tags($msg['content'] ?? '')) > 80 ? '...' : '' ?>
                    </div>
                </div>
                <span style="font-size: 12px; color: var(--text-muted); flex-shrink: 0; margin-left: 16px;"><?= htmlspecialchars($msg['created_at'] ?? '') ?></span>
            </div>
            <div class="message-full" style="display: none; margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border-light); font-size: 14px; color: var(--text); line-height: 1.8;">
                <?= nl2br(htmlspecialchars($msg['content'] ?? '')) ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">
        <svg width="48" height="48" style="color: #c0c8d8; margin-bottom: 12px; display: block; margin-left: auto; margin-right: auto;"><use href="#i-message"/></svg>
        暂无消息
    </div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<script>
    function toggleMessage(el, msgId) {
        var full = el.querySelector('.message-full');
        var preview = el.querySelector('.message-preview');
        if (full && preview) {
            if (full.style.display === 'none' || full.style.display === '') {
                full.style.display = 'block';
                preview.style.display = 'none';
            } else {
                full.style.display = 'none';
                preview.style.display = '';
            }
        }
    }
</script>