<?php
/**
 * 500 服务器错误 v10.0.0
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';
http_response_code(500);
$page_title = '500 服务器错误';
include TEMPLATES_PATH . 'header.php';
?>
<div class="error-page">
    <div class="error-box">
        <div class="error-box__code">500</div>
        <h2 class="error-box__title">服务器错误</h2>
        <p class="error-box__desc">服务器内部错误，请稍后重试。如问题持续存在，请联系管理员。</p>
        <a href="<?php echo site_url(); ?>" class="error-box__btn">返回首页</a>
    </div>
</div>
<?php include TEMPLATES_PATH . 'footer.php'; ?>