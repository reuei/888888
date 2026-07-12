<?php
$activeMenu = 'categories';
$pageTitle = '栏目管理';
include __DIR__ . '/header.php';

$msg = '';
$msgType = '';

if (isset($_GET['delete']) && $_GET['delete']) {
    $id = intval($_GET['delete']);
    $hasChildren = DB::fetchOne("SELECT COUNT(*) as cnt FROM categories WHERE parent_id=?", [$id])['cnt'];
    $hasArticles = DB::fetchOne("SELECT COUNT(*) as cnt FROM articles WHERE category_id=?", [$id])['cnt'];
    if ($hasChildren || $hasArticles) {
        $msg = '该栏目下有子栏目或文章，无法删除';
        $msgType = 'error';
    } else {
        DB::delete('categories', 'id=?', [$id]);
        header('Location: categories.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action == 'add' || $action == 'edit') {
        $data = [
            'parent_id' => intval($_POST['parent_id'] ?? 0),
            'name' => trim($_POST['name'] ?? ''),
            'slug' => trim($_POST['slug'] ?? ''),
            'type' => trim($_POST['type'] ?? 'article'),
            'sort_order' => intval($_POST['sort_order'] ?? 0),
            'show_in_menu' => intval($_POST['show_in_menu'] ?? 1),
            'description' => trim($_POST['description'] ?? ''),
        ];
        if (empty($data['name'])) {
            $msg = '栏目名称不能为空';
            $msgType = 'error';
        } else {
            if ($action == 'add') {
                DB::insert('categories', $data);
                $msg = '栏目添加成功';
            } else {
                $id = intval($_POST['id'] ?? 0);
                DB::update('categories', $data, 'id=?', [$id]);
                $msg = '栏目更新成功';
            }
            $msgType = 'success';
        }
    }
}

$categories = DB::fetchAll("SELECT * FROM categories ORDER BY sort_order ASC, id ASC");
$catTree = [];
foreach ($categories as $c) {
    if ($c['parent_id'] == 0) {
        $catTree[$c['id']] = $c;
        $catTree[$c['id']]['children'] = [];
    }
}
foreach ($categories as $c) {
    if ($c['parent_id'] > 0 && isset($catTree[$c['parent_id']])) {
        $catTree[$c['parent_id']]['children'][] = $c;
    }
}

$editCat = null;
if (isset($_GET['edit'])) {
    $editCat = DB::fetchOne("SELECT * FROM categories WHERE id=?", [intval($_GET['edit'])]);
}
?>

<div class="admin-card">
    <h3>栏目管理</h3>

    <?php if ($msg): ?>
    <div class="alert alert-<?php echo $msgType; ?>"><?php echo e($msg); ?></div>
    <?php endif; ?>

    <div style="margin-bottom:20px;">
        <h4 style="margin-bottom:12px;"><?php echo $editCat ? '编辑栏目' : '添加栏目'; ?></h4>
        <form method="post">
            <input type="hidden" name="action" value="<?php echo $editCat ? 'edit' : 'add'; ?>">
            <?php if ($editCat): ?>
            <input type="hidden" name="id" value="<?php echo $editCat['id']; ?>">
            <?php endif; ?>
            <div class="form-row">
                <div class="form-item">
                    <label>上级栏目</label>
                    <select name="parent_id">
                        <option value="0">顶级栏目</option>
                        <?php foreach ($catTree as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo ($editCat['parent_id'] ?? 0) == $c['id'] ? 'selected' : ''; ?>>
                            <?php echo e($c['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-item">
                    <label>栏目名称 *</label>
                    <input type="text" name="name" value="<?php echo e($editCat['name'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-item">
                    <label>栏目别名(URL标识)</label>
                    <input type="text" name="slug" value="<?php echo e($editCat['slug'] ?? ''); ?>">
                </div>
                <div class="form-item">
                    <label>栏目类型</label>
                    <select name="type">
                        <option value="article" <?php echo ($editCat['type'] ?? 'article') == 'article' ? 'selected' : ''; ?>>文章列表</option>
                        <option value="page" <?php echo ($editCat['type'] ?? '') == 'page' ? 'selected' : ''; ?>>单页</option>
                        <option value="link" <?php echo ($editCat['type'] ?? '') == 'link' ? 'selected' : ''; ?>>外链</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-item">
                    <label>排序</label>
                    <input type="number" name="sort_order" value="<?php echo intval($editCat['sort_order'] ?? 0); ?>">
                </div>
                <div class="form-item" style="padding-top:25px;">
                    <label style="display:flex; align-items:center; cursor:pointer;">
                        <input type="checkbox" name="show_in_menu" value="1" <?php echo ($editCat['show_in_menu'] ?? 1) ? 'checked' : ''; ?> style="width:auto; margin-right:5px;"> 导航显示
                    </label>
                </div>
            </div>
            <div class="form-item" style="margin-bottom:15px;">
                <label>栏目描述</label>
                <textarea name="description" rows="2"><?php echo e($editCat['description'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn-small btn-primary">
                <?php echo $editCat ? '更新栏目' : '添加栏目'; ?>
            </button>
            <?php if ($editCat): ?>
            <a href="categories.php" class="btn-small btn-default">取消编辑</a>
            <?php endif; ?>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>栏目名称</th>
                <th>别名</th>
                <th>类型</th>
                <th>排序</th>
                <th>导航显示</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($catTree as $c): ?>
            <tr style="background:#fafafa;">
                <td><?php echo $c['id']; ?></td>
                <td><strong><?php echo e($c['name']); ?></strong></td>
                <td><?php echo e($c['slug']); ?></td>
                <td><?php echo e($c['type']); ?></td>
                <td><?php echo $c['sort_order']; ?></td>
                <td><?php echo $c['show_in_menu'] ? '是' : '否'; ?></td>
                <td>
                    <a href="categories.php?edit=<?php echo $c['id']; ?>" class="btn-small btn-default">编辑</a>
                    <a href="categories.php?delete=<?php echo $c['id']; ?>" onclick="return confirm('确定删除？');" class="btn-small btn-danger">删除</a>
                </td>
            </tr>
            <?php foreach ($c['children'] as $child): ?>
            <tr>
                <td><?php echo $child['id']; ?></td>
                <td style="padding-left:30px;">└ <?php echo e($child['name']); ?></td>
                <td><?php echo e($child['slug']); ?></td>
                <td><?php echo e($child['type']); ?></td>
                <td><?php echo $child['sort_order']; ?></td>
                <td><?php echo $child['show_in_menu'] ? '是' : '否'; ?></td>
                <td>
                    <a href="categories.php?edit=<?php echo $child['id']; ?>" class="btn-small btn-default">编辑</a>
                    <a href="categories.php?delete=<?php echo $child['id']; ?>" onclick="return confirm('确定删除？');" class="btn-small btn-danger">删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/footer.php'; ?>
