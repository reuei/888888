<?php
/**
 * 仪表盘
 */
$active_page = 'dashboard';
require_once 'header.php';

// 统计数据
$stats = [
    ['label' => '产品数量', 'value' => count($site_data['products'] ?? []), 'icon' => 'fa-cube', 'color' => '#1a73e8'],
    ['label' => '合作伙伴', 'value' => count($site_data['partners'] ?? []), 'icon' => 'fa-handshake', 'color' => '#ff6b35'],
    ['label' => '轮播图', 'value' => count($site_data['slides'] ?? []), 'icon' => 'fa-images', 'color' => '#00a86b'],
    ['label' => '员工卡片', 'value' => count($site_data['employees'] ?? []), 'icon' => 'fa-user-tie', 'color' => '#9b59b6'],
];
?>

<div class="admin-content">
    <div class="stats-cards">
        <?php foreach ($stats as $s): ?>
        <div class="admin-stat">
            <div class="admin-stat-icon" style="background:<?php echo $s['color']; ?>22;color:<?php echo $s['color']; ?>;">
                <i class="fas <?php echo $s['icon']; ?>"></i>
            </div>
            <div class="admin-stat-content">
                <strong><?php echo $s['value']; ?></strong>
                <span><?php echo $s['label']; ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="admin-card">
        <h2><i class="fas fa-tachometer-alt" style="color:#1a73e8;"></i> 系统概览</h2>
        <div class="stats-grid" style="grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:20px;margin-top:20px;">
            <div style="padding:20px;background:#f8fafc;border-radius:10px;">
                <div style="color:#4a5568;font-size:14px;margin-bottom:8px;">
                    <i class="fas fa-circle" style="color:#00a86b;font-size:8px;"></i> 系统状态：<strong style="color:#00a86b;">正常运行</strong>
                </div>
                <div style="color:#4a5568;font-size:14px;margin-bottom:8px;">
                    <i class="fas fa-database" style="color:#1a73e8;"></i> 数据存储：<strong>JSON文件</strong>
                </div>
                <div style="color:#4a5568;font-size:14px;">
                    <i class="fas fa-clock" style="color:#ff6b35;"></i> PHP版本：<strong><?php echo phpversion(); ?></strong>
                </div>
            </div>
            <div style="padding:20px;background:#f8fafc;border-radius:10px;">
                <div style="color:#4a5568;font-size:14px;margin-bottom:8px;">
                    <i class="fas fa-building" style="color:#1a73e8;"></i> 公司：<strong><?php echo htmlspecialchars($site_data['site']['name'] ?? '语云科技'); ?></strong>
                </div>
                <div style="color:#4a5568;font-size:14px;margin-bottom:8px;">
                    <i class="fas fa-phone-alt" style="color:#ff6b35;"></i> 电话：<strong><?php echo htmlspecialchars($site_data['contact']['phone'] ?? '400-800-8541'); ?></strong>
                </div>
                <div style="color:#4a5568;font-size:14px;">
                    <i class="fas fa-envelope" style="color:#00a86b;"></i> 邮箱：<strong><?php echo htmlspecialchars($site_data['contact']['email'] ?? ''); ?></strong>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2><i class="fas fa-bolt" style="color:#ff6b35;"></i> 快速操作</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:12px;margin-top:20px;">
            <a href="site.php" class="btn btn-primary"><i class="fas fa-cog"></i> 网站设置</a>
            <a href="slides.php" class="btn btn-primary"><i class="fas fa-images"></i> 轮播图</a>
            <a href="products.php" class="btn btn-primary"><i class="fas fa-cube"></i> 产品管理</a>
            <a href="partners.php" class="btn btn-primary"><i class="fas fa-handshake"></i> 合作伙伴</a>
            <a href="contact.php" class="btn btn-primary"><i class="fas fa-address-card"></i> 联系方式</a>
            <a href="account.php" class="btn btn-secondary"><i class="fas fa-user"></i> 账号管理</a>
        </div>
    </div>

    <div class="admin-card">
        <h2><i class="fas fa-database" style="color:#9b59b6;"></i> 数据管理</h2>
        <form method="post" style="margin-top:16px;display:flex;gap:12px;flex-wrap:wrap;">
            <button type="submit" name="action" value="export_data" class="btn btn-primary"><i class="fas fa-download"></i> 导出数据</button>
            <button type="submit" name="action" value="reset_data" class="btn btn-secondary" onclick="return confirm('确定要恢复到默认数据吗？此操作不可撤销！')"><i class="fas fa-undo"></i> 恢复默认</button>
        </form>
    </div>
</div>
</div>
</body>
</html>
