<div class="card" style="max-width: 600px;">
    <div class="form-section">
        <div class="form-section-title">
            <svg width="18" height="18"><use href="#i-mail"/></svg>
            换绑邮箱
        </div>
        <div style="padding: 12px; background: var(--bg-tertiary); border-radius: var(--radius); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" style="color: var(--text-muted);"><use href="#i-info"/></svg>
            <span style="font-size: 13px; color: var(--text-secondary);">当前绑定邮箱：<strong style="color: var(--text);"><?= htmlspecialchars($user['email'] ?? '未绑定') ?></strong></span>
        </div>
        <form method="POST" action="/user/rebindEmail" data-ajax="true" id="rebindEmailForm">
            <div class="form-group">
                <label class="form-label" for="new_email">新邮箱</label>
                <input type="email" class="form-control" id="new_email" name="new_email" placeholder="请输入新邮箱地址" required>
            </div>
            <?php if (!empty($emailConfigured)): ?>
            <div class="form-group">
                <label class="form-label" for="email_code">验证码</label>
                <div style="display: flex; gap: 12px;">
                    <input type="text" class="form-control" id="email_code" name="code" placeholder="请输入邮箱验证码" required style="flex: 1;">
                    <button type="button" class="btn btn-outline btn-sm" id="sendEmailCode" style="flex-shrink: 0; white-space: nowrap;" onclick="sendEmailCode()">发送验证码</button>
                </div>
            </div>
            <?php else: ?>
            <div class="form-group">
                <label class="form-label">验证码</label>
                <input type="text" class="form-control" disabled placeholder="暂不支持验证码发送" style="background: var(--bg-tertiary); color: var(--text-muted);">
                <div class="form-help">管理员未配置邮件发送服务，暂不支持验证码发送</div>
            </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">
                <svg width="14" height="14" style="margin-right: 4px;"><use href="#i-check"/></svg>
                提交换绑
            </button>
        </form>
    </div>
</div>

<div class="card" style="max-width: 600px; margin-top: 24px;">
    <div class="form-section" style="margin-bottom: 0;">
        <div class="form-section-title">
            <svg width="18" height="18"><use href="#i-phone"/></svg>
            换绑手机号
        </div>
        <div style="padding: 12px; background: var(--bg-tertiary); border-radius: var(--radius); margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" style="color: var(--text-muted);"><use href="#i-info"/></svg>
            <span style="font-size: 13px; color: var(--text-secondary);">当前绑定手机：<strong style="color: var(--text);"><?= htmlspecialchars($user['phone'] ?? '未绑定') ?></strong></span>
        </div>
        <form method="POST" action="/user/rebindPhone" data-ajax="true" id="rebindPhoneForm">
            <div class="form-group">
                <label class="form-label" for="new_phone">新手机号</label>
                <input type="text" class="form-control" id="new_phone" name="new_phone" placeholder="请输入新手机号" required>
            </div>
            <?php if (!empty($smsConfigured)): ?>
            <div class="form-group">
                <label class="form-label" for="phone_code">验证码</label>
                <div style="display: flex; gap: 12px;">
                    <input type="text" class="form-control" id="phone_code" name="code" placeholder="请输入短信验证码" required style="flex: 1;">
                    <button type="button" class="btn btn-outline btn-sm" id="sendPhoneCode" style="flex-shrink: 0; white-space: nowrap;" onclick="sendPhoneCode()">发送验证码</button>
                </div>
            </div>
            <?php else: ?>
            <div class="form-group">
                <label class="form-label">验证码</label>
                <input type="text" class="form-control" disabled placeholder="暂不支持验证码发送" style="background: var(--bg-tertiary); color: var(--text-muted);">
                <div class="form-help">管理员未配置短信发送服务，暂不支持验证码发送</div>
            </div>
            <?php endif; ?>
            <button type="submit" class="btn btn-primary">
                <svg width="14" height="14" style="margin-right: 4px;"><use href="#i-check"/></svg>
                提交换绑
            </button>
        </form>
    </div>
</div>

<script>
    var emailCooldown = 0;
    var phoneCooldown = 0;

    function sendEmailCode() {
        if (emailCooldown > 0) return;
        var btn = document.getElementById('sendEmailCode');
        btn.disabled = true;
        emailCooldown = 60;
        var timer = setInterval(function() {
            emailCooldown--;
            btn.textContent = emailCooldown + 's 后重试';
            if (emailCooldown <= 0) {
                clearInterval(timer);
                btn.textContent = '发送验证码';
                btn.disabled = false;
            }
        }, 1000);
        fetch('/user/sendEmailCode', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: document.getElementById('new_email').value })
        }).then(function(res) { return res.json(); })
          .then(function(data) { if (data.message) alert(data.message); })
          .catch(function() {});
    }

    function sendPhoneCode() {
        if (phoneCooldown > 0) return;
        var btn = document.getElementById('sendPhoneCode');
        btn.disabled = true;
        phoneCooldown = 60;
        var timer = setInterval(function() {
            phoneCooldown--;
            btn.textContent = phoneCooldown + 's 后重试';
            if (phoneCooldown <= 0) {
                clearInterval(timer);
                btn.textContent = '发送验证码';
                btn.disabled = false;
            }
        }, 1000);
        fetch('/user/sendPhoneCode', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ phone: document.getElementById('new_phone').value })
        }).then(function(res) { return res.json(); })
          .then(function(data) { if (data.message) alert(data.message); })
          .catch(function() {});
    }
</script>
