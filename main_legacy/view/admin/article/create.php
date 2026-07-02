<div class="breadcrumb">运营管理 / 文章公告 / 发布公告</div>
<div class="page-header">
    <h2>发布公告</h2>
    <a href="<?php echo url('admin/article'); ?>" class="btn btn-outline">返回列表</a>
</div>

<div class="card" style="max-width: 800px;">
    <form id="createForm">
        <div class="form-group">
            <label>标题</label>
            <input type="text" name="title" required>
        </div>
        <div style="display: flex; gap: 12px;">
            <div class="form-group" style="flex: 1;">
                <label>分类</label>
                <select name="category">
                    <?php foreach ($categoryMap as $k => $v): ?>
                    <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="width: 120px;">
                <label>排序</label>
                <input type="number" name="sort" value="0">
            </div>
            <div class="form-group" style="width: 120px;">
                <label>状态</label>
                <select name="status">
                    <option value="1">启用</option>
                    <option value="0">禁用</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>内容</label>
            <textarea name="content" required rows="10"></textarea>
        </div>
        <button type="submit" class="btn" id="saveBtn">发布</button>
    </form>
</div>

<script>
document.getElementById('createForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = '发布中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('admin/article/save'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) {
            location.href = '<?php echo url('admin/article'); ?>';
        }
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '发布';
    }
});
</script>
