        <footer class="site-footer">
            <div class="container">
                <div class="footer-wrap">
                    <div class="footer-col">
                        <h4>关于我们</h4>
                        <ul>
                            <li><a href="#">网站简介</a></li>
                            <li><a href="#">联系方式</a></li>
                            <li><a href="#">工作邮箱</a></li>
                            <li><a href="#">设为首页</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>快速链接</h4>
                        <ul>
                            <li><a href="<?php echo BASE_URL; ?>category.php?slug=yaowen">要闻动态</a></li>
                            <li><a href="<?php echo BASE_URL; ?>category.php?slug=shencha">审查调查</a></li>
                            <li><a href="<?php echo BASE_URL; ?>category.php?slug=xunshi">巡视巡察</a></li>
                            <li><a href="<?php echo BASE_URL; ?>category.php?slug=fagui">党纪法规</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>互动交流</h4>
                        <ul>
                            <li><a href="<?php echo BASE_URL; ?>report.php">监督举报</a></li>
                            <li><a href="<?php echo BASE_URL; ?>message.php">留言板</a></li>
                            <li><a href="#">我要投稿</a></li>
                            <li><a href="#">意见建议</a></li>
                        </ul>
                    </div>
                    <div class="footer-col">
                        <h4>关注我们</h4>
                        <?php
                        $footerImage = getSetting('footer_image', '');
                        if ($footerImage):
                        ?>
                            <div class="footer-image">
                                <img src="<?php echo BASE_URL . UPLOAD_URL . e($footerImage); ?>" alt="关注二维码">
                            </div>
                        <?php else: ?>
                            <p style="font-size:12px; color:#888;">扫码关注官方微信</p>
                            <div style="width:120px; height:120px; background:#555; border-radius:4px; margin-top:10px; display:flex; align-items:center; justify-content:center; color:#888; font-size:12px;">
                                二维码
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="footer-bottom">
                    <p><?php echo e(getSetting('footer_copyright', '© ' . date('Y') . ' 清廉在线 版权所有')); ?></p>
                    <?php if (getSetting('icp', '')): ?>
                        <p><a href="https://beian.miit.gov.cn/" target="_blank"><?php echo e(getSetting('icp')); ?></a></p>
                    <?php endif; ?>
                </div>
            </div>
        </footer>
    </body>
</html>
