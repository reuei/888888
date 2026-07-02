<div class="user-layout">
    <div class="user-sidebar">
        <a href="<?php echo url('user'); ?>">个人中心</a>
        <a href="<?php echo url('user/license'); ?>">我的授权</a>
        <a href="<?php echo url('user/plugin'); ?>" class="active">我的插件</a>
        <a href="<?php echo url('user/order'); ?>">我的订单</a>
        <a href="<?php echo url('user/recharge'); ?>">余额充值</a>
        <a href="<?php echo url('user/profile'); ?>">修改资料</a>
        <a href="<?php echo url('user/password'); ?>">修改密码</a>
    </div>
    <div class="user-content">
        <div class="card">
            <h2 style="margin-bottom: 20px;">上传插件</h2>
            <form id="pluginForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label>插件名称</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>版本号</label>
                    <input type="text" name="version" required>
                </div>
                <div class="form-group">
                    <label>价格</label>
                    <input type="number" name="price" value="0" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label>插件描述</label>
                    <textarea name="description" rows="4"></textarea>
                </div>
                <div class="form-group">
                    <label>插件文件（ZIP）</label>
                    <input type="file" name="file" accept=".zip" required>
                </div>
                <button type="submit" class="btn">提交审核</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('pluginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('<?php echo url('user/doPluginUpload'); ?>', {
        method: 'POST',
        body: new FormData(e.target)
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0 && res.data.redirect) {
            location.href = res.data.redirect;
        }
    });
});
</script>
