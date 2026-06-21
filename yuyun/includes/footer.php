</div><!-- /.main-wrap -->

<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <div class="footer-logo">
                    <?php if (setting('site_logo')): ?>
                        <img src="<?php echo e(setting('site_logo')) ?>" alt="<?php echo e(setting('site_name')) ?>">
                    <?php else: ?>
                        <i class="iconfont icon-cloud"></i> <?php echo e(setting('site_name','语云科技')) ?>
                    <?php endif; ?>
                </div>
                <p class="sales-phone"><?php echo __('sales_phone') ?>：<a href="tel:<?php echo e(setting('sales_phone','400-800-8451')) ?>"><?php echo e(setting('sales_phone','400-800-8451')) ?></a></p>
                <p class="footer-slogan"><?php echo e(setting('site_slogan')) ?></p>
            </div>
            <div class="footer-links">
                <div class="link-group">
                    <h4><?php echo __('about') ?></h4>
                    <a href="<?php echo YUYUN_URL ?>/about.php"><?php echo __('about') ?></a>
                    <a href="<?php echo YUYUN_URL ?>/company.php"><?php echo __('company') ?></a>
                    <a href="<?php echo YUYUN_URL ?>/partners.php"><?php echo __('partners') ?></a>
                </div>
                <div class="link-group">
                    <h4><?php echo __('products') ?></h4>
                    <a href="<?php echo YUYUN_URL ?>/products.php"><?php echo __('products') ?></a>
                    <a href="<?php echo YUYUN_URL ?>/products.php">CDN</a>
                    <a href="<?php echo YUYUN_URL ?>/products.php"><?php echo e(__('contact')) ?></a>
                </div>
                <div class="link-group">
                    <h4><?php echo __('user_center') ?></h4>
                    <a href="<?php echo YUYUN_URL ?>/contact.php"><?php echo __('contact') ?></a>
                    <a href="<?php echo YUYUN_URL ?>/user/index.php"><?php echo __('user_center') ?></a>
                    <a href="<?php echo e(setting('international_url','https://cloud.loveym.cloud')) ?>" target="_blank"><?php echo __('intl') ?></a>
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
            <p class="copyright">&copy; <?php echo date('Y') ?> <?php echo e(setting('company_name','语云科技（美国）有限公司')) ?> <?php echo __('copyright') ?></p>
        </div>
    </div>
</footer>

<div class="float-tools">
    <a href="tel:<?php echo e(setting('sales_phone','400-800-8451')) ?>" class="float-btn" title="<?php echo __('phone') ?>"><i class="iconfont icon-phone"></i><span><?php echo __('phone') ?></span></a>
    <a href="<?php echo YUYUN_URL ?>/contact.php" class="float-btn" title="<?php echo __('online_support') ?>"><i class="iconfont icon-headset"></i><span><?php echo __('online_support') ?></span></a>
    <button class="float-btn" id="backTop" title="<?php echo __('back_to_top') ?>"><i class="iconfont icon-arrow-up"></i><span><?php echo __('back_to_top') ?></span></button>
</div>

<script src="<?php echo YUYUN_URL ?>/assets/js/main.js"></script>
</body>
</html>
