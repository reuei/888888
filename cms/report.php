<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = 'report';
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $contact = trim($_POST['contact'] ?? '');

    if (empty($title) || empty($content)) {
        $msg = '请填写标题和举报内容';
        $msgType = 'error';
    } else {
        DB::insert('messages', [
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'name' => $name,
            'contact' => $contact,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'status' => 0,
        ]);
        $msg = '举报信息已提交，感谢您的监督！';
        $msgType = 'success';
    }
}

$pageTitle = '信访举报';
include __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="crumbs">
        <a href="<?php echo BASE_URL; ?>index.php">首页</a>
        <span class="sep">/</span>
        <span>信访举报</span>
    </div>
</div>

<div class="container" style="padding-bottom:60px;">
    <div class="form-card" style="max-width:680px;">
        <div class="form-card-head">
            <h2>信访举报</h2>
            <p>PROCURATORIAL REPORT</p>
        </div>
        <div class="form-card-body">
            <div class="alert alert-info">
                <strong>举报须知：</strong>受理范围包括检察机关管辖的职务犯罪、刑事犯罪、民事行政申诉、公益诉讼线索等。提倡实名举报，我们将依法严格保护举报人个人信息。
            </div>

            <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msgType == 'success' ? 'success' : 'error'; ?>"><?php echo e($msg); ?></div>
            <?php endif; ?>

            <form method="post" data-toast-form>
                <div class="form-row">
                    <label>举报标题 <span class="req">*</span></label>
                    <input type="text" name="title" value="<?php echo e($_POST['title'] ?? ''); ?>" required placeholder="请简要描述举报事项" data-validate="title">
                    <div class="form-tip"></div>
                </div>
                <div class="form-row">
                    <label>举报内容 <span class="req">*</span></label>
                    <textarea name="content" rows="8" required placeholder="请详细描述被举报对象、违法事实、相关证据等情况" data-validate="content"><?php echo e($_POST['content'] ?? ''); ?></textarea>
                    <div class="form-tip"></div>
                </div>
                <div class="form-row" style="display:grid; grid-template-columns: 1fr 1fr; gap:14px;">
                    <div>
                        <label>您的姓名</label>
                        <input type="text" name="name" value="<?php echo e($_POST['name'] ?? ''); ?>" placeholder="选填，可匿名">
                    </div>
                    <div>
                        <label>联系方式</label>
                        <input type="text" name="contact" value="<?php echo e($_POST['contact'] ?? ''); ?>" placeholder="选填，手机号或邮箱" data-validate="phone">
                        <div class="form-tip"></div>
                    </div>
                </div>
                <button type="submit" class="btn btn-block">提 交 举 报</button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>