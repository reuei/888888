</div><!-- /.main-wrap -->

<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <div class="footer-logo">
                    <?php if (setting('site_logo')): ?>
                        <img src="<?php echo e(setting('site_logo')) ?>" alt="<?php echo e(setting('site_name')) ?>">
                    <?php else: ?>
                        <i class="iconfont icon-cloud"></i> <?php echo e(setting('site_name', L('nav.home', '语云科技'))) ?>
                    <?php endif; ?>
                </div>
                <p class="sales-phone"><?php echo L('footer.sales_phone', '销售电话') ?>：<a href="tel:<?php echo e(setting('sales_phone','400-800-8451')) ?>"><?php echo e(setting('sales_phone','400-800-8451')) ?></a></p>
                <p class="footer-slogan"><?php echo e(setting('site_slogan')) ?></p>
            </div>
            <div class="footer-links">
                <div class="link-group">
                    <h4><?php echo L('footer.about', '关于') ?></h4>
                    <a href="<?php echo YUYUN_URL ?>/about.php"><?php echo L('nav.about', '关于我们') ?></a>
                    <a href="<?php echo YUYUN_URL ?>/company.php"><?php echo L('nav.company', '公司简介') ?></a>
                    <a href="<?php echo YUYUN_URL ?>/partners.php"><?php echo L('nav.partners', '合作伙伴') ?></a>
                </div>
                <div class="link-group">
                    <h4><?php echo L('footer.products', '产品') ?></h4>
                    <a href="<?php echo YUYUN_URL ?>/products.php"><?php echo L('home.products_title', '云服务器') ?></a>
                    <a href="<?php echo YUYUN_URL ?>/products.php">CDN</a>
                    <a href="<?php echo YUYUN_URL ?>/products.php"><?php echo L('company.security', '高防 IP') ?></a>
                </div>
                <div class="link-group">
                    <h4><?php echo L('footer.support', '支持') ?></h4>
                    <a href="<?php echo YUYUN_URL ?>/contact.php"><?php echo L('nav.contact', '联系我们') ?></a>
                    <a href="<?php echo YUYUN_URL ?>/user/index.php"><?php echo L('footer.ticket', '工单中心') ?></a>
                    <a href="<?php echo e(setting('international_url','https://cloud.loveym.cloud')) ?>" target="_blank"><?php echo L('nav.international', '国际版官网') ?></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p><?php echo e(setting('footer_statement','语云科技® 等是我们（语云科技美国有限公司）在中国的注册授权。')) ?></p>
            <p class="beian">
                <a href="http://beian.miit.gov.cn/" target="_blank"><?php echo e(setting('site_icp','京ICP备XXXXXXXX号')) ?></a>
                <span class="divider">|</span>
                <span><?php echo e(setting('site_ev_license','电子增值服务产业证')) ?></span>
                <span class="divider">|</span>
                <span><?php echo e(setting('site_police','京公网安备XXXXXXXX号')) ?></span>
            </p>
            <p class="copyright">&copy; <?php echo date('Y') ?> <?php echo e(setting('company_name','语云科技（美国）有限公司')) ?> <?php echo L('footer.copyright', '版权所有') ?></p>
        </div>
    </div>
</footer>

<div class="float-tools">
    <a href="tel:<?php echo e(setting('sales_phone','400-800-8451')) ?>" class="float-btn" title="<?php echo L('footer.sales_phone', '销售电话') ?>"><i class="iconfont icon-phone"></i><span><?php echo L('btn.phone', '电话') ?></span></a>
    <a href="<?php echo YUYUN_URL ?>/contact.php" class="float-btn" title="<?php echo L('nav.contact', '在线咨询') ?>"><i class="iconfont icon-headset"></i><span><?php echo L('footer.support', '客服') ?></span></a>
    <button class="float-btn" id="backTop" title="<?php echo L('btn.later', '返回顶部') ?>"><i class="iconfont icon-arrow-up"></i><span><?php echo L('btn.later', '顶部') ?></span></button>
</div>

<script src="<?php echo YUYUN_URL ?>/assets/js/main.js"></script>
</body>
</html>
