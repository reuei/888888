<div class="page-header">
    <div>
        <h1 class="page-title">系统设置</h1>
        <div class="page-subtitle">配置站点基本信息、SMTP、支付通道等参数。</div>
    </div>
</div>

<div class="admin-settings-layout">
    <div class="admin-settings-nav">
        <a href="#tab-basic" class="settings-tab active" onclick="switchTab('basic', this)">
            <svg><use href="#i-settings"/></svg>
            <span>基本设置</span>
        </a>
        <a href="/admin/emailPool" class="settings-tab">
            <svg><use href="#i-key"/></svg>
            <span>邮箱池配置</span>
        </a>
        <a href="/admin/emailTemplates" class="settings-tab">
            <svg><use href="#i-message"/></svg>
            <span>邮件模板</span>
        </a>
        <a href="/admin/paymentChannels" class="settings-tab">
            <svg><use href="#i-orders"/></svg>
            <span>支付通道</span>
        </a>
        <a href="/admin/uploadFiles" class="settings-tab">
            <svg><use href="#i-box"/></svg>
            <span>文件管理</span>
        </a>
    </div>

    <div class="admin-settings-content">
        <div class="admin-settings-panel active" id="panel-basic">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">基本信息</h3>
                </div>
                <form method="POST" action="/admin/saveBasicSettings" data-ajax="true" class="admin-form-vertical">
                    <div class="form-group">
                        <label class="form-label">站点名称</label>
                        <input type="text" class="form-control" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">站点描述</label>
                        <textarea class="form-control" name="site_description" rows="3"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">站点域名</label>
                        <input type="url" class="form-control" name="site_url" value="<?= htmlspecialchars($settings['site_url'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">LOGO URL</label>
                        <input type="text" class="form-control" name="site_logo" value="<?= htmlspecialchars($settings['site_logo'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">ICP备案号</label>
                        <input type="text" class="form-control" name="site_icp" value="<?= htmlspecialchars($settings['site_icp'] ?? '') ?>">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:140px;">保存设置</button>
                </form>
            </div>

            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">SMTP 邮件配置</h3>
                </div>
                <form method="POST" action="/admin/saveSmtpSettings" data-ajax="true" class="admin-form-vertical">
                    <div class="admin-form-grid">
                        <div class="form-group">
                            <label class="form-label">SMTP 服务器</label>
                            <input type="text" class="form-control" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">SMTP 端口</label>
                            <input type="number" class="form-control" name="smtp_port" value="<?= $settings['smtp_port'] ?? 465 ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">SMTP 用户名</label>
                            <input type="text" class="form-control" name="smtp_username" value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">SMTP 密码</label>
                            <input type="password" class="form-control" name="smtp_password" value="<?= htmlspecialchars($settings['smtp_password'] ?? '') ?>">
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">发件人邮箱</label>
                            <input type="email" class="form-control" name="smtp_from" value="<?= htmlspecialchars($settings['smtp_from'] ?? '') ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:140px;margin-top:16px;">保存 SMTP</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab, el){
    document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.admin-settings-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + tab).classList.add('active');
}
</script>