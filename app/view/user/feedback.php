<div class="user-breadcrumb">
    <span>用户中心</span> / <span>意见反馈</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">意见反馈</h1>

<div class="card" style="max-width: 600px;">
    <div class="settings-section">
        <h3 class="settings-section-title" style="display: flex; align-items: center; gap: 8px; font-size: 16px; color: #1a1a2e; margin-bottom: 20px;">
            <svg width="18" height="18"><use href="#i-feedback"/></svg>提交反馈
        </h3>
        <form method="POST" action="/user/submitFeedback" data-ajax="true" id="feedbackForm">
            <div class="form-group">
                <label class="form-label" for="type">反馈类型</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="">请选择反馈类型</option>
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
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-feedback"/></svg>提交反馈
            </button>
        </form>
    </div>
</div>

