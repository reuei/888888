<?php
require __DIR__ . '/includes/config.php';
if (template_include('contact.php')) exit;
$pageTitle = __('page_contact');
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name && $email && $message) {
        $db = getDb();
        $now = date('Y-m-d H:i:s');
        $stmt = $db->prepare('INSERT INTO feedback (user_id, type, content, contact, created_at) VALUES (:uid, "contact", :c, :contact, :now)');
        $stmt->execute([':uid' => $_SESSION['user_id'] ?? null, ':c' => "姓名：{$name}\n邮箱：{$email}\n留言：\n{$message}", ':contact' => $email, ':now' => $now]);
        flash('success', __('contact_success'));
    } else {
        flash('error', __('fill_complete'));
    }
    redirect(YUYUN_URL . '/contact.php');
}
require __DIR__ . '/includes/header.php';
?>
<section class="page-banner">
    <div class="container">
        <h1><?php echo __('contact_title') ?></h1>
        <p><?php echo __('contact_sub') ?></p>
    </div>
</section>
<section class="section bg-white">
    <div class="container">
        <div class="card-grid" style="grid-template-columns:1fr 1.2fr;align-items:start">
            <div>
                <div class="text-center">
                    <div class="ip-illustration" style="width:120px;height:120px;margin-bottom:16px"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="10" width="36" height="26" rx="2"/><path d="M6 14l18 14 18-14"/><circle cx="38" cy="16" r="2" fill="currentColor" stroke="none"/></svg></div>
                </div>
                <h2 style="font-size:24px;margin-bottom:16px"><?php echo __('contact') ?></h2>
                <ul class="info-list">
                    <li><i class="iconfont icon-building"></i> <?php echo e(setting('company_name')) ?></li>
                    <li><i class="iconfont icon-map"></i> <?php echo e(setting('company_address')) ?></li>
                    <li><i class="iconfont icon-phone"></i> <?php echo e(setting('company_phone')) ?></li>
                    <li><i class="iconfont icon-envelope"></i> <?php echo e(setting('site_email')) ?></li>
                    <li><i class="iconfont icon-users"></i> <a href="<?php echo e(setting('company_group')) ?>" target="_blank"><?php echo __('official_group') ?></a></li>
                </ul>
                <?php
                $mapUrl = setting('company_map_url');
                if (!$mapUrl || stripos($mapUrl, 'baidu') !== false) {
                    $mapUrl = 'https://www.openstreetmap.org/export/embed.html?bbox=116.30%2C39.85%2C116.52%2C39.96&layer=mapnik&marker=39.9042%2C116.4074';
                }
                ?>
                <iframe class="map-embed" src="<?php echo e($mapUrl) ?>" loading="lazy" allowfullscreen></iframe>
            </div>
            <div>
                <div class="admin-card" style="box-shadow:var(--shadow)">
                    <h3 style="margin-bottom:18px"><i class="iconfont icon-send"></i> <?php echo __('message_content') ?></h3>
                    <?php echo render_flash() ?>
                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
                        <div class="form-row">
                            <div class="form-group">
                                <label><?php echo __('your_name') ?></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label><?php echo __('contact_email') ?></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><?php echo __('message_content') ?></label>
                            <textarea name="message" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="iconfont icon-send"></i> <?php echo __('submit_message') ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
