<section class="section">
    <div class="goods-detail">
        <div class="goods-cover goods-cover-lg">
            <div class="goods-cover-img"></div>
        </div>
        <div class="goods-detail-info">
            <h1 class="goods-detail-name"><?= h($good['name'] ?? '商品') ?></h1>
            <div class="goods-detail-price">
                <span class="price-symbol">¥</span>
                <span class="price-num"><?= format_money($good['price'] ?? 0) ?></span>
                <?php if (!empty($good['original_price'])): ?>
                <span class="price-original">¥<?= format_money($good['original_price']) ?></span>
                <?php endif; ?>
            </div>
            <div class="goods-detail-meta">
                <div class="meta-row"><span>销量</span><strong><?= (int) ($good['sold'] ?? 0) ?></strong></div>
                <div class="meta-row"><span>库存</span><strong><?= (int) ($good['stock'] ?? 999) ?></strong></div>
                <div class="meta-row"><span>发货</span><strong>自动发货</strong></div>
            </div>
            <div class="goods-detail-actions">
                <button class="btn btn-primary btn-lg">立即购买</button>
                <button class="btn btn-line btn-lg">加入购物车</button>
            </div>
            <div class="goods-detail-tips">
                <p>· 本商品为虚拟商品，发货后不支持退换</p>
                <p>· 购买后请妥善保管卡密信息</p>
                <p>· 如有问题请联系在线客服</p>
            </div>
        </div>
    </div>
</section>
