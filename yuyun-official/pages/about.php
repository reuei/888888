<?php
$mapUrl = mapEmbedUrl(getSetting('map_type', 'baidu'), getSetting('map_key'), getSetting('map_lat'), getSetting('map_lng'));
?>
<section class="page-banner">
    <div class="container">
        <h1>关于我们</h1>
        <p>了解语云科技，携手共创云端未来</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="about-grid reveal">
            <div class="about-info">
                <h2><?php echo yy_e(getSetting('company_name', '语云科技')); ?></h2>
                <p><?php echo nl2br(yy_e(getSetting('company_intro'))); ?></p>
                <p>我们始终坚持以客户为中心，以技术创新为驱动，为企业提供安全、稳定、高效的云计算服务。无论您是初创企业还是大型集团，语云科技都能为您量身定制数字化转型方案。</p>
                <a href="?page=products" class="btn btn-primary btn-lg" style="margin-top:12px;">了解我们的产品</a>
            </div>
            <div class="contact-card">
                <h3>联系方式</h3>
                <div class="info-row">
                    <i class="fa-solid fa-building"></i>
                    <div>
                        <strong>公司名称</strong>
                        <?php echo yy_e(getSetting('company_name')); ?>
                    </div>
                </div>
                <div class="info-row">
                    <i class="fa-solid fa-location-dot"></i>
                    <div>
                        <strong>公司地址</strong>
                        <?php echo yy_e(getSetting('company_address')); ?>
                    </div>
                </div>
                <div class="info-row">
                    <i class="fa-solid fa-phone"></i>
                    <div>
                        <strong>营销电话</strong>
                        <?php echo yy_e(getSetting('sales_phone', '400-800-8451')); ?>
                    </div>
                </div>
                <div class="info-row">
                    <i class="fa-solid fa-envelope"></i>
                    <div>
                        <strong>企业邮箱</strong>
                        <?php echo yy_e(getSetting('company_email')); ?>
                    </div>
                </div>
                <?php if (getSetting('group_chat')): ?>
                <div class="info-row">
                    <i class="fa-brands fa-weixin"></i>
                    <div>
                        <strong>官方群聊</strong>
                        <a href="<?php echo yy_e(getSetting('group_chat')); ?>" target="_blank">点击加入</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="section section-gray">
    <div class="container">
        <div class="section-header reveal">
            <h2>公司位置</h2>
            <p><?php echo yy_e(getSetting('company_address')); ?></p>
            <div class="section-title-line"></div>
        </div>
        <div class="content-block reveal" style="padding:0;overflow:hidden;">
            <iframe src="<?php echo yy_e($mapUrl); ?>" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>
