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
        header('Location: testimonials.php');
        exit;
    }

    $data = [
        'content' => yy_trim($_POST['content'] ?? ''),
        'author' => yy_trim($_POST['author'] ?? ''),
        'company' => yy_trim($_POST['company'] ?? ''),
        'stars' => intval($_POST['stars'] ?? 5),
        'sort_order' => intval($_POST['sort_order'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0,
    ];

    if ($id > 0) {
        dbUpdate('testimonials', $id, $data);
        addLog('更新客户评价', $data['author']);
        setFlash('success', '评价已更新');
    } else {
        dbInsert('testimonials', $data);
        addLog('添加客户评价', $data['author']);
        setFlash('success', '评价已添加');
    }
    header('Location: testimonials.php');
    exit;
}

if ($action === 'delete' && $id > 0) {
    if (!verifyCsrf($_GET['csrf_token'] ?? '')) {
        setFlash('danger', '安全验证失败');
    } else {
        dbDelete('testimonials', $id);
        addLog('删除客户评价', 'ID：' . $id);
        setFlash('success', '评价已删除');
    }
    header('Location: testimonials.php');
    exit;
}

$item = ($action === 'edit' && $id > 0) ? dbFind('testimonials', $id) : null;
$items = dbAll('testimonials', 'sort_order', 'ASC');

$pageTitle = '客户评价';
include __DIR__ . '/includes/header.php';
?>

<?php if ($action === 'edit' || $action === 'add'): ?>
<div class="card">
    <div class="card-header"><h2><?php echo $item ? '编辑评价' : '添加评价'; ?></h2></div>
    <div class="card-body">
        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo yy_e(csrfToken()); ?>">
            <div class="form-group">
                <label>评价内容</label>
                <textarea name="content" class="form-control" rows="4" required><?php echo yy_e($item['content'] ?? ''); ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>评价人</label>
                    <input type="text" name="author" class="form-control" value="<?php echo yy_e($item['author'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label>公司/职位</label>
                    <input type="text" name="company" class="form-control" value="<?php echo yy_e($item['company'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>星级（1-5）</label>
                    <input type="number" name="stars" class="form-control" min="1" max="5" value="<?php echo $item['stars'] ?? 5; ?>">
                </div>
                <div class="form-group">
                    <label>排序</label>
                    <input type="number" name="sort_order" class="form-control" value="<?php echo $item['sort_order'] ?? 0; ?>">
                </div>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="is_active" <?php echo ($item['is_active'] ?? 1) ? 'checked' : ''; ?>> 启用</label>
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
            <a href="testimonials.php" class="btn" style="background:#f0f0f0;">取消</a>
        </form>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <h2>客户评价列表</h2>
        <a href="?action=add" class="btn btn-primary btn-sm">+ 添加评价</a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr><th>排序</th><th>评价人</th><th>公司</th><th>星级</th><th>内容摘要</th><th>状态</th><th>操作</th></tr>
            </thead>
            <tbody>
                <?php foreach ($items as $it): ?>
                    <tr>
                        <td><?php echo $it['sort_order']; ?></td>
                        <td><?php echo yy_e($it['author']); ?></td>
                        <td><?php echo yy_e($it['company']); ?></td>
                        <td><?php echo str_repeat('<i class="fa-solid fa-star" style="color:#f59e0b;"></i>', intval($it['stars'])); ?></td>
                        <td><?php echo yy_e(yy_truncate($it['content'] ?? '', 30)); ?></td>
                        <td><?php echo $it['is_active'] ? '<span class="badge badge-success">启用</span>' : '<span class="badge badge-warning">禁用</span>'; ?></td>
                        <td>
                            <a href="?action=edit&id=<?php echo $it['id']; ?>" class="btn btn-sm btn-primary">编辑</a>
                            <a href="?action=delete&id=<?php echo $it['id']; ?>&csrf_token=<?php echo yy_e(csrfToken()); ?>" class="btn btn-sm btn-danger" onclick="return confirm('确定删除吗？')">删除</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?>
                    <tr><td colspan="7" style="text-align:center;color:#888;">暂无评价</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
