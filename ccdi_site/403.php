<?php
/**
 * 403 禁止访问
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';
http_response_code(403);
$page_title = '403 禁止访问';
include TEMPLATES_PATH . 'header.php';
?>
<div class="error-page">
    <div class="error-box">
        <div class="error-code">403</div>
        <div class="error-title">禁止访问</div>
        <div class="error-desc">您没有权限访问此页面或资源。</div>
        <a href="<?php echo site_url(); ?>" class="btn btn-primary">返回首页</a>
    </div>
</div>
<?php include TEMPLATES_PATH . 'footer.php'; ?>