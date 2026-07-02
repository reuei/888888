<div class="breadcrumb">订单管理 / 订单列表</div>
<div class="page-header">
    <h2>订单列表</h2>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('merchant/order'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="订单号 / 商品名称 / 联系方式">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>待支付</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>待发货</option>
            <option value="2" <?php echo $status === '2' ? 'selected' : ''; ?>>已发货</option>
            <option value="3" <?php echo $status === '3' ? 'selected' : ''; ?>>已完成</option>
            <option value="4" <?php echo $status === '4' ? 'selected' : ''; ?>>退款中</option>
            <option value="5" <?php echo $status === '5' ? 'selected' : ''; ?>>已关闭</option>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>订单号</th>
            <th>商品</th>
            <th>数量</th>
            <th>应付</th>
            <th>支付方式</th>
            <th>状态</th>
            <th>下单时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="8" style="text-align: center; color: #64748B; padding: 40px;">暂无订单</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo h($item['order_no']); ?></td>
            <td><?php echo h($item['goods_name']); ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td>¥ <?php echo $item['pay_amount']; ?></td>
            <td><?php echo h($item['pay_channel'] ?: '-'); ?></td>
            <td>
                <?php if ($item['status'] == 0): ?><span class="tag" style="background:#FEF3C7;color:#92400E;">待支付</span>
                <?php elseif ($item['status'] == 1): ?><span class="tag tag-blue">待发货</span>
                <?php elseif ($item['status'] == 2): ?><span class="tag tag-green">已发货</span>
                <?php elseif ($item['status'] == 3): ?><span class="tag tag-green">已完成</span>
                <?php elseif ($item['status'] == 4): ?><span class="tag tag-orange">退款中</span>
                <?php else: ?><span class="tag">已关闭</span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['create_time']; ?></td>
            <td>
                <a href="<?php echo url('merchant/order/detail', ['id' => $item['id']]); ?>" class="btn btn-sm btn-primary">详情</a>
                <?php if ($item['status'] == 1): ?>
                <a href="<?php echo url('merchant/order/detail', ['id' => $item['id']]); ?>" class="btn btn-sm btn-success">发货</a>
                <?php endif; ?>
                <?php if ($item['status'] == 0): ?>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="closeOrder(<?php echo $item['id']; ?>, '<?php echo h($item['order_no']); ?>')">关闭</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('merchant/order') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('merchant/order') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
async function closeOrder(id, orderNo) {
    if (!confirm('确认关闭订单 ' + orderNo + ' ？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('merchant/order/close'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
