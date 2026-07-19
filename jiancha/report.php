<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
$error = $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!checkCsrf()) { $error = '非法请求'; }
    else {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        if (!$title || !$content) { $error = '请填写标题和内容'; }
        elseif (strlen($title) < 4) { $error = '标题至少4个字符'; }
        elseif (strlen($content) < 10) { $error = '内容至少10个字符'; }
        else {
            try { DB::exec("CREATE TABLE IF NOT EXISTS messages (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, title TEXT, content TEXT, name TEXT, phone TEXT, status INTEGER DEFAULT 0, reply TEXT, create_time DATETIME DEFAULT CURRENT_TIMESTAMP)"); } catch (Exception $e) {}
            DB::insert('messages', ['user_id' => $_SESSION['user_id'] ?? 0, 'title' => $title, 'content' => $content, 'name' => $name, 'phone' => $phone]);
            $ok = '举报信息已提交，我们将尽快处理。';
        }
    }
}
$pageTitle = '信访举报';
include __DIR__ . '/includes/header.php';
?>
<div class="page-hero"><h1>信访举报</h1><p>12309 检察服务热线 · 依法受理群众举报</p></div>
<div class="section"><div class="container" style="max-width:700px">
<?php if ($ok): ?><div class="alert alert-success"><?php echo e($ok); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
<div class="card"><div class="card-body">
<form method="post">
<?php echo csrfField(); ?>
<div class="auth-field"><label>举报标题 <span class="req">*</span></label><input type="text" name="title" data-v="title" required></div>
<div class="auth-field"><label>举报内容 <span class="req">*</span></label><textarea name="content" rows="6" data-v="content" required></textarea></div>
<div class="auth-field"><label>您的姓名（选填）</label><input type="text" name="name"></div>
<div class="auth-field"><label>联系电话（选填）</label><input type="text" name="phone" data-v="phone"></div>
<div class="auth-field"><button type="submit" class="btn btn-primary btn-block">提交举报</button></div>
</form>
</div></div>
</div></div>
<?php include __DIR__ . '/includes/footer.php'; ?>