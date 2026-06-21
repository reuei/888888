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
                <p class="sales-phone">销售电话：<a href="tel:<?php echo e(setting('sales_phone','400-800-8451')) ?>"><?php echo e(setting('sales_phone','400-800-8451')) ?></a></p>
                <p class="footer-slogan"><?php echo e(setting('site_slogan')) ?></p>
            </div>
            <div class="footer-links">
                <div class="link-group">
                    <h4>关于</h4>
                    <a href="<?php echo YUYUN_URL ?>/about.php">关于我们</a>
                    <a href="<?php echo YUYUN_URL ?>/company.php">公司简介</a>
                    <a href="<?php echo YUYUN_URL ?>/partners.php">合作伙伴</a>
                </div>
                <div class="link-group">
                    <h4>产品</h4>
                    <a href="<?php echo YUYUN_URL ?>/products.php">云服务器</a>
                    <a href="<?php echo YUYUN_URL ?>/products.php">CDN 加速</a>
                    <a href="<?php echo YUYUN_URL ?>/products.php">高防 IP</a>
                </div>
                <div class="link-group">
                    <h4>支持</h4>
                    <a href="<?php echo YUYUN_URL ?>/contact.php">联系我们</a>
                    <a href="<?php echo YUYUN_URL ?>/user/index.php">工单中心</a>
                    <a href="<?php echo e(setting('international_url','https://cloud.loveym.cloud')) ?>" target="_blank">国际版官网</a>
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
            <p class="copyright">&copy; <?php echo date('Y') ?> <?php echo e(setting('company_name','语云科技（美国）有限公司')) ?> 版权所有</p>
        </div>
    </div>
</footer>

<div class="float-tools">
    <a href="tel:<?php echo e(setting('sales_phone','400-800-8451')) ?>" class="float-btn" title="销售电话"><i class="iconfont icon-phone"></i><span>电话</span></a>
    <a href="<?php echo YUYUN_URL ?>/contact.php" class="float-btn" title="在线咨询"><i class="iconfont icon-headset"></i><span>客服</span></a>
    <button class="float-btn" id="backTop" title="返回顶部"><i class="iconfont icon-arrow-up"></i><span>顶部</span></button>
</div>

<script src="<?php echo YUYUN_URL ?>/assets/js/main.js"></script>
</body>
</html>
