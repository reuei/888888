<div class="page-head">
    <h1 class="page-title">个人资料</h1>
</div>
<div class="panel">
    <form id="profileForm" class="form">
        <div class="form-group">
            <label>用户名</label>
            <input type="text" value="<?= h($user['username']) ?>" disabled>
        </div>
        <div class="form-group">
            <label>昵称</label>
            <input type="text" name="nickname" value="<?= h($user['nickname'] ?? '') ?>" data-validate="nickname" data-min="2" data-max="20">
            <p class="form-hint">2-20个字符</p>
        </div>
        <div class="form-group">
            <label>邮箱</label>
            <input type="email" name="email" value="<?= h($user['email'] ?? '') ?>" data-validate="email">
        </div>
        <div class="form-group">
            <label>头像URL</label>
            <input type="text" name="avatar" value="<?= h($user['avatar'] ?? '') ?>" placeholder="或上传图片">
        </div>
        <button type="submit" class="btn btn-primary">保存</button>
    </form>
</div>

<div class="panel">
    <div class="panel-head"><h3 class="panel-title">修改密码</h3></div>
    <form id="passwordForm" class="form">
        <div class="form-group">
            <label>当前密码</label>
            <input type="password" name="old_password" required>
        </div>
        <div class="form-group">
            <label>新密码</label>
            <input type="password" name="new_password" data-validate="password" data-min="6" required>
        </div>
        <div class="form-group">
            <label>确认新密码</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">修改密码</button>
    </form>
</div>
