<div class="page-header">
    <h2>授权产品</h2>
    <a href="<?php echo url('admin/product/add'); ?>" class="btn btn-sm">新增产品</a>
</div>

<div class="card">
    <form method="get" action="<?php echo url('admin/product'); ?>" class="search-bar">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="产品名称">
        <button type="submit" class="btn btn-sm">搜索</button>
    </form>

    <?php if (empty($list)): ?>
    <div class="empty-tip">暂无产品</div>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>ID</th><th>名称</th><th>授权类型</th><th>价格</th><th>有效期</th><th>排序</th><th>状态</th><th>操作</th></tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo h($item['name']); ?></td>
                <td><?php echo $item['license_type'] === 'domain' ? '域名授权' : '授权码'; ?></td>
                <td><?php echo format_price($item['price']); ?></td>
                <td><?php echo $item['valid_days'] > 0 ? $item['valid_days'] . ' 天' : '永久'; ?></td>
                <td><?php echo $item['sort']; ?></td>
                <td><span class="tag <?php echo $item['status'] == 1 ? 'tag-green' : 'tag-red'; ?>"><?php echo $item['status'] == 1 ? '上架' : '下架'; ?></span></td>
                <td>
                    <a href="<?php echo url('admin/product/edit', ['id' => $item['id']]); ?>" class="btn btn-sm">编辑</a>
                    <button class="btn btn-sm btn-outline toggle-status" data-id="<?php echo $item['id']; ?>"><?php echo $item['status'] == 1 ? '下架' : '上架'; ?></button>
                    <button class="btn btn-sm btn-danger delete" data-id="<?php echo $item['id']; ?>">删除</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo pagination($total, $page, 15, url('admin/product', ['keyword' => $keyword, 'page' => '{page}'])); ?>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.toggle-status').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch('<?php echo url('admin/product/toggleStatus'); ?>', {method:'POST', body:new URLSearchParams({id})})
            .then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
document.querySelectorAll('.delete').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('确认删除？')) return;
        const id = this.dataset.id;
        fetch('<?php echo url('admin/product/delete'); ?>', {method:'POST', body:new URLSearchParams({id})})
            .then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
</script>
