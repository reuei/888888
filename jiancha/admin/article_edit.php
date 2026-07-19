<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
if (!isAdmin()) { redirect('login.php'); exit; }
$id = intval($_GET['id'] ?? 0);
$article = $id ? DB::fetchOne("SELECT * FROM articles WHERE id=?", [$id]) : null;
$error = $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkCsrf()) { $error = '非法请求'; }
    else {
        $title = trim($_POST['title'] ?? '');
        $content = $_POST['content'] ?? '';
        $catId = intval($_POST['category_id'] ?? 0);
        $isTop = intval($_POST['is_top'] ?? 0);
        $status = intval($_POST['status'] ?? 1);
        $source = trim($_POST['source'] ?? '');
        if (!$title || !$content) { $error = '请填写标题和内容'; }
        else {
            $data = ['title' => $title, 'content' => $content, 'category_id' => $catId, 'is_top' => $isTop, 'status' => $status, 'source' => $source];
            if ($id) {
                DB::update('articles', $data, 'id=?', [$id]);
                $ok = '更新成功';
            } else {
                $data['publish_time'] = date('Y-m-d H:i:s');
                $data['views'] = 0;
                $id = DB::insert('articles', $data);
                $ok = '发布成功';
            }
            $article = DB::fetchOne("SELECT * FROM articles WHERE id=?", [$id]);
        }
    }
}
$adminTitle = $article ? '编辑文章' : '新增文章';
$cats = DB::fetchAll("SELECT * FROM categories ORDER BY sort_order ASC");
include __DIR__ . '/header.php';
?>
<?php if ($ok): ?><div class="alert alert-success"><?php echo e($ok); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
<div class="admin-card">
<form method="post">
<?php echo csrfField(); ?>
<div class="admin-form admin-form-row">
<div class="admin-form"><label>标题</label><input type="text" name="title" value="<?php echo e($article['title'] ?? ''); ?>" required></div>
<div class="admin-form"><label>栏目</label><select name="category_id"><option value="0">- 选择 -</option><?php foreach ($cats as $c): ?><option value="<?php echo $c['id']; ?>" <?php echo ($article['category_id'] ?? 0) == $c['id'] ? 'selected' : ''; ?>><?php echo e($c['name']); ?></option><?php endforeach; ?></select></div>
</div>
<div class="admin-form admin-form-row">
<div class="admin-form"><label>来源</label><input type="text" name="source" value="<?php echo e($article['source'] ?? ''); ?>"></div>
<div class="admin-form"><label>状态</label><select name="status"><option value="1" <?php echo ($article['status'] ?? 1) == 1 ? 'selected' : ''; ?>>发布</option><option value="0" <?php echo ($article['status'] ?? 1) == 0 ? 'selected' : ''; ?>>草稿</option></select></div>
</div>
<div class="admin-form"><label><input type="checkbox" name="is_top" value="1" <?php echo ($article['is_top'] ?? 0) ? 'checked' : ''; ?>> 置顶</label></div>
<div class="admin-form"><label>内容</label><textarea name="content" rows="16" style="font-family:consolas,monospace;font-size:13px"><?php echo e($article['content'] ?? ''); ?></textarea></div>
<div class="admin-form"><button type="submit" class="btn btn-primary"><?php echo $article ? '更新' : '发布'; ?></button> <a href="articles.php" class="btn" style="background:var(--bg);color:var(--text)">返回</a></div>
</form>
</div>
<?php include __DIR__ . '/footer.php'; ?>