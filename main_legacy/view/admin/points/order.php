<div class="page-header">
    <h2>积分兑换订单</h2>
</div>

<div class="search-bar">
    <form method="get" action="<?php echo url('admin/points/order'); ?>" style="display:flex; gap:12px; flex:1;">
        <select name="status" style="padding:8px 12px; border:1px solid #CBD5E1; border-radius:6px;">
            <option value="">全部状态</option>
            <?php foreach ($statusMap as $k => $v): ?>
            <option value="<?php echo $k; ?>" <?php echo $status === (string)$k ? 'selected' : ''; ?>><?php echo $v; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm">筛选</button>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>单号</th>
                <th>用户</th>
                <th>商品</th>
                <th>积分</th>
                <th>联系方式</th>
                <th>状态</th>
                <th>时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo h($item['order_no']); ?></td>
                <td><?php echo h($item['nickname'] ?: $item['mobile']); ?></td>
                <td><?php echo h($item['title']); ?></td>
                <td><?php echo $item['points']; ?></td>
                <td><?php echo h($item['contact']); ?></td>
                <td>
                    <?php
                    $color = $item['status'] == 1 ? 'tag-green' : ($item['status'] == 2 ? 'tag-orange' : 'tag-blue');
                    ?>
                    <span class="tag <?php echo $color; ?>"><?php echo $statusMap[$item['status']] ?? '未知'; ?></span>
                </td>
                <td><?php echo $item['create_time']; ?></td>
                <td>
                    <?php if ($item['status'] == 0): ?>
                    <button type="button" class="btn btn-sm btn-success" onclick="handleOrder(<?php echo $item['id']; ?>, 1)">发放</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="handleOrder(<?php echo $item['id']; ?>, 2)">取消</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination" style="display:flex; justify-content:center; gap:8px; margin-top:16px;">
    <a href="<?php echo url('admin/points/order') . '?page=' . ($page - 1) . '&status=' . $status; ?>" class="btn btn-sm btn-outline <?php echo $page <= 1 ? 'disabled' : ''; ?>">上一页</a>
    <span style="padding:5px 10px; color:#64748B;"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
    <a href="<?php echo url('admin/points/order') . '?page=' . ($page + 1) . '&status=' . $status; ?>" class="btn btn-sm btn-outline <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">下一页</a>
</div>
<?php endif; ?>

<script>
function handleOrder(id, status) {
    let deliver = '';
    if (status === 1) {
        deliver = prompt('请输入发放内容（卡密/优惠券码/物流单号等）：') || '';
    }
    if (!confirm('确认处理该兑换订单？')) return;
    fetch('<?php echo url("admin/points/handleOrder"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&status=' + status + '&deliver_content=' + encodeURIComponent(deliver)
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) location.reload();
    });
}
</script>
