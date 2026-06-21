<?php
/**
 * 轮播图管理（列表型）
 */
$active_page = 'slides';
require_once 'header.php';
$items = $site_data['slides'] ?? [];
// 定义字段
$fields = [
    ['key' => 'slide_title', 'label' => '标题', 'type' => 'text'],
    ['key' => 'slide_subtitle', 'label' => '副标题', 'type' => 'text'],
    ['key' => 'slide_desc', 'label' => '描述', 'type' => 'textarea'],
    ['key' => 'slide_image', 'label' => '图片URL', 'type' => 'text'],
    ['key' => 'slide_link', 'label' => '链接', 'type' => 'text'],
    ['key' => 'slide_color', 'label' => '主题色', 'type' => 'color', 'default' => '#1a73e8'],
];
?>
<div class="admin-content">
    <div class="admin-card">
        <h2 style="display:flex;align-items:center;justify-content:space-between;">
            <span><i class="fas fa-images" style="color:#1a73e8;"></i> 轮播图管理</span>
            <button type="button" class="btn btn-primary btn-sm" id="addBtn"><i class="fas fa-plus"></i> 添加</button>
        </h2>
        <form method="post" id="form_list">
            <input type="hidden" name="action" value="save_slides">
            <div id="listContainer" style="margin-top:20px;">
                <?php if (empty($items)): ?>
                <p style="color:#6b7280;text-align:center;padding:40px;background:#f8fafc;border-radius:8px;">暂无数据，点击上方"添加"按钮创建</p>
                <?php endif; ?>
                <?php foreach ($items as $idx => $item): ?>
                <div class="item-card">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    <strong style="color:#1a73e8;">#<?php echo $idx + 1; ?></strong>
                    <span style="color:#4a5568;"><?php echo htmlspecialchars($item['title'] ?? ''); ?></span>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="this.closest('.item-card').remove()" style="margin-left:auto;">
                        <i class="fas fa-trash" style="color:#e74c3c;"></i> 删除
                    </button>
                </div>
                <?php foreach ($fields as $f): ?>
                <?php $val = $item[str_replace('slide_', '', $f['key'])] ?? ($f['default'] ?? ''); ?>
                <div class="form-group">
                    <label class="form-label"><?php echo $f['label']; ?></label>
                    <?php if ($f['type'] === 'textarea'): ?>
                        <textarea class="form-textarea" name="<?php echo $f['key']; ?>[]" rows="2"><?php echo htmlspecialchars($val); ?></textarea>
                    <?php elseif ($f['type'] === 'color'): ?>
                        <input type="color" class="form-input" name="<?php echo $f['key']; ?>[]" value="<?php echo htmlspecialchars($val); ?>" style="width:80px;padding:4px;height:38px;">
                    <?php else: ?>
                        <input type="text" class="form-input" name="<?php echo $f['key']; ?>[]" value="<?php echo htmlspecialchars($val); ?>">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="margin-top:20px;text-align:right;">
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> 保存全部</button>
        </div>
    </form>
    <div style="display:none;" id="itemTemplate">
        <div class="item-card">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                <strong style="color:#1a73e8;">#<span class="idxLabel"></span></strong>
                <span style="color:#4a5568;">新项目</span>
                <button type="button" class="btn btn-ghost btn-sm" onclick="this.closest('.item-card').remove()" style="margin-left:auto;">
                    <i class="fas fa-trash" style="color:#e74c3c;"></i> 删除
                </button>
            </div>
            <?php foreach ($fields as $f): ?>
            <div class="form-group">
                <label class="form-label"><?php echo $f['label']; ?></label>
                <?php if ($f['type'] === 'textarea'): ?>
                    <textarea class="form-textarea" name="<?php echo $f['key']; ?>[]" rows="2"></textarea>
                <?php elseif ($f['type'] === 'color'): ?>
                    <input type="color" class="form-input" name="<?php echo $f['key']; ?>[]" value="<?php echo $f['default'] ?? '#1a73e8'; ?>" style="width:80px;padding:4px;height:38px;">
                <?php else: ?>
                    <input type="text" class="form-input" name="<?php echo $f['key']; ?>[]" value="<?php echo $f['default'] ?? ''; ?>">
                <?php endif; ?>
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
    const tmpl = document.getElementById('itemTemplate');
    // 移除空状态
    const empty = container.querySelector('p');
    if (empty) empty.remove();
    const clone = tmpl.children[0].cloneNode(true);
    counter++;
    clone.querySelector('.idxLabel').textContent = counter;
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
