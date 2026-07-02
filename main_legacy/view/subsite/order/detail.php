<div class="breadcrumb">订单管理 / <a href="<?php echo url('subsite/order'); ?>">分站订单列表</a> / 订单详情</div>
<div class="page-header">
    <h2>分站订单详情</h2>
    <div>
        <?php if ($order['status'] == 1): ?>
        <a href="javascript:;" class="btn btn-success" onclick="deliver(<?php echo $order['id']; ?>)">标记发货</a>
        <a href="javascript:;" class="btn btn-warning" style="margin-left: 8px;" onclick="refund(<?php echo $order['id']; ?>)">退款</a>
        <?php endif; ?>
        <?php if ($order['status'] == 0): ?>
        <a href="javascript:;" class="btn btn-danger" onclick="closeOrder(<?php echo $order['id']; ?>)">关闭订单</a>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;">订单信息</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 14px;">
        <div><span style="color: #64748B;">订单ID：</span><?php echo $order['id']; ?></div>
        <div><span style="color: #64748B;">订单号：</span><?php echo h($order['order_no']); ?></div>
        <div><span style="color: #64748B;">商品名称：</span><?php echo h($order['goods_name']); ?></div>
        <div><span style="color: #64748B;">数量：</span><?php echo $order['quantity']; ?></div>
        <div><span style="color: #64748B;">单价：</span>¥ <?php echo $order['price']; ?></div>
        <div><span style="color: #64748B;">应付金额：</span>¥ <?php echo $order['total_amount']; ?></div>
        <div><span style="color: #64748B;">实付金额：</span>¥ <?php echo $order['pay_amount']; ?></div>
        <div><span style="color: #64748B;">支付渠道：</span><?php echo h($order['pay_channel'] ?: '-'); ?></div>
        <div><span style="color: #64748B;">支付时间：</span><?php echo $order['pay_time'] ?: '-'; ?></div>
        <div><span style="color: #64748B;">所属商户：</span><?php echo h($order['shop_name'] ?? '-'); ?></div>
        <div><span style="color: #64748B;">商户手机：</span><?php echo h($order['merchant_mobile'] ?: '-'); ?></div>
        <div><span style="color: #64748B;">所属分站：</span><?php echo h($order['subsite_name'] ?? '-'); ?></div>
        <div><span style="color: #64748B;">买家IP：</span><?php echo h($order['client_ip'] ?: '-'); ?></div>
        <div><span style="color: #64748B;">下单时间：</span><?php echo $order['create_time']; ?></div>
        <div><span style="color: #64748B;">状态：</span>
            <?php
            $statusColors = [0 => 'tag-orange', 1 => 'tag-blue', 2 => 'tag-green', 3 => 'tag-green', 4 => 'tag-orange', 5 => 'tag'];
            $color = $statusColors[$order['status']] ?? 'tag';
            ?>
            <span class="tag <?php echo $color; ?>"><?php echo $statusMap[$order['status']] ?? '未知'; ?></span>
        </div>
        <div><span style="color: #64748B;">风控：</span><?php echo $order['risk_flag'] ? '<span class="tag tag-red">风控</span>' : '<span class="tag">正常</span>'; ?></div>
    </div>
</div>

<?php if (!empty($cards)): ?>
<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;">卡密信息</h3>
    <table>
        <tr>
            <th>卡密内容</th>
            <th>备注</th>
        </tr>
        <?php foreach ($cards as $c): ?>
        <tr>
            <td><code style="font-family: monospace;"><?php echo h($c['content']); ?></code></td>
            <td><?php echo h($c['remark'] ?: '-'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

<?php if ($complaint): ?>
<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;">投诉信息</h3>
    <div style="font-size: 14px; line-height: 1.8;">
        <div><span style="color: #64748B;">投诉类型：</span><?php echo h($complaint['type']); ?></div>
        <div><span style="color: #64748B;">投诉内容：</span><?php echo h($complaint['content']); ?></div>
        <div><span style="color: #64748B;">投诉状态：</span><?php echo $complaint['status'] ? '<span class="tag tag-green">已处理</span>' : '<span class="tag tag-orange">待处理</span>'; ?></div>
        <?php if ($complaint['status']): ?>
        <div><span style="color: #64748B;">处理结果：</span><?php echo h($complaint['result']); ?></div>
        <div><span style="color: #64748B;">处理备注：</span><?php echo h($complaint['remark'] ?: '-'); ?></div>
        <div><span style="color: #64748B;">处理时间：</span><?php echo $complaint['handle_time']; ?></div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<script>
async function deliver(id) {
    if (!confirm('确认标记该订单为已发货？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('subsite/order/deliver'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function refund(id) {
    const reason = prompt('请输入退款原因：');
    if (!reason) return;
    if (!confirm('确认对该订单发起退款？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('reason', reason);
    const res = await fetch('<?php echo url('subsite/order/refund'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function closeOrder(id) {
    if (!confirm('确认关闭该订单？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('subsite/order/close'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
