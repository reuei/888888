<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="18" height="18" style="margin-right: 6px; color: var(--primary);"><use href="#i-feedback"/></svg>
            提交反馈
        </h3>
    </div>
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
            <textarea class="form-control" id="content" name="content" rows="6" placeholder="请详细描述您的反馈内容，包括遇到的问题、建议或期望的改进..." required></textarea>
            <div class="form-help">请尽可能详细地描述，以便我们更好地为您解决问题。</div>
        </div>
        <div class="form-group">
            <label class="form-label" for="contact">联系方式 <span style="font-weight: 400; color: var(--text-muted);">（选填）</span></label>
            <input type="text" class="form-control" id="contact" name="contact" placeholder="QQ / 邮箱 / 手机号，方便我们与您联系">
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" style="margin-right: 4px;"><use href="#i-send"/></svg>
            提交反馈
        </button>
    </form>
</div>

<?php if (!empty($feedbacks)): ?>
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="18" height="18" style="margin-right: 6px; color: var(--primary);"><use href="#i-clock"/></svg>
            我的反馈记录
            <span style="font-weight: 400; font-size: 13px; color: var(--text-muted); margin-left: 8px;">共 <?= count($feedbacks) ?> 条</span>
        </h3>
    </div>
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <?php foreach ($feedbacks as $fb): ?>
        <?php
            $typeLabel = '其他';
            $typeBadge = 'badge-info';
            switch ($fb['type'] ?? '') {
                case 'feedback': $typeLabel = '意见反馈'; $typeBadge = 'badge-info'; break;
                case 'report':   $typeLabel = '举报';     $typeBadge = 'badge-danger'; break;
                case 'bug':      $typeLabel = 'Bug 反馈'; $typeBadge = 'badge-warning'; break;
                case 'feature':  $typeLabel = '功能建议'; $typeBadge = 'badge-success'; break;
                case 'question': $typeLabel = '使用问题'; $typeBadge = 'badge-info'; break;
            }
            $statusLabel = '待处理';
            $statusBadge = 'badge-warning';
            switch ($fb['status'] ?? 'pending') {
                case 'approved':  $statusLabel = '已处理'; $statusBadge = 'badge-success'; break;
                case 'rejected':  $statusLabel = '已驳回'; $statusBadge = 'badge-danger'; break;
                case 'processing':$statusLabel = '处理中'; $statusBadge = 'badge-info'; break;
            }
        ?>
        <div class="feedback-item">
            <div class="feedback-header">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="badge <?= $typeBadge ?>"><?= $typeLabel ?></span>
                    <span class="badge <?= $statusBadge ?>"><?= $statusLabel ?></span>
                </div>
                <div style="font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
                    <svg width="12" height="12"><use href="#i-clock"/></svg>
                    <?= htmlspecialchars($fb['created_at'] ?? '') ?>
                </div>
            </div>
            <div class="feedback-content"><?= htmlspecialchars($fb['content'] ?? '') ?></div>
            <?php if (!empty($fb['reply'])): ?>
            <div class="feedback-reply">
                <div class="feedback-reply-title">管理员回复</div>
                <div class="feedback-reply-content"><?= htmlspecialchars($fb['reply']) ?></div>
            </div>
            <?php endif; ?>
            <?php if (!empty($fb['reject_reason'])): ?>
            <div style="background: var(--danger-light); border-left: 3px solid var(--danger); padding: 12px 16px; border-radius: 0 var(--radius) var(--radius) 0;">
                <div style="font-size: 12px; font-weight: 600; color: var(--danger); margin-bottom: 4px;">驳回原因</div>
                <div style="font-size: 13px; color: var(--danger); line-height: 1.6; white-space: pre-wrap;"><?= htmlspecialchars($fb['reject_reason']) ?></div>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
