<?php
require __DIR__ . '/includes/config.php';
if (template_include('about.php')) exit;
$pageTitle = '关于我们';
require __DIR__ . '/includes/header.php';
?>
<section class="page-banner">
    <div class="container">
        <h1>关于我们</h1>
        <p>了解语云科技的使命与价值观</p>
    </div>
</section>
<section class="section bg-white">
    <div class="container">
        <div class="card-grid" style="grid-template-columns:1fr 1.3fr">
            <div>
                <h2 style="font-size:26px;margin-bottom:14px"><?php echo e(setting('company_name','语云科技（美国）有限公司')) ?></h2>
                <p style="color:var(--text-2);line-height:1.8;margin-bottom:24px"><?php echo nl2br(e(setting('company_intro','语云科技专注于为全球企业与开发者提供安全、稳定、高效的云计算、网络加速与数字化解决方案。'))) ?></p>
                <ul class="info-list">
                    <li><i class="fa-solid fa-location-dot"></i> <?php echo e(setting('company_address','中国北京市朝阳区')) ?></li>
                    <li><i class="fa-solid fa-phone"></i> <?php echo e(setting('company_phone','400-800-8451')) ?></li>
                    <li><i class="fa-solid fa-users"></i> <a href="<?php echo e(setting('company_group','#')) ?>" target="_blank">官方群聊</a></li>
                    <li><i class="fa-solid fa-globe"></i> <a href="<?php echo e(setting('international_url','https://cloud.loveym.cloud')) ?>" target="_blank">国际版官网</a></li>
                </ul>
            </div>
            <div>
                <iframe class="map-embed" src="<?php echo e(setting('company_map_url','https://map.baidu.com/search/北京市')) ?>" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
