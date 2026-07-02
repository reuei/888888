<div class="breadcrumb">商品管理 / 商品列表</div>
<div class="page-header">
    <h2>商品列表</h2>
    <div>
        <a href="<?php echo url('merchant/goods/import'); ?>" class="btn btn-outline">批量导入卡密</a>
        <a href="<?php echo url('merchant/goods/create'); ?>" class="btn" style="margin-left: 8px;">新增商品</a>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('merchant/goods'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="搜索商品名称">
        <select name="category_id" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部分类</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?php echo $c['id']; ?>" <?php echo $categoryId === (string) $c['id'] ? 'selected' : ''; ?>><?php echo h($c['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>上架中</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>已下架</option>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>商品ID</th>
            <th>商品名称</th>
            <th>分类</th>
            <th>类型</th>
            <th>售价</th>
            <th>库存</th>
            <th>销量</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="9" style="text-align: center; color: #64748B; padding: 40px;">暂无商品，请先新增商品</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['name']); ?></td>
            <td><?php echo h($item['category_name'] ?? '-'); ?></td>
            <td>
                <?php if ($item['type'] == 1): ?><span class="tag tag-blue">卡密</span>
                <?php elseif ($item['type'] == 2): ?><span class="tag tag-orange">人工</span>
                <?php else: ?><span class="tag tag-green">自动</span>
                <?php endif; ?>
            </td>
            <td>¥ <?php echo $item['price']; ?></td>
            <td><?php echo $item['stock']; ?></td>
            <td><?php echo $item['sold']; ?></td>
            <td>
                <?php if ($item['status'] == 1): ?>
                <span class="tag tag-green">上架中</span>
                <?php else: ?>
                <span class="tag">已下架</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="<?php echo url('merchant/goods/create', ['id' => $item['id']]); ?>" class="btn btn-sm btn-primary">编辑</a>
                <?php if ($item['status'] == 1): ?>
                <a href="javascript:;" class="btn btn-sm btn-warning" onclick="toggleStatus(<?php echo $item['id']; ?>, 0)">下架</a>
                <?php else: ?>
                <a href="javascript:;" class="btn btn-sm btn-success" onclick="toggleStatus(<?php echo $item['id']; ?>, 1)">上架</a>
                <?php endif; ?>
                <a href="<?php echo url('merchant/card', ['goods_id' => $item['id']]); ?>" class="btn btn-sm">卡密</a>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="deleteItem(<?php echo $item['id']; ?>, '<?php echo h($item['name']); ?>')">删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('merchant/goods') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status . '&category_id=' . $categoryId; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('merchant/goods') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status . '&category_id=' . $categoryId; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
async function toggleStatus(id, status) {
    if (!confirm(status == 1 ? '确认上架？' : '确认下架？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('status', status);
    const res = await fetch('<?php echo url('merchant/goods/toggleStatus'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function deleteItem(id, name) {
    if (!confirm('确认删除商品「' + name + '」？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('merchant/goods/delete'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
