<?php
/**
 * 网站底部模板 v6.0.0
 * 中央纪委国家监委网站 CMS 系统
 */
?>
    </div><!-- .container -->
</main><!-- .site-main -->

<footer class="site-footer">
    <div class="site-footer__inner">
        <?php $footer_img = get_footer_image(); ?>
        <?php if ($footer_img): ?>
        <div class="footer-image">
            <img src="<?php echo $footer_img; ?>" alt="机关标识">
        </div>
        <?php endif; ?>

        <div class="footer-links">
            <div class="footer-links__col">
                <h4 class="footer-links__col-title">关于我们</h4>
                <ul class="footer-links__list">
                    <li><a href="#" class="footer-links__link">中央纪委国家监委简介</a></li>
                    <li><a href="#" class="footer-links__link">组织机构</a></li>
                    <li><a href="#" class="footer-links__link">工作程序</a></li>
                    <li><a href="#" class="footer-links__link">历史沿革</a></li>
                </ul>
            </div>
            <div class="footer-links__col">
                <h4 class="footer-links__col-title">信息发布</h4>
                <ul class="footer-links__list">
                    <li><a href="<?php echo site_url('category.php?slug=yaowen'); ?>" class="footer-links__link">要闻</a></li>
                    <li><a href="<?php echo site_url('category.php?slug=shenchadiaocha'); ?>" class="footer-links__link">审查调查</a></li>
                    <li><a href="<?php echo site_url('category.php?slug=xunshixuncha'); ?>" class="footer-links__link">巡视巡察</a></li>
                    <li><a href="<?php echo site_url('category.php?slug=gongzuodongtai'); ?>" class="footer-links__link">工作动态</a></li>
                </ul>
            </div>
            <div class="footer-links__col">
                <h4 class="footer-links__col-title">监督举报</h4>
                <ul class="footer-links__list">
                    <li><a href="<?php echo site_url('report.php'); ?>" class="footer-links__link">我要举报</a></li>
                    <li><a href="<?php echo site_url('report.php?type=message'); ?>" class="footer-links__link">在线留言</a></li>
                    <li><a href="#" class="footer-links__link">举报指南</a></li>
                    <li><a href="#" class="footer-links__link">举报查询</a></li>
                </ul>
            </div>
            <div class="footer-links__col">
                <h4 class="footer-links__col-title">党纪法规</h4>
                <ul class="footer-links__list">
                    <li><a href="<?php echo site_url('category.php?slug=dangjifagui'); ?>" class="footer-links__link">党纪法规库</a></li>
                    <li><a href="<?php echo site_url('category.php?slug=jifabaike'); ?>" class="footer-links__link">纪法百科</a></li>
                    <li><a href="#" class="footer-links__link">明纪释法</a></li>
                    <li><a href="#" class="footer-links__link">业务讲堂</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p><?php echo htmlspecialchars(site_config('footer_text', '版权所有 © 中央纪委国家监委')); ?></p>
            <?php $icp = site_config('icp_number', ''); ?>
            <?php if ($icp): ?><p><?php echo htmlspecialchars($icp); ?></p><?php endif; ?>
            <p>
                <a href="#">设为首页</a> |
                <a href="#">网站声明</a> |
                <a href="#">联系我们</a> |
                <a href="#">站点地图</a>
            </p>
        </div>
    </div>
</footer>

<!-- 返回顶部 -->
<button class="back-to-top" id="backToTop" title="返回顶部">
    <i class="fas fa-arrow-up"></i>
</button>

<script src="<?php echo site_url('assets/js/main.js?v=6.0.0'); ?>"></script>
</body>
</html>