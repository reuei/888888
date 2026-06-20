<?php
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/config.php';
require_once YUYUN_ROOT . '/includes/functions.php';
require_once YUYUN_ROOT . '/includes/auth.php';
requireAdminLogin();

$db = YuyunDB::getInstance();
$counts = [
    'slides' => count(dbActive('slides')),
    'products' => count(dbActive('products')),
    'partners' => count(dbActive('partners')),
    'messages' => count($db->getType() === 'json' ? $db->jsonWhere('messages', ['status' => 0], 'id', 'DESC') : $db->query("SELECT id FROM messages WHERE status = 0")),
];

$pageTitle = '控制台';
include __DIR__ . '/includes/header.php';
?>
<div class="stats">
    <div class="stat-card">
        <h3><?php echo $counts['slides']; ?></h3>
        <p>轮播图数量</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $counts['products']; ?></h3>
        <p>产品数量</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $counts['partners']; ?></h3>
        <p>合作伙伴</p>
    </div>
    <div class="stat-card">
        <h3><?php echo $counts['messages']; ?></h3>
        <p>待审核留言</p>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2>快捷入口</h2></div>
    <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="settings.php" class="btn btn-primary"><i class="fa-solid fa-gears"></i> 站点配置</a>
        <a href="slides.php" class="btn btn-primary"><i class="fa-solid fa-images"></i> 轮播图管理</a>
        <a href="products.php" class="btn btn-primary"><i class="fa-solid fa-cubes"></i> 产品管理</a>
        <a href="messages.php" class="btn btn-primary"><i class="fa-solid fa-envelope"></i> 留言管理</a>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2>使用说明</h2></div>
    <div class="card-body">
        <p>1. 首次使用请前往「站点配置」完善公司信息、联系方式、备案号等内容。</p>
        <p>2. 在「模板切换」中可选择不同的前台模板风格。</p>
        <p>3. 上传的图片建议为 jpg/png/webp/svg 格式，单张不超过 5MB。</p>
        <p>4. 安装完成后建议删除或重命名根目录的 install.php 文件。</p>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
