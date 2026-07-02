<div class="breadcrumb">模板前端 / 购卡页模板</div>
<div class="page-header">
    <h2>购卡页模板配置</h2>
</div>

<div class="card" style="max-width: 720px;">
    <form id="settingForm">
        <input type="hidden" name="group" value="template">

        <h3 style="margin-bottom: 16px; font-size: 15px; color: #334155;">SEO 设置</h3>
        <div class="form-group">
            <label>页面标题</label>
            <input type="text" name="goods_seo_title" value="<?php echo h($config['template_goods_seo_title'] ?? '全部商品'); ?>">
        </div>

        <h3 style="margin: 24px 0 16px; font-size: 15px; color: #334155;">列表设置</h3>
        <div style="display: flex; gap: 12px;">
            <div class="form-group" style="flex: 1;">
                <label>每页商品数</label>
                <input type="number" name="goods_page_size" min="12" max="100" value="<?php echo h($config['template_goods_page_size'] ?? '24'); ?>">
            </div>
            <div class="form-group" style="flex: 1;">
                <label>默认排序</label>
                <select name="goods_default_sort" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;">
                    <option value="sold" <?php echo ($config['template_goods_default_sort'] ?? 'sold') === 'sold' ? 'selected' : ''; ?>>按销量</option>
                    <option value="id" <?php echo ($config['template_goods_default_sort'] ?? 'sold') === 'id' ? 'selected' : ''; ?>>按最新</option>
                    <option value="price_asc" <?php echo ($config['template_goods_default_sort'] ?? 'sold') === 'price_asc' ? 'selected' : ''; ?>>价格从低到高</option>
                    <option value="price_desc" <?php echo ($config['template_goods_default_sort'] ?? 'sold') === 'price_desc' ? 'selected' : ''; ?>>价格从高到低</option>
                </select>
            </div>
        </div>

        <h3 style="margin: 24px 0 16px; font-size: 15px; color: #334155;">显示项</h3>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
            <div class="form-group">
                <label>显示库存</label>
                <select name="goods_show_stock" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;">
                    <option value="1" <?php echo ($config['template_goods_show_stock'] ?? '1') === '1' ? 'selected' : ''; ?>>显示</option>
                    <option value="0" <?php echo ($config['template_goods_show_stock'] ?? '1') === '0' ? 'selected' : ''; ?>>隐藏</option>
                </select>
            </div>
            <div class="form-group">
                <label>显示销量</label>
                <select name="goods_show_sold" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;">
                    <option value="1" <?php echo ($config['template_goods_show_sold'] ?? '1') === '1' ? 'selected' : ''; ?>>显示</option>
                    <option value="0" <?php echo ($config['template_goods_show_sold'] ?? '1') === '0' ? 'selected' : ''; ?>>隐藏</option>
                </select>
            </div>
            <div class="form-group">
                <label>显示商户</label>
                <select name="goods_show_merchant" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;">
                    <option value="1" <?php echo ($config['template_goods_show_merchant'] ?? '1') === '1' ? 'selected' : ''; ?>>显示</option>
                    <option value="0" <?php echo ($config['template_goods_show_merchant'] ?? '1') === '0' ? 'selected' : ''; ?>>隐藏</option>
                </select>
            </div>
            <div class="form-group">
                <label>显示推荐</label>
                <select name="goods_show_recommend" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;">
                    <option value="1" <?php echo ($config['template_goods_show_recommend'] ?? '1') === '1' ? 'selected' : ''; ?>>显示</option>
                    <option value="0" <?php echo ($config['template_goods_show_recommend'] ?? '1') === '0' ? 'selected' : ''; ?>>隐藏</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>推荐商品数量</label>
            <input type="number" name="goods_recommend_limit" min="0" max="20" value="<?php echo h($config['template_goods_recommend_limit'] ?? '6'); ?>">
        </div>

        <div class="form-group">
            <label>空数据提示</label>
            <input type="text" name="goods_empty_tip" value="<?php echo h($config['template_goods_empty_tip'] ?? '暂无相关商品'); ?>">
        </div>

        <button type="submit" class="btn" id="saveBtn">保存配置</button>
    </form>
</div>

<script>
document.getElementById('settingForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = '保存中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('admin/template/save'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '保存配置';
    }
});
</script>
