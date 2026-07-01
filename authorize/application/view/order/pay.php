<div class="card" style="max-width: 500px; margin: 40px auto;">
    <h2 style="margin-bottom: 20px;">订单支付</h2>
    <div style="background:#F8FAFC; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
        <p><strong>订单号：</strong><?php echo h($order['order_no']); ?></p>
        <p><strong>商品：</strong><?php echo h($order['item_name']); ?></p>
        <p style="margin-bottom:0; color:#EF4444; font-size: 20px; font-weight: 600;">
            应付：<?php echo format_price($order['pay_amount']); ?>
        </p>
    </div>
    <p style="margin-bottom: 16px; color: #475569;">当前余额：<strong><?php echo format_price($user['balance']); ?></strong></p>
    <button id="payBtn" class="btn btn-block btn-lg">立即支付</button>
</div>

<script>
document.getElementById('payBtn').addEventListener('click', function() {
    if (!confirm('确认使用余额支付？')) return;
    const btn = this;
    btn.disabled = true;
    btn.textContent = '支付中...';
    fetch('<?php echo url('order/doPay'); ?>', {
        method: 'POST',
        body: new URLSearchParams({order_no: '<?php echo $order['order_no']; ?>'})
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0 && res.data.redirect) {
            location.href = res.data.redirect;
        } else {
            btn.disabled = false;
            btn.textContent = '立即支付';
        }
    });
});
</script>
