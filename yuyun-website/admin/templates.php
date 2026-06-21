<?php
/**
 * 语云科技 - 模板管理页面
 * 查看可用模板、切换模板、上传自定义模板
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/core/Functions.php';
require_admin();

$currentTemplate = get_config('template') ?: 'default';
$successMsg = '';
$errorMsg = '';

// 处理切换模板
if (isset($_POST['switch_template'])) {
    $templateName = trim($_POST['template_name'] ?? '');

    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $templateName)) {
        $errorMsg = '模板名称包含非法字符';
    } else {
        $templatePath = TEMPLATES_PATH . $templateName;
        if (!is_dir($templatePath)) {
            $errorMsg = '指定的模板不存在：' . e($templateName);
        } else {
            set_config('template', $templateName);
            log_message("管理员切换模板为: {$templateName}");
            $currentTemplate = $templateName;
            $successMsg = "已成功切换到「{$templateName}」模板！";
        }
    }
}

// 处理上传自定义模板
if (isset($_POST['upload_template']) && isset($_FILES['template_zip'])) {
    $file = $_FILES['template_zip'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($ext !== 'zip') {
        $errorMsg = '只支持ZIP格式的模板包';
    } elseif ($file['size'] > 20971520) {
        $errorMsg = '模板包大小不能超过20MB';
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMsg = '文件上传失败';
    } else {
        $templateName = pathinfo($file['name'], PATHINFO_FILENAME);
        $templateName = preg_replace('/[^a-zA-Z0-9_-]/', '', $templateName) ?: ('custom_' . time());
        $targetDir = TEMPLATES_PATH . $templateName;

        if (is_dir($targetDir)) {
            // 删除旧目录
            function _delDir($dir) {
                if (!is_dir($dir)) return;
                foreach (scandir($dir) as $item) {
                    if ($item === '.' || $item === '..') continue;
                    is_dir($dir.'/'.$item) ? _delDir($dir.'/'.$item) : unlink($dir.'/'.$item);
                }
                rmdir($dir);
            }
            _delDir($targetDir);
        }

        mkdir($targetDir, 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($file['tmp_name']) === true) {
            $zip->extractTo($targetDir);
            $zip->close();
            log_message("管理员上传了自定义模板: {$templateName}");
            $successMsg = "模板「{$templateName}」上传成功！";
        } else {
            rmdir($targetDir);
            $errorMsg = 'ZIP文件解压失败，请检查文件格式是否正确';
        }
    }
}

// 获取所有可用模板
$templates = [];
if (is_dir(TEMPLATES_PATH)) {
    $dirs = array_filter(scandir(TEMPLATES_PATH), fn($d) => $d !== '.' && $d !== '..' && is_dir(TEMPLATES_PATH . '/' . $d));
    sort($dirs);

    foreach ($dirs as $dir) {
        $tplPath = TEMPLATES_PATH . '/' . $dir;
        $info = [
            'name' => $dir,
            'display_name' => ucfirst($dir),
            'description' => '',
            'author' => '',
            'version' => '1.0.0',
            'screenshot' => '',
            'is_active' => ($dir === $currentTemplate)
        ];

        $configFile = $tplPath . '/template.json';
        if (file_exists($configFile)) {
            $cfg = json_decode(file_get_contents($configFile), true);
            if ($cfg) $info = array_merge($info, $cfg);
        }

        // 检测截图
        if (empty($info['screenshot'])) {
            foreach (['screenshot.png','preview.png','thumb.jpg','thumb.png'] as $sf) {
                if (file_exists($tplPath . '/' . $sf)) { $info['screenshot'] = '/templates/' . $dir . '/' . $sf; break; }
            }
        }

        $templates[] = $info;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>模板管理 - 语云科技后台</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- 侧边栏 -->
    <?php include __DIR__ . '/_sidebar.php'; ?>

    <!-- 顶部导航 -->
    <header class="header">
        <div class="header-left">
            <button class="mobile-menu-btn" onclick="document.getElementById('sidebar').classList.add('mobile-show'); document.querySelector('.sidebar-overlay').classList.add('show');">
                <i class="fas fa-bars"></i>
            </button>
            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fas fa-home"></i></a>
                <span class="breadcrumb-separator">/</span>
                <span>模板管理</span>
            </div>
        </div>
        <div class="header-right">
            <div class="user-dropdown">
                <div class="user-avatar"><?php echo mb_substr($_SESSION['admin_name'] ?? '管', 0, 1); ?></div>
                <div class="user-info"><div class="name"><?php echo e($_SESSION['admin_name'] ?? '管理员'); ?></div><div class="role">超级管理员</div></div>
            </div>
        </div>
    </header>

    <!-- 主内容区 -->
    <main class="main-content">
        <!-- 操作提示 -->
        <?php if ($successMsg): ?>
            <div style="background:rgba(40,167,69,0.12); border:1px solid rgba(40,167,69,0.3); color:#51cf66; padding:12px 16px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
                <i class="fas fa-check-circle"></i> <?php echo e($successMsg); ?>
            </div>
        <?php endif; ?>
        <?php if ($errorMsg): ?>
            <div style="background:rgba(220,53,69,0.12); border:1px solid rgba(220,53,69,0.3); color:#ff6b6b; padding:12px 16px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:10px;">
                <i class="fas fa-exclamation-circle"></i> <?php echo e($errorMsg); ?>
            </div>
        <?php endif; ?>

        <!-- 上传自定义模板 -->
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header">
                <h3><i class="fas fa-upload"></i> 上传自定义模板</h3>
            </div>
            <form method="POST" enctype="multipart/form-data" style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
                <label style="
                    display:inline-flex; align-items:center; gap:8px; padding:10px 20px;
                    background:var(--card-bg); border:2px dashed var(--input-border); border-radius:8px;
                    cursor:pointer; transition:all 0.3s; color:var(--text-secondary);
                " onmouseover="this.style.borderColor='var(--primary-color)';this.style.color='var(--primary-color)'"
                   onmouseout="this.style.borderColor='var(--input-border)';this.style.color='var(--text-secondary)'">
                    <i class="fas fa-file-archive"></i>
                    <span id="uploadFileName">选择ZIP模板包</span>
                    <input type="file" name="template_zip" accept=".zip" required onchange="document.getElementById('uploadFileName').textContent=this.files[0]?.name||'选择文件'" style="display:none;">
                </label>
                <button type="submit" name="upload_template" class="btn btn-primary btn-sm">
                    <i class="fas fa-cloud-upload-alt"></i> 上传并安装
                </button>
                <small style="color:var(--text-muted);">支持ZIP格式，最大20MB，解压后自动识别为模板</small>
            </form>
        </div>

        <!-- 当前使用提示 -->
        <div style="
            background: linear-gradient(135deg, rgba(0,102,204,0.1), rgba(255,107,0,0.05));
            border: 1px solid rgba(0,102,204,0.2); border-radius: 10px;
            padding: 16px 24px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;
        ">
            <div>
                <strong><i class="fas fa-palette" style="color:var(--primary-color);"></i> 当前使用的模板：</strong>
                <code style="background:rgba(0,102,204,0.15); color:var(--primary-color); padding:2px 10px; border-radius:4px; font-size:14px;"><?php echo e($currentTemplate); ?></code>
            </div>
            <a href="<?php echo get_config('site_url') ?: '/'; ?>" target="_blank" class="btn btn-outline btn-sm">
                <i class="fas fa-external-link-alt"></i> 预览前台
            </a>
        </div>

        <!-- 模板列表 -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-th-large"></i> 可用模板列表</h3>
                <span style="color:var(--text-muted); font-size:13px;">共 <?php echo count($templates); ?> 个模板</span>
            </div>

            <div class="template-grid">
                <?php if (empty($templates)): ?>
                    <div class="empty-state" style="grid-column:1/-1;">
                        <i class="fas fa-folder-open"></i>
                        <h3>暂无可用模板</h3>
                        <p>请上传模板或确保 templates/ 目录下存在模板文件夹</p>
                    </div>
                <?php else:
                    foreach ($templates as $template):
                        $isActive = $template['is_active'];
                ?>
                    <div class="template-card <?php echo $isActive ? 'active' : ''; ?>">
                        <?php if ($isActive): ?>
                            <span class="active-badge"><i class="fas fa-check"></i> 使用中</span>
                        <?php endif; ?>

                        <div class="template-screenshot">
                            <?php if (!empty($template['screenshot'])): ?>
                                <img src="<?php echo e($template['screenshot']); ?>" alt="<?php echo e($template['display_name']); ?>">
                            <?php else: ?>
                                <i class="fas fa-image"></i>
                                <p style="margin-top:8px;">暂无预览图</p>
                            <?php endif; ?>
                        </div>

                        <div class="template-info">
                            <h4><?php echo e($template['display_name']); ?> <code style="font-size:11px; background:var(--table-header); padding:1px 6px; border-radius:3px;"><?php echo e($template['name']); ?></code></h4>
                            <p><?php echo e($template['description']) ?: '暂无描述信息'; ?></p>

                            <div class="template-meta">
                                <span><i class="fas fa-user-edit"></i> <?php echo e($template['author']) ?: '未知作者'; ?></span>
                                <span><i class="fas fa-tag"></i> v<?php echo e($template['version']); ?></span>
                            </div>

                            <?php if (!$isActive): ?>
                                <form method="POST" style="margin-top:14px;" onsubmit="return confirm('确定要切换到「<?php echo e($template['display_name']); ?>」模板吗？')">
                                    <input type="hidden" name="template_name" value="<?php echo e($template['name']); ?>">
                                    <button type="submit" name="switch_template" class="btn btn-primary btn-sm btn-block">
                                        <i class="fas fa-exchange-alt"></i> 切换到此模板
                                    </button>
                                </form>
                            <?php else: ?>
                                <div style="margin-top:14px;">
                                    <span class="badge badge-success" style="width:100%; text-align:center; padding:8px;"><i class="fas fa-check-circle"></i> 当前正在使用</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </main>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
