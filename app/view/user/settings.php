<div class="card" style="max-width: 700px;">
    <div class="form-section">
        <div class="form-section-title">
            <svg width="18" height="18"><use href="#i-user"/></svg>
            个人资料
        </div>
        <form method="POST" action="/user/updateSettings" data-ajax="true" id="profileForm">
            <div class="two-col">
                <div class="form-group">
                    <label class="form-label">用户名</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled>
                    <div class="form-help">用户名不可修改</div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">邮箱</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                </div>
            </div>
            <div class="two-col">
                <div class="form-group">
                    <label class="form-label" for="qq">QQ号</label>
                    <input type="text" class="form-control" id="qq" name="qq" value="<?= htmlspecialchars($user['qq'] ?? '') ?>" placeholder="选填">
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">手机号</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="选填">
                </div>
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <button type="submit" class="btn btn-primary">
                    <svg width="14" height="14" style="margin-right: 4px;"><use href="#i-check"/></svg>
                    保存资料
                </button>
                <a href="/user/rebind" class="btn btn-outline">
                    <svg width="14" height="14" style="margin-right: 4px;"><use href="#i-key"/></svg>
                    换绑邮箱/手机号
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card" style="max-width: 700px; margin-top: 24px;">
    <div class="form-section" style="margin-bottom: 0;">
        <div class="form-section-title">
            <svg width="18" height="18"><use href="#i-lock"/></svg>
            修改密码
        </div>
        <form method="POST" action="/user/updatePassword" data-ajax="true" id="passwordForm">
            <div class="form-group">
                <label class="form-label" for="old_password">原密码</label>
                <input type="password" class="form-control" id="old_password" name="old_password" placeholder="请输入原密码" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="new_password">新密码</label>
                <input type="password" class="form-control" id="new_password" name="password" placeholder="至少6位新密码" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary">
                <svg width="14" height="14" style="margin-right: 4px;"><use href="#i-check"/></svg>
                修改密码
            </button>
        </form>
    </div>
</div>

<div class="card" style="max-width: 700px; margin-top: 24px; border-color: var(--danger-light);">
    <div class="form-section" style="margin-bottom: 0;">
        <div class="form-section-title" style="color: var(--danger);">
            <svg width="18" height="18"><use href="#i-warn"/></svg>
            危险操作
        </div>
        <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px;">
            注销账户将永久删除您的所有数据，包括订单、授权、消息等。请谨慎操作。
        </p>
        <button type="button" class="btn btn-danger" onclick="confirmDeleteAccount()">
            <svg width="14" height="14" style="margin-right: 4px;"><use href="#i-trash"/></svg>
            注销账户
        </button>
    </div>
</div>

<script>
    function confirmDeleteAccount() {
        if (confirm('确定要注销账户吗？此操作不可撤销！')) {
            if (confirm('再次确认：账户注销后将无法恢复，所有数据将被永久删除！')) {
                window.location.href = '/user/deleteAccount';
            }
        }
    }
</script>
