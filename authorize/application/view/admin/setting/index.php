<div class="page-header">
    <h2>系统设置</h2>
</div>

<div class="card">
    <form id="settingForm">
        <?php foreach ($config as $item): ?>
        <div class="form-group">
            <label><?php echo h($item['description'] ?: $item['cfg_key']); ?></label>
            <?php if (strpos($item['cfg_key'], 'copyright') !== false || strpos($item['cfg_key'], 'desc') !== false): ?>
            <textarea name="<?php echo $item['cfg_key']; ?>" rows="3"><?php echo h($item['cfg_value']); ?></textarea>
            <?php else: ?>
            <input type="text" name="<?php echo $item['cfg_key']; ?>" value="<?php echo h($item['cfg_value']); ?>">
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <button type="submit" class="btn">保存</button>
    </form>
</div>

<script>
document.getElementById('settingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('<?php echo url('admin/setting/save'); ?>', {method:'POST', body:new FormData(e.target)})
        .then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
});
</script>
