<footer class="footer">
<div class="container">
<div class="footer-grid">
<div class="footer-col">
<h4>关于本站</h4>
<p><?php echo e(siteName()); ?>信息公开平台，依法接受群众监督，推进检务公开。</p>
</div>
<div class="footer-col">
<h4>快速导航</h4>
<p><a href="<?php echo SITE_URL; ?>report.php">信访举报</a></p>
<p><a href="<?php echo SITE_URL; ?>message.php">在线留言</a></p>
<p><a href="<?php echo SITE_URL; ?>search.php">信息检索</a></p>
</div>
<div class="footer-col">
<h4>联系方式</h4>
<p>电话：12309</p>
<p>地址：人民检察院办公大楼</p>
<p>邮箱：jiancha@example.gov</p>
</div>
<div class="footer-col">
<h4>友情链接</h4>
<p><a href="https://www.spp.gov.cn/" target="_blank">最高人民检察院</a></p>
<p><a href="https://www.12309.gov.cn/" target="_blank">12309服务中心</a></p>
</div>
<div class="footer-col footer-qr">
<h4>关注公众号</h4>
<?php $fi = getSetting('footer_image'); if ($fi): ?>
<img src="<?php echo SITE_URL . UPLOAD_URL . e($fi); ?>" alt="二维码">
<?php else: ?>
<div class="qr-placeholder">机关<br>二维码</div>
<?php endif; ?>
</div>
</div>
<div class="footer-bottom">
<p><?php echo e(getSetting('footer_copyright', '&copy; 2026 ' . siteName() . ' 版权所有')); ?></p>
<?php $icp = getSetting('icp'); if ($icp): ?><p><?php echo e($icp); ?></p><?php endif; ?>
</div>
</div>
</footer>
<div class="modal-backdrop" id="modalBackdrop"></div>
<div class="modal" id="modal">
<div class="modal-head"><h3 id="modalTitle">提示</h3><button class="modal-close" id="modalClose">&times;</button></div>
<div class="modal-body" id="modalBody"></div>
<div class="modal-foot"><button class="btn btn-primary" id="modalConfirm">确定</button></div>
</div>
<div class="toast" id="toast"></div>
</body>
</html>
