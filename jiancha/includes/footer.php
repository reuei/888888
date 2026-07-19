<?php
$footerImg = getSetting('footer_image', '');
?>
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-col">
        <h4><?php echo e(siteName()); ?></h4>
        <p style="font-size:12px;line-height:1.8;margin-top:8px">人民检察院是国家的法律监督机关，依法行使检察权，维护宪法和法律权威，保障社会公平正义。</p>
      </div>
      <div class="footer-col">
        <h4>检务公开</h4>
        <ul>
          <li><a href="<?php echo SITE_URL; ?>category.php?slug=yaowen">检察要闻</a></li>
          <li><a href="<?php echo SITE_URL; ?>category.php?slug=shencha">审查起诉</a></li>
          <li><a href="<?php echo SITE_URL; ?>category.php?slug=xunshi">公益诉讼</a></li>
          <li><a href="<?php echo SITE_URL; ?>category.php?slug=fagui">法律法规</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>诉讼服务</h4>
        <ul>
          <li><a href="<?php echo SITE_URL; ?>report.php">信访举报</a></li>
          <li><a href="<?php echo SITE_URL; ?>message.php">在线留言</a></li>
          <li><a href="<?php echo SITE_URL; ?>message.php">案件查询</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>便民工具</h4>
        <ul>
          <li><a href="<?php echo SITE_URL; ?>user.php">个人中心</a></li>
          <li><a href="<?php echo SITE_URL; ?>search.php">信息检索</a></li>
          <li><a href="<?php echo SITE_URL; ?>topic.php">专题专栏</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>联系我们</h4>
        <ul>
          <li><a href="#">12309 检察服务热线</a></li>
          <li><a href="#">jubao@spp.gov.cn</a></li>
        </ul>
        <?php if ($footerImg): ?>
        <div class="footer-qr"><img src="<?php echo SITE_URL . UPLOAD_URL . e($footerImg); ?>" alt="官方微信"></div>
        <?php else: ?>
        <div class="footer-qr">官方微信</div>
        <?php endif; ?>
      </div>
    </div>
    <div class="footer-bottom">
      <p><?php echo e(getSetting('footer_copyright', '© ' . date('Y') . ' ' . siteName() . ' 版权所有')); ?></p>
      <?php if ($icp = getSetting('icp')): ?><p><a href="https://beian.miit.gov.cn"><?php echo e($icp); ?></a></p><?php endif; ?>
    </div>
  </div>
</footer>
</body>
</html>