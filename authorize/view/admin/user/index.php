<div class="page-header">
    <h2>用户管理</h2>
</div>

<div class="card">
    <form method="get" action="<?php echo url('admin/user'); ?>" class="search-bar">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="账号/昵称/邮箱">
        <button type="submit" class="btn btn-sm">搜索</button>
    </form>

    <?php if (empty($list)): ?>
    <div class="empty-tip">暂无用户</div>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>ID</th><th>账号</th><th>昵称</th><th>邮箱</th><th>余额</th><th>状态</th><th>注册时间</th><th>操作</th></tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo h($item['username']); ?></td>
                <td><?php echo h($item['nickname']); ?></td>
                <td><?php echo h($item['email']); ?></td>
                <td><?php echo format_price($item['balance']); ?></td>
                <td><span class="tag <?php echo $item['status'] == 1 ? 'tag-green' : 'tag-red'; ?>"><?php echo $item['status'] == 1 ? '正常' : '禁用'; ?></span></td>
                <td><?php echo $item['create_time']; ?></td>
                <td>
                    <a href="<?php echo url('admin/user/edit', ['id' => $item['id']]); ?>" class="btn btn-sm">编辑</a>
                    <button class="btn btn-sm btn-outline toggle-status" data-id="<?php echo $item['id']; ?>"><?php echo $item['status'] == 1 ? '禁用' : '启用'; ?></button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo pagination($total, $page, 15, url('admin/user', ['keyword' => $keyword, 'page' => '{page}'])); ?>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.toggle-status').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        fetch('<?php echo url('admin/user/toggleStatus'); ?>', {method:'POST', body:new URLSearchParams({id})})
            .then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
</script>
