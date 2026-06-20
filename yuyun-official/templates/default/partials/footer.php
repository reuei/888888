<?php
$siteTitle = getSetting('site_title', '语云科技');
$logo = getSetting('site_logo');
$phone = getSetting('sales_phone', '400-800-8451');
$icp = getSetting('icp');
$icpGongan = getSetting('icp_gongan');
$license = getSetting('license');
$footerText = getSetting('footer_text');
$links = dbAll('links', 'sort_order', 'ASC');
?>
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="./" class="logo">
                    <?php if ($logo && file_exists(YUYUN_ROOT . '/' . $logo)): ?>
                        <img src="<?php echo yy_e($logo); ?>" alt="<?php echo yy_e($siteTitle); ?>">
                    <?php else: ?>
                        <i class="fa-solid fa-cloud"></i>
                    <?php endif; ?>
                    <span><?php echo yy_e($siteTitle); ?></span>
                </a>
                <div class="sales-phone"><i class="fa-solid fa-phone"></i> 销售电话：<?php echo yy_e($phone); ?></div>
                <p><?php echo yy_e(getSetting('company_intro')); ?></p>
                <?php if ($footerText): ?>
                    <p style="margin-top:12px;"><?php echo yy_e($footerText); ?></p>
                <?php endif; ?>
            </div>
            <div class="footer-col">
                <h4>快速链接</h4>
                <a href="./">首页</a>
                <a href="?page=about">关于我们</a>
                <a href="?page=company">公司简介</a>
                <a href="?page=products">产品介绍</a>
            </div>
            <div class="footer-col">
                <h4>服务支持</h4>
                <a href="?page=contact">联系我们</a>
                <a href="?page=partners">合作伙伴</a>
                <a href="<?php echo yy_e(getSetting('international_url', 'https://cloud.loveym.cloud')); ?>" target="_blank">国际版官网</a>
                <a href="./admin/">后台管理</a>
            </div>
            <div class="footer-col">
                <h4>友情链接</h4>
                <?php foreach ($links as $link): ?>
                    <a href="<?php echo yy_e($link['url']); ?>" target="_blank"><?php echo yy_e($link['name']); ?></a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© <?php echo date('Y'); ?> <?php echo yy_e($siteTitle); ?> 版权所有</p>
            <p class="footer-authorize"><?php echo yy_e($footerText ?: '语云科技®等是我们（语云科技美国有限公司）在中国的注册授权'); ?></p>
            <p>
                <?php if ($icp): ?><a href="https://beian.miit.gov.cn/" target="_blank"><?php echo yy_e($icp); ?></a><?php endif; ?>
                <?php if ($license): ?>&nbsp;|&nbsp;<?php echo yy_e($license); ?><?php endif; ?>
                <?php if ($icpGongan): ?>&nbsp;|&nbsp;<a href="https://www.beian.gov.cn/" target="_blank"><?php echo yy_e($icpGongan); ?></a><?php endif; ?>
            </p>
        </div>
    </div>
</footer>

<div class="float-contact">
    <div class="float-panel">
        <h4>联系我们</h4>
        <a href="tel:<?php echo yy_e($phone); ?>"><i class="fa-solid fa-phone"></i> <?php echo yy_e($phone); ?></a>
        <?php if (getSetting('service_phone')): ?>
            <a href="tel:<?php echo yy_e(getSetting('service_phone')); ?>"><i class="fa-solid fa-headset"></i> 客服：<?php echo yy_e(getSetting('service_phone')); ?></a>
        <?php endif; ?>
        <a href="mailto:<?php echo yy_e(getSetting('company_email')); ?>"><i class="fa-solid fa-envelope"></i> <?php echo yy_e(getSetting('company_email')); ?></a>
        <?php if (getSetting('group_chat')): ?>
            <a href="<?php echo yy_e(getSetting('group_chat')); ?>" target="_blank"><i class="fa-brands fa-weixin"></i> 官方群聊</a>
        <?php endif; ?>
        <a href="?page=contact"><i class="fa-solid fa-comment-dots"></i> 在线留言</a>
    </div>
    <div class="float-stack">
        <div class="float-btn" id="floatServiceBtn"><i class="fa-solid fa-headset"></i></div>
        <a href="tel:<?php echo yy_e($phone); ?>" class="float-btn float-phone" title="销售电话"><i class="fa-solid fa-phone"></i></a>
        <div class="float-btn float-wechat" id="floatWechatBtn" title="官方群聊/微信"><i class="fa-brands fa-weixin"></i></div>
        <div class="float-btn float-top" id="floatTopBtn" title="返回顶部"><i class="fa-solid fa-arrow-up"></i></div>
    </div>
</div>
<div class="qr-modal" id="qrModal">
    <div class="qr-box">
        <button class="qr-close" id="qrClose">&times;</button>
        <h4>官方群聊</h4>
        <p>扫描二维码或<a href="<?php echo yy_e(getSetting('group_chat')); ?>" target="_blank">点击加入</a></p>
        <div class="qr-placeholder"><i class="fa-brands fa-weixin" style="font-size:48px;color:#07c160;"></i></div>
        <p style="font-size:12px;color:#888;">请在后台「站点配置」上传群二维码图片替换此处</p>
    </div>
</div>

<div class="modal-overlay" id="globalModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="globalModalTitle">标题</h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="globalModalBody"></div>
    </div>
</div>
