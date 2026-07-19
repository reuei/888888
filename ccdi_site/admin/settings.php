<?php
/**
 * 后台管理 - 系统设置
 * 网站基本配置、图片上传、数据库备份
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/../includes/init.php';
require_admin();

// 确保 site_config 表存在
db()->exec("CREATE TABLE IF NOT EXISTS site_config (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    config_key TEXT NOT NULL UNIQUE,
    config_value TEXT DEFAULT '',
    updated_at TEXT DEFAULT (datetime('now','localtime'))
)");

// ==================== 数据库备份下载 ====================
if (get('action') === 'backup') {
    if (!csrf_verify(get('csrf_token', ''))) {
        die('安全验证失败，请刷新页面后重试');
    }
    $db_file = DB_PATH;
    if (!file_exists($db_file)) {
        die('数据库文件不存在');
    }
    $backup_name = 'ccdi_site_backup_' . date('Ymd_His') . '.db';
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $backup_name . '"');
    header('Content-Length: ' . filesize($db_file));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    ob_clean();
    flush();
    readfile($db_file);
    exit;
}

// ==================== 加载当前配置 ====================
$configs = [];
$rows = db_fetch_all("SELECT config_key, config_value FROM site_config");
foreach ($rows as $row) {
    $configs[$row['config_key']] = $row['config_value'];
}

$message = '';
$error = '';

// ==================== 保存设置 ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify(post('csrf_token', ''))) {
        $error = '安全验证失败，请刷新页面后重试';
    } else {
        $fields = [
            'site_name'        => trim(post('site_name', '')),
            'site_description' => trim(post('site_description', '')),
            'site_keywords'    => trim(post('site_keywords', '')),
            'footer_text'      => trim(post('footer_text', '')),
            'preloader_enabled' => post('preloader_enabled', '0'),
            'popup_enabled'    => post('popup_enabled', '0'),
            'icp_number'       => trim(post('icp_number', '')),
            'contact_email'    => trim(post('contact_email', '')),
            'report_email'     => trim(post('report_email', '')),
        ];

        // 验证基本字段
        if (empty($fields['site_name'])) {
            $error = '网站名称不能为空';
        }

        if (empty($error)) {
            // 处理图片上传
            $image_fields = ['footer_image', 'banner_image', 'preloader_image'];
            foreach ($image_fields as $img_field) {
                $file_key = $img_field . '_file';
                if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] !== UPLOAD_ERR_NO_FILE) {
                    $upload_result = upload_file($_FILES[$file_key], 'settings');
                    if (isset($upload_result['success']) && $upload_result['success']) {
                        // 删除旧图片
                        $old_value = isset($configs[$img_field]) ? $configs[$img_field] : '';
                        if (!empty($old_value)) {
                            $old_path = UPLOADS_PATH . $old_value;
                            if (file_exists($old_path)) {
                                @unlink($old_path);
                            }
                        }
                        $fields[$img_field] = $upload_result['path'];
                    } elseif (isset($upload_result['error'])) {
                        $error = $upload_result['error'];
                        break;
                    }
                }
            }
        }

        if (empty($error)) {
            $now = date('Y-m-d H:i:s');
            foreach ($fields as $key => $value) {
                $existing = db_fetch("SELECT id FROM site_config WHERE config_key = ?", [$key]);
                if ($existing) {
                    db_update('site_config', ['config_value' => $value, 'updated_at' => $now], 'config_key = ?', [$key]);
                } else {
                    db_insert('site_config', ['config_key' => $key, 'config_value' => $value, 'updated_at' => $now]);
                }
            }

            // 清除静态缓存，重新加载
            $configs = [];
            $rows = db_fetch_all("SELECT config_key, config_value FROM site_config");
            foreach ($rows as $row) {
                $configs[$row['config_key']] = $row['config_value'];
            }

            add_log('settings_update', '更新了系统设置');
            $message = '系统设置保存成功！';
        }
    }
}

// ==================== 辅助函数 ====================
function config_val($key, $default = '') {
    global $configs;
    return isset($configs[$key]) ? htmlspecialchars($configs[$key], ENT_QUOTES, 'UTF-8') : $default;
}

function config_val_raw($key, $default = '') {
    global $configs;
    return isset($configs[$key]) ? $configs[$key] : $default;
}

function image_preview_url($path) {
    if (!empty($path) && file_exists(UPLOADS_PATH . $path)) {
        return site_url('uploads/' . $path);
    }
    return '';
}

include __DIR__ . '/header.php';
?>

<div class="admin-page-header">
    <h2 class="admin-page-title">系统设置</h2>
</div>

<?php if ($message): ?>
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="post" action="<?php echo admin_url('settings.php'); ?>" enctype="multipart/form-data" class="settings-form">
    <?php echo csrf_field(); ?>

    <!-- ==================== 基本信息 ==================== -->
    <div class="form-section">
        <h3 class="form-section-title"><i class="fas fa-globe"></i> 网站基本信息</h3>
        <div class="form-section-body">
            <div class="form-group">
                <label for="site_name">网站名称 <span class="required">*</span></label>
                <input type="text" id="site_name" name="site_name" value="<?php echo config_val('site_name', '中央纪委国家监委网站'); ?>" required placeholder="请输入网站名称" maxlength="100">
                <span class="form-hint">网站标题，将显示在浏览器标签栏和页面头部</span>
            </div>

            <div class="form-group">
                <label for="site_description">网站描述</label>
                <textarea id="site_description" name="site_description" rows="3" placeholder="请输入网站描述信息" maxlength="500"><?php echo config_val('site_description', ''); ?></textarea>
                <span class="form-hint">用于搜索引擎的 meta description，建议不超过 200 字</span>
            </div>

            <div class="form-group">
                <label for="site_keywords">网站关键词</label>
                <input type="text" id="site_keywords" name="site_keywords" value="<?php echo config_val('site_keywords', ''); ?>" placeholder="多个关键词用英文逗号分隔" maxlength="300">
                <span class="form-hint">用于搜索引擎的 meta keywords，如：中央纪委,国家监委,反腐败,纪检监察</span>
            </div>
        </div>
    </div>

    <!-- ==================== 图片设置 ==================== -->
    <div class="form-section">
        <h3 class="form-section-title"><i class="fas fa-images"></i> 图片设置</h3>
        <div class="form-section-body">
            <div class="form-group">
                <label for="footer_image_file">页脚机关图片</label>
                <div class="image-upload-row">
                    <input type="file" id="footer_image_file" name="footer_image_file" accept="image/jpeg,image/png,image/gif,image/webp">
                    <?php $footer_img = config_val_raw('footer_image', ''); ?>
                    <?php $footer_preview = image_preview_url($footer_img); ?>
                    <?php if ($footer_preview): ?>
                        <div class="image-preview">
                            <img src="<?php echo $footer_preview; ?>" alt="页脚机关图片预览">
                            <span class="image-preview-name"><?php echo htmlspecialchars(basename($footer_img)); ?></span>
                        </div>
                    <?php else: ?>
                        <div class="image-preview image-preview-empty">
                            <i class="fas fa-image"></i>
                            <span>暂无图片</span>
                        </div>
                    <?php endif; ?>
                </div>
                <span class="form-hint">显示在网站页脚位置的机关单位标识图片，支持 JPG、PNG、GIF、WebP 格式</span>
            </div>

            <div class="form-group">
                <label for="banner_image_file">首页横幅图片</label>
                <div class="image-upload-row">
                    <input type="file" id="banner_image_file" name="banner_image_file" accept="image/jpeg,image/png,image/gif,image/webp">
                    <?php $banner_img = config_val_raw('banner_image', ''); ?>
                    <?php $banner_preview = image_preview_url($banner_img); ?>
                    <?php if ($banner_preview): ?>
                        <div class="image-preview">
                            <img src="<?php echo $banner_preview; ?>" alt="首页横幅图片预览">
                            <span class="image-preview-name"><?php echo htmlspecialchars(basename($banner_img)); ?></span>
                        </div>
                    <?php else: ?>
                        <div class="image-preview image-preview-empty">
                            <i class="fas fa-image"></i>
                            <span>暂无图片</span>
                        </div>
                    <?php endif; ?>
                </div>
                <span class="form-hint">网站首页顶部横幅大图，建议尺寸 1920×400px</span>
            </div>

            <div class="form-group">
                <label for="preloader_image_file">预载图片</label>
                <div class="image-upload-row">
                    <input type="file" id="preloader_image_file" name="preloader_image_file" accept="image/jpeg,image/png,image/gif,image/webp">
                    <?php $preloader_img = config_val_raw('preloader_image', ''); ?>
                    <?php $preloader_preview = image_preview_url($preloader_img); ?>
                    <?php if ($preloader_preview): ?>
                        <div class="image-preview">
                            <img src="<?php echo $preloader_preview; ?>" alt="预载图片预览">
                            <span class="image-preview-name"><?php echo htmlspecialchars(basename($preloader_img)); ?></span>
                        </div>
                    <?php else: ?>
                        <div class="image-preview image-preview-empty">
                            <i class="fas fa-image"></i>
                            <span>暂无图片</span>
                        </div>
                    <?php endif; ?>
                </div>
                <span class="form-hint">页面加载时显示的预载动画图片，建议使用 GIF 或 PNG 格式</span>
            </div>
        </div>
    </div>

    <!-- ==================== 功能开关 ==================== -->
    <div class="form-section">
        <h3 class="form-section-title"><i class="fas fa-toggle-on"></i> 功能开关</h3>
        <div class="form-section-body">
            <div class="form-group">
                <label>是否启用预载</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="preloader_enabled" value="1" <?php echo config_val_raw('preloader_enabled', '0') === '1' ? 'checked' : ''; ?>>
                        <span class="radio-mark"></span> 启用
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="preloader_enabled" value="0" <?php echo config_val_raw('preloader_enabled', '0') !== '1' ? 'checked' : ''; ?>>
                        <span class="radio-mark"></span> 禁用
                    </label>
                </div>
                <span class="form-hint">开启后，网站访问时将显示预载动画</span>
            </div>

            <div class="form-group">
                <label>是否启用弹窗</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="popup_enabled" value="1" <?php echo config_val_raw('popup_enabled', '0') === '1' ? 'checked' : ''; ?>>
                        <span class="radio-mark"></span> 启用
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="popup_enabled" value="0" <?php echo config_val_raw('popup_enabled', '0') !== '1' ? 'checked' : ''; ?>>
                        <span class="radio-mark"></span> 禁用
                    </label>
                </div>
                <span class="form-hint">开启后，网站首页将显示弹窗公告</span>
            </div>
        </div>
    </div>

    <!-- ==================== 联系与备案信息 ==================== -->
    <div class="form-section">
        <h3 class="form-section-title"><i class="fas fa-address-card"></i> 联系与备案信息</h3>
        <div class="form-section-body">
            <div class="form-group">
                <label for="footer_text">页脚版权文字</label>
                <input type="text" id="footer_text" name="footer_text" value="<?php echo config_val('footer_text', ''); ?>" placeholder="如：© 2024 中央纪委国家监委网站 版权所有" maxlength="300">
                <span class="form-hint">显示在网站页脚的版权信息文字</span>
            </div>

            <div class="form-group">
                <label for="icp_number">ICP备案号</label>
                <input type="text" id="icp_number" name="icp_number" value="<?php echo config_val('icp_number', ''); ?>" placeholder="如：京ICP备XXXXXXXX号" maxlength="100">
                <span class="form-hint">网站ICP备案号，将显示在页脚位置</span>
            </div>

            <div class="form-row">
                <div class="form-group form-group-half">
                    <label for="contact_email">联系邮箱</label>
                    <input type="email" id="contact_email" name="contact_email" value="<?php echo config_val('contact_email', ''); ?>" placeholder="如：contact@example.com" maxlength="100">
                    <span class="form-hint">网站对外公布的联系邮箱</span>
                </div>
                <div class="form-group form-group-half">
                    <label for="report_email">举报邮箱</label>
                    <input type="email" id="report_email" name="report_email" value="<?php echo config_val('report_email', ''); ?>" placeholder="如：jubao@example.com" maxlength="100">
                    <span class="form-hint">网站对外公布的举报受理邮箱</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== 数据库管理 ==================== -->
    <div class="form-section">
        <h3 class="form-section-title"><i class="fas fa-database"></i> 数据库管理</h3>
        <div class="form-section-body">
            <div class="db-backup-row">
                <div class="db-backup-info">
                    <span class="db-backup-label">SQLite 数据库备份</span>
                    <span class="db-backup-desc">下载当前数据库文件的完整备份，用于数据迁移或灾难恢复。数据库路径：<?php echo htmlspecialchars(DB_PATH); ?></span>
                    <span class="db-backup-size">文件大小：<?php echo file_exists(DB_PATH) ? number_format(filesize(DB_PATH) / 1024, 2) . ' KB' : '未知'; ?></span>
                </div>
                <a href="<?php echo admin_url('settings.php?action=backup&csrf_token=' . urlencode(csrf_token())); ?>" class="btn btn-download" onclick="return confirm('确定要下载数据库备份文件吗？');">
                    <i class="fas fa-download"></i> 下载备份
                </a>
            </div>
        </div>
    </div>

    <!-- ==================== 提交按钮 ==================== -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save"></i> 保存设置
        </button>
        <button type="reset" class="btn btn-secondary btn-lg">
            <i class="fas fa-undo"></i> 重置表单
        </button>
    </div>
</form>

<style>
/* ========== 页面头部 ========== */
.admin-page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.admin-page-title { margin: 0; font-size: 20px; color: #333; }

/* ========== 按钮 ========== */
.btn { display: inline-block; padding: 8px 20px; font-size: 14px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; transition: all 0.3s; }
.btn-primary { background: #c41230; color: #fff; }
.btn-primary:hover { background: #a00e28; }
.btn-secondary { background: #f0f0f0; color: #333; }
.btn-secondary:hover { background: #e0e0e0; }
.btn-lg { padding: 12px 32px; font-size: 15px; }
.btn-download { background: #1890ff; color: #fff; }
.btn-download:hover { background: #1476d6; }

/* ========== 提示信息 ========== */
.alert { padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; display: flex; align-items: center; gap: 8px; }
.alert-success { background: #f6ffed; border: 1px solid #b7eb8f; color: #389e0d; }
.alert-error { background: #fff2f0; border: 1px solid #ffccc7; color: #cf1322; }

/* ========== 表单容器 ========== */
.settings-form { max-width: 900px; }

/* ========== 表单分区 ========== */
.form-section { background: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); margin-bottom: 24px; overflow: hidden; }
.form-section-title { margin: 0; padding: 16px 24px; font-size: 16px; font-weight: 600; color: #c41230; background: #fef5f6; border-bottom: 1px solid #fce4e7; display: flex; align-items: center; gap: 10px; }
.form-section-title i { font-size: 16px; }
.form-section-body { padding: 24px; }

/* ========== 表单控件 ========== */
.form-group { margin-bottom: 20px; }
.form-group:last-child { margin-bottom: 0; }
.form-group label { display: block; font-size: 14px; font-weight: 600; color: #333; margin-bottom: 8px; }
.form-group input[type="text"],
.form-group input[type="email"],
.form-group textarea { width: 100%; padding: 10px 14px; border: 1px solid #d9d9d9; border-radius: 6px; font-size: 14px; font-family: inherit; transition: all 0.3s; box-sizing: border-box; color: #333; background: #fff; }
.form-group input[type="text"]:focus,
.form-group input[type="email"]:focus,
.form-group textarea:focus { border-color: #c41230; outline: none; box-shadow: 0 0 0 3px rgba(196,18,48,0.08); }
.form-group textarea { resize: vertical; line-height: 1.6; }
.form-group input[type="file"] { padding: 6px 0; font-size: 14px; }
.form-hint { display: block; font-size: 12px; color: #999; margin-top: 6px; line-height: 1.5; }
.required { color: #c41230; margin-left: 2px; }

/* ========== 表单行 ========== */
.form-row { display: flex; gap: 20px; }
.form-group-half { flex: 1; }

/* ========== 单选按钮组 ========== */
.radio-group { display: flex; gap: 24px; margin-top: 4px; }
.radio-label { display: inline-flex; align-items: center; gap: 8px; font-size: 14px; color: #555; cursor: pointer; font-weight: 400; }
.radio-label input[type="radio"] { width: 18px; height: 18px; accent-color: #c41230; cursor: pointer; margin: 0; }

/* ========== 图片上传预览 ========== */
.image-upload-row { display: flex; align-items: flex-start; gap: 20px; margin-top: 4px; flex-wrap: wrap; }
.image-upload-row input[type="file"] { flex-shrink: 0; }
.image-preview { display: flex; flex-direction: column; align-items: center; border: 1px solid #e8e8e8; border-radius: 6px; padding: 8px; background: #fafafa; min-width: 120px; }
.image-preview img { max-width: 200px; max-height: 120px; border-radius: 4px; object-fit: contain; }
.image-preview-name { font-size: 11px; color: #999; margin-top: 6px; word-break: break-all; text-align: center; max-width: 180px; }
.image-preview-empty { padding: 28px 20px; color: #ccc; min-width: 120px; display: flex; flex-direction: column; align-items: center; gap: 8px; }
.image-preview-empty i { font-size: 36px; }
.image-preview-empty span { font-size: 12px; }

/* ========== 数据库备份区 ========== */
.db-backup-row { display: flex; align-items: center; justify-content: space-between; gap: 20px; }
.db-backup-info { display: flex; flex-direction: column; gap: 6px; }
.db-backup-label { font-size: 14px; font-weight: 600; color: #333; }
.db-backup-desc { font-size: 13px; color: #666; line-height: 1.5; }
.db-backup-size { font-size: 12px; color: #999; }

/* ========== 提交按钮区 ========== */
.form-actions { margin-top: 24px; padding: 20px 0; display: flex; gap: 16px; align-items: center; }

/* ========== 响应式 ========== */
@media (max-width: 768px) {
    .form-row { flex-direction: column; gap: 0; }
    .image-upload-row { flex-direction: column; }
    .db-backup-row { flex-direction: column; align-items: stretch; }
    .db-backup-row .btn { text-align: center; }
}
</style>

<?php
include __DIR__ . '/footer.php';