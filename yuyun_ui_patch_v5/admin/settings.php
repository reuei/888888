<?php
$pageTitle = __('admin_settings');
require __DIR__ . '/../includes/admin_header.php';
$db = getDb();
$keys = ['site_name','site_slogan','site_short','sales_phone','company_name','company_short','company_address','company_phone','company_group','company_intro','company_map_url','site_email','site_email_from','international_url','site_icp','site_police','site_license','site_ev_license','footer_statement','banner_enabled','banner_text','banner_bg_color','banner_icon','staff_bg_color','email_verify_enabled'];
$imageKeys = ['site_logo','site_favicon','site_license_image','site_ev_license_image','site_security_image','site_trust_image','staff_bg_image'];
$bannerIcons = ['bell'=>'铃铛','megaphone'=>'喇叭','info'=>'信息','cloud'=>'云朵','certificate'=>'证书','shield'=>'盾牌','globe'=>'地球'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        verify_csrf();
        $saveError = false;
        foreach ($keys as $k) {
            $val = trim($_POST[$k] ?? '');
            if ($k === 'banner_enabled' || $k === 'email_verify_enabled') {
                $val = isset($_POST[$k]) && $_POST[$k] === '1' ? '1' : '0';
            }
            if (!setSettingSafe($k, $val)) {
                $saveError = true;
            }
        }
        foreach ($imageKeys as $f) {
            if (!empty($_FILES[$f]['tmp_name'])) {
                try {
                    $path = upload_file($_FILES[$f], 'cert', ['image/jpeg','image/png','image/webp','image/gif','image/x-icon','image/vnd.microsoft.icon']);
                    setSettingSafe($f, $path);
                } catch (Exception $e) {
                    flash('error', $f . __('upload_failed') . '：' . $e->getMessage());
                }
            }
        }
        if ($saveError) {
            flash('error', __('save_config') . '：部分设置保存失败，请检查数据库权限');
        } else {
            flash('success', __('config_saved'));
        }
    } catch (Throwable $e) {
        flash('error', __('save_config') . '：' . $e->getMessage());
    }
    redirect(YUYUN_URL . '/admin/settings.php');
}
?>
<div class="admin-card">
    <h3 style="margin-bottom:20px"><?php echo __('admin_settings') ?></h3>
    <?php echo render_flash() ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
        <div class="form-row">
            <div class="form-group"><label><?php echo __('site_name_label') ?></label><input type="text" name="site_name" class="form-control" value="<?php echo e(setting('site_name')) ?>"></div>
            <div class="form-group"><label><?php echo __('site_short_label') ?></label><input type="text" name="site_short" class="form-control" value="<?php echo e(setting('site_short')) ?>"></div>
        </div>
        <div class="form-group"><label><?php echo __('site_slogan_label') ?></label><input type="text" name="site_slogan" class="form-control" value="<?php echo e(setting('site_slogan')) ?>"></div>
        <div class="form-row">
            <div class="form-group"><label><?php echo __('site_logo') ?></label><input type="file" name="site_logo" class="form-control" accept="image/*"> <?php if (setting('site_logo')): ?><p style="font-size:12px"><img src="<?php echo e(setting('site_logo')) ?>" style="max-height:40px;margin-top:6px;border-radius:4px"></p><?php endif; ?></div>
            <div class="form-group"><label><?php echo __('site_favicon') ?></label><input type="file" name="site_favicon" class="form-control" accept="image/*,.ico"> <?php if (setting('site_favicon')): ?><p style="font-size:12px"><?php echo e(setting('site_favicon')) ?></p><?php endif; ?></div>
        </div>

        <h4 style="margin:24px 0 14px;color:var(--dark);font-size:16px"><?php echo __('banner_settings') ?></h4>
        <div class="form-row">
            <div class="form-group"><label><input type="checkbox" name="banner_enabled" value="1" <?php echo setting('banner_enabled','1')==='1'?'checked':'' ?>> <?php echo __('banner_enable') ?></label></div>
            <div class="form-group"><label><?php echo __('banner_bg_color') ?></label><input type="color" name="banner_bg_color" class="form-control" value="<?php echo e(setting('banner_bg_color','#0a0a0a')) ?>" style="height:40px"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label><?php echo __('banner_text_label') ?></label><input type="text" name="banner_text" class="form-control" value="<?php echo e(setting('banner_text')) ?>"></div>
            <div class="form-group"><label><?php echo __('banner_icon_label') ?></label>
                <select name="banner_icon" class="form-control">
                    <?php foreach ($bannerIcons as $k=>$v): ?>
                    <option value="<?php echo e($k) ?>" <?php echo setting('banner_icon','megaphone')===$k?'selected':'' ?>><?php echo e($v) ?>（icon-<?php echo e($k) ?>）</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <h4 style="margin:24px 0 14px;color:var(--dark);font-size:16px"><?php echo __('cert_images') ?></h4>
        <div class="form-row">
            <div class="form-group"><label><?php echo __('business_license_image') ?></label><input type="file" name="site_license_image" class="form-control" accept="image/*"> <?php if (setting('site_license_image')): ?><p style="font-size:12px"><img src="<?php echo e(setting('site_license_image')) ?>" style="max-height:60px;margin-top:6px;border-radius:4px;border:1px solid #eee"></p><?php endif; ?></div>
            <div class="form-group"><label><?php echo __('ev_license_image') ?></label><input type="file" name="site_ev_license_image" class="form-control" accept="image/*"> <?php if (setting('site_ev_license_image')): ?><p style="font-size:12px"><img src="<?php echo e(setting('site_ev_license_image')) ?>" style="max-height:60px;margin-top:6px;border-radius:4px;border:1px solid #eee"></p><?php endif; ?></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label><?php echo __('security_cert_image') ?></label><input type="file" name="site_security_image" class="form-control" accept="image/*"> <?php if (setting('site_security_image')): ?><p style="font-size:12px"><img src="<?php echo e(setting('site_security_image')) ?>" style="max-height:60px;margin-top:6px;border-radius:4px;border:1px solid #eee"></p><?php endif; ?></div>
            <div class="form-group"><label><?php echo __('trust_cert_image') ?></label><input type="file" name="site_trust_image" class="form-control" accept="image/*"> <?php if (setting('site_trust_image')): ?><p style="font-size:12px"><img src="<?php echo e(setting('site_trust_image')) ?>" style="max-height:60px;margin-top:6px;border-radius:4px;border:1px solid #eee"></p><?php endif; ?></div>
        </div>

        <h4 style="margin:24px 0 14px;color:var(--dark);font-size:16px"><?php echo __('staff_section') ?></h4>
        <div class="form-row">
            <div class="form-group"><label><?php echo __('staff_bg_image') ?></label><input type="file" name="staff_bg_image" class="form-control" accept="image/*"> <?php if (setting('staff_bg_image')): ?><p style="font-size:12px"><img src="<?php echo e(setting('staff_bg_image')) ?>" style="max-height:60px;margin-top:6px;border-radius:4px;border:1px solid #eee"></p><?php endif; ?></div>
            <div class="form-group"><label><?php echo __('staff_bg_color') ?></label><input type="color" name="staff_bg_color" class="form-control" value="<?php echo e(setting('staff_bg_color','#f5f7fa')) ?>" style="height:40px"></div>
        </div>

        <h4 style="margin:24px 0 14px;color:var(--dark);font-size:16px"><?php echo __('email_verify_settings') ?></h4>
        <div class="form-row">
            <div class="form-group"><label><input type="checkbox" name="email_verify_enabled" value="1" <?php echo setting('email_verify_enabled','0')==='1'?'checked':'' ?>> <?php echo __('email_verify_enable') ?></label>
                <p style="font-size:12px;color:var(--text-2)"><?php echo __('email_verify_desc') ?></p>
            </div>
            <div class="form-group"><label><?php echo __('email_sender') ?></label><input type="text" name="site_email_from" class="form-control" value="<?php echo e(setting('site_email_from')) ?>"></div>
        </div>

        <div class="form-row">
            <div class="form-group"><label><?php echo __('sales_phone_label') ?></label><input type="text" name="sales_phone" class="form-control" value="<?php echo e(setting('sales_phone')) ?>"></div>
            <div class="form-group"><label><?php echo __('contact_email_label') ?></label><input type="text" name="site_email" class="form-control" value="<?php echo e(setting('site_email')) ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label><?php echo __('company_name_label') ?></label><input type="text" name="company_name" class="form-control" value="<?php echo e(setting('company_name')) ?>"></div>
            <div class="form-group"><label><?php echo __('company_short_label') ?></label><input type="text" name="company_short" class="form-control" value="<?php echo e(setting('company_short')) ?>"></div>
        </div>
        <div class="form-group"><label><?php echo __('company_address_label') ?></label><input type="text" name="company_address" class="form-control" value="<?php echo e(setting('company_address')) ?>"></div>
        <div class="form-row">
            <div class="form-group"><label><?php echo __('company_phone_label') ?></label><input type="text" name="company_phone" class="form-control" value="<?php echo e(setting('company_phone')) ?>"></div>
            <div class="form-group"><label><?php echo __('company_group_label') ?></label><input type="text" name="company_group" class="form-control" value="<?php echo e(setting('company_group')) ?>"></div>
        </div>
        <div class="form-group"><label><?php echo __('company_map_url') ?></label><input type="text" name="company_map_url" class="form-control" value="<?php echo e(setting('company_map_url')) ?>"></div>
        <div class="form-group"><label><?php echo __('company_intro_label') ?></label><textarea name="company_intro" class="form-control"><?php echo e(setting('company_intro')) ?></textarea></div>
        <div class="form-row">
            <div class="form-group"><label><?php echo __('international_url_label') ?></label><input type="text" name="international_url" class="form-control" value="<?php echo e(setting('international_url')) ?>"></div>
            <div class="form-group"><label><?php echo __('icp_label') ?></label><input type="text" name="site_icp" class="form-control" value="<?php echo e(setting('site_icp')) ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label><?php echo __('police_label') ?></label><input type="text" name="site_police" class="form-control" value="<?php echo e(setting('site_police')) ?>"></div>
            <div class="form-group"><label><?php echo __('ev_license_name') ?></label><input type="text" name="site_ev_license" class="form-control" value="<?php echo e(setting('site_ev_license')) ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label><?php echo __('business_license_name') ?></label><input type="text" name="site_license" class="form-control" value="<?php echo e(setting('site_license')) ?>"></div>
            <div class="form-group"><label><?php echo __('footer_statement') ?></label><input type="text" name="footer_statement" class="form-control" value="<?php echo e(setting('footer_statement')) ?>"></div>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo __('save_config') ?></button>
    </form>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
