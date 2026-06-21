<?php
/**
 * 语云科技 - 系统管理页面
 * 系统信息展示、缓存清理、数据备份恢复、日志查看等
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/core/Functions.php';
require_admin();

$msg = '';
$msgType = 'success';

// 获取系统信息
$sysInfo = [
    'php_version' => PHP_VERSION,
    'php_sapi' => php_sapi_name(),
    'server_os' => PHP_OS,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'server_ip' => $_SERVER['SERVER_ADDR'] ?? gethostbyname(gethostname()),
    'document_root' => $_SERVER['DOCUMENT_ROOT'],
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time') . '秒',
    'upload_max_size' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'mysql_status' => extension_loaded('mysqli') || extension_loaded('pdo_mysql') ? '已安装' : '未安装',
    'gd_status' => extension_loaded('gd') ? '已安装' : '未安装',
    'zip_status' => class_exists('ZipArchive') ? '可用' : '不可用',
    'openssl_status' => extension_loaded('openssl') ? '已安装' : '未安装',
];

$diskTotal = function_exists('disk_total_space') ? round(disk_total_space('/') / (1024*1024*1024), 2) : 0;
$diskFree = function_exists('disk_free_space') ? round(disk_free_space('/') / (1024*1024*1024), 2) : 0;
$diskUsed = $diskTotal - $diskFree;
$diskPercent = $diskTotal > 0 ? round($diskUsed / $diskTotal * 100, 1) : 0;

// 处理操作
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'clear_cache':
        $cacheDir = YUYUN_ROOT . '/data/cache';
        $cleared = 0;
        if (is_dir($cacheDir)) {
            foreach (glob($cacheDir . '/*.*') as $f) {
                if (is_file($f)) { unlink($f); $cleared++; }
            }
        }
        $msg = "缓存清理完成，共清除 {$cleared} 个缓存文件";
        log_message("管理员执行了缓存清理操作");
        break;

    case 'backup':
        header('Content-Type: application/json; charset=utf-8');
        $backup = [
            'version' => '1.0.0',
            'backup_time' => date('Y-m-d H:i:s'),
            'config' => get_config(),
            'banners' => get_content('banners'),
            'partners' => get_content('partners'),
            'products' => get_content('products'),
            'staff' => get_content('staff'),
            'certificates' => get_content('certificates'),
            'links' => get_content('links'),
            'tickets' => get_content('tickets'),
            'ticket_replies' => get_content('ticket_replies'),
            'users' => get_content('users')
        ];
        $filename = "yuyun_backup_" . date('Ymd_His') . ".json";
        header("Content-Disposition: attachment; filename={$filename}");
        echo json_encode($backup, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        log_message("管理员执行了数据备份操作");
        exit;

    case 'restore':
        if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['backup_file']['tmp_name'];
            $content = file_get_contents($file);
            $backup = json_decode($content, true);

            if (!$backup || !is_array($backup)) {
                $msg = '备份文件格式错误，请选择有效的JSON备份文件';
                $msgType = 'error';
            } else {
                $restored = 0;
                $types = ['banners','partners','products','staff','certificates','links','tickets','ticket_replies','users'];
                foreach ($types as $type) {
                    if (isset($backup[$type]) && is_array($backup[$type])) {
                        save_content($type, $backup[$type]);
                        $restored++;
                    }
                }
                if (isset($backup['config']) && is_array($backup['config'])) {
                    $cfgFile = DATA_PATH . 'config.json';
                    file_put_contents($cfgFile, json_encode(array_merge(get_config(), $backup['config']), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                    $restored++;
                }
                $msg = "数据恢复成功！共恢复了 {$restored} 类数据";
                log_message("管理员执行了数据恢复操作");
            }
        } else {
            $msg = '请选择要导入的备份文件';
            $msgType = 'error';
        }
        break;

    case 'clear_logs':
        $logDir = YUYUN_ROOT . '/data/logs';
        $cleared = 0;
        if (is_dir($logDir)) {
            foreach (glob($logDir . '/*.log') as $f) {
                if (is_file($f)) { unlink($f); $cleared++; }
            }
        }
        $msg = "日志文件已清理，共删除 {$cleared} 个日志文件";
        log_message("管理员执行了日志清理操作");
        break;
}

// 读取最近日志
$logDir = YUYUN_ROOT . '/data/logs';
$recentLogs = [];
if (is_dir($logDir)) {
    $logFiles = glob($logDir . '/*.log');
    rsort($logFiles);
    $latestFile = $logFiles[0] ?? null;

    if ($latestFile && file_exists($latestFile)) {
        $lines = file($latestFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $recentLogs = array_slice(array_reverse($lines), 0, 30);
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统管理 - 语云科技后台</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- 侧边栏 -->
    <?php include __DIR__ . '/_sidebar.php'; ?>

    <!-- 顶部导航 -->
    <header class="header">
        <div class="header-left">
            <button class="mobile-menu-btn" onclick="document.getElementById('sidebar').classList.add('mobile-show'); document.querySelector('.sidebar-overlay').classList.add('show');"><i class="fas fa-bars"></i></button>
            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fas fa-home"></i></a>
                <span class="breadcrumb-separator">/</span>
                <span>系统管理</span>
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
        <?php if ($msg): ?>
            <div style="
                background:<?php echo $msgType === 'error' ? 'rgba(220,53,69,0.12)' : 'rgba(40,167,69,0.12)'; ?>;
                border:1px solid <?php echo $msgType === 'error' ? 'rgba(220,53,69,0.3)' : 'rgba(40,167,69,0.3)'; ?>;
                color:<?php echo $msgType === 'error' ? '#ff6b6b' : '#51cf66'; ?>;
                padding:12px 16px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:10px;
            ">
                <i class="fas fa-<?php echo $msgType === 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                <?php echo e($msg); ?>
            </div>
        <?php endif; ?>

        <!-- 系统概览 -->
        <div class="stats-grid" style="margin-bottom:24px;">
            <div class="stat-card">
                <div class="stat-icon blue"><i class="fab fa-php"></i></div>
                <div class="stat-info">
                    <h4><?php echo $sysInfo['php_version']; ?></h4>
                    <p>PHP 版本</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-hdd"></i></div>
                <div class="stat-info">
                    <h4><?php echo $diskFree; ?>G / <?php echo $diskTotal; ?>G</h4>
                    <p>磁盘空间 (<?php echo $diskPercent; ?>% 已用)</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i class="fas fa-memory"></i></div>
                <div class="stat-info">
                    <h4><?php echo $sysInfo['memory_limit']; ?></h4>
                    <p>内存限制</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <h4><?php echo $sysInfo['max_execution_time']; ?></h4>
                    <p>最大执行时间</p>
                </div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap:24px;">
            <!-- 系统详细信息 -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-server"></i> 系统环境信息</h3>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <tbody>
                            <tr><td width="140" style="color:var(--text-muted);">操作系统</td><td><strong><?php echo $sysInfo['server_os']; ?></strong></td></tr>
                            <tr><td style="color:var(--text-muted);">Web服务器</td><td><strong><?php echo $sysInfo['server_software']; ?></strong></td></tr>
                            <tr><td style="color:var(--text-muted);">PHP SAPI</td><td><code><?php echo $sysInfo['php_sapi']; ?></code></td></tr>
                            <tr><td style="color:var(--text-muted);">服务器IP</td><td><code><?php echo $sysInfo['server_ip']; ?></code></td></tr>
                            <tr><td style="color:var(--text-muted);">文档根目录</td><td style="word-break:break-all;"><code style="font-size:12px;"><?php echo $sysInfo['document_root']; ?></code></td></tr>
                            <tr><td style="color:var(--text-muted);">MySQL支持</td><td><span class="badge badge-<?php echo $sysInfo['mysql_status'] === '已安装' ? 'success' : 'danger'; ?>"><?php echo $sysInfo['mysql_status']; ?></span></td></tr>
                            <tr><td style="color:var(--text-muted);">GD库</td><td><span class="badge badge-<?php echo $sysInfo['gd_status'] === '已安装' ? 'success' : 'danger'; ?>"><?php echo $sysInfo['gd_status']; ?></span></td></tr>
                            <tr><td style="color:var(--text-muted);">ZIP扩展</td><td><span class="badge badge-<?php echo $sysInfo['zip_status'] === '可用' ? 'success' : 'danger'; ?>"><?php echo $sysInfo['zip_status']; ?></span></td></tr>
                            <tr><td style="color:var(--text-muted);">OpenSSL</td><td><span class="badge badge-<?php echo $sysInfo['openssl_status'] === '已安装' ? 'success' : 'danger'; ?>"><?php echo $sysInfo['openssl_status']; ?></span></td></tr>
                            <tr><td style="color:var(--text-muted);">上传限制</td><td><strong><?php echo $sysInfo['upload_max_size']; ?></strong></td></tr>
                            <tr><td style="color:var(--text-muted);">POST限制</td><td><strong><?php echo $sysInfo['post_max_size']; ?></strong></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 系统操作 -->
            <div>
                <!-- 维护工具 -->
                <div class="card" style="margin-bottom:24px;">
                    <div class="card-header">
                        <h3><i class="fas fa-tools"></i> 系统维护工具</h3>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <form method="POST" onsubmit="return confirm('确定要清除所有缓存吗？')">
                            <input type="hidden" name="action" value="clear_cache">
                            <button type="submit" class="btn btn-outline" style="width:100%; justify-content:flex-start;" data-clear-cache>
                                <i class="fas fa-broom"></i> 清除系统缓存
                            </button>
                        </form>

                        <form method="POST" action="" onsubmit="this.submit(); this.querySelector('button').disabled=true; this.querySelector('button').innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> 导出中...';">
                            <input type="hidden" name="action" value="backup">
                            <button type="submit" class="btn btn-outline" style="width:100%; justify-content:flex-start;" data-backup>
                                <i class="fas fa-download"></i> 数据备份 (导出JSON)
                            </button>
                        </form>

                        <form method="POST" enctype="multipart/form-data" onsubmit="return confirm('确定要导入备份数据吗？这将覆盖当前数据！')">
                            <input type="hidden" name="action" value="restore">
                            <div style="display:flex; gap:10px; align-items:center;">
                                <label style="
                                    flex:1; display:flex; align-items:center; gap:8px; padding:10px 14px;
                                    background:var(--input-bg); border:1px solid var(--input-border); border-radius:8px;
                                    cursor:pointer; color:var(--text-secondary); font-size:13px; transition:border-color 0.2s;
                                " onmouseover="this.style.borderColor='var(--primary-color)'"
                                   onmouseout="this.style.borderColor='var(--input-border)'">
                                    <i class="fas fa-file-upload"></i>
                                    <span id="restoreFileName">选择备份文件 (.json)</span>
                                    <input type="file" name="backup_file" accept=".json" required onchange="document.getElementById('restoreFileName').textContent=this.files[0]?.name||'选择文件'" style="display:none;">
                                </label>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-upload"></i> 导入恢复
                                </button>
                            </div>
                        </form>

                        <form method="POST" onsubmit="return confirm('确定要清空所有日志文件吗？此操作不可恢复！')">
                            <input type="hidden" name="action" value="clear_logs">
                            <button type="submit" class="btn btn-outline" style="width:100%; justify-content:flex-start; color:var(--warning-color);">
                                <i class="fas fa-trash-alt"></i> 清空日志文件
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 操作日志 -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-history"></i> 最近操作日志</h3>
                        <span style="font-size:12px; color:var(--text-muted);">
                            <?php echo count($recentLogs); ?> 条记录
                        </span>
                    </div>
                    <?php if (empty($recentLogs)): ?>
                        <div class="empty-state" style="padding:30px;">
                            <i class="fas fa-file-alt" style="opacity:0.2;"></i>
                            <p style="margin-top:8px; color:var(--text-muted);">暂无日志记录</p>
                        </div>
                    <?php else: ?>
                        <div class="log-list">
                            <?php foreach ($recentLogs as $logLine):
                                // 解析日志格式: [时间] [级别] 消息
                                if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \[(\w+)\] (.+)$/i', trim($logLine), $matches)):
                                    $time = $matches[1];
                                    $level = strtolower($matches[2]);
                                    $message = $matches[3];
                            ?>
                                <div class="log-entry">
                                    <span class="log-time"><?php echo substr($time, 5); ?></span>
                                    <span class="log-level <?php echo $level; ?>"><?php echo strtoupper($level); ?></span>
                                    <span class="log-msg"><?php echo e($message); ?></span>
                                </div>
                            <?php else: ?>
                                <div class="log-entry">
                                    <span class="log-msg" style="color:var(--text-muted);"><?php echo e(trim($logLine)); ?></span>
                                </div>
                            <?php endif; endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- 关于信息 -->
        <div class="card" style="margin-top:24px;">
            <div class="card-header">
                <h3><i class="fas fa-info-circle"></i> 关于本系统</h3>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:24px; align-items:center;">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:56px;height:56px;background:linear-gradient(135deg,var(--primary-color),var(--accent-color));border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:24px;color:white;font-weight:bold;">
                        语
                    </div>
                    <div>
                        <h4 style="margin-bottom:2px;">语云科技企业官网管理系统</h4>
                        <p style="font-size:13px; color:var(--text-muted);">版本 v1.0.0 &nbsp;|&nbsp; 基于 PHP + JSON 存储</p>
                    </div>
                </div>
                <div style="flex:1; display:flex; gap:20px; flex-wrap:wrap; font-size:13px; color:var(--text-secondary);">
                    <span><i class="fas fa-code-branch"></i> 架构：MVC轻量级</span>
                    <span><i class="fas fa-database"></i> 存储：JSON文件</span>
                    <span><i class="fas fa-shield-alt"></i> 安全：CSRF防护 + Session认证</span>
                    <span><i class="fas fa-palette"></i> UI：响应式深色主题</span>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
