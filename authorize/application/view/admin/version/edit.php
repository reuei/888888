<div class="page-header">
    <h2><?php echo $version ? '编辑版本' : '新增版本'; ?></h2>
</div>

<div class="card">
    <form id="versionForm" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $version['id'] ?? 0; ?>">
        <div class="form-group">
            <label>版本号</label>
            <input type="text" name="version" value="<?php echo h($version['version'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label>发布日期</label>
            <input type="date" name="release_date" value="<?php echo h($version['release_date'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>更新说明</label>
            <textarea name="update_desc" rows="4"><?php echo h($version['update_desc'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>是否强制更新</label>
            <select name="force_update">
                <option value="0" <?php echo ($version['force_update'] ?? 0) == 0 ? 'selected' : ''; ?>>否</option>
                <option value="1" <?php echo ($version['force_update'] ?? 0) == 1 ? 'selected' : ''; ?>>是</option>
            </select>
        </div>
        <div class="form-group">
            <label>标记为最新</label>
            <select name="is_latest">
                <option value="0" <?php echo ($version['is_latest'] ?? 0) == 0 ? 'selected' : ''; ?>>否</option>
                <option value="1" <?php echo ($version['is_latest'] ?? 0) == 1 ? 'selected' : ''; ?>>是</option>
            </select>
        </div>
        <div class="form-group">
            <label>更新包文件（ZIP）<?php echo $version ? '（不选则保留原文件）' : ''; ?></label>
            <input type="file" name="file" accept=".zip" <?php echo $version ? '' : 'required'; ?>>
        </div>
        <button type="submit" class="btn">保存</button>
        <a href="<?php echo url('admin/version'); ?>" class="btn btn-outline">返回</a>
    </form>
</div>

<script>
document.getElementById('versionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('<?php echo url('admin/version/save'); ?>', {method:'POST', body:new FormData(e.target)})
        .then(r => r.json()).then(res => {
            alert(res.msg);
            if (res.code === 0 && res.data.redirect) location.href = res.data.redirect;
        });
});
</script>
