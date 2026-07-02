<div class="card" style="max-width: 600px; margin: 40px auto;">
    <h2 style="margin-bottom: 20px;">确认订单</h2>
    <div style="background:#F8FAFC; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
        <p><strong>商品：</strong><?php echo h($item['name']); ?></p>
        <p><strong>单价：</strong><?php echo format_price($item['price']); ?></p>
        <p><strong>数量：</strong><?php echo $quantity; ?></p>
        <p style="margin-bottom:0; color:#EF4444; font-size: 18px; font-weight: 600;">
            应付总额：<?php echo format_price($totalAmount); ?>
        </p>
    </div>

    <form id="orderForm">
        <input type="hidden" name="type" value="<?php echo $itemType; ?>">
        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
        <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">
        <button type="submit" class="btn btn-block btn-lg">提交订单</button>
    </form>
</div>

<script>
document.getElementById('orderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    fetch('<?php echo url('order/doCreate'); ?>', {
        method: 'POST',
        body: new FormData(form)
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0 && res.data.redirect) {
            location.href = res.data.redirect;
        }
    });
});
</script>
