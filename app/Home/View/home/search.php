<section class="section">
    <div class="section-head">
        <h2 class="section-title">搜索：<?= h($keyword) ?></h2>
        <p class="section-sub">共 <?= count($goods) ?> 个结果</p>
    </div>
    <div class="goods-grid">
        <?php foreach ($goods as $g): ?>
        <a href="/goods/<?= (int) $g['id'] ?>" class="goods-card">
            <div class="goods-cover"><div class="goods-cover-img"></div></div>
            <div class="goods-body">
                <div class="goods-name"><?= h($g['name']) ?></div>
                <div class="goods-foot">
                    <div class="goods-price">¥<?= format_money($g['price']) ?></div>
                    <div class="goods-sold">已售 <?= (int) $g['sold'] ?></div>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>
