<div class="breadcrumb">商品管理 / <?php echo $goods ? '编辑商品' : '新增商品'; ?></div>
<div class="page-header">
    <h2><?php echo $goods ? '编辑商品' : '新增商品'; ?></h2>
    <a href="<?php echo url('merchant/goods'); ?>" class="btn btn-outline">返回列表</a>
</div>

<div class="card" style="max-width: 720px;">
    <form id="goodsForm">
        <input type="hidden" name="id" value="<?php echo $goods['id'] ?? 0; ?>">
        <div class="form-group">
            <label>商品名称</label>
            <input type="text" name="name" value="<?php echo h($goods['name'] ?? ''); ?>" placeholder="请输入商品名称" required>
        </div>
        <div style="display: flex; gap: 16px;">
            <div class="form-group" style="flex: 1;">
                <label>商品分类</label>
                <select name="category_id" required>
                    <option value="">请选择分类</option>
                    <?php foreach ($categories as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo (isset($goods['category_id']) && $goods['category_id'] == $c['id']) ? 'selected' : ''; ?>><?php echo h($c['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>商品类型</label>
                <select name="type" <?php echo $goods ? 'disabled' : ''; ?>>
                    <option value="1" <?php echo (isset($goods['type']) && $goods['type'] == 1) ? 'selected' : ''; ?>>卡密自动发货</option>
                    <option value="2" <?php echo (isset($goods['type']) && $goods['type'] == 2) ? 'selected' : ''; ?>>人工发货</option>
                    <option value="3" <?php echo (isset($goods['type']) && $goods['type'] == 3) ? 'selected' : ''; ?>>自动发货</option>
                </select>
                <?php if ($goods): ?>
                <input type="hidden" name="type" value="<?php echo $goods['type']; ?>">
                <?php endif; ?>
            </div>
        </div>
        <div style="display: flex; gap: 16px;">
            <div class="form-group" style="flex: 1;">
                <label>售价（元）</label>
                <input type="number" step="0.01" min="0.01" name="price" value="<?php echo $goods['price'] ?? ''; ?>" placeholder="0.00" required>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>原价（元）</label>
                <input type="number" step="0.01" min="0" name="original_price" value="<?php echo $goods['original_price'] ?? ''; ?>" placeholder="0.00">
            </div>
        </div>
        <div style="display: flex; gap: 16px;">
            <div class="form-group" style="flex: 1;">
                <label>库存</label>
                <input type="number" min="0" name="stock" value="<?php echo $goods['stock'] ?? '0'; ?>" <?php echo (isset($goods['type']) && $goods['type'] == 1) ? 'disabled' : ''; ?> required>
                <?php if (isset($goods['type']) && $goods['type'] == 1): ?>
                <div style="color: #64748B; font-size: 12px; margin-top: 4px;">卡密类商品库存通过卡密管理维护</div>
                <input type="hidden" name="stock" value="<?php echo $goods['stock']; ?>">
                <?php endif; ?>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>库存预警阈值</label>
                <input type="number" min="1" name="low_stock" value="<?php echo $goods['low_stock'] ?? '10'; ?>" required>
            </div>
        </div>
        <div class="form-group">
            <label>封面图片 URL</label>
            <input type="text" name="cover" value="<?php echo h($goods['cover'] ?? ''); ?>" placeholder="http://...">
        </div>
        <div class="form-group">
            <label>商品说明</label>
            <textarea name="content" rows="6" placeholder="填写商品介绍、使用说明等"><?php echo h($goods['content'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label>状态</label>
            <select name="status">
                <option value="1" <?php echo (isset($goods['status']) && $goods['status'] == 1) ? 'selected' : ''; ?>>立即上架</option>
                <option value="0" <?php echo (isset($goods['status']) && $goods['status'] == 0) ? 'selected' : ''; ?>>先下架</option>
            </select>
        </div>
        <button type="submit" class="btn" id="saveBtn">保存</button>
    </form>
</div>

<script>
document.getElementById('goodsForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = '保存中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('merchant/goods/save'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0 && data.data.redirect) {
            location.href = data.data.redirect;
        }
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '保存';
    }
});
</script>
