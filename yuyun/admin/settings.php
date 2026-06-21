<?php
$pageTitle = '站点配置';
require __DIR__ . '/../includes/admin_header.php';
$db = getDb();
$keys = ['site_name','site_slogan','site_short','sales_phone','company_name','company_short','company_address','company_phone','company_group','company_intro','company_map_url','site_email','international_url','site_icp','site_police','site_license','site_ev_license','footer_statement','top_banner_enabled','top_banner_bg','top_banner_text','top_banner_icon','site_default_theme','site_language','site_email_verify'];
$imageKeys = ['site_logo','site_favicon','site_license_image','site_ev_license_image','site_security_image','site_trust_image','site_staff_bg_image'];
$iconList = ['bell','announcement','gift','info','cloud','shield','envelope','phone'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($keys as $k) {
        setSetting($k, trim($_POST[$k] ?? ''));
    }
    foreach ($imageKeys as $f) {
        if (!empty($_FILES[$f]['tmp_name'])) {
            try {
                $path = upload_file($_FILES[$f], 'cert', ['image/jpeg','image/png','image/webp','image/gif','image/x-icon','image/vnd.microsoft.icon']);
                setSetting($f, $path);
            } catch (Exception $e) {
                flash('error', $f . '上传失败：' . $e->getMessage());
            }
        }
    }
    flash('success', '配置已保存');
    redirect(YUYUN_URL . '/admin/settings.php');
}
?>
<div class="admin-card">
    <h3 style="margin-bottom:20px">站点配置</h3>
    <?php echo render_flash() ?>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
        <div class="form-row">
            <div class="form-group"><label>网站名称</label><input type="text" name="site_name" class="form-control" value="<?php echo e(setting('site_name')) ?>"></div>
            <div class="form-group"><label>网站短称</label><input type="text" name="site_short" class="form-control" value="<?php echo e(setting('site_short')) ?>"></div>
        </div>
        <div class="form-group"><label>网站标语</label><input type="text" name="site_slogan" class="form-control" value="<?php echo e(setting('site_slogan')) ?>"></div>
        <div class="form-row">
            <div class="form-group"><label>LOGO 图片</label><input type="file" name="site_logo" class="form-control" accept="image/*"> <?php if (setting('site_logo')): ?><p style="font-size:12px"><img src="<?php echo e(setting('site_logo')) ?>" style="max-height:40px;margin-top:6px;border-radius:4px"></p><?php endif; ?></div>
            <div class="form-group"><label>浏览器图标</label><input type="file" name="site_favicon" class="form-control" accept="image/*,.ico"> <?php if (setting('site_favicon')): ?><p style="font-size:12px">当前：<?php echo e(setting('site_favicon')) ?></p><?php endif; ?></div>
        </div>
        <h4 style="margin:24px 0 14px;color:var(--dark);font-size:16px">资质证照图片（首页点击放大预览）</h4>
        <div class="form-row">
            <div class="form-group"><label>营业执照图片</label><input type="file" name="site_license_image" class="form-control" accept="image/*"> <?php if (setting('site_license_image')): ?><p style="font-size:12px"><img src="<?php echo e(setting('site_license_image')) ?>" style="max-height:60px;margin-top:6px;border-radius:4px;border:1px solid #eee"></p><?php endif; ?></div>
            <div class="form-group"><label>增值电信业务许可证图片</label><input type="file" name="site_ev_license_image" class="form-control" accept="image/*"> <?php if (setting('site_ev_license_image')): ?><p style="font-size:12px"><img src="<?php echo e(setting('site_ev_license_image')) ?>" style="max-height:60px;margin-top:6px;border-radius:4px;border:1px solid #eee"></p><?php endif; ?></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>等保认证图片</label><input type="file" name="site_security_image" class="form-control" accept="image/*"> <?php if (setting('site_security_image')): ?><p style="font-size:12px"><img src="<?php echo e(setting('site_security_image')) ?>" style="max-height:60px;margin-top:6px;border-radius:4px;border:1px solid #eee"></p><?php endif; ?></div>
            <div class="form-group"><label>可信云认证图片</label><input type="file" name="site_trust_image" class="form-control" accept="image/*"> <?php if (setting('site_trust_image')): ?><p style="font-size:12px"><img src="<?php echo e(setting('site_trust_image')) ?>" style="max-height:60px;margin-top:6px;border-radius:4px;border:1px solid #eee"></p><?php endif; ?></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>销售电话</label><input type="text" name="sales_phone" class="form-control" value="<?php echo e(setting('sales_phone')) ?>"></div>
            <div class="form-group"><label>联系邮箱</label><input type="text" name="site_email" class="form-control" value="<?php echo e(setting('site_email')) ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>公司名称</label><input type="text" name="company_name" class="form-control" value="<?php echo e(setting('company_name')) ?>"></div>
            <div class="form-group"><label>公司简称</label><input type="text" name="company_short" class="form-control" value="<?php echo e(setting('company_short')) ?>"></div>
        </div>
        <div class="form-group"><label>公司地址</label><input type="text" name="company_address" class="form-control" value="<?php echo e(setting('company_address')) ?>"></div>
        <div class="form-row">
            <div class="form-group"><label>公司电话</label><input type="text" name="company_phone" class="form-control" value="<?php echo e(setting('company_phone')) ?>"></div>
            <div class="form-group"><label>官方群聊链接</label><input type="text" name="company_group" class="form-control" value="<?php echo e(setting('company_group')) ?>"></div>
        </div>
        <div class="form-group"><label>地图链接 / 嵌入 URL</label><input type="text" name="company_map_url" class="form-control" value="<?php echo e(setting('company_map_url')) ?>"></div>
        <div class="form-group"><label>公司简介</label><textarea name="company_intro" class="form-control"><?php echo e(setting('company_intro')) ?></textarea></div>
        <div class="form-row">
            <div class="form-group"><label>国际版官网链接</label><input type="text" name="international_url" class="form-control" value="<?php echo e(setting('international_url')) ?>"></div>
            <div class="form-group"><label>备案号</label><input type="text" name="site_icp" class="form-control" value="<?php echo e(setting('site_icp')) ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>公安网备案号</label><input type="text" name="site_police" class="form-control" value="<?php echo e(setting('site_police')) ?>"></div>
            <div class="form-group"><label>增值电信业务经营许可证名称</label><input type="text" name="site_ev_license" class="form-control" value="<?php echo e(setting('site_ev_license')) ?>"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>营业执照名称</label><input type="text" name="site_license" class="form-control" value="<?php echo e(setting('site_license')) ?>"></div>
            <div class="form-group"><label>底部授权声明</label><input type="text" name="footer_statement" class="form-control" value="<?php echo e(setting('footer_statement')) ?>"></div>
        </div>

        <h4 style="margin:28px 0 14px;color:var(--dark);font-size:16px">顶部公告横幅</h4>
        <div class="form-row">
            <div class="form-group"><label><input type="checkbox" name="top_banner_enabled" value="1" <?php echo setting('top_banner_enabled')?'checked':'' ?>> 启用顶部公告</label></div>
            <div class="form-group"><label>公告背景色</label><input type="color" name="top_banner_bg" class="form-control" value="<?php echo e(setting('top_banner_bg','#ff6a00')) ?>" style="height:40px"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>公告图标</label>
                <select name="top_banner_icon" class="form-control">
                    <?php foreach ($iconList as $ic): ?>
                    <option value="<?php echo e($ic) ?>" <?php echo setting('top_banner_icon','bell')===$ic?'selected':'' ?>><?php echo e($ic) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>公告文字</label><input type="text" name="top_banner_text" class="form-control" value="<?php echo e(setting('top_banner_text','欢迎来到语云科技官网，我们提供全球领先的云计算与网络安全服务！')) ?>"></div>
        </div>

        <h4 style="margin:28px 0 14px;color:var(--dark);font-size:16px">主题、语言与邮箱验证</h4>
        <div class="form-row">
            <div class="form-group"><label>默认主题</label>
                <select name="site_default_theme" class="form-control">
                    <option value="light" <?php echo setting('site_default_theme','light')==='light'?'selected':'' ?>>浅色</option>
                    <option value="dark" <?php echo setting('site_default_theme','light')==='dark'?'selected':'' ?>>深色</option>
                </select>
            </div>
            <div class="form-group"><label>默认语言</label>
                <select name="site_language" class="form-control">
                    <option value="zh" <?php echo setting('site_language','zh')==='zh'?'selected':'' ?>>中文</option>
                    <option value="en" <?php echo setting('site_language','zh')==='en'?'selected':'' ?>>English</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group"><label><input type="checkbox" name="site_email_verify" value="1" <?php echo setting('site_email_verify')?'checked':'' ?>> 注册后需邮箱验证</label></div>
            <div class="form-group"><label>员工区背景图</label><input type="file" name="site_staff_bg_image" class="form-control" accept="image/*"> <?php if (setting('site_staff_bg_image')): ?><p style="font-size:12px"><img src="<?php echo e(setting('site_staff_bg_image')) ?>" style="max-height:60px;margin-top:6px;border-radius:4px;border:1px solid #eee"></p><?php endif; ?></div>
        </div>
        <button type="submit" class="btn btn-primary">保存配置</button>
    </form>
</div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
