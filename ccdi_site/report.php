<?php
/**
 * 监督举报 / 在线留言页面
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

<div class="page-content">
    <div class="container">
        <div class="report-page">
            <div class="report-header">
                <h1><i class="fas fa-bullhorn"></i> <?php echo $type === 'report' ? '监督举报' : '在线留言'; ?></h1>
                <p>欢迎通过本平台反映问题、提出意见建议。请如实填写以下信息。</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if (!$success): ?>
            <form method="post" class="report-form" id="reportForm" novalidate>
                <?php echo csrf_field(); ?>
                <input type="hidden" name="report_type" value="<?php echo $type === 'report' ? 'report' : 'message'; ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">姓名</label>
                        <input type="text" id="name" name="name" placeholder="请输入您的姓名">
                    </div>
                    <div class="form-group">
                        <label for="email">邮箱</label>
                        <input type="email" id="email" name="email" placeholder="请输入您的邮箱">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">电话</label>
                        <input type="text" id="phone" name="phone" placeholder="请输入您的联系电话">
                    </div>
                    <div class="form-group">
                        <label for="title">标题</label>
                        <input type="text" id="title" name="title" placeholder="请输入标题">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="content">内容 <span class="required">*</span></label>
                    <textarea id="content" name="content" rows="8" required placeholder="请详细描述您要反映的问题或意见建议..."></textarea>
                    <span class="form-feedback" id="contentFeedback"></span>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> 提交<?php echo $type === 'report' ? '举报' : '留言'; ?>
                    </button>
                </div>
            </form>
            <?php endif; ?>
            
            <div class="report-info-box">
                <h3><i class="fas fa-info-circle"></i> 举报须知</h3>
                <ul>
                    <li>检举、控告人应据实检举、控告，不得捏造事实、制造假证、诬告陷害他人。</li>
                    <li>提倡实名举报，我们将严格保密举报人信息。</li>
                    <li>举报电话：<strong>12388</strong></li>
                    <li>举报网站：<a href="http://www.12388.gov.cn" target="_blank">www.12388.gov.cn</a></li>
                    <li>来信地址：中央纪委国家监委信访室</li>
                </ul>
            </div>
        </div>
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
                content.classList.add('invalid'); content.classList.remove('valid');
                contentFb.textContent = '请输入内容'; contentFb.className = 'form-feedback error';
            } else if (val.length < 10) {
                content.classList.add('invalid'); content.classList.remove('valid');
                contentFb.textContent = '内容至少10个字符'; contentFb.className = 'form-feedback error';
            } else {
                content.classList.remove('invalid'); content.classList.add('valid');
                contentFb.textContent = '内容有效'; contentFb.className = 'form-feedback success';
            }
        });
    }
});
</script>

<?php include TEMPLATES_PATH . 'footer.php'; ?>