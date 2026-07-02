<div class="page-header">
    <h2>订单详情</h2>
    <a href="<?php echo url('admin/order'); ?>" class="btn btn-outline btn-sm">返回</a>
</div>

<div class="card">
    <p><strong>订单号：</strong><?php echo h($order['order_no']); ?></p>
    <p><strong>用户：</strong><?php echo h($order['username'] ?: $order['nickname'] ?: '-'); ?></p>
    <p><strong>商品：</strong><?php echo h($order['item_name']); ?></p>
    <p><strong>类型：</strong><?php echo $order['item_type'] === 'product' ? '授权产品' : '插件'; ?></p>
    <p><strong>单价：</strong><?php echo format_price($order['price']); ?></p>
    <p><strong>数量：</strong><?php echo $order['quantity']; ?></p>
    <p><strong>应付总额：</strong><?php echo format_price($order['pay_amount']); ?></p>
    <p><strong>支付方式：</strong><?php echo h($order['pay_channel']); ?></p>
    <p><strong>状态：</strong><?php echo $order['status'] == 1 ? '已支付' : ($order['status'] == 0 ? '待支付' : '已取消'); ?></p>
    <p><strong>创建时间：</strong><?php echo $order['create_time']; ?></p>
    <?php if ($order['pay_time']): ?><p><strong>支付时间：</strong><?php echo $order['pay_time']; ?></p><?php endif; ?>
</div>

<?php if (!empty($licenses)): ?>
<div class="card">
    <div class="section-title">已发放授权</div>
    <table>
        <thead>
            <tr><th>授权码</th><th>类型</th><th>过期时间</th></tr>
        </thead>
        <tbody>
            <?php foreach ($licenses as $l): ?>
            <tr>
                <td><?php echo h($l['auth_code']); ?></td>
                <td><?php echo $l['license_type'] === 'domain' ? '域名授权' : '授权码'; ?></td>
                <td><?php echo $l['expire_time'] ?: '永久'; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
