<?php
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/config.php';
require_once YUYUN_ROOT . '/includes/functions.php';
require_once YUYUN_ROOT . '/includes/auth.php';
requireAdminLogin();

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('danger', '安全验证失败，请刷新页面重试');
        header('Location: links.php');
        exit;
    }
    $data = [
        'name' => yy_trim($_POST['name'] ?? ''),
        'url' => yy_trim($_POST['url'] ?? ''),
        'sort_order' => intval($_POST['sort_order'] ?? 0),
    ];
    if ($id > 0) {
        dbUpdate('links', $id, $data);
        setFlash('success', '友情链接已更新');
    } else {
        dbInsert('links', $data);
        setFlash('success', '友情链接已添加');
    }
    header('Location: links.php');
    exit;
}

if ($action === 'delete' && $id > 0) {
    if (!verifyCsrf($_GET['csrf_token'] ?? '')) {
        setFlash('danger', '安全验证失败');
    } else {
        dbDelete('links', $id);
        setFlash('success', '友情链接已删除');
    }
    header('Location: links.php');
    exit;
}

$item = ($action === 'edit' && $id > 0) ? dbFind('links', $id) : null;
$items = dbAll('links', 'sort_order', 'ASC');

$pageTitle = '友情链接';
include __DIR__ . '/includes/header.php';
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card">
    <div class="card-header"><h2><?php echo $action === 'edit' ? '编辑友情链接' : '添加友情链接'; ?></h2></div>
    <div class="card-body">
        <form method="post" action="?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo yy_e(csrfToken()); ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>名称</label>
                    <input type="text" name="name" class="form-control" value="<?php echo yy_e($item['name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>链接</label>
                    <input type="text" name="url" class="form-control" value="<?php echo yy_e($item['url'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>排序</label>
                    <input type="number" name="sort_order" class="form-control" value="<?php echo yy_e($item['sort_order'] ?? 0); ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
            <a href="links.php" class="btn" style="background:#f0f0f0;">返回</a>
        </form>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <h2>友情链接列表</h2>
        <a href="?action=add" class="btn btn-primary"><i class="fa-solid fa-plus"></i> 添加</a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr><th>ID</th><th>名称</th><th>链接</th><th>排序</th><th>操作</th></tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it): ?>
                    <tr>
                        <td><?php echo $it['id']; ?></td>
                        <td><?php echo yy_e($it['name']); ?></td>
                        <td><?php echo yy_e($it['url']); ?></td>
                        <td><?php echo $it['sort_order']; ?></td>
                        <td>
                            <a href="?action=edit&id=<?php echo $it['id']; ?>" class="btn btn-sm btn-primary">编辑</a>
                            <a href="?action=delete&id=<?php echo $it['id']; ?>&csrf_token=<?php echo yy_e(csrfToken()); ?>" class="btn btn-sm btn-danger" onclick="return confirm('确定删除吗？')">删除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
