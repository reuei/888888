<style>
.pay-card {
    max-width: 520px;
    margin: 0 auto;
}
.pay-amount {
    text-align: center;
    padding: 24px 0;
    border-bottom: 1px solid #E2E8F0;
    margin-bottom: 20px;
}
.pay-amount .label { color: #64748B; font-size: 13px; margin-bottom: 8px; }
.pay-amount .amount { font-size: 36px; color: #EF4444; font-weight: 700; }
.order-info { margin-bottom: 20px; }
.order-info .row { display: flex; justify-content: space-between; padding: 8px 0; color: #475569; }
.channel-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
.channel-item {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.channel-item:hover, .channel-item.selected {
    border-color: #2563EB;
    background: #EFF6FF;
}
.channel-item input { margin-right: 12px; }
</style>

<div class="card pay-card">
    <div class="pay-amount">
        <div class="label">应付金额</div>
        <div class="amount">¥<?php echo $order['pay_amount']; ?></div>
    </div>

    <div class="order-info">
        <div class="row"><span>订单编号</span><span><?php echo h($order['order_no']); ?></span></div>
        <div class="row"><span>商品名称</span><span><?php echo h($order['goods_name']); ?></span></div>
        <div class="row"><span>购买数量</span><span><?php echo $order['quantity']; ?></span></div>
        <div class="row"><span>联系方式</span><span><?php echo h($order['contact']); ?></span></div>
    </div>

    <div style="margin-bottom: 10px; font-weight: 500;">选择支付方式</div>
    <div class="channel-list" id="channelList">
        <?php foreach ($channels as $ch): ?>
        <label class="channel-item <?php echo ($channels[0]['code'] ?? '') === $ch['code'] ? 'selected' : ''; ?>">
            <input type="radio" name="channel" value="<?php echo h($ch['code']); ?>" <?php echo ($channels[0]['code'] ?? '') === $ch['code'] ? 'checked' : ''; ?>>
            <span><?php echo h($ch['name']); ?></span>
        </label>
        <?php endforeach; ?>
    </div>

    <button type="button" class="btn btn-lg btn-block" id="payBtn">确认支付</button>
    <div style="text-align: center; margin-top: 12px;">
        <a href="<?php echo url('index/order', ['no' => $order['order_no']]); ?>" style="color: #64748B; font-size: 13px;">返回订单详情</a>
    </div>
</div>

<script>
const channelItems = document.querySelectorAll('.channel-item');
channelItems.forEach(item => {
    item.addEventListener('click', () => {
        channelItems.forEach(i => i.classList.remove('selected'));
        item.classList.add('selected');
        item.querySelector('input').checked = true;
    });
});

document.getElementById('payBtn').addEventListener('click', async () => {
    const btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.textContent = '支付中...';
    const channel = document.querySelector('input[name="channel"]:checked').value;
    const form = new FormData();
    form.append('order_no', '<?php echo $order['order_no']; ?>');
    form.append('channel', channel);
    try {
        const res = await fetch('<?php echo url('index/doPay'); ?>', { method: 'POST', body: form });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0 && data.data.redirect) {
            location.href = data.data.redirect;
        }
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '确认支付';
    }
});
</script>
