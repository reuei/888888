<div class="user-breadcrumb">
    <a href="/user/dashboard">用户中心</a>
    <span class="separator">/</span>
    <span class="current">意见反馈</span>
</div>

<div class="page-title">
    <svg width="24" height="24" style="vertical-align: middle; margin-right: 10px; color: var(--primary);"><use href="#i-feedback"/></svg>
    <span>意见反馈</span>
</div>

<!-- ==========================================================================
     FEEDBACK FORM
     ========================================================================== -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="16" height="16" style="vertical-align: middle; margin-right: 6px; color: var(--primary);"><use href="#i-edit"/></svg>
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

        <div style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" style="vertical-align: middle; margin-right: 6px;"><use href="#i-send"/></svg>
                提交反馈
            </button>
        </div>
    </form>
</div>

<!-- ==========================================================================
     FEEDBACK HISTORY
     ========================================================================== -->
<?php if (!empty($feedbacks)): ?>
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="16" height="16" style="vertical-align: middle; margin-right: 6px; color: var(--primary);"><use href="#i-clock"/></svg>
            我的反馈记录
            <span style="font-weight: 400; font-size: 13px; color: var(--text-muted); margin-left: 8px;">共 <?= count($feedbacks) ?> 条</span>
        </h3>
    </div>

    <?php foreach ($feedbacks as $fb): ?>
    <div class="feedback-item" style="border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; margin-bottom: 16px; transition: box-shadow var(--transition-fast);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
            <div style="display: flex; align-items: center; gap: 8px;">
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
                ?>
                <span class="badge <?= $typeBadge ?>"><?= $typeLabel ?></span>

                <?php
                $statusLabel = '待处理';
                $statusBadge = 'badge-warning';
                switch ($fb['status'] ?? 'pending') {
                    case 'approved':  $statusLabel = '已处理'; $statusBadge = 'badge-success'; break;
                    case 'rejected':  $statusLabel = '已驳回'; $statusBadge = 'badge-danger'; break;
                    case 'processing':$statusLabel = '处理中'; $statusBadge = 'badge-info'; break;
                }
                ?>
                <span class="badge <?= $statusBadge ?>"><?= $statusLabel ?></span>
            </div>

            <div style="font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
                <svg width="14" height="14"><use href="#i-clock"/></svg>
                <?= htmlspecialchars($fb['created_at'] ?? '') ?>
            </div>
        </div>

        <div style="color: var(--text); font-size: 14px; line-height: 1.7; margin-bottom: 12px; white-space: pre-wrap;">
            <?= htmlspecialchars($fb['content'] ?? '') ?>
        </div>

        <?php if (!empty($fb['reply'])): ?>
        <div style="background: var(--primary-light); border-left: 3px solid var(--primary); padding: 12px 16px; border-radius: 0 var(--radius) var(--radius) 0; margin-bottom: 8px;">
            <div style="font-size: 12px; font-weight: 600; color: var(--primary); margin-bottom: 4px;">管理员回复</div>
            <div style="font-size: 13px; color: var(--text-secondary); line-height: 1.6; white-space: pre-wrap;"><?= htmlspecialchars($fb['reply']) ?></div>
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
<?php endif; ?>