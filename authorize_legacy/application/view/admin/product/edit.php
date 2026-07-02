<div class="page-header">
    <h2><?php echo $product ? '编辑产品' : '新增产品'; ?></h2>
</div>

<div class="card">
    <form id="productForm">
        <input type="hidden" name="id" value="<?php echo $product['id'] ?? 0; ?>">
        <div class="form-group">
            <label>产品名称</label>
            <input type="text" name="name" value="<?php echo h($product['name'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label>产品描述</label>
            <textarea name="description" rows="4"><?php echo h($product['description'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>价格</label>
            <input type="number" name="price" value="<?php echo $product['price'] ?? 0; ?>" step="0.01" min="0">
        </div>
        <div class="form-group">
            <label>授权类型</label>
            <select name="license_type">
                <option value="code" <?php echo ($product['license_type'] ?? 'code') === 'code' ? 'selected' : ''; ?>>授权码</option>
                <option value="domain" <?php echo ($product['license_type'] ?? '') === 'domain' ? 'selected' : ''; ?>>域名授权</option>
            </select>
        </div>
        <div class="form-group">
            <label>有效期（天，0 为永久）</label>
            <input type="number" name="valid_days" value="<?php echo $product['valid_days'] ?? 0; ?>" min="0">
        </div>
        <div class="form-group">
            <label>排序</label>
            <input type="number" name="sort" value="<?php echo $product['sort'] ?? 0; ?>">
        </div>
        <div class="form-group">
            <label>状态</label>
            <select name="status">
                <option value="1" <?php echo ($product['status'] ?? 1) == 1 ? 'selected' : ''; ?>>上架</option>
                <option value="0" <?php echo ($product['status'] ?? 1) == 0 ? 'selected' : ''; ?>>下架</option>
            </select>
        </div>
        <button type="submit" class="btn">保存</button>
        <a href="<?php echo url('admin/product'); ?>" class="btn btn-outline">返回</a>
    </form>
</div>

<script>
document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('<?php echo url('admin/product/save'); ?>', {method:'POST', body:new FormData(e.target)})
        .then(r => r.json()).then(res => {
            alert(res.msg);
            if (res.code === 0 && res.data.redirect) location.href = res.data.redirect;
        });
});
</script>
