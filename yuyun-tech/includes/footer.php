<?php
/**
 * 通用页脚组件
 */
require_once dirname(__FILE__, 2) . '/config.php';
$icp = $site_data['icp'] ?? [];
?>

<!-- CTA Banner -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>立即开启您的云端之旅</h2>
            <p>7x24小时专业服务 · 免费技术支持 · 一站式解决方案</p>
            <a href="contact.php" class="btn btn-lg">免费咨询 <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- 右侧悬浮客服 -->
<div class="floating-contact">
    <button class="fc-btn orange" data-modal="phone">
        <i class="fas fa-phone-alt"></i>电话
    </button>
    <button class="fc-btn" data-modal="qq">
        <i class="fab fa-qq"></i>QQ
    </button>
    <button class="fc-btn" data-modal="wechat">
        <i class="fab fa-weixin"></i>微信
    </button>
    <button class="fc-btn" data-modal="feedback">
        <i class="fas fa-comment-dots"></i>反馈
    </button>
</div>

<!-- 返回顶部 -->
<button class="back-to-top" aria-label="返回顶部">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- 页脚 -->
<footer class="footer">
    <div class="container">
        <div class="footer-top">
            <div>
                <div class="footer-brand">
                    <span class="logo-icon">Y</span>
                    <div class="logo-text" style="color:#fff;"><?php echo htmlspecialchars($site_data['site']['name'] ?? '语云科技'); ?>
                        <small style="color:rgba(255,255,255,0.6);">YUYUN TECH</small>
                    </div>
                </div>
                <p class="footer-desc">
                    全球领先的云服务提供商，致力于为企业客户提供安全、稳定、高效的云计算解决方案。
                </p>
                <div class="footer-phone">销售热线</div>
                <div class="footer-phone-value">400-800-8451</div>
            </div>

            <div>
                <h5>产品中心</h5>
                <ul>
                    <li><a href="products.php">云服务器 ECS</a></li>
                    <li><a href="products.php">云数据库 RDS</a></li>
                    <li><a href="products.php">CDN加速</a></li>
                    <li><a href="products.php">对象存储 OSS</a></li>
                    <li><a href="products.php">SSL证书</a></li>
                </ul>
            </div>

            <div>
                <h5>快速导航</h5>
                <ul>
                    <li><a href="about.php">关于我们</a></li>
                    <li><a href="company.php">公司简介</a></li>
                    <li><a href="partners.php">合作伙伴</a></li>
                    <li><a href="contact.php">联系我们</a></li>
                    <li><a href="https://cloud.loveym.cloud" target="_blank">国际版官网</a></li>
                </ul>
            </div>

            <div>
                <h5>联系方式</h5>
                <ul>
                    <li><a href="contact.php"><i class="fas fa-phone-alt"></i> 400-800-8541</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> sales@yuyun-tech.com</a></li>
                    <li><a href="contact.php"><i class="fas fa-map-marker-alt"></i> 青岛市南区语云大厦</a></li>
                    <li><a href="#"><i class="fab fa-qq"></i> QQ: 800888888</a></li>
                    <li><a href="#"><i class="fab fa-weixin"></i> 微信: yuyun_tech</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            Copyright &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($site_data['site']['name'] ?? '语云科技'); ?> 美国有限公司 版权所有
        </div>

        <div class="footer-bottom" style="padding-top:0;">
            <a href="https://beian.miit.gov.cn/" target="_blank"><?php echo htmlspecialchars($icp['icp_number'] ?? '鲁ICP备2024000000号-1'); ?></a>
            <span style="margin:0 12px;">|</span>
            <a href="#" class="police-badge">
                <i class="fas fa-shield-alt"></i>
                <?php echo htmlspecialchars($icp['police_number'] ?? '鲁公网安备 37020000000000号'); ?>
            </a>
            <span style="margin:0 12px;">|</span>
            <a href="#"><?php echo htmlspecialchars($icp['license_number'] ?? '增值电信业务经营许可证 B1-20240000'); ?></a>
        </div>

        <div class="footer-notice">
            语云科技<sup>&reg;</sup> 等是我们（语云科技美国有限公司）在中国的注册授权商标
        </div>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>
