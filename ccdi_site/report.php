<?php
/**
 * 监督举报 / 在线留言页面 v9.0.0
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';

$page_title = '监督举报';
$type = get('type', 'report');
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify(post(CSRF_TOKEN_NAME))) {
        $error = '表单验证失败，请刷新页面重试';
    } else {
        $name = post('name');
        $email = post('email');
        $phone = post('phone');
        $title = post('title');
        $content = post('content');
        $report_type = post('report_type', 'report');

        if (empty($content)) {
            $error = '请输入内容';
        } else {
            $data = [
                'user_id' => current_user_id(),
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'title' => $title ?: '无标题',
                'content' => $content,
                'type' => $report_type,
                'report_type' => $report_type,
                'status' => 'pending',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($report_type === 'report') {
                $id = db_insert('reports', $data);
            } else {
                $id = db_insert('messages', $data);
            }

            if ($id) {
                $success = '提交成功！我们将尽快处理您的' . ($report_type === 'report' ? '举报' : '留言') . '。';
            } else {
                $error = '提交失败，请稍后重试';
            }
        }
    }
}

include TEMPLATES_PATH . 'header.php';
?>

<div class="report-page">
    <div class="report-page__header">
        <h1 class="report-page__title"><i class="fas fa-bullhorn"></i> <?php echo $type === 'report' ? '监督举报' : '在线留言'; ?></h1>
        <p class="report-page__desc">欢迎通过本平台反映问题、提出意见建议。请如实填写以下信息。</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="post" class="report-form" id="reportForm" novalidate>
        <?php echo csrf_field(); ?>
        <input type="hidden" name="report_type" value="<?php echo $type === 'report' ? 'report' : 'message'; ?>">

        <div class="report-form__grid">
            <div class="report-form__group">
                <label for="name" class="report-form__label">姓名</label>
                <input type="text" id="name" name="name" placeholder="请输入您的姓名" class="form-input">
            </div>
            <div class="report-form__group">
                <label for="email" class="report-form__label">邮箱</label>
                <input type="email" id="email" name="email" placeholder="请输入您的邮箱" class="form-input">
            </div>
        </div>

        <div class="report-form__grid">
            <div class="report-form__group">
                <label for="phone" class="report-form__label">电话</label>
                <input type="text" id="phone" name="phone" placeholder="请输入您的联系电话" class="form-input">
            </div>
            <div class="report-form__group">
                <label for="title" class="report-form__label">标题</label>
                <input type="text" id="title" name="title" placeholder="请输入标题" class="form-input">
            </div>
        </div>

        <div class="report-form__group report-form__full">
            <label for="content" class="report-form__label report-form__label--required">内容</label>
            <textarea id="content" name="content" rows="8" required placeholder="请详细描述您要反映的问题或意见建议..." class="form-input"></textarea>
            <span class="form-feedback" id="contentFeedback"></span>
        </div>

        <div class="report-form__group report-form__full">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> 提交<?php echo $type === 'report' ? '举报' : '留言'; ?>
            </button>
        </div>
    </form>
    <?php endif; ?>

    <div class="report-info-box">
        <div class="report-info-box__title"><i class="fas fa-info-circle"></i> 举报须知</div>
        <ul>
            <li>检举、控告人应据实检举、控告，不得捏造事实、制造假证、诬告陷害他人。</li>
            <li>提倡实名举报，我们将严格保密举报人信息。</li>
            <li>举报电话：<strong>12388</strong></li>
            <li>举报网站：<a href="http://www.12388.gov.cn" target="_blank" style="color:var(--color-primary);">www.12388.gov.cn</a></li>
            <li>来信地址：中央纪委国家监委信访室</li>
        </ul>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var content = document.getElementById('content');
    var contentFb = document.getElementById('contentFeedback');
    if (content) {
        content.addEventListener('input', function() {
            var val = content.value.trim();
            if (val === '') {
                content.classList.add('form-input--error'); content.classList.remove('form-input--success');
                contentFb.textContent = '请输入内容'; contentFb.className = 'form-feedback form-feedback--error';
            } else if (val.length < 10) {
                content.classList.add('form-input--error'); content.classList.remove('form-input--success');
                contentFb.textContent = '内容至少10个字符'; contentFb.className = 'form-feedback form-feedback--error';
            } else {
                content.classList.remove('form-input--error'); content.classList.add('form-input--success');
                contentFb.textContent = '内容有效'; contentFb.className = 'form-feedback form-feedback--success';
            }
        });
    }
});
</script>

<?php include TEMPLATES_PATH . 'footer.php'; ?>