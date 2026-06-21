<?php
/**
 * 语云科技官网 - 安装/初始化程序
 * YuYun Tech - Installer
 */

// 环境检测
$php_ok = version_compare(PHP_VERSION, '7.0', '>=');
$json_ok = extension_loaded('json');
$write_ok = is_writable(__DIR__ . '/data') || is_writable(__DIR__);
$session_ok = session_status() !== PHP_SESSION_DISABLED;

$installed = file_exists(__DIR__ . '/data/site_data.json');

// 安装处理
if (isset($_POST['do_install'])) {
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0755, true);
    }
    require_once __DIR__ . '/config.php';
    // 默认数据已经在 config 中通过 get_default_data 生成
    $data = get_default_data();
    file_put_contents(__DIR__ . '/data/site_data.json', json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    // 写入锁
    file_put_contents(__DIR__ . '/data/installed.log', date('Y-m-d H:i:s') . ' 系统安装成功');
    header('Location: admin/login.php?installed=1');
    exit;
}

if (isset($_POST['do_reinstall'])) {
    @unlink(__DIR__ . '/data/site_data.json');
    header('Location: install.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>语云科技官网 - 安装程序</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<style>
    body { background: linear-gradient(135deg, #0f1b3d, #1a2d5c); min-height: 100vh; padding: 30px 15px; color: #1a1a2e; }
    .install-box { max-width: 720px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
    .install-header { background: linear-gradient(135deg, #1a73e8, #ff6b35); padding: 30px; text-align: center; color: #fff; }
    .install-header h1 { font-size: 28px; font-weight: 700; }
    .install-header p { font-size: 14px; opacity: 0.9; margin-top: 8px; }
    .install-body { padding: 40px; }
    .steps { display: flex; border-bottom: 2px solid #e9ecef; margin-bottom: 30px; }
    .step { flex: 1; padding: 12px; text-align: center; font-weight: 600; color: #6b7280; font-size: 14px; position: relative; }
    .step.active { color: #1a73e8; border-bottom: 3px solid #1a73e8; margin-bottom: -2px; }
    .check-list { margin: 20px 0; }
    .check-item {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 18px; background: #f8fafc;
        border-radius: 8px; margin-bottom: 10px; font-size: 15px;
    }
    .check-item.ok { background: rgba(0,168,107,0.08); color: #00a86b; }
    .check-item.err { background: rgba(231,76,60,0.08); color: #e74c3c; }
    .btn-block { text-align: center; margin-top: 30px; }
    .info-panel { background: #f0f7ff; border: 1px solid #d0e4ff; border-radius: 8px; padding: 20px; margin: 20px 0; color: #1a5a9e; font-size: 14px; line-height: 1.8; }
    .info-panel strong { color: #1a73e8; }
    code { background: #1a1a2e; color: #ff6b35; padding: 2px 8px; border-radius: 4px; font-family: monospace; font-size: 13px; }
</style>
</head>
<body>
<div class="install-box">
    <div class="install-header">
        <i class="fas fa-cloud" style="font-size:48px;margin-bottom:12px;"></i>
        <h1>语云科技企业官网系统</h1>
        <p>YuYun Tech Enterprise Website System v1.0 · 安装向导</p>
    </div>

    <div class="install-body">
        <div class="steps">
            <div class="step active"><i class="fas fa-check-circle"></i> 环境检测</div>
            <div class="step active"><i class="fas fa-cog"></i> 初始化</div>
            <div class="step"><i class="fas fa-flag-checkered"></i> 完成</div>
        </div>

        <?php if ($installed && !isset($_GET['force'])): ?>
            <div class="info-panel" style="background:rgba(0,168,107,0.08);border-color:#86d7b8;color:#007a55;">
                <i class="fas fa-check-circle" style="font-size:22px;margin-right:8px;"></i>
                <strong>系统已安装！</strong> 您现在可以直接进入使用，如需重新安装，请点击下方"重新安装"按钮。
            </div>
            <div class="btn-block" style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                <a href="index.php" class="btn btn-primary btn-lg"><i class="fas fa-home"></i> 访问网站首页</a>
                <a href="admin/login.php" class="btn btn-secondary btn-lg"><i class="fas fa-user-shield"></i> 进入后台管理</a>
                <form method="post" style="display:inline;">
                    <button type="submit" name="do_reinstall" class="btn btn-ghost btn-lg" onclick="return confirm('确定要重新安装吗？将清除现有配置。')"><i class="fas fa-sync-alt"></i> 重新安装</button>
                </form>
            </div>
        <?php else: ?>
            <h3 style="color:#1a1a2e;font-size:20px;font-weight:700;margin-bottom:16px;"><i class="fas fa-server" style="color:#1a73e8;"></i> 运行环境检测</h3>
            <div class="check-list">
                <div class="check-item <?php echo $php_ok ? 'ok' : 'err'; ?>">
                    <i class="fas fa-<?php echo $php_ok ? 'check' : 'times'; ?>-circle"></i>
                    <span>PHP 版本 >= 7.0 （当前 <?php echo phpversion(); ?>）</span>
                </div>
                <div class="check-item <?php echo $json_ok ? 'ok' : 'err'; ?>">
                    <i class="fas fa-<?php echo $json_ok ? 'check' : 'times'; ?>-circle"></i>
                    <span>JSON 扩展</span>
                </div>
                <div class="check-item <?php echo $session_ok ? 'ok' : 'err'; ?>">
                    <i class="fas fa-<?php echo $session_ok ? 'check' : 'times'; ?>-circle"></i>
                    <span>Session 支持</span>
                </div>
                <div class="check-item <?php echo $write_ok ? 'ok' : 'err'; ?>">
                    <i class="fas fa-<?php echo $write_ok ? 'check' : 'times'; ?>-circle"></i>
                    <span>目录写入权限（用于存储 JSON 数据）</span>
                </div>
                <div class="check-item ok">
                    <i class="fas fa-database" style="color:#9b59b6;"></i>
                    <span>数据存储：JSON 文件模式（无需 MySQL）</span>
                </div>
            </div>

            <div class="info-panel">
                <strong><i class="fas fa-info-circle"></i> 默认登录账号：</strong><br>
                用户名： <code>admin</code> &nbsp;&nbsp; 密码： <code>admin123</code><br>
                安装后请尽快进入后台 <strong>账号管理</strong> 中修改密码。
            </div>

            <?php if (!$php_ok || !$json_ok || !$session_ok): ?>
            <div class="info-panel" style="background:rgba(231,76,60,0.08);border-color:#f5c6cb;color:#c0392b;">
                <i class="fas fa-exclamation-triangle"></i> <strong>环境检测未通过</strong>，请检查 PHP 环境后重试。
            </div>
            <?php endif; ?>

            <div class="btn-block">
                <form method="post">
                    <a href="index.php" class="btn btn-ghost btn-lg" style="margin-right:12px;"><i class="fas fa-home"></i> 先看看首页</a>
                    <button type="submit" name="do_install" class="btn btn-primary btn-lg" <?php echo (!$php_ok || !$json_ok || !$session_ok) ? 'disabled' : ''; ?>>
                        <i class="fas fa-cog"></i> 立即安装
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <div style="margin-top:40px;padding-top:20px;border-top:1px solid #e9ecef;text-align:center;font-size:13px;color:#6b7280;">
            语云科技 © <?php echo date('Y'); ?> · 企业官网系统 v1.0 · 为您提供一站式云服务解决方案
        </div>
    </div>
</div>
</body>
</html>
