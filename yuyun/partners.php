<?php
require __DIR__ . '/includes/config.php';
if (template_include('partners.php')) exit;
$pageTitle = L('partners.title', '合作伙伴');
require __DIR__ . '/includes/header.php';
$db = getDb();
$partners = $db->query('SELECT * FROM partners WHERE is_active=1 ORDER BY sort_order,id')->fetchAll(PDO::FETCH_ASSOC);
?>
<section class="page-banner">
    <div class="container">
        <div class="ip-illustration ip-shield page-intro-illustration"></div>
        <h1><?php echo L('partners.title', '合作伙伴') ?></h1>
        <p><?php echo L('partners.subtitle', '携手共建开放共赢的云生态') ?></p>
    </div>
</section>
<section class="section bg-white">
    <div class="container">
        <div class="page-intro">
            <p>我们与全球领先的云厂商、网络服务商及技术社区深度合作，整合优势资源，为客户提供更稳定、更安全、更具性价比的解决方案。</p>
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
                    <a href="<?php echo e($p['link']) ?>" target="_blank" class="more">访问官网 <i class="iconfont icon-arrow-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
