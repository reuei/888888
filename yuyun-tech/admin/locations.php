<?php
/**
 * 地区分布
 */
$active_page = 'locations';
require_once 'header.php';
$items = $site_data['locations'] ?? [];
$keyPrefix = 'loc_';
$fields = [
    ['key' => 'loc_region', 'label' => '地区名称', 'type' => 'text'],
    ['key' => 'loc_cities', 'label' => '城市（用逗号或空格分隔）', 'type' => 'text'],
];
?>
<div class="admin-content">
    <div class="admin-card">
        <h2 style="display:flex;align-items:center;justify-content:space-between;">
            <span><i class="fas fa-map-marker-alt" style="color:#ff6b35;"></i> 公司分布地区管理</span>
            <button type="button" class="btn btn-primary btn-sm" id="addBtn"><i class="fas fa-plus"></i> 添加</button>
        </h2>
        <p style="color:#4a5568;margin-top:12px;font-size:14px;">说明：公司分布地图定位坐标在 index.php 中可编辑。此处管理列表与卡片显示。</p>
        <form method="post" id="form_list">
            <input type="hidden" name="action" value="save_locations">
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
