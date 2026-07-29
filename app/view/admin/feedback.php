<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 24px;">反馈管理</h1>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-feedback"/></svg>反馈列表
        </h3>
    </div>
    <?php if (!empty($feedbacks)): ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>用户</th>
                    <th>类型</th>
                    <th>内容</th>
                    <th>状态</th>
                    <th>时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $fb): ?>
                <tr>
                    <td><?= $fb['id'] ?? '' ?></td>
                    <td><?= htmlspecialchars($fb['username'] ?? $fb['user_id'] ?? '') ?></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($fb['type'] ?? '—') ?></span></td>
                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($fb['content'] ?? '') ?></td>
                    <td>
                        <?php $status = $fb['status'] ?? 0; ?>
                        <span class="badge <?= $status === 1 ? 'badge-success' : ($status === -1 ? 'badge-danger' : 'badge-warning') ?>">
                            <?= $status === 1 ? '已处理' : ($status === -1 ? '已驳回' : '待处理') ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($fb['created_at'] ?? '') ?></td>
                    <td>
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="openReplyModal(<?= $fb['id'] ?? 0 ?>, '<?= htmlspecialchars($fb['username'] ?? '', ENT_QUOTES) ?>')">
                            <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-reply"/></svg> 回复
                        </a>
                        <?php if (($fb['status'] ?? 0) === 0): ?>
                        <form method="POST" action="/admin/resolveFeedback" data-ajax="true" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $fb['id'] ?? 0 ?>">
                            <button type="submit" class="btn btn-sm" style="background: #52c41a; color: #fff;">
                                <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-check"/></svg> 已处理
                            </button>
                        </form>
                        <form method="POST" action="/admin/rejectFeedback" data-ajax="true" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $fb['id'] ?? 0 ?>">
                            <button type="submit" class="btn btn-sm" style="background: #ff4d4f; color: #fff;">
                                <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-close"/></svg> 驳回
                            </button>
                        </form>
                        <?php else: ?>
                        <span style="color: #687690; font-size: 13px;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">暂无反馈数据</div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Reply Modal -->
<div class="announcement-modal" id="replyModal">
    <div class="am-overlay" onclick="closeReplyModal()"></div>
    <div class="am-dialog" style="max-width: 500px;">
        <div class="am-header">
            <h3 id="replyModalTitle">回复反馈</h3>
            <button class="am-close" onclick="closeReplyModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="am-body">
            <form method="POST" action="/admin/replyFeedback" data-ajax="true" id="replyForm">
                <input type="hidden" name="id" id="reply_id">
                <div class="form-group">
                    <label class="form-label" for="reply_content">回复内容</label>
                    <textarea class="form-control textarea" id="reply_content" name="reply" rows="5" placeholder="请输入回复内容" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-send"/></svg>发送回复
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openReplyModal(id, username) {
    document.getElementById('reply_id').value = id;
    document.getElementById('reply_content').value = '';
    document.getElementById('replyModalTitle').textContent = '回复 ' + username + ' 的反馈';
    document.getElementById('replyModal').classList.add('show');
}
function closeReplyModal() {
    document.getElementById('replyModal').classList.remove('show');
}
</script>