<div class="breadcrumb">商品管理 / 分站库存监控</div>
<div class="page-header">
    <h2>分站库存监控</h2>
    <a href="<?php echo url('subsite/goods'); ?>" class="btn btn-outline">商品列表</a>
</div>

<div class="card" style="margin-bottom: 16px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px;">
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">库存紧张</div>
            <div style="font-size: 20px; font-weight: 600; color: #F59E0B;"><?php echo $stat['low_stock'] ?? 0; ?></div>
        </div>
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">已售罄</div>
            <div style="font-size: 20px; font-weight: 600; color: #EF4444;"><?php echo $stat['out_stock'] ?? 0; ?></div>
        </div>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('subsite/goods/stock'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="商品名称 / ID / 商户">
        <select name="stock_type" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="low" <?php echo $stockType === 'low' ? 'selected' : ''; ?>>库存紧张</option>
            <option value="out" <?php echo $stockType === 'out' ? 'selected' : ''; ?>>已售罄</option>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>商品ID</th>
            <th>商品名称</th>
            <th>所属商户</th>
            <th>分类</th>
            <th>售价</th>
            <th>当前库存</th>
            <th>库存预警线</th>
            <th>卡密库存</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="10" style="text-align: center; color: #64748B; padding: 40px;">暂无<?php echo $stockType === 'out' ? '售罄' : '库存紧张'; ?>商品</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td>
                <div style="font-weight: 500;"><?php echo h($item['name']); ?></div>
                <div style="color: #94A3B8; font-size: 12px;"><?php echo $item['type'] == 1 ? '卡密' : ($item['type'] == 2 ? '人工' : '自动'); ?></div>
            </td>
            <td><?php echo h($item['shop_name'] ?? '-'); ?></td>
            <td><?php echo h($item['category_name'] ?? '-'); ?></td>
            <td>¥ <?php echo $item['price']; ?></td>
            <td style="color: <?php echo $item['stock'] <= 0 ? '#EF4444' : '#F59E0B'; ?>; font-weight: 600;"><?php echo $item['stock']; ?></td>
            <td><?php echo $item['low_stock']; ?></td>
            <td><?php echo $item['type'] == 1 ? ($item['card_stock'] ?? 0) : '-'; ?></td>
            <td>
                <?php if ($item['status'] == 1): ?>
                <span class="tag tag-green">上架中</span>
                <?php else: ?>
                <span class="tag">已下架</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="<?php echo url('subsite/goods/detail') . '?id=' . $item['id']; ?>" class="btn btn-sm">详情</a>
                <?php if ($item['status'] == 1): ?>
                <a href="javascript:;" class="btn btn-sm btn-warning" onclick="toggleStatus(<?php echo $item['id']; ?>, 0)" style="margin-left: 4px;">下架</a>
                <?php else: ?>
                <a href="javascript:;" class="btn btn-sm btn-success" onclick="toggleStatus(<?php echo $item['id']; ?>, 1)" style="margin-left: 4px;">上架</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('subsite/goods/stock') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&stock_type=' . $stockType; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('subsite/goods/stock') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&stock_type=' . $stockType; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
async function toggleStatus(id, status) {
    const labels = { 0: '下架', 1: '上架' };
    if (!confirm('确认' + labels[status] + '该商品？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('status', status);
    const res = await fetch('<?php echo url('subsite/goods/toggleStatus'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
