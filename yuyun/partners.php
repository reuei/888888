<?php
require __DIR__ . '/includes/config.php';
if (template_include('partners.php')) exit;
$pageTitle = '合作伙伴';
require __DIR__ . '/includes/header.php';
$db = getDb();
$partners = $db->query('SELECT * FROM partners WHERE is_active=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="page-banner">
    <div class="container">
        <h1>合作伙伴</h1>
        <p>携手行业领袖，共建生态未来</p>
    </div>
</section>
<section class="section bg-white">
    <div class="container">
        <div class="card-grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
            <?php foreach ($partners as $p): ?>
            <div class="product-card" style="text-align:center">
                <?php if ($p['logo']): ?>
                    <img src="<?php echo e($p['logo']) ?>" alt="<?php echo e($p['name']) ?>" style="max-height:60px;margin-bottom:16px">
                <?php else: ?>
                    <div class="icon" style="margin:0 auto 16px"><i class="fa-solid fa-handshake"></i></div>
                <?php endif; ?>
                <h3><?php echo e($p['name']) ?></h3>
                <?php if ($p['link']): ?>
                    <a href="<?php echo e($p['link']) ?>" target="_blank" class="more">访问官网 <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
