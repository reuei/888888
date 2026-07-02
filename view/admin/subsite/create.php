<div class="breadcrumb">分站管理 / 新建分站</div>
<div class="page-header">
    <h2>新建分站</h2>
    <a href="<?php echo url('admin/subsite'); ?>" class="btn btn-outline">返回列表</a>
</div>

<div class="card" style="max-width: 640px;">
    <form id="subsiteForm">
        <div class="form-group" style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 500;">分站名称 <span style="color: #EF4444;">*</span></label>
            <input type="text" name="name" required placeholder="如：华东一号分站" style="width: 100%; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 500;">域名前缀 <span style="color: #EF4444;">*</span></label>
            <div style="display: flex; align-items: center;">
                <input type="text" name="domain_prefix" required placeholder="如：east1" style="flex: 1; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px 0 0 6px; border-right: none;">
                <span style="padding: 8px 12px; background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 0 6px 6px 0; color: #64748B;">.example.com</span>
            </div>
            <p style="color: #64748B; font-size: 12px; margin-top: 6px;">仅允许小写字母、数字、下划线、中划线</p>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 500;">超管账号 <span style="color: #EF4444;">*</span></label>
            <input type="text" name="admin_user" required placeholder="如：admin_east1" style="width: 100%; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 500;">超管密码 <span style="color: #EF4444;">*</span></label>
            <input type="password" name="admin_pass" required placeholder="请设置强密码" style="width: 100%; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 500;">默认费率分组</label>
            <select name="rate_group_id" style="width: 100%; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
                <?php foreach ($rateGroups as $rg): ?>
                <option value="<?php echo $rg['id']; ?>"><?php echo h($rg['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label style="display: block; margin-bottom: 6px; font-weight: 500;">结算周期模板</label>
            <select name="settle_template" style="width: 100%; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
                <option value="T+0">T+0</option>
                <option value="T+1" selected>T+1</option>
                <option value="T+7">T+7</option>
            </select>
        </div>

        <button type="submit" class="btn" id="submitBtn">创建分站</button>
    </form>
</div>

<script>
document.getElementById('subsiteForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = '创建中...';

    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('admin/subsite/save'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) {
            location.href = data.data.redirect;
        }
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '创建分站';
    }
});
</script>
