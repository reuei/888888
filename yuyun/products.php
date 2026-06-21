<?php
require __DIR__ . '/includes/config.php';
if (template_include('products.php')) exit;
$pageTitle = '产品介绍';
require __DIR__ . '/includes/header.php';
$db = getDb();
$products = $db->query('SELECT * FROM products WHERE is_active=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="page-banner">
    <div class="container">
        <h1>产品介绍</h1>
        <p>全栈云产品与数字化解决方案</p>
    </div>
</section>
<section class="section bg-white">
    <div class="container">
        <div class="card-grid">
            <?php foreach ($products as $prod): ?>
            <div class="product-card" onclick="openProductModal('<?php echo e($prod['name']) ?>','<?php echo e($prod['detail'] ?: $prod['summary']) ?>')">
                <?php if ($prod['image']): ?>
                    <img src="<?php echo e($prod['image']) ?>" alt="<?php echo e($prod['name']) ?>" style="width:100%;height:160px;object-fit:cover;border-radius:6px;margin-bottom:16px">
                <?php endif; ?>
                <div class="icon"><i class="fa-solid <?php echo e($prod['icon'] ?: 'fa-cube') ?>"></i></div>
                <h3><?php echo e($prod['name']) ?></h3>
                <p><?php echo e($prod['summary']) ?></p>
                <span class="more">了解详情 <i class="fa-solid fa-chevron-right"></i></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<div class="modal-overlay" id="productModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalProductTitle">产品详情</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body" id="modalProductBody"></div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
