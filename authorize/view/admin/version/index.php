<div class="page-header">
    <h2>版本更新包</h2>
    <a href="<?php echo url('admin/version/add'); ?>" class="btn btn-sm">新增版本</a>
</div>

<div class="card">
    <?php if (empty($list)): ?>
    <div class="empty-tip">暂无版本</div>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>ID</th><th>版本号</th><th>MD5</th><th>大小</th><th>发布日期</th><th>强制更新</th><th>最新</th><th>操作</th></tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo h($item['version']); ?></td>
                <td><?php echo h($item['file_md5']); ?></td>
                <td><?php echo format_size($item['file_size']); ?></td>
                <td><?php echo $item['release_date']; ?></td>
                <td><?php echo $item['force_update'] ? '是' : '否'; ?></td>
                <td><?php echo $item['is_latest'] ? '<span class="tag tag-green">是</span>' : '否'; ?></td>
                <td>
                    <a href="<?php echo url('admin/version/edit', ['id' => $item['id']]); ?>" class="btn btn-sm">编辑</a>
                    <button class="btn btn-sm btn-danger delete" data-id="<?php echo $item['id']; ?>">删除</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo pagination($total, $page, 15, url('admin/version', ['page' => '{page}'])); ?>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.delete').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('确认删除？')) return;
        const id = this.dataset.id;
        fetch('<?php echo url('admin/version/delete'); ?>', {method:'POST', body:new URLSearchParams({id})})
            .then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
</script>
