<?php
$activeMenu = 'pages';
$pageTitle = '单页管理';
include __DIR__ . '/header.php';

$msg = '';
$msgType = '';

if (isset($_GET['delete']) && $_GET['delete']) {
    $id = intval($_GET['delete']);
    DB::delete('pages', 'id=?', [$id]);
    header('Location: pages.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $data = [
        'title' => trim($_POST['title'] ?? ''),
        'slug' => trim($_POST['slug'] ?? ''),
        'content' => $_POST['content'] ?? '',
        'status' => intval($_POST['status'] ?? 1),
    ];

    if (empty($data['title'])) {
        $msg = '请填写标题';
        $msgType = 'error';
    } else {
        if ($action == 'add') {
            DB::insert('pages', $data);
            $msg = '单页创建成功';
            $msgType = 'success';
        } elseif ($action == 'edit') {
            $id = intval($_POST['id'] ?? 0);
            $data['update_time'] = date('Y-m-d H:i:s');
            DB::update('pages', $data, 'id=?', [$id]);
            $msg = '单页更新成功';
            $msgType = 'success';
        }
    }
}

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$total = DB::fetchOne("SELECT COUNT(*) as cnt FROM pages")['cnt'];
$pages = DB::fetchAll("SELECT * FROM pages ORDER BY id DESC LIMIT $offset, $perPage");

$editPage = null;
if (isset($_GET['edit'])) {
    $editPage = DB::fetchOne("SELECT * FROM pages WHERE id=?", [intval($_GET['edit'])]);
}
?>

<div class="admin-card">
    <h3><?php echo $editPage ? '编辑单页' : '新建单页'; ?></h3>
    <?php if ($msg): ?>
    <div class="alert alert-<?php echo $msgType; ?>"><?php echo e($msg); ?></div>
    <?php endif; ?>
    <form method="post">
        <input type="hidden" name="action" value="<?php echo $editPage ? 'edit' : 'add'; ?>">
        <?php if ($editPage): ?>
        <input type="hidden" name="id" value="<?php echo $editPage['id']; ?>">
        <?php endif; ?>
        <div class="form-row">
            <div class="form-item">
                <label>标题 *</label>
                <input type="text" name="title" value="<?php echo e($editPage['title'] ?? ''); ?>" required>
            </div>
            <div class="form-item">
                <label>别名(URL标识)</label>
                <input type="text" name="slug" value="<?php echo e($editPage['slug'] ?? ''); ?>">
            </div>
        </div>
        <div class="form-item" style="margin-bottom:15px;">
            <label>内容</label>
            <textarea name="content" rows="15" style="font-family:monospace;"><?php echo e($editPage['content'] ?? ''); ?></textarea>
        </div>
        <div class="form-item" style="margin-bottom:15px;">
            <label style="display:flex; align-items:center; cursor:pointer;">
                <input type="checkbox" name="status" value="1" <?php echo ($editPage['status'] ?? 1) == 1 ? 'checked' : ''; ?> style="width:auto; margin-right:5px;"> 发布
            </label>
        </div>
        <button type="submit" class="btn btn-primary">保存</button>
        <?php if ($editPage): ?>
        <a href="pages.php" class="btn btn-secondary" style="margin-left:10px;">取消编辑</a>
        <?php endif; ?>
    </form>
</div>

<div class="admin-card">
    <h3>单页列表</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>标题</th>
                <th>别名</th>
                <th>状态</th>
                <th>更新时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages as $p): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><?php echo e($p['title']); ?></td>
                <td><?php echo e($p['slug'] ?: '-'); ?></td>
                <td>
                    <span class="badge <?php echo $p['status'] == 1 ? 'badge-success' : 'badge-warning'; ?>">
                        <?php echo $p['status'] == 1 ? '已发布' : '草稿'; ?>
                    </span>
                </td>
                <td><?php echo formatDate($p['update_time']); ?></td>
                <td>
                    <a href="pages.php?edit=<?php echo $p['id']; ?>" class="btn-small btn-default">编辑</a>
                    <a href="page.php?id=<?php echo $p['id']; ?>" target="_blank" class="btn-small btn-default">查看</a>
                    <a href="pages.php?delete=<?php echo $p['id']; ?>" onclick="return confirm('确定删除？');" class="btn-small btn-danger">删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$pages): ?>
            <tr><td colspan="6" style="text-align:center; color:#999; padding:30px 0;">暂无单页</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/footer.php'; ?>
