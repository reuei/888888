<div class="breadcrumb">模板前端 / 首页模板</div>
<div class="page-header">
    <h2>首页模板配置</h2>
</div>

<div class="card" style="max-width: 720px;">
    <form id="settingForm">
        <input type="hidden" name="group" value="template">

        <h3 style="margin-bottom: 16px; font-size: 15px; color: #334155;">SEO 设置</h3>
        <div class="form-group">
            <label>首页 SEO 标题</label>
            <input type="text" name="home_seo_title" value="<?php echo h($config['template_home_seo_title'] ?? ''); ?>" placeholder="留空使用站点名称">
        </div>
        <div class="form-group">
            <label>SEO 关键词</label>
            <input type="text" name="home_seo_keywords" value="<?php echo h($config['template_home_seo_keywords'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>SEO 描述</label>
            <textarea name="home_seo_description" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;"><?php echo h($config['template_home_seo_description'] ?? ''); ?></textarea>
        </div>

        <h3 style="margin: 24px 0 16px; font-size: 15px; color: #334155;">分类展示</h3>
        <div style="display: flex; gap: 12px;">
            <div class="form-group" style="flex: 1;">
                <label>显示分类导航</label>
                <select name="home_show_categories" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;">
                    <option value="1" <?php echo ($config['template_home_show_categories'] ?? '1') === '1' ? 'selected' : ''; ?>>显示</option>
                    <option value="0" <?php echo ($config['template_home_show_categories'] ?? '1') === '0' ? 'selected' : ''; ?>>隐藏</option>
                </select>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>分类数量</label>
                <input type="number" name="home_category_limit" min="1" max="50" value="<?php echo h($config['template_home_category_limit'] ?? '12'); ?>">
            </div>
        </div>

        <h3 style="margin: 24px 0 16px; font-size: 15px; color: #334155;">公告展示</h3>
        <div style="display: flex; gap: 12px;">
            <div class="form-group" style="flex: 1;">
                <label>显示公告栏</label>
                <select name="home_show_articles" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;">
                    <option value="1" <?php echo ($config['template_home_show_articles'] ?? '1') === '1' ? 'selected' : ''; ?>>显示</option>
                    <option value="0" <?php echo ($config['template_home_show_articles'] ?? '1') === '0' ? 'selected' : ''; ?>>隐藏</option>
                </select>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>公告数量</label>
                <input type="number" name="home_article_limit" min="1" max="20" value="<?php echo h($config['template_home_article_limit'] ?? '5'); ?>">
            </div>
        </div>

        <h3 style="margin: 24px 0 16px; font-size: 15px; color: #334155;">商品展示</h3>
        <div style="display: flex; gap: 12px;">
            <div class="form-group" style="flex: 1;">
                <label>商品排序</label>
                <select name="home_goods_order" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;">
                    <option value="sold" <?php echo ($config['template_home_goods_order'] ?? 'sold') === 'sold' ? 'selected' : ''; ?>>按销量</option>
                    <option value="id" <?php echo ($config['template_home_goods_order'] ?? 'sold') === 'id' ? 'selected' : ''; ?>>按最新</option>
                    <option value="price_asc" <?php echo ($config['template_home_goods_order'] ?? 'sold') === 'price_asc' ? 'selected' : ''; ?>>价格从低到高</option>
                    <option value="price_desc" <?php echo ($config['template_home_goods_order'] ?? 'sold') === 'price_desc' ? 'selected' : ''; ?>>价格从高到低</option>
                </select>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>商品数量</label>
                <input type="number" name="home_goods_limit" min="1" max="100" value="<?php echo h($config['template_home_goods_limit'] ?? '24'); ?>">
            </div>
        </div>

        <h3 style="margin: 24px 0 16px; font-size: 15px; color: #334155;">底部统计栏</h3>
        <div style="display: flex; gap: 12px;">
            <div class="form-group" style="flex: 1;">
                <label>显示统计栏</label>
                <select name="home_show_stats" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;">
                    <option value="1" <?php echo ($config['template_home_show_stats'] ?? '1') === '1' ? 'selected' : ''; ?>>显示</option>
                    <option value="0" <?php echo ($config['template_home_show_stats'] ?? '1') === '0' ? 'selected' : ''; ?>>隐藏</option>
                </select>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>统计栏文案</label>
                <input type="text" name="home_stats_text" value="<?php echo h($config['template_home_stats_text'] ?? '平台交易 安全快捷'); ?>">
            </div>
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
