<?php
/**
 * 403 禁止访问 v9.0.0
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';
http_response_code(403);
$page_title = '403 禁止访问';
include TEMPLATES_PATH . 'header.php';
?>
<div class="error-page">
    <div class="error-box">
        <div class="error-box__code">403</div>
        <h2 class="error-box__title">禁止访问</h2>
        <p class="error-box__desc">您没有权限访问此页面或资源。</p>
        <a href="<?php echo site_url(); ?>" class="error-box__btn">返回首页</a>
    </div>
</div>
<?php include TEMPLATES_PATH . 'footer.php'; ?>