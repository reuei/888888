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
        <div class="card-grid" style="grid-template-columns:1fr 1.3fr;align-items:center">
            <div class="text-center">
                <div class="ip-illustration"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="24" cy="14" r="8"/><path d="M6 44c0-12 8-18 18-18s18 6 18 18"/><circle cx="40" cy="20" r="5"/><path d="M32 44c0-7 4-11 9-11s9 4 9 11"/></svg></div>
                <h2 style="font-size:26px;margin-bottom:14px"><?php echo e(setting('company_name','语云科技（美国）有限公司')) ?></h2>
                <p style="color:var(--text-2);line-height:1.8"><?php echo nl2br(e(setting('company_intro','语云科技专注于为全球企业与开发者提供安全、稳定、高效的云计算、网络加速与数字化解决方案。'))) ?></p>
            </div>
            <div>
                <ul class="info-list">
                    <li><i class="iconfont icon-map"></i> <?php echo e(setting('company_address','中国北京市朝阳区')) ?></li>
                    <li><i class="iconfont icon-phone"></i> <?php echo e(setting('company_phone','400-800-8451')) ?></li>
                    <li><i class="iconfont icon-users"></i> <a href="<?php echo e(setting('company_group','#')) ?>" target="_blank">官方群聊</a></li>
                    <li><i class="iconfont icon-cloud"></i> <a href="<?php echo e(setting('international_url','https://cloud.loveym.cloud')) ?>" target="_blank">国际版官网</a></li>
                </ul>
                <iframe class="map-embed" src="<?php echo e(setting('company_map_url','https://map.baidu.com/search/北京市')) ?>" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
