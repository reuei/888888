<?php
/**
 * 语云科技 - 站点设置管理
 * 分Tab: 基本信息、联系信息、备案信息、邮件配置
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/core/Functions.php';
require_admin();

$config = get_config();
$successMsg = '';
$errorMsg = '';

// 处理保存操作
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $allowedFields = [
        'site_name', 'site_url', 'site_logo', 'site_description', 'site_keywords',
        'contact_phone', 'contact_email', 'contact_qq', 'contact_wechat',
        'company_address', 'icp_number', 'telecom_license', 'police_record',
        'smtp_host', 'smtp_port', 'smtp_user', 'smtp_password', 'smtp_encryption',
        'admin_email', 'site_status'
    ];

    foreach ($allowedFields as $field) {
        if (isset($_POST[$field])) {
            $config[$field] = trim($_POST[$field]);
        }
    }

    // 处理Logo上传
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
        require_once YUYUN_ROOT . '/core/Uploader.php';
        $uploader = new Uploader('images');
        $result = $uploader->upload($_FILES['logo_file']);
        if ($result['success']) {
            $config['site_logo'] = $result['path'];
        }
    }

    // 保存配置
    set_config('config', $config);
    log_message('管理员更新了站点设置');
    $successMsg = '设置已成功保存！';

    // 重新加载配置
    $config = get_config();
}

$activeTab = $_GET['tab'] ?? 'basic';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>站点设置 - 语云科技后台</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- 侧边栏 -->
    <?php include __DIR__ . '/_sidebar.php'; ?>

    <!-- 顶部导航栏 -->
    <header class="header">
        <div class="header-left">
            <button class="mobile-menu-btn" onclick="document.getElementById('sidebar').classList.add('mobile-show'); document.querySelector('.sidebar-overlay').classList.add('show');">
                <i class="fas fa-bars"></i>
            </button>
            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fas fa-home"></i></a>
                <span class="breadcrumb-separator">/</span>
                <span>站点设置</span>
            </div>
        </div>
        <div class="header-right">
            <div class="user-dropdown">
                <div class="user-avatar"><?php echo mb_substr($_SESSION['admin_name'] ?? '管', 0, 1); ?></div>
                <div class="user-info">
                    <div class="name"><?php echo e($_SESSION['admin_name'] ?? '管理员'); ?></div>
                    <div class="role">超级管理员</div>
                </div>
            </div>
        </div>
    </header>

    <!-- 主内容区 -->
    <main class="main-content">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-cog"></i> 站点设置</h3>
            </div>

            <?php if ($successMsg): ?>
                <div style="background: rgba(40,167,69,0.12); border:1px solid rgba(40,167,69,0.3); color:#51cf66; padding:12px 16px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-check-circle"></i> <?php echo e($successMsg); ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMsg): ?>
                <div style="background: rgba(220,53,69,0.12); border:1px solid rgba(220,53,69,0.3); color:#ff6b6b; padding:12px 16px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo e($errorMsg); ?>
                </div>
            <?php endif; ?>

            <!-- Tab导航 -->
            <div class="tabs" id="settingsTabs">
                <div class="tab-item <?php echo $activeTab === 'basic' ? 'active' : ''; ?>" data-tab="basic">基本信息</div>
                <div class="tab-item <?php echo $activeTab === 'contact' ? 'active' : ''; ?>" data-tab="contact">联系信息</div>
                <div class="tab-item <?php echo $activeTab === 'filing' ? 'active' : ''; ?>" data-tab="filing">备案信息</div>
                <div class="tab-item <?php echo $activeTab === 'mail' ? 'active' : ''; ?>" data-tab="mail">邮件配置</div>
            </div>

            <form method="POST" action="" enctype="multipart/form-data" id="settingsForm">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

                <!-- 基本信息 Tab -->
                <div class="tab-pane <?php echo $activeTab === 'basic' ? 'active' : ''; ?>" id="tab-basic">
                    <h4 style="margin-bottom:20px; font-size:15px;"><i class="fas fa-info-circle" style="color:var(--primary-color);"></i> 基本信息设置</h4>

                    <!-- Logo上传 -->
                    <div class="form-group">
                        <label class="form-label">网站Logo</label>
                        <div class="logo-upload">
                            <div class="logo-preview">
                                <?php if (!empty($config['site_logo'])): ?>
                                    <img src="<?php echo e($config['site_logo']); ?>" alt="Logo" id="logoPreviewImg">
                                <?php else: ?>
                                    <i class="fas fa-image" style="color:var(--text-muted); font-size:24px;"></i>
                                    <img src="" alt="" id="logoPreviewImg" style="display:none;">
                                <?php endif; ?>
                            </div>
                            <div style="flex:1;">
                                <input type="file" name="logo_file" id="logoFile" accept="image/*"
                                       onchange="previewLogo(this)">
                                <p class="form-help">支持 JPG、PNG、SVG 格式，建议尺寸 200x60 像素，大小不超过 2MB</p>
                            </div>
                        </div>
                        <input type="hidden" name="site_logo" value="<?php echo e($config['site_logo'] ?? ''); ?>">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">网站名称 <span class="required">*</span></label>
                            <input type="text" name="site_name" class="form-control"
                                   value="<?php echo e($config['site_name'] ?? '语云科技'); ?>"
                                   placeholder="请输入网站名称" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">网站地址</label>
                            <input type="url" name="site_url" class="form-control"
                                   value="<?php echo e($config['site_url'] ?? ''); ?>"
                                   placeholder="https://www.example.com">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">网站描述 (SEO)</label>
                        <textarea name="site_description" class="form-control" rows="3"
                                  placeholder="请输入网站描述，用于SEO优化"><?php echo e($config['site_description'] ?? ''); ?></textarea>
                        <p class="form-help">建议长度在50-300个字符之间</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">关键词 (SEO)</label>
                        <input type="text" name="site_keywords" class="form-control"
                               value="<?php echo e($config['site_keywords'] ?? ''); ?>"
                               placeholder="关键词1, 关键词2, 关键词3">
                        <p class="form-help">多个关键词用英文逗号分隔</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">网站状态</label>
                        <select name="site_status" class="form-control">
                            <option value="online" <?php echo ($config['site_status'] ?? '') === 'online' ? 'selected' : ''; ?>>正常运营</option>
                            <option value="maintenance" <?php echo ($config['site_status'] ?? '') === 'maintenance' ? 'selected' : ''; ?>>维护中</option>
                            <option value="offline" <?php echo ($config['site_status'] ?? '') === 'offline' ? 'selected' : ''; ?>>已下线</option>
                        </select>
                    </div>
                </div>

                <!-- 联系信息 Tab -->
                <div class="tab-pane <?php echo $activeTab === 'contact' ? 'active' : ''; ?>" id="tab-contact">
                    <h4 style="margin-bottom:20px; font-size:15px;"><i class="fas fa-phone-alt" style="color:var(--primary-color);"></i> 联系方式设置</h4>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-phone"></i> 联系电话</label>
                            <input type="text" name="contact_phone" class="form-control"
                                   value="<?php echo e($config['contact_phone'] ?? ''); ?>"
                                   placeholder="如：400-888-8888">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fas fa-envelope"></i> 联系邮箱</label>
                            <input type="email" name="contact_email" class="form-control"
                                   value="<?php echo e($config['contact_email'] ?? ''); ?>"
                                   placeholder="如：contact@example.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label"><i class="fab fa-qq"></i> QQ号码</label>
                            <input type="text" name="contact_qq" class="form-control"
                                   value="<?php echo e($config['contact_qq'] ?? ''); ?>"
                                   placeholder="QQ客服号码">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><i class="fab fa-weixin"></i> 微信号</label>
                            <input type="text" name="contact_wechat" class="form-control"
                                   value="<?php echo e($config['contact_wechat'] ?? ''); ?>"
                                   placeholder="微信公众号或个人微信号">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fas fa-map-marker-alt"></i> 公司地址</label>
                        <textarea name="company_address" class="form-control" rows="2"
                                  placeholder="请输入详细的公司地址"><?php echo e($config['company_address'] ?? ''); ?></textarea>
                    </div>
                </div>

                <!-- 备案信息 Tab -->
                <div class="tab-pane <?php echo $activeTab === 'filing' ? 'active' : ''; ?>" id="tab-filing">
                    <h4 style="margin-bottom:20px; font-size:15px;"><i class="fas fa-shield-alt" style="color:var(--primary-color);"></i> 备案信息设置</h4>

                    <div class="form-group">
                        <label class="form-label">ICP备案号</label>
                        <input type="text" name="icp_number" class="form-control"
                               value="<?php echo e($config['icp_number'] ?? ''); ?>"
                               placeholder="如：京ICP备xxxxxxxx号">
                        <p class="form-help">工信部ICP备案号，将显示在页面底部</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">增值电信业务经营许可证</label>
                        <input type="text" name="telecom_license" class="form-control"
                               value="<?php echo e($config['telecom_license'] ?? ''); ?>"
                               placeholder="如：京B2-xxxxxxxx">
                    </div>

                    <div class="form-group">
                        <label class="form-label">公安备案号</label>
                        <input type="text" name="police_record" class="form-control"
                               value="<?php echo e($config['police_record'] ?? ''); ?>"
                               placeholder="如：京公网安备 xxxxxxxxx号">
                    </div>
                </div>

                <!-- 邮件配置 Tab -->
                <div class="tab-pane <?php echo $activeTab === 'mail' ? 'active' : ''; ?>" id="tab-mail">
                    <h4 style="margin-bottom:20px; font-size:15px;"><i class="fas fa-envelope-open-text" style="color:var(--primary-color);"></i> SMTP邮件服务配置</h4>
                    <p style="color:var(--text-secondary); margin-bottom:24px;">配置邮件服务后可用于发送验证码、通知等邮件。</p>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">SMTP服务器地址</label>
                            <input type="text" name="smtp_host" class="form-control"
                                   value="<?php echo e($config['smtp_host'] ?? ''); ?>"
                                   placeholder="如：smtp.qq.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">端口</label>
                            <input type="text" name="smtp_port" class="form-control"
                                   value="<?php echo e($config['smtp_port'] ?? '465'); ?>"
                                   placeholder="常用：25 / 465 / 587">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">发件邮箱账号</label>
                            <input type="text" name="smtp_user" class="form-control"
                                   value="<?php echo e($config['smtp_user'] ?? ''); ?>"
                                   placeholder="完整的邮箱地址">
                        </div>
                        <div class="form-group">
                            <label class="form-label">加密方式</label>
                            <select name="smtp_encryption" class="form-control">
                                <option value="">无加密</option>
                                <option value="ssl" <?php echo ($config['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                <option value="tls" <?php echo ($config['smtp_encryption'] ?? '') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">SMTP密码 / 授权码</label>
                        <input type="password" name="smtp_password" class="form-control"
                               value=""
                               placeholder="请输入密码（留空则不修改）">
                        <p class="form-help">通常使用邮箱的授权码而非登录密码</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">管理员接收邮箱</label>
                        <input type="email" name="admin_email" class="form-control"
                               value="<?php echo e($config['admin_email'] ?? ''); ?>"
                               placeholder="系统通知将发送到此邮箱">
                    </div>

                    <div style="background:var(--table-header); border-radius:8px; padding:16px; margin-top:16px;">
                        <button type="button" class="btn btn-outline btn-sm" onclick="testMail()">
                            <i class="fas fa-paper-plane"></i> 发送测试邮件
                        </button>
                        <span id="mailTestResult" style="margin-left:12px; font-size:13px;"></span>
                    </div>
                </div>

                <!-- 提交按钮 -->
                <div class="card-footer">
                    <button type="submit" name="save_settings" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> 保存设置
                    </button>
                    <button type="reset" class="btn btn-outline">
                        <i class="fas fa-undo"></i> 重置
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script src="../assets/js/admin.js"></script>
    <script>
        // Logo预览
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('logoPreviewImg');
                    img.src = e.target.result;
                    img.style.display = '';
                    img.previousElementSibling?.remove(); // 移除占位图标
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Tab切换逻辑
        document.querySelectorAll('#settingsTabs .tab-item').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('#settingsTabs .tab-item').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                tab.classList.add('active');
                const tabId = 'tab-' + tab.dataset.tab;
                document.getElementById(tabId)?.classList.add('active');

                // 更新URL hash但不刷新
                history.replaceState(null, '', '?tab=' + tab.dataset.tab);
            });
        });

        // 测试邮件
        function testMail() {
            const resultEl = document.getElementById('mailTestResult');
            resultEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 发送中...';

            fetch('../api/auth.php?action=test-mail', { method: 'POST' })
                .then(r => r.json())
                .then(res => {
                    resultEl.innerHTML = res.code === 200
                        ? '<span style="color:var(--success-color)"><i class="fas fa-check"></i> 测试邮件已发送</span>'
                        : '<span style="color:var(--danger-color)"><i class="fas fa-times"></i> ' + (res.message || '发送失败') + '</span>';
                })
                .catch(() => {
                    resultEl.innerHTML = '<span style="color:var(--danger-color)"><i class="fas fa-times"></i> 网络错误</span>';
                });
        }

        // 页面加载时激活正确的Tab
        window.addEventListener('DOMContentLoaded', () => {
            const hash = location.hash.replace('#', '');
            if (hash) {
                const targetTab = document.querySelector(`#settingsTabs [data-tab="${hash}"]`);
                if (targetTab) targetTab.click();
            }
        });
    </script>
</body>
</html>
