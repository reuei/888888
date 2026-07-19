<?php
/**
 * 404 页面未找到
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';
http_response_code(404);
$page_title = '404 页面未找到';
include TEMPLATES_PATH . 'header.php';
?>
<div class="error-page">
    <div class="error-box">
        <div class="error-code">404</div>
        <div class="error-title">页面未找到</div>
        <div class="error-desc">您访问的页面不存在或已被移除。</div>
        <a href="<?php echo site_url(); ?>" class="btn btn-primary">返回首页</a>
    </div>
</div>
<?php include TEMPLATES_PATH . 'footer.php'; ?>