<div class="breadcrumb">订单管理 / 订单详情</div>
<div class="page-header">
    <h2>订单详情</h2>
    <a href="<?php echo url('merchant/order'); ?>" class="btn btn-outline">返回列表</a>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
    <div class="card">
        <div class="section-title" style="margin-top: 0;">订单信息</div>
        <div style="line-height: 2; color: #475569;">
            <div><strong>订单编号：</strong><?php echo h($order['order_no']); ?></div>
            <div><strong>订单状态：</strong>
                <?php if ($order['status'] == 0): ?>待支付
                <?php elseif ($order['status'] == 1): ?>待发货
                <?php elseif ($order['status'] == 2): ?>已发货
                <?php elseif ($order['status'] == 3): ?>已完成
                <?php elseif ($order['status'] == 4): ?>退款中
                <?php else: ?>已关闭
                <?php endif; ?>
            </div>
            <div><strong>商品名称：</strong><?php echo h($order['goods_name']); ?></div>
            <div><strong>购买数量：</strong><?php echo $order['quantity']; ?></div>
            <div><strong>商品单价：</strong>¥ <?php echo $order['price']; ?></div>
            <div><strong>商品总价：</strong>¥ <?php echo $order['total_amount']; ?></div>
            <div><strong>应付金额：</strong><span style="color: #EF4444; font-weight: 600;">¥ <?php echo $order['pay_amount']; ?></span></div>
            <?php if ($order['coupon_amount'] > 0): ?>
            <div><strong>优惠券抵扣：</strong>-¥ <?php echo $order['coupon_amount']; ?> (<?php echo h($order['coupon_code']); ?>)</div>
            <?php endif; ?>
            <div><strong>支付方式：</strong><?php echo h($order['pay_channel'] ?: '-'); ?></div>
            <div><strong>支付时间：</strong><?php echo $order['pay_time'] ?: '-'; ?></div>
            <div><strong>联系方式：</strong><?php echo h($order['contact']); ?></div>
            <div><strong>下单时间：</strong><?php echo $order['create_time']; ?></div>
        </div>
    </div>

    <div class="card">
        <div class="section-title" style="margin-top: 0;">发货信息</div>
        <?php if ($order['status'] == 1): ?>
        <form id="deliverForm">
            <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
            <?php if ($order['goods_type'] == 1): ?>
            <div class="form-group">
                <label>发货内容（卡密类将自动从库存取 <?php echo $order['quantity']; ?> 条卡密）</label>
                <textarea name="content" rows="6" placeholder="如需手动覆盖可填写，否则留空自动取卡密"><?php echo h($order['deliver_content'] ?? ''); ?></textarea>
                <div style="color: #64748B; font-size: 12px; margin-top: 4px;">留空则系统自动从卡密库存中取货</div>
            </div>
            <?php else: ?>
            <div class="form-group">
                <label>发货内容</label>
                <textarea name="content" rows="6" placeholder="填写账号密码、充值凭证、下载链接等" required></textarea>
            </div>
            <?php endif; ?>
            <button type="submit" class="btn" id="deliverBtn">确认发货</button>
        </form>
        <?php elseif ($order['status'] >= 2 && $order['status'] != 5): ?>
        <div class="form-group">
            <label>发货内容</label>
            <textarea rows="8" readonly style="background: #F8FAFC;"><?php echo h($order['deliver_content'] ?: '暂无发货内容'); ?></textarea>
        </div>
        <div style="color: #64748B; font-size: 13px;">发货时间：<?php echo $order['deliver_time'] ?: '-'; ?></div>
        <?php else: ?>
        <div style="color: #64748B; text-align: center; padding: 40px 0;">当前订单无需发货或不可发货</div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($cards)): ?>
<div class="card" style="margin-top: 16px;">
    <div class="section-title" style="margin-top: 0;">已发卡密</div>
    <table>
        <tr>
            <th>卡密内容</th>
            <th>售出时间</th>
        </tr>
        <?php foreach ($cards as $card): ?>
        <tr>
            <td><code><?php echo h($card['content']); ?></code></td>
            <td><?php echo $card['sale_time']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php endif; ?>

<script>
document.getElementById('deliverForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('deliverBtn');
    btn.disabled = true;
    btn.textContent = '发货中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('merchant/order/deliver'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '确认发货';
    }
});
</script>
