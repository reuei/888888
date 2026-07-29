<div class="user-breadcrumb">
    <span>用户中心</span> / <span>账户设置</span> / <span>换绑信息</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">换绑信息</h1>

<div class="card" style="max-width: 600px;">
    <!-- 换绑邮箱 -->
    <div class="settings-section">
        <h3 class="settings-section-title" style="display: flex; align-items: center; gap: 8px; font-size: 16px; color: #1a1a2e; margin-bottom: 20px;">
            <svg width="18" height="18"><use href="#i-mail"/></svg>换绑邮箱
        </h3>
        <form method="POST" action="/user/rebindEmail" data-ajax="true" id="rebindEmailForm">
            <div class="form-group">
                <label class="form-label">当前邮箱</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" disabled>
            </div>
            <div class="form-group">
                <label class="form-label" for="new_email">新邮箱</label>
                <input type="email" class="form-control" id="new_email" name="new_email" placeholder="请输入新邮箱地址" required>
            </div>
            <?php if (!empty($emailConfigured)): ?>
            <div class="form-group">
                <label class="form-label" for="email_code">验证码</label>
                <div style="display: flex; gap: 12px;">
                    <input type="text" class="form-control" id="email_code" name="code" placeholder="请输入邮箱验证码" required style="flex: 1;">
                    <button type="button" class="btn btn-outline btn-sm" id="sendEmailCode" style="flex-shrink: 0; white-space: nowrap;">发送验证码</button>
                </div>
            </div>
            <?php else: ?>
            <div class="form-group">
                <label class="form-label">验证码</label>
                <input type="text" class="form-control" disabled placeholder="暂不支持验证码发送" style="background: #f5f5f5; color: #999;">
                <div class="form-help" style="font-size: 12px; color: #999; margin-top: 4px;">管理员未配置邮件发送服务，暂不支持验证码发送</div>
            </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">提交换绑</button>
        </form>
    </div>

    <hr style="margin: 28px 0; border: none; border-top: 1px solid #f0f0f0;">

    <!-- 换绑手机号 -->
    <div class="settings-section">
        <h3 class="settings-section-title" style="display: flex; align-items: center; gap: 8px; font-size: 16px; color: #1a1a2e; margin-bottom: 20px;">
            <svg width="18" height="18"><use href="#i-phone"/></svg>换绑手机号
        </h3>
        <form method="POST" action="/user/rebindPhone" data-ajax="true" id="rebindPhoneForm">
            <div class="form-group">
                <label class="form-label">当前手机号</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '未绑定') ?>" disabled>
            </div>
            <div class="form-group">
                <label class="form-label" for="new_phone">新手机号</label>
                <input type="text" class="form-control" id="new_phone" name="new_phone" placeholder="请输入新手机号" required>
            </div>
            <?php if (!empty($smsConfigured)): ?>
            <div class="form-group">
                <label class="form-label" for="phone_code">验证码</label>
                <div style="display: flex; gap: 12px;">
                    <input type="text" class="form-control" id="phone_code" name="code" placeholder="请输入短信验证码" required style="flex: 1;">
                    <button type="button" class="btn btn-outline btn-sm" id="sendPhoneCode" style="flex-shrink: 0; white-space: nowrap;">发送验证码</button>
                </div>
            </div>
            <?php else: ?>
            <div class="form-group">
                <label class="form-label">验证码</label>
                <input type="text" class="form-control" disabled placeholder="暂不支持验证码发送" style="background: #f5f5f5; color: #999;">
                <div class="form-help" style="font-size: 12px; color: #999; margin-top: 4px;">管理员未配置短信发送服务，暂不支持验证码发送</div>
            </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">提交换绑</button>
        </form>
    </div>
</div>