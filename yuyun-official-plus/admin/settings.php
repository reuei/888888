<?php
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/config.php';
require_once YUYUN_ROOT . '/includes/functions.php';
require_once YUYUN_ROOT . '/includes/auth.php';
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('danger', '安全验证失败，请刷新页面重试');
        header('Location: settings.php');
        exit;
    }

    $keys = [
        'site_title','site_keywords','site_description','site_logo','site_favicon',
        'company_name','company_address','company_intro','sales_phone','service_phone','company_email','group_chat',
        'icp','icp_gongan','license','footer_text',
        'map_type','map_key','map_lat','map_lng',
        'current_template','popup_enabled','popup_title','popup_content','international_url'
    ];
    foreach ($keys as $k) {
        $val = $_POST[$k] ?? '';
        if ($k === 'popup_enabled') {
            $val = isset($_POST[$k]) ? '1' : '0';
        }
        setSetting($k, $val);
    }

    // Handle file uploads
    if (!empty($_FILES['site_logo_file']['tmp_name'])) {
        $up = yyUpload($_FILES['site_logo_file'], 'logo');
        if (empty($up['error'])) setSetting('site_logo', $up['path']);
    }
    if (!empty($_FILES['site_favicon_file']['tmp_name'])) {
        $up = yyUpload($_FILES['site_favicon_file'], 'logo');
        if (empty($up['error'])) setSetting('site_favicon', $up['path']);
    }

    setFlash('success', '站点配置已保存');
    header('Location: settings.php');
    exit;
}

$settings = getSettings([
    'site_title','site_keywords','site_description','site_logo','site_favicon',
    'company_name','company_address','company_intro','sales_phone','service_phone','company_email','group_chat',
    'icp','icp_gongan','license','footer_text',
    'map_type','map_key','map_lat','map_lng',
    'current_template','popup_enabled','popup_title','popup_content','international_url'
]);

