</main>

<!-- Footer -->
<footer class="site-footer">
    <div class="footer-main">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col footer-brand">
                    <div class="footer-logo">
                        <img src="<?php echo h(DB::getSettingValue('logo', '/assets/images/logo.png')); ?>" alt="<?php echo h(SITE_NAME); ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <span style="display:none;"><?php echo h(SITE_NAME); ?></span>
                    </div>
                    <p class="footer-desc"><?php echo h(DB::getSettingValue('about_content', '')); ?></p>
                </div>
                
                <div class="footer-col">
                    <h4 class="footer-title">快速导航</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo siteUrl(); ?>">首页</a></li>
                        <li><a href="<?php echo siteUrl('about.php'); ?>">关于我们</a></li>
                        <li><a href="<?php echo siteUrl('service.php'); ?>">服务项目</a></li>
                        <li><a href="<?php echo siteUrl('case.php'); ?>">客户案例</a></li>
                        <li><a href="<?php echo siteUrl('news.php'); ?>">新闻资讯</a></li>
                        <li><a href="<?php echo siteUrl('contact.php'); ?>">联系我们</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h4 class="footer-title">服务项目</h4>
                    <ul class="footer-links">
                        <?php
                        $services = DB::getList('services', ['status' => 1], 'sort', 'ASC', 6);
                        foreach ($services as $s):
                        ?>
                        <li><a href="<?php echo siteUrl('service.php'); ?>"><?php echo h($s['title']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="footer-col footer-contact">
                    <h4 class="footer-title">联系我们</h4>
                    <ul class="footer-contact-list">
                        <li><i class="icon-phone"></i> <?php echo h(DB::getSettingValue('contact_phone', '')); ?></li>
                        <li><i class="icon-email"></i> <?php echo h(DB::getSettingValue('contact_email', '')); ?></li>
                        <li><i class="icon-location"></i> <?php echo h(DB::getSettingValue('contact_address', '')); ?></li>
                    </ul>
                    <div class="footer-social">
                        <a href="#" class="social-link" aria-label="微信">微</a>
                        <a href="#" class="social-link" aria-label="微博">博</a>
                        <a href="#" class="social-link" aria-label="抖音">抖</a>
                        <a href="#" class="social-link" aria-label="领英">领</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <div class="container">
            <p><?php echo h(DB::getSettingValue('copyright', '© 2024 ' . SITE_NAME . '. All Rights Reserved.')); ?></p>
            <?php $icp = DB::getSettingValue('icp', ''); if ($icp): ?>
            <p class="icp"><?php echo h($icp); ?></p>
            <?php endif; ?>
        </div>
    </div>
</footer>

<!-- Back to Top -->
<a href="#" class="back-to-top" id="backToTop" aria-label="返回顶部">↑</a>

<!-- Floating Contact -->
<div class="floating-contact" id="floatingContact">
    <a href="<?php echo siteUrl('contact.php'); ?>" title="在线咨询">
        <span class="fc-icon">💬</span>
        <span class="fc-text">咨询</span>
    </a>
</div>

<script src="/assets/js/main.js"></script>
<script src="/assets/js/carousel.js"></script>
</body>
</html>
