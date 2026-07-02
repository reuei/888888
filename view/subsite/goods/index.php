<div class="breadcrumb">商品管理 / 分站商品列表</div>
<div class="page-header">
    <h2>分站商品列表</h2>
    <div>
        <a href="javascript:;" class="btn btn-outline" onclick="batchOffline()">批量下架</a>
        <a href="<?php echo url('subsite/goods/stock'); ?>" class="btn" style="margin-left: 8px;">库存监控</a>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('subsite/goods'); ?>" style="flex-wrap: wrap;">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="商品名称 / ID / 商户">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>上架中</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>已下架</option>
        </select>
        <select name="category_id" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部分类</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?php echo $c['id']; ?>" <?php echo $categoryId === (string) $c['id'] ? 'selected' : ''; ?>><?php echo h($c['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="merchant_id" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部商户</option>
            <?php foreach ($merchants as $m): ?>
            <option value="<?php echo $m['id']; ?>" <?php echo $merchantId === (string) $m['id'] ? 'selected' : ''; ?>><?php echo h($m['shop_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th><input type="checkbox" id="checkAll" style="cursor: pointer;"></th>
            <th>商品ID</th>
            <th>商品名称</th>
            <th>所属商户</th>
            <th>分类</th>
            <th>售价</th>
            <th>库存 / 已售</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="9" style="text-align: center; color: #64748B; padding: 40px;">暂无商品数据</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><input type="checkbox" class="check-item" value="<?php echo $item['id']; ?>" style="cursor: pointer;"></td>
            <td><?php echo $item['id']; ?></td>
            <td>
                <div style="font-weight: 500;"><?php echo h($item['name']); ?></div>
                <div style="color: #94A3B8; font-size: 12px;"><?php echo $item['type'] == 1 ? '卡密' : ($item['type'] == 2 ? '人工' : '自动'); ?></div>
            </td>
            <td><?php echo h($item['shop_name'] ?? '-'); ?></td>
            <td><?php echo h($item['category_name'] ?? '-'); ?></td>
            <td>¥ <?php echo $item['price']; ?></td>
            <td>
                <div><?php echo $item['stock']; ?></div>
                <div style="color: #94A3B8; font-size: 12px;">已售 <?php echo $item['sold']; ?></div>
            </td>
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
        <a href="<?php echo url('subsite/goods') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status . '&category_id=' . $categoryId . '&merchant_id=' . $merchantId; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('subsite/goods') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status . '&category_id=' . $categoryId . '&merchant_id=' . $merchantId; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('checkAll').addEventListener('change', (e) => {
    document.querySelectorAll('.check-item').forEach(cb => cb.checked = e.target.checked);
});

function getCheckedIds() {
    return Array.from(document.querySelectorAll('.check-item:checked')).map(cb => cb.value);
}

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

async function batchOffline() {
    const ids = getCheckedIds();
    if (!ids.length) {
        alert('请选择商品');
        return;
    }
    const reason = prompt('请输入批量下架原因：') || '分站批量下架';
    if (!confirm('确认批量下架选中的 ' + ids.length + ' 个商品？')) return;
    const form = new FormData();
    ids.forEach(id => form.append('ids[]', id));
    form.append('reason', reason);
    const res = await fetch('<?php echo url('subsite/goods/batchOffline'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
