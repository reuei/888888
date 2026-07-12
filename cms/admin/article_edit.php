<?php
$activeMenu = 'articles';
$pageTitle = '编辑文章';
include __DIR__ . '/header.php';

$id = intval($_GET['id'] ?? 0);
$article = $id ? DB::fetchOne("SELECT * FROM articles WHERE id=?", [$id]) : null;
$categories = DB::fetchAll("SELECT * FROM categories ORDER BY id ASC");

$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'category_id' => intval($_POST['category_id'] ?? 0),
        'title' => trim($_POST['title'] ?? ''),
        'summary' => trim($_POST['summary'] ?? ''),
        'content' => $_POST['content'] ?? '',
        'author' => trim($_POST['author'] ?? ''),
        'source' => trim($_POST['source'] ?? ''),
        'is_top' => intval($_POST['is_top'] ?? 0),
        'is_hot' => intval($_POST['is_hot'] ?? 0),
        'status' => intval($_POST['status'] ?? 1),
        'update_time' => date('Y-m-d H:i:s'),
    ];

    if (empty($data['title'])) {
        $msg = '请填写文章标题';
        $msgType = 'error';
    } else {
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == UPLOAD_ERR_OK) {
            $result = uploadFile('cover_image');
            if ($result['success']) {
                $data['cover_image'] = $result['path'];
            }
        }

        if ($article) {
            DB::update('articles', $data, 'id=?', [$id]);
            $msg = '文章更新成功';
            $msgType = 'success';
            $article = DB::fetchOne("SELECT * FROM articles WHERE id=?", [$id]);
        } else {
            $data['publish_time'] = date('Y-m-d H:i:s');
            $newId = DB::insert('articles', $data);
            $msg = '文章创建成功';
            $msgType = 'success';
            header('Location: articles.php');
            exit;
        }
    }
}
?>

<div class="admin-card">
    <h3><?php echo $article ? '编辑文章' : '新建文章'; ?></h3>

    <?php if ($msg): ?>
    <div class="alert alert-<?php echo $msgType; ?>"><?php echo e($msg); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-item">
                <label>文章标题 *</label>
                <input type="text" name="title" value="<?php echo e($article['title'] ?? ''); ?>" required>
            </div>
            <div class="form-item">
                <label>所属栏目</label>
                <select name="category_id">
                    <?php foreach ($categories as $c): ?>
                    <option value="<?php echo $c['id']; ?>" <?php echo ($article['category_id'] ?? 0) == $c['id'] ? 'selected' : ''; ?>>
                        <?php echo e($c['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-item">
                <label>作者</label>
                <input type="text" name="author" value="<?php echo e($article['author'] ?? ''); ?>">
            </div>
            <div class="form-item">
                <label>来源</label>
                <input type="text" name="source" value="<?php echo e($article['source'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-item">
                <label>封面图</label>
                <input type="file" name="cover_image" accept="image/*">
                <?php if (!empty($article['cover_image'])): ?>
                <p style="margin-top:5px; font-size:12px; color:#999;">
                    当前：<img src="../<?php echo UPLOAD_URL . e($article['cover_image']); ?>" style="max-width:100px; max-height:60px; vertical-align:middle;">
                </p>
                <?php endif; ?>
            </div>
            <div class="form-item">
                <label>&nbsp;</label>
                <div style="display:flex; gap:20px; padding-top:8px;">
                    <label style="display:flex; align-items:center; cursor:pointer;">
                        <input type="checkbox" name="is_top" value="1" <?php echo !empty($article['is_top']) ? 'checked' : ''; ?> style="width:auto; margin-right:5px;"> 置顶
                    </label>
                    <label style="display:flex; align-items:center; cursor:pointer;">
                        <input type="checkbox" name="is_hot" value="1" <?php echo !empty($article['is_hot']) ? 'checked' : ''; ?> style="width:auto; margin-right:5px;"> 热门
                    </label>
                    <label style="display:flex; align-items:center; cursor:pointer;">
                        <input type="checkbox" name="status" value="1" <?php echo ($article['status'] ?? 1) == 1 ? 'checked' : ''; ?> style="width:auto; margin-right:5px;"> 发布
                    </label>
                </div>
            </div>
        </div>

        <div class="form-item" style="margin-bottom:15px;">
            <label>文章摘要</label>
            <textarea name="summary" rows="2"><?php echo e($article['summary'] ?? ''); ?></textarea>
        </div>

        <div class="form-item" style="margin-bottom:20px;">
            <label>文章内容</label>
            <textarea name="content" rows="20" id="contentEditor" style="font-family:monospace;"><?php echo e($article['content'] ?? ''); ?></textarea>
            <p style="font-size:12px; color:#999; margin-top:5px;">支持HTML标签，可直接粘贴HTML代码</p>
        </div>

        <button type="submit" class="btn btn-primary">保存</button>
        <a href="articles.php" class="btn btn-secondary" style="margin-left:10px;">返回列表</a>
    </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
