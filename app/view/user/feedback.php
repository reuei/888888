<div class="user-breadcrumb">
    <span>用户中心</span> / <span>意见反馈</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">意见反馈</h1>

<div class="card" style="max-width: 600px; margin-bottom: 24px;">
    <div class="settings-section">
        <h3 class="settings-section-title" style="display: flex; align-items: center; gap: 8px; font-size: 16px; color: #1a1a2e; margin-bottom: 20px;">
            <svg width="18" height="18"><use href="#i-feedback"/></svg>提交反馈
        </h3>
        <form method="POST" action="/user/submitFeedback" data-ajax="true" id="feedbackForm">
            <div class="form-group">
                <label class="form-label" for="type">反馈类型</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="">请选择反馈类型</option>
                    <option value="feedback">意见反馈</option>
                    <option value="report">举报</option>
                    <option value="bug">Bug 反馈</option>
                    <option value="feature">功能建议</option>
                    <option value="question">使用问题</option>
                    <option value="other">其他</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="content">反馈内容</label>
                <textarea class="form-control textarea" id="content" name="content" rows="6" placeholder="请详细描述您的反馈内容..." required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="contact">联系方式</label>
                <input type="text" class="form-control" id="contact" name="contact" placeholder="QQ/邮箱/手机号（选填）">
            </div>
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-send"/></svg>提交反馈
            </button>
        </form>
    </div>
</div>

<?php if (!empty($feedbacks)): ?>
<div class="card" style="max-width: 600px;">
    <h3 class="settings-section-title" style="display: flex; align-items: center; gap: 8px; font-size: 16px; color: #1a1a2e; margin-bottom: 20px;">我的反馈记录</h3>
    <?php foreach ($feedbacks as $fb): ?>
    <div style="border: 1px solid var(--border); padding: 16px; margin-bottom: 12px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span class="badge <?= $fb['type'] === 'report' ? 'badge-danger' : 'badge-info' ?>"><?= htmlspecialchars($fb['type'] ?? 'feedback') ?></span>
            <span class="badge <?= ($fb['status'] ?? 'pending') === 'approved' ? 'badge-success' : (($fb['status'] ?? 'pending') === 'rejected' ? 'badge-danger' : 'badge-warning') ?>">
                <?= ($fb['status'] ?? 'pending') === 'approved' ? '已处理' : (($fb['status'] ?? 'pending') === 'rejected' ? '已驳回' : (($fb['status'] ?? 'pending') === 'processing' ? '处理中' : '待处理')) ?>
            </span>
        </div>
        <p style="color: var(--text); font-size: 14px; margin-bottom: 8px;"><?= htmlspecialchars($fb['content'] ?? '') ?></p>
        <?php if (!empty($fb['reply'])): ?>
        <div style="background: var(--primary-light); padding: 10px 14px; font-size: 13px; color: var(--text-secondary);">
            <strong>管理员回复：</strong><?= htmlspecialchars($fb['reply']) ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($fb['reject_reason'])): ?>
        <div style="background: #fff2f0; padding: 10px 14px; font-size: 13px; color: #ff4d4f;">
            <strong>驳回原因：</strong><?= htmlspecialchars($fb['reject_reason']) ?>
        </div>
        <?php endif; ?>
        <div style="font-size: 12px; color: var(--text-muted); margin-top: 8px;"><?= htmlspecialchars($fb['created_at'] ?? '') ?></div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>