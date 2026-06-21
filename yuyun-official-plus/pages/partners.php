<?php
$partners = dbActive('partners', 'sort_order', 'ASC');
?>
<section class="page-banner">
    <div class="container">
        <h1>合作伙伴</h1>
        <p>携手全球领先企业，共建云端生态</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header reveal">
            <h2>我们与以下企业/组织携手共进</h2>
            <p>合作共赢，共同推动云计算行业发展</p>
            <div class="section-title-line"></div>
        </div>
        <div class="partner-grid">
            <?php foreach ($partners as $p): ?>
                <div class="partner-card reveal">
                    <div class="logo-box">
                        <?php if ($p['logo'] && file_exists(YUYUN_ROOT . '/' . $p['logo'])): ?>
                            <img src="<?php echo yy_e($p['logo']); ?>" alt="<?php echo yy_e($p['name']); ?>" style="max-height:56px;max-width:120px;object-fit:contain;">
                        <?php else: ?>
                            <i class="fa-solid fa-handshake"></i>
                        <?php endif; ?>
                    </div>
                    <h4><?php echo yy_e($p['name']); ?></h4>
                    <?php if ($p['link'] && $p['link'] !== '#'): ?>
                        <a href="<?php echo yy_e($p['link']); ?>" target="_blank" class="btn btn-outline" style="margin-top:12px;font-size:12px;padding:6px 16px;">访问官网</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
