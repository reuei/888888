<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 24px;">系统设置</h1>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-settings"/></svg>站点配置
        </h3>
    </div>
    <form method="POST" action="/admin/saveSettings" data-ajax="true" style="max-width: 600px;">
        <div class="form-group">
            <label class="form-label" for="site_name">网站名称</label>
            <input type="text" class="form-control" id="site_name" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" placeholder="请输入网站名称" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="site_desc">网站描述</label>
            <input type="text" class="form-control" id="site_desc" name="site_desc" value="<?= htmlspecialchars($settings['site_desc'] ?? '') ?>" placeholder="请输入网站描述">
        </div>
        <div class="form-group">
            <label class="form-label" for="contact_email">联系邮箱</label>
            <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>" placeholder="请输入联系邮箱">
        </div>
        <div class="form-group">
            <label class="form-label" for="qq">客服QQ</label>
            <input type="text" class="form-control" id="qq" name="qq" value="<?= htmlspecialchars($settings['qq'] ?? '') ?>" placeholder="请输入客服QQ">
        </div>
        <div class="form-group">
            <label class="form-label" for="icp">备案号</label>
            <input type="text" class="form-control" id="icp" name="icp" value="<?= htmlspecialchars($settings['icp'] ?? '') ?>" placeholder="请输入ICP备案号">
            <div class="help-text" style="font-size: 12px; color: #687690; margin-top: 4px;">显示在页面底部</div>
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-check"/></svg>保存设置
        </button>
    </form>
</div>