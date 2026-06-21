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

$recentMessages = $db->getType() === 'json'
    ? array_slice($db->jsonAll('messages', 'id', 'DESC'), 0, 5)
    : $db->query("SELECT * FROM messages ORDER BY id DESC LIMIT 5");

$recentLogs = getLogs(8);

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
        <p>未读留言</p>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2>快捷入口</h2></div>
    <div class="card-body" style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="settings.php" class="btn btn-primary"><i class="fa-solid fa-gears"></i> 站点配置</a>
        <a href="slides.php" class="btn btn-primary"><i class="fa-solid fa-images"></i> 轮播图管理</a>
        <a href="products.php" class="btn btn-primary"><i class="fa-solid fa-cubes"></i> 产品管理</a>
        <a href="partners.php" class="btn btn-primary"><i class="fa-solid fa-handshake"></i> 合作伙伴</a>
        <a href="messages.php" class="btn btn-primary"><i class="fa-solid fa-envelope"></i> 留言管理</a>
        <a href="filemanager.php" class="btn btn-primary"><i class="fa-solid fa-folder-open"></i> 文件管理</a>
        <a href="backup.php" class="btn btn-primary"><i class="fa-solid fa-database"></i> 备份恢复</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:20px;">
    <div class="card">
        <div class="card-header"><h2>最新留言</h2></div>
        <div class="card-body" style="padding:0;">
            <table class="table" style="margin:0;">
                <thead>
                    <tr><th>姓名</th><th>电话</th><th>内容</th><th>状态</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($recentMessages)): ?>
                        <tr><td colspan="4" style="text-align:center;color:#888;">暂无留言</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentMessages as $m): ?>
                            <tr>
                                <td><?php echo yy_e($m['name']); ?></td>
                                <td><?php echo yy_e($m['phone']); ?></td>
                                <td><?php echo yy_e(yy_truncate($m['content'] ?? '', 20)); ?></td>
                                <td><?php echo ($m['status'] ?? 0) ? '<span class="badge badge-success">已读</span>' : '<span class="badge badge-warning">未读</span>'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>最近操作</h2></div>
        <div class="card-body" style="padding:0;">
            <table class="table" style="margin:0;">
                <thead>
                    <tr><th>操作</th><th>详情</th><th>时间</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($recentLogs)): ?>
                        <tr><td colspan="3" style="text-align:center;color:#888;">暂无日志</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentLogs as $log): ?>
                            <tr>
                                <td><?php echo yy_e($log['action']); ?></td>
                                <td><?php echo yy_e(yy_truncate($log['detail'] ?? '', 24)); ?></td>
                                <td><?php echo yy_e($log['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2>使用说明</h2></div>
    <div class="card-body">
        <p>1. 首次使用请前往「站点配置」完善公司信息、联系方式、备案号等内容。</p>
        <p>2. 在「模板切换」中可选择 8 套不同的前台模板风格。</p>
        <p>3. 上传的图片建议为 jpg/png/webp/svg 格式，单张不超过 5MB。</p>
        <p>4. 安装完成后建议删除或重命名根目录的 install.php 文件。</p>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
