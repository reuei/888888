<?php
require __DIR__ . '/includes/config.php';
if (template_include('products.php')) exit;
$pageTitle = L('products.title', '产品介绍');
require __DIR__ . '/includes/header.php';
$db = getDb();
$products = $db->query('SELECT * FROM products WHERE is_active=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="page-banner">
    <div class="container">
        <div class="ip-illustration ip-cloud page-intro-illustration"></div>
        <h1><?php echo L('products.title', '产品介绍') ?></h1>
        <p><?php echo L('products.subtitle', '安全、稳定、高效的云计算与数字化服务') ?></p>
    </div>
</section>
<section class="section bg-white">
    <div class="container">
        <div class="page-intro">
            <p>我们提供覆盖云计算、网络安全、企业服务的全栈产品，支持弹性扩展与全球部署，帮助客户降低 IT 成本、提升业务连续性。</p>
        </div>
        <div class="card-grid">
            <?php foreach ($products as $prod):
                $iconClass = map_fa_to_iconfont($prod['icon'] ?: 'fa-cube');
            ?>
            <div class="product-card" onclick="openProductModal('<?php echo e($prod['name']) ?>','<?php echo e($prod['detail'] ?: $prod['summary']) ?>')">
                <?php if ($prod['image']): ?>
                    <img src="<?php echo e($prod['image']) ?>" alt="<?php echo e($prod['name']) ?>" style="width:100%;height:160px;object-fit:cover;border-radius:6px;margin-bottom:16px">
                <?php endif; ?>
                <div class="icon"><i class="iconfont <?php echo e($iconClass) ?> icon-2xl"></i></div>
                <h3><?php echo e($prod['name']) ?></h3>
                <p><?php echo e($prod['summary']) ?></p>
                <span class="more"><?php echo L('btn.learn_more', '了解详情') ?> <i class="iconfont icon-chevron-right"></i></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<div class="modal-overlay" id="productModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalProductTitle">产品详情</h3>
            <button class="modal-close"><i class="iconfont icon-close"></i></button>
        </div>
        <div class="modal-body" id="modalProductBody"></div>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
