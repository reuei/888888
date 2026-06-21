<?php
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/config.php';
require_once YUYUN_ROOT . '/includes/functions.php';
require_once YUYUN_ROOT . '/includes/auth.php';
requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
    $db = YuyunDB::getInstance();
    if ($db->getType() === 'json') {
        file_put_contents(YUYUN_ROOT . '/data/json/logs.json', '[]', LOCK_EX);
    } else {
        $db->execute("DELETE FROM logs");
    }
    setFlash('success', '日志已清空');
    header('Location: logs.php');
    exit;
}

$logs = getLogs(100);
$pageTitle = '操作日志';
include __DIR__ . '/includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h2>操作日志（最近 100 条）</h2>
        <form method="post" style="display:inline;" onsubmit="return confirm('确定清空所有日志吗？')">
            <input type="hidden" name="csrf_token" value="<?php echo yy_e(csrfToken()); ?>">
            <button type="submit" class="btn btn-sm btn-danger">清空日志</button>
        </form>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr><th>ID</th><th>操作</th><th>详情</th><th>IP</th><th>时间</th></tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="5" style="text-align:center;color:#888;">暂无日志</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo $log['id']; ?></td>
                            <td><?php echo yy_e($log['action']); ?></td>
                            <td><?php echo yy_e($log['detail']); ?></td>
                            <td><?php echo yy_e($log['ip']); ?></td>
                            <td><?php echo yy_e($log['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
