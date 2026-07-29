<div class="user-breadcrumb">
    <span>用户中心</span> / <span>账户设置</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">账户设置</h1>

<div class="card" style="max-width: 600px;">
    <div class="settings-section">
        <h3 class="settings-section-title" style="display: flex; align-items: center; gap: 8px; font-size: 16px; color: #1a1a2e; margin-bottom: 20px;">
            <svg width="18" height="18"><use href="#i-user"/></svg>个人资料
        </h3>
        <form method="POST" action="/user/updateSettings" data-ajax="true" id="profileForm">
            <div class="form-group">
                <label class="form-label">用户名</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" disabled>
                <div class="form-help" style="font-size: 12px; color: #999; margin-top: 4px;">用户名不可修改</div>
            </div>
            <div class="form-group">
                <label class="form-label" for="email">邮箱</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="qq">QQ号</label>
                <input type="text" class="form-control" id="qq" name="qq" value="<?= htmlspecialchars($user['qq'] ?? '') ?>" placeholder="选填">
            </div>
            <div class="form-group">
                <label class="form-label" for="phone">手机号</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="选填">
            </div>
            <div style="display: flex; gap: 12px; align-items: center;">
                <button type="submit" class="btn btn-primary">保存资料</button>
                <a href="/user/rebind" class="btn btn-outline btn-sm" style="font-size:12px;">换绑邮箱/手机号</a>
            </div>
        </form>
    </div>

    <hr style="margin: 28px 0; border: none; border-top: 1px solid #f0f0f0;">

    <div class="settings-section">
        <h3 class="settings-section-title" style="display: flex; align-items: center; gap: 8px; font-size: 16px; color: #1a1a2e; margin-bottom: 20px;">
            <svg width="18" height="18"><use href="#i-key"/></svg>修改密码
        </h3>
        <form method="POST" action="/user/updatePassword" data-ajax="true" id="passwordForm">
            <div class="form-group">
                <label class="form-label" for="old_password">原密码</label>
                <input type="password" class="form-control" id="old_password" name="old_password" placeholder="请输入原密码" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="new_password">新密码</label>
                <input type="password" class="form-control" id="new_password" name="password" placeholder="至少6位新密码" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary">修改密码</button>
        </form>
    </div>
</div>