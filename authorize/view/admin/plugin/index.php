<div class="page-header">
    <h2>插件管理</h2>
</div>

<div class="card">
    <form method="get" action="<?php echo url('admin/plugin'); ?>" class="search-bar">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="插件名称">
        <select name="status">
            <option value="">全部状态</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>待审核</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>上架</option>
            <option value="2" <?php echo $status === '2' ? 'selected' : ''; ?>>下架</option>
        </select>
        <button type="submit" class="btn btn-sm">搜索</button>
    </form>

    <?php if (empty($list)): ?>
    <div class="empty-tip">暂无插件</div>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>ID</th><th>名称</th><th>作者</th><th>版本</th><th>价格</th><th>下载量</th><th>状态</th><th>操作</th></tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo h($item['name']); ?></td>
                <td><?php echo h($item['author']); ?></td>
                <td><?php echo h($item['version']); ?></td>
                <td><?php echo $item['price'] > 0 ? format_price($item['price']) : '免费'; ?></td>
                <td><?php echo $item['download_count']; ?></td>
                <td>
                    <?php if ($item['status'] == 0): ?><span class="tag tag-orange">待审核</span>
                    <?php elseif ($item['status'] == 1): ?><span class="tag tag-green">上架</span>
                    <?php else: ?><span class="tag tag-red">下架</span><?php endif; ?>
                </td>
                <td>
                    <?php if ($item['status'] == 0): ?>
                    <a href="<?php echo url('admin/plugin/review', ['id' => $item['id']]); ?>" class="btn btn-sm">审核</a>
                    <?php else: ?>
                    <button class="btn btn-sm btn-outline toggle-status" data-id="<?php echo $item['id']; ?>"><?php echo $item['status'] == 1 ? '下架' : '上架'; ?></button>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-danger delete" data-id="<?php echo $item['id']; ?>">删除</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo pagination($total, $page, 15, url('admin/plugin', ['keyword' => $keyword, 'status' => $status, 'page' => '{page}'])); ?>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.toggle-status').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch('<?php echo url('admin/plugin/toggleStatus'); ?>', {method:'POST', body:new URLSearchParams({id})})
            .then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
document.querySelectorAll('.delete').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('确认删除？')) return;
        const id = this.dataset.id;
        fetch('<?php echo url('admin/plugin/delete'); ?>', {method:'POST', body:new URLSearchParams({id})})
            .then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
</script>
