<?php
/**
 * 工作人员页面 v7.0.0
 * 中央纪委国家监委网站 CMS 系统
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';

$page_title = '工作人员';

// 获取所有在职工作人员，按排序顺序排列
$staff_members = db_fetch_all(
    "SELECT * FROM staff WHERE status = 1 ORDER BY sort_order ASC, id ASC"
);

// 根据姓名生成颜色（用于头像占位符）
function staff_avatar_color($name) {
    $colors = [
        '#c41230', '#1a5276', '#0e6655', '#7d3c98',
        '#b9770e', '#2e86c1', '#0b5345', '#a04000',
        '#6c3483', '#117a65', '#cb4335', '#2471a3'
    ];
    $hash = 0;
    for ($i = 0; $i < mb_strlen($name, 'UTF-8'); $i++) {
        $hash = (($hash << 5) - $hash) + ord(mb_substr($name, $i, 1, 'UTF-8'));
        $hash = $hash & 0xffffffff; // 32-bit overflow
    }
    if ($hash < 0) $hash = -$hash;
    return $colors[$hash % count($colors)];
}

// 获取姓名首字母
function staff_initials($name) {
    $name = trim($name);
    if (empty($name)) return '?';
    $len = mb_strlen($name, 'UTF-8');
    if ($len <= 2) {
        return mb_substr($name, 0, 1, 'UTF-8');
    }
    return mb_substr($name, 0, 1, 'UTF-8');
}

include TEMPLATES_PATH . 'header.php';
?>

<div class="container">
    <div class="staff-page">
        <div class="article-detail__breadcrumb">
            <a href="<?php echo site_url(); ?>">首页</a>
            <span class="article-detail__breadcrumb-sep">/</span>
            工作人员
        </div>

        <h1>工作人员</h1>

        <?php if (empty($staff_members)): ?>
        <div class="empty-state">
            <div class="empty-state__icon"><i class="fas fa-user-friends"></i></div>
            <h3 class="empty-state__title">暂无工作人员信息</h3>
            <p class="empty-state__desc">工作人员信息正在更新中，请稍后再来</p>
        </div>
        <?php else: ?>
        <div class="staff-grid">
            <?php foreach ($staff_members as $member): ?>
            <div class="staff-card">
                <?php if (!empty($member['avatar'])): ?>
                <img src="<?php echo site_url('uploads/' . $member['avatar']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" class="staff-card__avatar">
                <?php else: ?>
                <div class="staff-placeholder" style="background-color: <?php echo staff_avatar_color($member['name']); ?>;">
                    <?php echo htmlspecialchars(staff_initials($member['name'])); ?>
                </div>
                <?php endif; ?>
                <div class="staff-card__name"><?php echo htmlspecialchars($member['name']); ?></div>
                <?php if (!empty($member['title'])): ?>
                <div class="staff-card__title"><?php echo htmlspecialchars($member['title']); ?></div>
                <?php endif; ?>
                <?php if (!empty($member['department'])): ?>
                <div class="staff-card__department"><?php echo htmlspecialchars($member['department']); ?></div>
                <?php endif; ?>
                <?php if (!empty($member['bio'])): ?>
                <div class="staff-card__bio"><?php echo htmlspecialchars($member['bio']); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include TEMPLATES_PATH . 'footer.php'; ?>