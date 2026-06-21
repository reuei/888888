<?php
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/config.php';
require_once YUYUN_ROOT . '/includes/functions.php';
require_once YUYUN_ROOT . '/includes/auth.php';
requireAdminLogin();

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('danger', '安全验证失败，请刷新页面重试');
        header('Location: messages.php');
        exit;
    }

    $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
    $ids = isset($_POST['ids']) && is_array($_POST['ids']) ? array_map('intval', $_POST['ids']) : [];

    if (!empty($ids)) {
        $db = YuyunDB::getInstance();
        if ($db->getType() === 'json') {
            $data = $db->jsonAll('messages', 'id', 'DESC');
            foreach ($data as &$item) {
                if (in_array($item['id'], $ids)) {
                    $item['status'] = $status;
                }
            }
            file_put_contents(YUYUN_ROOT . '/data/json/messages.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
        } else {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $db->execute("UPDATE messages SET status = ? WHERE id IN ($placeholders)", array_merge([$status], $ids));
        }
        setFlash('success', '留言状态已更新');
    } else {
        setFlash('warning', '未选择任何留言');
    }
    header('Location: messages.php');
    exit;
}

if ($action === 'delete' && $id > 0) {
    $token = $_GET['csrf_token'] ?? '';
    if (!verifyCsrf($token)) {
        setFlash('danger', '安全验证失败');
    } else {
        dbDelete('messages', $id);
        setFlash('success', '留言已删除');
    }
    header('Location: messages.php');
    exit;
}

if ($action === 'read' && $id > 0) {
    $token = $_GET['csrf_token'] ?? '';
    if (!verifyCsrf($token)) {
        setFlash('danger', '安全验证失败');
    } else {
        dbUpdate('messages', $id, ['status' => 1]);
        setFlash('success', '留言已标记为已读');
    }
    header('Location: messages.php');
    exit;
}

if ($action === 'unread' && $id > 0) {
    $token = $_GET['csrf_token'] ?? '';
    if (!verifyCsrf($token)) {
        setFlash('danger', '安全验证失败');
    } else {
        dbUpdate('messages', $id, ['status' => 0]);
        setFlash('success', '留言已标记为未读');
    }
    header('Location: messages.php');
    exit;
}

$item = ($action === 'view' && $id > 0) ? dbFind('messages', $id) : null;
if ($item && $item['status'] == 0) {
    dbUpdate('messages', $id, ['status' => 1]);
    $item['status'] = 1;
}

$filter = $_GET['filter'] ?? 'all';
$db = YuyunDB::getInstance();
if ($db->getType() === 'json') {
    $items = $db->jsonAll('messages', 'id', 'DESC');
    if ($filter === 'unread') {
        $items = array_values(array_filter($items, fn($m) => ($m['status'] ?? 0) == 0));
    } elseif ($filter === 'read') {
        $items = array_values(array_filter($items, fn($m) => ($m['status'] ?? 0) == 1));
    }
} else {
    $where = '';
    $params = [];
    if ($filter === 'unread') {
        $where = 'WHERE status = 0';
    } elseif ($filter === 'read') {
        $where = 'WHERE status = 1';
    }
    $items = $db->query("SELECT * FROM messages $where ORDER BY id DESC", $params);
}

$pageTitle = '留言管理';
include __DIR__ . '/includes/header.php';
?>

<?php if ($action === 'view' && $item): ?>
<div class="card">
    <div class="card-header">
        <h2>留言详情</h2>
        <a href="messages.php" class="btn" style="background:#f0f0f0;">返回列表</a>
    </div>
    <div class="card-body">
        <div class="form-group"><label>姓名</label><div class="form-control" style="background:#f8f9fa;"><?php echo yy_e($item['name']); ?></div></div>
        <div class="form-group"><label>电话</label><div class="form-control" style="background:#f8f9fa;"><?php echo yy_e($item['phone']); ?></div></div>
        <div class="form-group"><label>邮箱</label><div class="form-control" style="background:#f8f9fa;"><?php echo yy_e($item['email'] ?? '-'); ?></div></div>
        <div class="form-group"><label>时间</label><div class="form-control" style="background:#f8f9fa;"><?php echo yy_e($item['created_at'] ?? ''); ?></div></div>
        <div class="form-group"><label>状态</label><div class="form-control" style="background:#f8f9fa;"><?php echo $item['status'] ? '已读' : '<span style="color:#f56c00;font-weight:600;">未读</span>'; ?></div></div>
        <div class="form-group"><label>留言内容</label><div class="form-control" style="background:#f8f9fa;min-height:120px;height:auto;white-space:pre-wrap;"><?php echo yy_e($item['content']); ?></div></div>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <h2>留言列表</h2>
        <div>
            <a href="?filter=all" class="btn btn-sm <?php echo $filter === 'all' ? 'btn-primary' : ''; ?>">全部</a>
            <a href="?filter=unread" class="btn btn-sm <?php echo $filter === 'unread' ? 'btn-primary' : ''; ?>">未读</a>
            <a href="?filter=read" class="btn btn-sm <?php echo $filter === 'read' ? 'btn-primary' : ''; ?>">已读</a>
        </div>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?php echo yy_e(csrfToken()); ?>">
            <div style="margin-bottom:12px;">
                <button type="submit" name="status" value="1" class="btn btn-primary btn-sm">标记为已读</button>
                <button type="submit" name="status" value="0" class="btn btn-sm" style="background:#f0f0f0;">标记为未读</button>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th style="width:40px;"><input type="checkbox" id="checkAll"></th>
                        <th>状态</th><th>姓名</th><th>电话</th><th>邮箱</th><th>内容摘要</th><th>时间</th><th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr><td colspan="8" style="text-align:center;color:#888;">暂无留言</td></tr>
                    <?php else: ?>
                        <?php foreach ($items as $it): ?>
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="<?php echo $it['id']; ?>"></td>
                                <td><?php echo ($it['status'] ?? 0) ? '<span class="badge badge-success">已读</span>' : '<span class="badge badge-warning">未读</span>'; ?></td>
                                <td><?php echo yy_e($it['name']); ?></td>
                                <td><?php echo yy_e($it['phone']); ?></td>
                                <td><?php echo yy_e($it['email'] ?? '-'); ?></td>
                                <td><?php echo yy_e(yy_truncate($it['content'] ?? '', 40)); ?></td>
                                <td><?php echo yy_e($it['created_at'] ?? ''); ?></td>
                                <td>
                                    <a href="?action=view&id=<?php echo $it['id']; ?>" class="btn btn-sm btn-primary">查看</a>
                                    <?php if ($it['status'] ?? 0): ?>
                                        <a href="?action=unread&id=<?php echo $it['id']; ?>&csrf_token=<?php echo yy_e(csrfToken()); ?>" class="btn btn-sm" style="background:#f0f0f0;">未读</a>
                                    <?php else: ?>
                                        <a href="?action=read&id=<?php echo $it['id']; ?>&csrf_token=<?php echo yy_e(csrfToken()); ?>" class="btn btn-sm btn-success">已读</a>
                                    <?php endif; ?>
                                    <a href="?action=delete&id=<?php echo $it['id']; ?>&csrf_token=<?php echo yy_e(csrfToken()); ?>" class="btn btn-sm btn-danger" onclick="return confirm('确定删除该留言吗？')">删除</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
    </div>
</div>
<script>
document.getElementById('checkAll').addEventListener('change', function() {
    document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = this.checked);
});
</script>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
