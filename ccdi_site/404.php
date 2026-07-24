<?php
/**
 * 404 页面未找到 v9.0.0
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';
http_response_code(404);
$page_title = '404 页面未找到';
include TEMPLATES_PATH . 'header.php';
?>
<div class="error-page">
    <div class="error-box">
        <div class="error-box__code">404</div>
        <h2 class="error-box__title">页面未找到</h2>
        <p class="error-box__desc">您访问的页面不存在或已被移除。</p>
        <a href="<?php echo site_url(); ?>" class="error-box__btn">返回首页</a>
    </div>
</div>
<?php include TEMPLATES_PATH . 'footer.php'; ?>