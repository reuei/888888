<div class="page-header">
    <h2>授权码管理</h2>
</div>

<div class="card">
    <form method="get" action="<?php echo url('admin/license'); ?>" class="search-bar">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="授权码/域名/账号">
        <select name="product_id">
            <option value="0">全部产品</option>
            <?php foreach ($products as $p): ?>
            <option value="<?php echo $p['id']; ?>" <?php echo $productId == $p['id'] ? 'selected' : ''; ?>><?php echo h($p['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm">搜索</button>
    </form>

    <?php if (empty($list)): ?>
    <div class="empty-tip">暂无授权</div>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>ID</th><th>产品</th><th>授权码</th><th>类型</th><th>绑定域名</th><th>所属用户</th><th>状态</th><th>过期时间</th><th>操作</th></tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo h($item['product_name']); ?></td>
                <td><?php echo h($item['auth_code']); ?></td>
                <td><?php echo $item['license_type'] === 'domain' ? '域名授权' : '授权码'; ?></td>
                <td><?php echo h($item['auth_domain'] ?: '-'); ?></td>
                <td><?php echo h($item['username'] ?: $item['nickname'] ?: '-'); ?></td>
                <td><span class="tag <?php echo $item['status'] == 1 ? 'tag-green' : 'tag-red'; ?>"><?php echo $item['status'] == 1 ? '正常' : '禁用'; ?></span></td>
                <td><?php echo $item['expire_time'] ?: '永久'; ?></td>
                <td>
                    <button class="btn btn-sm btn-outline toggle-status" data-id="<?php echo $item['id']; ?>"><?php echo $item['status'] == 1 ? '禁用' : '启用'; ?></button>
                    <button class="btn btn-sm btn-danger delete" data-id="<?php echo $item['id']; ?>">删除</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo pagination($total, $page, 15, url('admin/license', ['keyword' => $keyword, 'product_id' => $productId, 'page' => '{page}'])); ?>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.toggle-status').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch('<?php echo url('admin/license/toggleStatus'); ?>', {method:'POST', body:new URLSearchParams({id})})
            .then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
document.querySelectorAll('.delete').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('确认删除？')) return;
        const id = this.dataset.id;
        fetch('<?php echo url('admin/license/delete'); ?>', {method:'POST', body:new URLSearchParams({id})})
            .then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
</script>
