<div class="page-header">
    <h2>审核插件</h2>
</div>

<div class="card">
    <p><strong>插件名称：</strong><?php echo h($plugin['name']); ?></p>
    <p><strong>作者：</strong><?php echo h($plugin['author']); ?></p>
    <p><strong>版本：</strong><?php echo h($plugin['version']); ?></p>
    <p><strong>价格：</strong><?php echo $plugin['price'] > 0 ? format_price($plugin['price']) : '免费'; ?></p>
    <p><strong>MD5：</strong><?php echo h($plugin['file_md5']); ?></p>
    <p><strong>描述：</strong></p>
    <div style="background:#F8FAFC; padding: 12px; border-radius: 6px; margin-bottom: 16px;">
        <?php echo nl2br(h($plugin['description'])); ?>
    </div>
    <a class="btn btn-sm" href="<?php echo base_url($plugin['file_path']); ?>" download>下载文件</a>
</div>

<div class="card">
    <form id="reviewForm">
        <input type="hidden" name="id" value="<?php echo $plugin['id']; ?>">
        <div class="form-group">
            <label>审核结果</label>
            <select name="status">
                <option value="1">通过上架</option>
                <option value="2">拒绝下架</option>
            </select>
        </div>
        <button type="submit" class="btn">提交</button>
        <a href="<?php echo url('admin/plugin'); ?>" class="btn btn-outline">返回</a>
    </form>
</div>

<script>
document.getElementById('reviewForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('<?php echo url('admin/plugin/doReview'); ?>', {method:'POST', body:new FormData(e.target)})
        .then(r => r.json()).then(res => {
            alert(res.msg);
            if (res.code === 0 && res.data.redirect) location.href = res.data.redirect;
        });
});
</script>
