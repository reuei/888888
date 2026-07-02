<div class="container">
    <div class="card" style="margin-bottom: 16px;">
        <div style="display:flex; gap:16px;">
            <?php if ($goods['image']): ?>
            <img src="<?php echo base_url($goods['image']); ?>" style="width:160px; height:160px; object-fit:cover; border-radius:8px;">
            <?php endif; ?>
            <div style="flex:1;">
                <h2 style="font-size: 20px; margin-bottom: 8px;"><?php echo h($goods['title']); ?></h2>
                <div style="color:#EF4444; font-size: 24px; font-weight: 700; margin-bottom: 8px;"><?php echo $goods['points']; ?> 积分</div>
                <div style="color:#64748B; font-size: 13px; margin-bottom: 16px;">库存 <?php echo $goods['stock']; ?> · 已兑 <?php echo $goods['sold']; ?></div>
                <?php if ($user): ?>
                <div style="font-size: 13px; color:#64748B; margin-bottom: 12px;">我的积分：<?php echo number_format($user['points']); ?></div>
                <div style="display:flex; gap:12px; align-items:center;">
                    <input type="number" id="quantity" value="1" min="1" max="<?php echo $goods['stock']; ?>" style="width:80px; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                    <input type="text" id="contactInfo" placeholder="联系方式/收货地址" style="flex:1; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                    <button type="button" class="btn" onclick="redeem()">立即兑换</button>
                </div>
                <?php else: ?>
                <a href="<?php echo url('index/user'); ?>" class="btn">请先登录</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 style="font-size: 16px; margin-bottom: 12px;">商品详情</h3>
        <div style="color:#475569; line-height:1.6;"><?php echo nl2br(h($goods['description'])); ?></div>
    </div>
</div>

<script>
function redeem() {
    const quantity = document.getElementById('quantity').value;
    const contact = document.getElementById('contactInfo').value;
    if (!confirm('确认使用 <?php echo $goods['points']; ?> 积分兑换该商品？')) return;
    fetch('<?php echo url("index/pointsRedeem"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'goods_id=<?php echo $goods['id']; ?>&quantity=' + quantity + '&contact=' + encodeURIComponent(contact)
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) location.href = '<?php echo url('index/pointsCenter'); ?>';
    });
}
</script>
