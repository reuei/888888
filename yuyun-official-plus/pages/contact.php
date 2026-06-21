<?php
$mapUrl = mapEmbedUrl(getSetting('map_type', 'baidu'), getSetting('map_key'), getSetting('map_lat'), getSetting('map_lng'));
?>
<section class="page-banner">
    <div class="container">
        <h1>联系我们</h1>
        <p>随时为您提供专业的云计算咨询服务</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="about-grid reveal">
            <div class="content-block" style="margin-bottom:0;">
                <h2>在线留言</h2>
                <p>填写以下表单，我们将尽快与您联系。</p>
                <form id="contactForm" method="post" action="ajax/message.php">
                    <input type="hidden" name="csrf_token" value="<?php echo yy_e(csrfToken()); ?>">
                    <div class="form-group">
                        <label>您的姓名</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>联系电话</label>
                        <input type="tel" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>电子邮箱</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>留言内容</label>
                        <textarea name="content" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">提交留言</button>
                </form>
            </div>
            <div>
                <div class="contact-card" style="margin-bottom:24px;">
                    <h3>联系方式</h3>
                    <div class="info-row">
                        <i class="fa-solid fa-phone"></i>
                        <div><strong>销售电话</strong><?php echo yy_e(getSetting('sales_phone', '400-800-8451')); ?></div>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-headset"></i>
                        <div><strong>客服电话</strong><?php echo yy_e(getSetting('service_phone', '400-800-8451')); ?></div>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-envelope"></i>
                        <div><strong>企业邮箱</strong><?php echo yy_e(getSetting('company_email')); ?></div>
                    </div>
                    <div class="info-row">
                        <i class="fa-solid fa-location-dot"></i>
                        <div><strong>公司地址</strong><?php echo yy_e(getSetting('company_address')); ?></div>
                    </div>
                    <?php if (getSetting('group_chat')): ?>
                    <div class="info-row">
                        <i class="fa-brands fa-weixin"></i>
                        <div><strong>官方群聊</strong><a href="<?php echo yy_e(getSetting('group_chat')); ?>" target="_blank">点击加入</a></div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="content-block" style="padding:0;overflow:hidden;">
                    <iframe src="<?php echo yy_e($mapUrl); ?>" width="100%" height="320" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>
