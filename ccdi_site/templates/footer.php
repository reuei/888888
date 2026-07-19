<?php
/**
 * 网站底部模板 v4.0.0
 * 中央纪委国家监委网站 CMS 系统
 */
?>
    </div><!-- .container -->
</main><!-- .site-main -->

<footer class="site-footer">
    <div class="container">
        <?php $footer_img = get_footer_image(); ?>
        <?php if ($footer_img): ?>
        <div class="footer-image-section">
            <img src="<?php echo $footer_img; ?>" alt="机关标识" class="footer-image">
        </div>
        <?php endif; ?>

        <div class="footer-links">
            <div class="footer-column">
                <h4>关于我们</h4>
                <ul>
                    <li><a href="#">中央纪委国家监委简介</a></li>
                    <li><a href="#">组织机构</a></li>
                    <li><a href="#">工作程序</a></li>
                    <li><a href="#">历史沿革</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>信息发布</h4>
                <ul>
                    <li><a href="<?php echo site_url('category.php?slug=yaowen'); ?>">要闻</a></li>
                    <li><a href="<?php echo site_url('category.php?slug=shenchadiaocha'); ?>">审查调查</a></li>
                    <li><a href="<?php echo site_url('category.php?slug=xunshixuncha'); ?>">巡视巡察</a></li>
                    <li><a href="<?php echo site_url('category.php?slug=gongzuodongtai'); ?>">工作动态</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>监督举报</h4>
                <ul>
                    <li><a href="<?php echo site_url('report.php'); ?>">我要举报</a></li>
                    <li><a href="<?php echo site_url('report.php?type=message'); ?>">在线留言</a></li>
                    <li><a href="#">举报指南</a></li>
                    <li><a href="#">举报查询</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h4>党纪法规</h4>
                <ul>
                    <li><a href="<?php echo site_url('category.php?slug=dangjifagui'); ?>">党纪法规库</a></li>
                    <li><a href="<?php echo site_url('category.php?slug=jifabaike'); ?>">纪法百科</a></li>
                    <li><a href="#">明纪释法</a></li>
                    <li><a href="#">业务讲堂</a></li>
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

<script src="<?php echo site_url('assets/js/main.js?v=4.0.0'); ?>"></script>
</body>
</html>