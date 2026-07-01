<div class="page-header">
    <h2>编辑用户</h2>
</div>

<div class="card">
    <form id="userForm">
        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
        <div class="form-group">
            <label>账号</label>
            <input type="text" value="<?php echo h($user['username']); ?>" disabled>
        </div>
        <div class="form-group">
            <label>昵称</label>
            <input type="text" name="nickname" value="<?php echo h($user['nickname']); ?>">
        </div>
        <div class="form-group">
            <label>邮箱</label>
            <input type="text" name="email" value="<?php echo h($user['email']); ?>">
        </div>
        <div class="form-group">
            <label>手机号</label>
            <input type="text" name="mobile" value="<?php echo h($user['mobile']); ?>">
        </div>
        <div class="form-group">
            <label>余额</label>
            <input type="number" name="balance" value="<?php echo $user['balance']; ?>" step="0.01">
        </div>
        <div class="form-group">
            <label>状态</label>
            <select name="status">
                <option value="1" <?php echo $user['status'] == 1 ? 'selected' : ''; ?>>正常</option>
                <option value="0" <?php echo $user['status'] == 0 ? 'selected' : ''; ?>>禁用</option>
            </select>
        </div>
        <div class="form-group">
            <label>新密码（留空不修改）</label>
            <input type="password" name="password">
        </div>
        <button type="submit" class="btn">保存</button>
        <a href="<?php echo url('admin/user'); ?>" class="btn btn-outline">返回</a>
    </form>
</div>

<script>
document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('<?php echo url('admin/user/save'); ?>', {method:'POST', body:new FormData(e.target)})
        .then(r => r.json()).then(res => {
            alert(res.msg);
            if (res.code === 0) location.href = '<?php echo url('admin/user'); ?>';
        });
});
</script>
