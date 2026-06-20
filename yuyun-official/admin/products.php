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
        header('Location: products.php');
        exit;
    }
    $data = [
        'icon' => yy_trim($_POST['icon'] ?? 'fa-cube'),
        'title' => yy_trim($_POST['title'] ?? ''),
        'summary' => yy_trim($_POST['summary'] ?? ''),
        'detail' => yy_trim($_POST['detail'] ?? ''),
        'sort_order' => intval($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];
    if (!empty($_FILES['image']['tmp_name'])) {
        $up = yyUpload($_FILES['image'], 'products');
        if (empty($up['error'])) $data['image'] = $up['path'];
    }
    if ($id > 0) {
        dbUpdate('products', $id, $data);
        setFlash('success', '产品已更新');
    } else {
        dbInsert('products', $data);
        setFlash('success', '产品已添加');
    }
    header('Location: products.php');
    exit;
}

if ($action === 'delete' && $id > 0) {
    if (!verifyCsrf($_GET['csrf_token'] ?? '')) {
        setFlash('danger', '安全验证失败');
    } else {
        dbDelete('products', $id);
        setFlash('success', '产品已删除');
    }
    header('Location: products.php');
    exit;
}

$item = ($action === 'edit' && $id > 0) ? dbFind('products', $id) : null;
$items = dbAll('products', 'sort_order', 'ASC');

$pageTitle = '产品管理';
include __DIR__ . '/includes/header.php';
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card">
    <div class="card-header"><h2><?php echo $action === 'edit' ? '编辑产品' : '添加产品'; ?></h2></div>
    <div class="card-body">
        <form method="post" action="?action=<?php echo $action; ?><?php echo $id ? '&id=' . $id : ''; ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo yy_e(csrfToken()); ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>产品名称</label>
                    <input type="text" name="title" class="form-control" value="<?php echo yy_e($item['title'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>图标类名（Font Awesome）</label>
                    <input type="text" name="icon" class="form-control" value="<?php echo yy_e($item['icon'] ?? 'fa-cube'); ?>">
                </div>
            </div>
            <div class="form-group">
                <label>产品简介</label>
                <textarea name="summary" class="form-control"><?php echo yy_e($item['summary'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>产品详情</label>
                <textarea name="detail" class="form-control" rows="5"><?php echo yy_e($item['detail'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>产品图片</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <?php if (!empty($item['image'])): ?>
                    <div class="form-hint">当前：<img src="../<?php echo yy_e($item['image']); ?>" style="max-height:60px;"></div>
                <?php endif; ?>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>排序</label>
                    <input type="number" name="sort_order" class="form-control" value="<?php echo yy_e($item['sort_order'] ?? 0); ?>">
                </div>
                <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:10px;">
                    <label class="checkbox-label"><input type="checkbox" name="is_active" value="1" <?php echo (!isset($item) || ($item['is_active'] ?? 1) == 1) ? 'checked' : ''; ?>> 启用</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
            <a href="products.php" class="btn" style="background:#f0f0f0;">返回</a>
        </form>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <h2>产品列表</h2>
        <a href="?action=add" class="btn btn-primary"><i class="fa-solid fa-plus"></i> 添加</a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr><th>ID</th><th>图标</th><th>名称</th><th>排序</th><th>状态</th><th>操作</th></tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it): ?>
                    <tr>
                        <td><?php echo $it['id']; ?></td>
                        <td><i class="fa-solid <?php echo yy_e($it['icon']); ?>"></i></td>
                        <td><?php echo yy_e($it['title']); ?></td>
                        <td><?php echo $it['sort_order']; ?></td>
                        <td><?php echo $it['is_active'] ? '<span class="badge badge-success">启用</span>' : '<span class="badge badge-warning">禁用</span>'; ?></td>
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
