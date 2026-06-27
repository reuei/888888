<?php
require __DIR__ . '/includes/config.php';
if (template_include('partners.php')) exit;
$pageTitle = __('page_partners');
require __DIR__ . '/includes/header.php';
$db = getDb();
$partners = $db->query('SELECT * FROM partners WHERE is_active=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="page-banner">
    <div class="container">
        <h1><?php echo __('partners_title_page') ?></h1>
        <p><?php echo __('partners_sub') ?></p>
    </div>
</section>
<section class="section bg-white">
    <div class="container">
        <div class="text-center">
            <div class="ip-illustration" style="width:120px;height:120px;margin-bottom:24px"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="16" cy="14" r="7"/><circle cx="32" cy="14" r="7"/><path d="M4 44c0-10 6-16 12-16s12 6 12 16"/><path d="M20 44c0-10 6-16 12-16s12 6 12 16"/><path d="M24 28l-4-4 4-4"/><path d="M24 28l4-4-4-4"/></svg></div>
        </div>
        <div class="card-grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
            <?php foreach ($partners as $p): ?>
            <div class="product-card" style="text-align:center">
                <?php if ($p['logo']): ?>
                    <img src="<?php echo e($p['logo']) ?>" alt="<?php echo e($p['name']) ?>" style="max-height:60px;margin-bottom:16px">
                <?php else: ?>
                    <div class="icon" style="margin:0 auto 16px"><i class="iconfont icon-handshake icon-2xl"></i></div>
                <?php endif; ?>
                <h3><?php echo e($p['name']) ?></h3>
                <?php if ($p['link']): ?>
                    <a href="<?php echo e($p['link']) ?>" target="_blank" class="more"><?php echo __('visit_official') ?> <i class="iconfont icon-arrow-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