$templates = [
    'default' => '企业蓝（腾讯云风格）',
    'dark' => '科技黑（Cloudflare 深色风格）',
    'white' => '云白（魔方财务浅色风格）',
    'tech-purple' => '科技紫（赛博渐变）',
    'finance-gold' => '金融金（高端商务）',
    'fresh-green' => '清新绿（绿色环保）',
    'ocean-blue' => '海洋蓝（深邃科技）',
    'sunset-orange' => '日落橙（热情活力）',
    'minimal-black' => '极简黑（黑白纯粹）'
];
$pageTitle = '站点配置';
include __DIR__ . '/includes/header.php';
?>
<form method="post" action="" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?php echo yy_e(csrfToken()); ?>">
    <div class="card">
        <div class="card-header"><h2>基本信息</h2></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>网站标题</label>
                    <input type="text" name="site_title" class="form-control" value="<?php echo yy_e($settings['site_title']); ?>">
                </div>
                <div class="form-group">
                    <label>当前模板</label>
                    <select name="current_template" class="form-control">
                        <?php foreach ($templates as $k => $v): ?>
                            <option value="<?php echo yy_e($k); ?>" <?php echo $settings['current_template'] === $k ? 'selected' : ''; ?>><?php echo yy_e($v); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>网站 LOGO</label>
                <input type="file" name="site_logo_file" class="form-control" accept="image/*">
                <?php if ($settings['site_logo']): ?>
                    <div class="form-hint">当前：<img src="../<?php echo yy_e($settings['site_logo']); ?>" style="max-height:30px;vertical-align:middle;"></div>
                    <input type="hidden" name="site_logo" value="<?php echo yy_e($settings['site_logo']); ?>">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>网站 Favicon</label>
                <input type="file" name="site_favicon_file" class="form-control" accept="image/*">
                <?php if ($settings['site_favicon']): ?>
                    <div class="form-hint">当前：<img src="../<?php echo yy_e($settings['site_favicon']); ?>" style="max-height:20px;vertical-align:middle;"></div>
                    <input type="hidden" name="site_favicon" value="<?php echo yy_e($settings['site_favicon']); ?>">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>SEO 关键词</label>
                <input type="text" name="site_keywords" class="form-control" value="<?php echo yy_e($settings['site_keywords']); ?>">
            </div>
            <div class="form-group">
                <label>SEO 描述</label>
                <textarea name="site_description" class="form-control"><?php echo yy_e($settings['site_description']); ?></textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>公司信息</h2></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>公司名称</label>
                    <input type="text" name="company_name" class="form-control" value="<?php echo yy_e($settings['company_name']); ?>">
                </div>
                <div class="form-group">
                    <label>销售电话</label>
                    <input type="text" name="sales_phone" class="form-control" value="<?php echo yy_e($settings['sales_phone']); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>客服电话</label>
                    <input type="text" name="service_phone" class="form-control" value="<?php echo yy_e($settings['service_phone']); ?>">
                </div>
                <div class="form-group">
                    <label>企业邮箱</label>
                    <input type="email" name="company_email" class="form-control" value="<?php echo yy_e($settings['company_email']); ?>">
                </div>
            </div>
            <div class="form-group">
                <label>公司地址</label>
                <input type="text" name="company_address" class="form-control" value="<?php echo yy_e($settings['company_address']); ?>">
            </div>
            <div class="form-group">
                <label>官方群聊链接</label>
                <input type="text" name="group_chat" class="form-control" value="<?php echo yy_e($settings['group_chat']); ?>">
            </div>
            <div class="form-group">
                <label>公司简介</label>
                <textarea name="company_intro" class="form-control"><?php echo yy_e($settings['company_intro']); ?></textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>备案与页脚</h2></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>ICP 备案号</label>
                    <input type="text" name="icp" class="form-control" value="<?php echo yy_e($settings['icp']); ?>">
                </div>
                <div class="form-group">
                    <label>公安网备案号</label>
                    <input type="text" name="icp_gongan" class="form-control" value="<?php echo yy_e($settings['icp_gongan']); ?>">
                </div>
            </div>
            <div class="form-group">
                <label>增值电信业务经营许可证号</label>
                <input type="text" name="license" class="form-control" value="<?php echo yy_e($settings['license']); ?>">
            </div>
            <div class="form-group">
                <label>页脚授权文字</label>
                <textarea name="footer_text" class="form-control"><?php echo yy_e($settings['footer_text']); ?></textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>地图配置</h2></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>地图类型</label>
                    <select name="map_type" class="form-control">
                        <option value="baidu" <?php echo $settings['map_type'] === 'baidu' ? 'selected' : ''; ?>>百度地图</option>
                        <option value="gaode" <?php echo $settings['map_type'] === 'gaode' ? 'selected' : ''; ?>>高德地图</option>
                        <option value="tencent" <?php echo $settings['map_type'] === 'tencent' ? 'selected' : ''; ?>>腾讯地图</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>地图密钥（可选）</label>
                    <input type="text" name="map_key" class="form-control" value="<?php echo yy_e($settings['map_key']); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>纬度</label>
                    <input type="text" name="map_lat" class="form-control" value="<?php echo yy_e($settings['map_lat']); ?>">
                </div>
                <div class="form-group">
                    <label>经度</label>
                    <input type="text" name="map_lng" class="form-control" value="<?php echo yy_e($settings['map_lng']); ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>首页弹窗</h2></div>
        <div class="card-body">
            <div class="form-group">
                <label class="checkbox-label"><input type="checkbox" name="popup_enabled" value="1" <?php echo $settings['popup_enabled'] == '1' ? 'checked' : ''; ?>> 启用首页弹窗</label>
            </div>
            <div class="form-group">
                <label>弹窗标题</label>
                <input type="text" name="popup_title" class="form-control" value="<?php echo yy_e($settings['popup_title']); ?>">
            </div>
            <div class="form-group">
                <label>弹窗内容</label>
                <textarea name="popup_content" class="form-control"><?php echo yy_e($settings['popup_content']); ?></textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h2>国际版链接</h2></div>
        <div class="card-body">
            <div class="form-group">
                <label>国际版官网 URL</label>
                <input type="text" name="international_url" class="form-control" value="<?php echo yy_e($settings['international_url']); ?>">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-lg" style="width:100%;"><i class="fa-solid fa-save"></i> 保存配置</button>
</form>
<?php include __DIR__ . '/includes/footer.php'; ?>
