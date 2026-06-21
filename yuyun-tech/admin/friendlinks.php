<?php
/**
 * 友情链接
 */
$active_page = 'friendlinks';
require_once 'header.php';
$items = $site_data['friendlinks'] ?? [];
$keyPrefix = 'fl_';
$fields = [
    ['key' => 'fl_name', 'label' => '名称', 'type' => 'text'],
    ['key' => 'fl_url', 'label' => '链接URL', 'type' => 'text'],
];
?>
<div class="admin-content">
    <div class="admin-card">
        <h2 style="display:flex;align-items:center;justify-content:space-between;">
            <span><i class="fas fa-link" style="color:#00a86b;"></i> 友情链接管理</span>
            <button type="button" class="btn btn-primary btn-sm" id="addBtn"><i class="fas fa-plus"></i> 添加</button>
        </h2>
        <form method="post" id="form_list">
            <input type="hidden" name="action" value="save_friendlinks">
            <div id="listContainer" style="margin-top:20px;"><?php require 'helper_items.php'; ?></div>
            <div style="margin-top:20px;text-align:right;">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> 保存全部</button>
            </div>
        </form>
        <div style="display:none;" id="itemTemplate">
            <div class="item-card">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    <strong style="color:#1a73e8;">#<span class="idxLabel"></span></strong>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="this.closest('.item-card').remove()" style="margin-left:auto;"><i class="fas fa-trash" style="color:#e74c3c;"></i> 删除</button>
                </div>
                <?php foreach ($fields as $f): ?>
                <div class="form-group">
                    <label class="form-label"><?php echo $f['label']; ?></label>
                    <input type="text" class="form-input" name="<?php echo $f['key']; ?>[]" value="">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<script>
let counter = <?php echo count($items); ?>;
document.getElementById('addBtn').addEventListener('click', function() {
    const container = document.getElementById('listContainer');
    const empty = container.querySelector('p'); if (empty) empty.remove();
    const clone = document.getElementById('itemTemplate').children[0].cloneNode(true);
    counter++; clone.querySelector('.idxLabel').textContent = counter;
    container.appendChild(clone);
});
document.getElementById('form_list').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fetch('', { method: 'POST', body: fd })
        .then(r => r.json()).then(d => {
            if (d.code === 0) { showToast(d.msg || '保存成功', 'success'); setTimeout(()=>location.reload(), 800); }
            else { showToast(d.msg || '保存失败', 'danger'); }
        }).catch(() => showToast('请求失败', 'danger'));
});
</script>
</div>
</body>
</html>
