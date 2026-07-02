<div class="breadcrumb">店铺设置 / 实名认证</div>
<div class="page-header">
    <h2>实名认证</h2>
</div>

<div class="card">
    <div style="margin-bottom: 20px;">
        <span>当前状态：</span>
        <span class="tag <?php echo $merchant['auth_status_class']; ?>"><?php echo h($merchant['auth_status_text']); ?></span>
        <?php if ($merchant['auth_status'] == 3): ?>
            <p style="color: #DC2626; margin-top: 8px;">驳回原因：<?php echo h($merchant['auth_remark'] ?? ''); ?></p>
        <?php endif; ?>
        <?php if ($merchant['auth_status'] == 2 && $merchant['auth_time']): ?>
            <p class="hint" style="margin-top: 8px;">审核通过时间：<?php echo h($merchant['auth_time']); ?></p>
        <?php endif; ?>
    </div>

    <?php if ($merchant['auth_status'] == 2): ?>
        <div class="card" style="background: #F0FDF4;">
            <p>实名认证已通过，认证信息如下：</p>
            <div style="margin-top: 12px;">
                <p><strong>真实姓名：</strong><?php echo h($merchant['real_name']); ?></p>
                <p><strong>身份证号：</strong><?php echo h(substr_replace($merchant['id_card_no'], '********', 6, 8)); ?></p>
            </div>
        </div>
    <?php else: ?>
        <form id="authForm" enctype="multipart/form-data">
            <div class="form-group">
                <label>真实姓名 <span style="color: #EF4444;">*</span></label>
                <input type="text" name="real_name" value="<?php echo h($merchant['real_name'] ?? ''); ?>" placeholder="请输入真实姓名" required>
            </div>
            <div class="form-group">
                <label>身份证号 <span style="color: #EF4444;">*</span></label>
                <input type="text" name="id_card_no" value="<?php echo h($merchant['id_card_no'] ?? ''); ?>" placeholder="请输入18位身份证号" maxlength="18" required>
            </div>
            <div class="form-group">
                <label>身份证正面 <span style="color: #EF4444;">*</span></label>
                <input type="file" name="id_card_front" accept="image/*" required>
                <?php if (!empty($merchant['id_card_front'])): ?>
                    <p class="hint">已上传：<a href="<?php echo base_url($merchant['id_card_front']); ?>" target="_blank">查看原图</a></p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>身份证反面 <span style="color: #EF4444;">*</span></label>
                <input type="file" name="id_card_back" accept="image/*" required>
                <?php if (!empty($merchant['id_card_back'])): ?>
                    <p class="hint">已上传：<a href="<?php echo base_url($merchant['id_card_back']); ?>" target="_blank">查看原图</a></p>
                <?php endif; ?>
            </div>
            <p class="hint" style="margin-bottom: 16px;">支持 JPG/PNG/GIF 格式，单张不超过 5MB</p>
            <button type="submit" class="btn" id="submitBtn" <?php echo $merchant['auth_status'] == 1 ? 'disabled' : ''; ?>>
                <?php echo $merchant['auth_status'] == 1 ? '审核中' : '提交认证'; ?>
            </button>
        </form>
    <?php endif; ?>
</div>

<?php if ($merchant['auth_status'] != 2): ?>
<script>
document.getElementById('authForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = '提交中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('merchant/setting/saveAuth'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '提交认证';
    }
});
</script>
<?php endif; ?>
