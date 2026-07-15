<?php
$siteName = getSetting('site_name', SITE_NAME);
?>
<footer class="foot">
    <div class="container">
        <div class="foot-top">
            <div class="foot-brand">
                <div class="brand-mark" style="margin-bottom:14px;">
                    <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" width="50" height="50">
                        <defs>
                            <linearGradient id="footGold" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#e8c97c"/>
                                <stop offset="100%" stop-color="#a68419"/>
                            </linearGradient>
                        </defs>
                        <path d="M50 12 L58 24 L72 22 L70 36 L82 42 L72 50 L74 64 L60 62 L50 74 L40 62 L26 64 L28 50 L18 42 L30 36 L28 22 L42 24 Z" fill="url(#footGold)"/>
                        <line x1="50" y1="28" x2="50" y2="38" stroke="#0a2540" stroke-width="1.5"/>
                        <line x1="50" y1="38" x2="36" y2="46" stroke="#0a2540" stroke-width="1.5"/>
                        <line x1="50" y1="38" x2="64" y2="46" stroke="#0a2540" stroke-width="1.5"/>
                        <circle cx="36" cy="46" r="4" fill="#0a2540"/>
                        <circle cx="64" cy="46" r="4" fill="#0a2540"/>
                        <text x="50" y="62" text-anchor="middle" fill="#0a2540" font-size="11" font-weight="bold" font-family="serif">检察</text>
                    </svg>
                </div>
                <h4 style="color:#fff; font-size:18px; letter-spacing:3px; margin-bottom:8px; font-family:var(--pk-font-serif);"><?php echo e($siteName); ?></h4>
                <p>人民检察院是国家的法律监督机关，依法行使检察权，维护宪法和法律权威，保障社会公平正义。</p>
            </div>

            <div class="foot-col">
                <h4>检务公开</h4>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>category.php?slug=yaowen">检察要闻</a></li>
                    <li><a href="<?php echo BASE_URL; ?>category.php?slug=shencha">审查起诉</a></li>
                    <li><a href="<?php echo BASE_URL; ?>category.php?slug=xunshi">公益诉讼</a></li>
                    <li><a href="<?php echo BASE_URL; ?>category.php?slug=fagui">法律法规</a></li>
                </ul>
            </div>

            <div class="foot-col">
                <h4>诉讼服务</h4>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>report.php">信访举报</a></li>
                    <li><a href="<?php echo BASE_URL; ?>message.php">律师阅卷</a></li>
                    <li><a href="<?php echo BASE_URL; ?>message.php">案件查询</a></li>
                    <li><a href="<?php echo BASE_URL; ?>message.php">申诉受理</a></li>
                </ul>
            </div>

            <div class="foot-col">
                <h4>便民工具</h4>
                <ul>
                    <li><a href="<?php echo BASE_URL; ?>user.php">个人中心</a></li>
                    <li><a href="<?php echo BASE_URL; ?>search.php">信息检索</a></li>
                    <li><a href="<?php echo BASE_URL; ?>topic.php">专题专栏</a></li>
                    <li><a href="<?php echo BASE_URL; ?>login.php">用户登录</a></li>
                </ul>
            </div>

            <div class="foot-col">
                <h4>联系我们</h4>
                <ul>
                    <li><a href="javascript:void(0)">12309 检察服务热线</a></li>
                    <li><a href="javascript:void(0)">jubao@spp.gov.cn</a></li>
                    <li><a href="javascript:void(0)">北京市东城区北河沿大街147号</a></li>
                </ul>
                <?php $footerImage = getSetting('footer_image', ''); ?>
                <?php if ($footerImage): ?>
                <div class="foot-qr">
                    <img src="<?php echo BASE_URL . UPLOAD_URL . e($footerImage); ?>" alt="官方微信">
                </div>
                <?php else: ?>
                <div class="foot-qr">官方<br>微信</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="foot-bottom">
            <p><?php echo e(getSetting('footer_copyright', '© ' . date('Y') . ' ' . $siteName . '  版权所有  主办单位：人民检察院')); ?></p>
            <?php if (getSetting('icp', '')): ?>
                <p><a href="https://beian.miit.gov.cn/" target="_blank"><?php echo e(getSetting('icp')); ?></a></p>
            <?php endif; ?>
            <p style="margin-top:8px; font-size:11px; opacity:0.5;">本网站适用于PHP 7.0+ / SQLite · 支持虚拟主机部署</p>
        </div>
    </div>

    <div class="modal-mask" id="modalMask">
        <div class="modal-box">
            <div class="modal-head">
                <h3 id="modalTitle">提示</h3>
                <button class="modal-close" onclick="closeModal()">×</button>
            </div>
            <div class="modal-body" id="modalBody"></div>
            <div class="modal-foot">
                <button class="btn" onclick="closeModal()">确 定</button>
            </div>
        </div>
    </div>

    <div class="toast-stack" id="toastStack"></div>
</footer>
</body>
</html>