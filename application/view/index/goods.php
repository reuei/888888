<style>
.goods-detail {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 24px;
}
.goods-cover-large {
    width: 100%;
    height: 240px;
    background: #F1F5F9;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94A3B8;
    overflow: hidden;
}
.goods-cover-large img { width: 100%; height: 100%; object-fit: cover; }
.goods-title { font-size: 22px; font-weight: 600; margin-bottom: 12px; }
.goods-price-large { font-size: 28px; color: #EF4444; font-weight: 700; margin-bottom: 16px; }
.goods-tags { margin-bottom: 16px; }
.goods-desc {
    background: #F8FAFC;
    border-radius: 8px;
    padding: 16px;
    margin-top: 16px;
    min-height: 120px;
    color: #475569;
}
.buy-form {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #E2E8F0;
}
.quantity-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}
.quantity-row input {
    width: 80px;
    text-align: center;
    padding: 8px;
    border: 1px solid #CBD5E1;
    border-radius: 6px;
}
@media (max-width: 768px) {
    .goods-detail { grid-template-columns: 1fr; }
}
</style>

<div class="card goods-detail">
    <div>
        <div class="goods-cover-large">
            <?php if ($goods['cover']): ?>
            <img src="<?php echo h($goods['cover']); ?>" alt="<?php echo h($goods['name']); ?>">
            <?php else: ?>
            暂无图片
            <?php endif; ?>
        </div>
    </div>
    <div>
        <div class="goods-title"><?php echo h($goods['name']); ?></div>
        <div class="goods-price-large">¥<?php echo $goods['price']; ?></div>
        <div class="goods-tags">
            <span class="tag tag-blue"><?php echo h($goods['category_name'] ?? '默认分类'); ?></span>
            <span class="tag tag-green">库存 <?php echo $goods['stock']; ?></span>
            <span class="tag">已售 <?php echo $goods['sold']; ?></span>
            <span class="tag">店铺：<?php echo h($goods['shop_name'] ?? '官方'); ?></span>
        </div>

        <div class="goods-desc">
            <?php echo nl2br(h($goods['content'] ?: '暂无商品说明')); ?>
        </div>

        <form class="buy-form" id="buyForm">
            <input type="hidden" name="goods_id" value="<?php echo $goods['id']; ?>">
            <div class="quantity-row">
                <label style="font-weight:500;">购买数量</label>
                <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $goods['stock']; ?>">
            </div>
            <div class="form-group">
                <label>联系方式（用于查询订单）</label>
                <input type="text" name="contact" id="contact" placeholder="邮箱 / QQ / 手机号" required>
            </div>
            <button type="submit" class="btn btn-lg btn-block" id="buyBtn">立即购买</button>
        </form>
    </div>
</div>

<?php if (!empty($goodsBottom)): ?>
<div class="section-title" style="margin-top: 24px;">
    <span>精选推荐</span>
</div>
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 12px;">
    <?php foreach ($goodsBottom as $ad): ?>
    <a href="<?php echo h($ad['link'] ?: 'javascript:;'); ?>" target="_blank" style="border-radius: 8px; overflow: hidden; border: 1px solid #E2E8F0; display: block;">
        <img src="<?php echo h($ad['image']); ?>" alt="<?php echo h($ad['title']); ?>" style="width: 100%; height: 120px; object-fit: cover; display: block;">
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php if (!empty($recommend)): ?>
<div class="section-title" style="margin-top: 24px;">
    <span>同类推荐</span>
</div>
<div class="grid">
    <?php foreach ($recommend as $item): ?>
    <div class="goods-card">
        <a href="<?php echo url('index/goods', ['id' => $item['id']]); ?>">
            <div class="goods-cover">
                <?php if ($item['cover']): ?>
                <img src="<?php echo h($item['cover']); ?>" alt="<?php echo h($item['name']); ?>">
                <?php else: ?>
                暂无图片
                <?php endif; ?>
            </div>
            <div class="goods-info">
                <div class="goods-name"><?php echo h($item['name']); ?></div>
                <div class="goods-meta">
                    <span class="goods-price">¥<?php echo $item['price']; ?></span>
                    <span class="goods-sold">已售 <?php echo $item['sold']; ?></span>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<script>
document.getElementById('buyForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('buyBtn');
    btn.disabled = true;
    btn.textContent = '提交中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('index/buy'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0 && data.data.redirect) {
            location.href = data.data.redirect;
        }
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '立即购买';
    }
});
</script>
