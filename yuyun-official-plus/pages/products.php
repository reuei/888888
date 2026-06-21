<?php
$products = dbActive('products', 'sort_order', 'ASC');
?>
<section class="page-banner">
    <div class="container">
        <h1>产品介绍</h1>
        <p>全栈式云计算产品，满足企业多样化需求</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header reveal">
            <h2>我们的产品</h2>
            <p>点击查看产品详情，获取专属解决方案</p>
            <div class="section-title-line"></div>
        </div>
        <div class="card-grid">
            <?php foreach ($products as $p): ?>
                <div class="card reveal" data-product-detail data-title="<?php echo yy_e($p['title']); ?>" data-detail="<?php echo yy_e($p['detail'] ?: $p['summary']); ?>" data-image="<?php echo yy_e($p['image']); ?>">
                    <?php if ($p['image'] && file_exists(YUYUN_ROOT . '/' . $p['image'])): ?>
                        <img src="<?php echo yy_e($p['image']); ?>" alt="<?php echo yy_e($p['title']); ?>" style="width:100%;height:180px;object-fit:cover;border-radius:8px;margin-bottom:16px;">
                    <?php else: ?>
                        <div class="card-icon"><i class="fa-solid <?php echo yy_e($p['icon'] ?: 'fa-cube'); ?>"></i></div>
                    <?php endif; ?>
                    <h3><?php echo yy_e($p['title']); ?></h3>
                    <p><?php echo yy_e($p['summary']); ?></p>
                    <span class="btn btn-outline" style="margin-top:12px;width:100%;">查看详情</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
