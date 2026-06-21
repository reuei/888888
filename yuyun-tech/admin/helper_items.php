<?php
/**
 * 生成已有项目的渲染代码
 * 参数:
 *  - $items (从外面传入)
 *  - $fields (从外面传入)
 *  - $keyPrefix: 字段前缀，如 product_
 */
if (empty($items)) {
    echo '<p style="color:#6b7280;text-align:center;padding:40px;background:#f8fafc;border-radius:8px;">暂无数据，点击上方"添加"按钮创建</p>';
    return;
}
foreach ($items as $idx => $item): ?>
<div class="item-card">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
        <strong style="color:#1a73e8;">#<?php echo $idx + 1; ?></strong>
        <span style="color:#4a5568;">
            <?php
            $first_field = current($fields);
            $first_key = str_replace($keyPrefix, '', $first_field['key']);
            echo htmlspecialchars($item[$first_key] ?? '');
            ?>
        </span>
        <button type="button" class="btn btn-ghost btn-sm" onclick="this.closest('.item-card').remove()" style="margin-left:auto;">
            <i class="fas fa-trash" style="color:#e74c3c;"></i> 删除
        </button>
    </div>
    <?php foreach ($fields as $f):
        $clean_key = str_replace($keyPrefix, '', $f['key']);
        $val = $item[$clean_key] ?? ($f['default'] ?? '');
    ?>
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
