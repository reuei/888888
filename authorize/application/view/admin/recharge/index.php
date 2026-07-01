<div class="page-header">
    <h2>充值申请</h2>
</div>

<div class="card">
    <form method="get" action="<?php echo url('admin/recharge'); ?>" class="search-bar">
        <select name="status">
            <option value="">全部状态</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>待处理</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>已到账</option>
            <option value="2" <?php echo $status === '2' ? 'selected' : ''; ?>>已拒绝</option>
        </select>
        <button type="submit" class="btn btn-sm">筛选</button>
    </form>

    <?php if (empty($list)): ?>
    <div class="empty-tip">暂无充值申请</div>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>ID</th><th>用户</th><th>金额</th><th>渠道</th><th>备注</th><th>状态</th><th>时间</th><th>操作</th></tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo h($item['username'] ?: $item['nickname'] ?: '-'); ?></td>
                <td><?php echo format_price($item['amount']); ?></td>
                <td><?php echo h($item['pay_channel']); ?></td>
                <td><?php echo h($item['pay_remark']); ?></td>
                <td>
                    <?php if ($item['status'] == 0): ?><span class="tag tag-orange">待处理</span>
                    <?php elseif ($item['status'] == 1): ?><span class="tag tag-green">已到账</span>
                    <?php else: ?><span class="tag tag-red">已拒绝</span><?php endif; ?>
                </td>
                <td><?php echo $item['create_time']; ?></td>
                <td>
                    <?php if ($item['status'] == 0): ?>
                    <button class="btn btn-sm approve" data-id="<?php echo $item['id']; ?>">到账</button>
                    <button class="btn btn-sm btn-outline reject" data-id="<?php echo $item['id']; ?>">拒绝</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo pagination($total, $page, 15, url('admin/recharge', ['status' => $status, 'page' => '{page}'])); ?>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.approve').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('确认已到账？')) return;
        const id = this.dataset.id;
        fetch('<?php echo url('admin/recharge/approve'); ?>', {method:'POST', body:new URLSearchParams({id})})
            .then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
document.querySelectorAll('.reject').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('确认拒绝？')) return;
        const id = this.dataset.id;
        fetch('<?php echo url('admin/recharge/reject'); ?>', {method:'POST', body:new URLSearchParams({id})})
            .then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
</script>
