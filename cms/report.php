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
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <span class="sep">/</span>
            <span>信访举报</span>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="section" style="max-width:700px; margin:0 auto;">
                <div class="section-header">
                    <h3>人民检察院信访举报</h3>
                </div>
                <div class="section-body">
                    <div style="background:#fff7e6; border:1px solid #ffd591; padding:15px; border-radius:4px; margin-bottom:20px; font-size:13px; color:#d46b08;">
                        <p><strong>举报须知：</strong></p>
                        <p style="margin-top:8px;">1. 受理范围：检察机关管辖的职务犯罪、刑事犯罪、民事行政申诉、公益诉讼线索等举报事项；</p>
                        <p>2. 提倡实名举报，我们将依法严格保护举报人个人信息；</p>
                        <p>3. 举报内容应客观真实，不得捏造事实诬告陷害他人，否则依法追究法律责任；</p>
                        <p>4. 受理电话：12309（检察机关统一举报热线）</p>
                    </div>

                    <?php if ($msg): ?>
                    <div class="form-<?php echo $msgType; ?>" style="padding:10px 15px; border-radius:4px; margin-bottom:20px; <?php echo $msgType == 'success' ? 'background:#f6ffed; color:#52c41a; border:1px solid #b7eb8f;' : 'background:#fff1f0; color:#f5222d; border:1px solid #ffa39e;'; ?>">
                        <?php echo e($msg); ?>
                    </div>
                    <?php endif; ?>

                    <form method="post" data-toast-form>
                        <div class="form-item">
                            <label>举报标题 *</label>
                            <input type="text" name="title" value="<?php echo e($_POST['title'] ?? ''); ?>" required placeholder="请简要描述举报事项" data-validate="title">
                            <div class="field-tip"></div>
                        </div>
                        <div class="form-item">
                            <label>举报内容 *</label>
                            <textarea name="content" rows="8" required placeholder="请详细描述被举报对象、违法事实、相关证据等情况" data-validate="content"><?php echo e($_POST['content'] ?? ''); ?></textarea>
                            <div class="field-tip"></div>
                        </div>
                        <div class="form-item">
                            <label>您的姓名（选填）</label>
                            <input type="text" name="name" value="<?php echo e($_POST['name'] ?? ''); ?>" placeholder="选填，可匿名举报">
                        </div>
                        <div class="form-item">
                            <label>联系方式（选填）</label>
                            <input type="text" name="contact" value="<?php echo e($_POST['contact'] ?? ''); ?>" placeholder="手机号或邮箱，方便我们联系您" data-validate="phone">
                            <div class="field-tip"></div>
                        </div>
                        <button type="submit" class="btn btn-block">提交举报</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
