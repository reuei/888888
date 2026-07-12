<?php
$activeMenu = 'messages';
$pageTitle = '留言与举报';
include __DIR__ . '/header.php';

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$type = $_GET['type'] ?? '';

$where = ['1=1'];
$params = [];
if ($type) {
    $where[] = 'type=?';
    $params[] = $type;
}
$whereStr = implode(' AND ', $where);

$total = DB::fetchOne("SELECT COUNT(*) as cnt FROM messages WHERE $whereStr", $params)['cnt'];
$messages = DB::fetchAll("SELECT * FROM messages WHERE $whereStr ORDER BY id DESC LIMIT $offset, $perPage", $params);

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id'] ?? 0);
    if ($action == 'approve') {
        DB::update('messages', ['status' => 1], 'id=?', [$id]);
    } elseif ($action == 'reject') {
        DB::update('messages', ['status' => 2], 'id=?', [$id]);
    } elseif ($action == 'delete') {
        DB::delete('messages', 'id=?', [$id]);
    }
    header('Location: messages.php?page=' . $page . ($type ? '&type=' . $type : ''));
    exit;
}

$viewMsg = null;
if (isset($_GET['view'])) {
    $viewMsg = DB::fetchOne("SELECT * FROM messages WHERE id=?", [intval($_GET['view'])]);
    if ($viewMsg && $viewMsg['status'] == 0) {
        DB::update('messages', ['status' => 1], 'id=?', [$viewMsg['id']]);
    }
}
?>

<div class="admin-card">
    <h3>留言与举报管理</h3>
    <div style="margin-bottom:15px;">
        <a href="messages.php" class="btn-small <?php echo !$type ? 'btn-primary' : 'btn-default'; ?>">全部</a>
        <a href="messages.php?type=message" class="btn-small <?php echo $type == 'message' ? 'btn-primary' : 'btn-default'; ?>">留言板</a>
        <a href="messages.php?type=report" class="btn-small <?php echo $type == 'report' ? 'btn-primary' : 'btn-default'; ?>">监督举报</a>
    </div>

    <?php if ($viewMsg): ?>
    <div style="margin-bottom:20px; padding:20px; background:#fafafa; border-radius:6px;">
        <h4 style="margin-bottom:10px;">详情查看</h4>
        <p><strong>类型：</strong><?php echo $viewMsg['type'] == 'report' ? '举报' : '留言'; ?></p>
        <p><strong>标题：</strong><?php echo e($viewMsg['title']); ?></p>
        <p><strong>姓名：</strong><?php echo e($viewMsg['name'] ?: '匿名'); ?></p>
        <p><strong>联系方式：</strong><?php echo e($viewMsg['contact'] ?: '-'); ?></p>
        <p><strong>IP：</strong><?php echo e($viewMsg['ip']); ?></p>
        <p><strong>提交时间：</strong><?php echo formatDate($viewMsg['create_time'], 'Y-m-d H:i:s'); ?></p>
        <p style="margin-top:10px;"><strong>内容：</strong></p>
        <div style="padding:15px; background:#fff; border-radius:4px; margin-top:5px; white-space:pre-wrap;"><?php echo e($viewMsg['content']); ?></div>
        <p style="margin-top:15px;">
            <a href="messages.php?page=<?php echo $page; ?><?php echo $type ? '&type=' . $type : ''; ?>" class="btn-small btn-default">返回列表</a>
        </p>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>类型</th>
                <th>标题</th>
                <th>姓名</th>
                <th>状态</th>
                <th>提交时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $m): ?>
            <tr>
                <td><?php echo $m['id']; ?></td>
                <td>
                    <span class="badge <?php echo $m['type'] == 'report' ? 'badge-danger' : 'badge-info'; ?>">
                        <?php echo $m['type'] == 'report' ? '举报' : '留言'; ?>
                    </span>
                </td>
                <td><?php echo e(truncateStr($m['title'] ?: '无标题', 30)); ?></td>
                <td><?php echo e($m['name'] ?: '匿名'); ?></td>
                <td>
                    <?php
                    $statusMap = [0 => '待处理', 1 => '已处理', 2 => '已拒绝'];
                    $statusClass = [0 => 'badge-warning', 1 => 'badge-success', 2 => 'badge-danger'];
                    ?>
                    <span class="badge <?php echo $statusClass[$m['status']] ?? 'badge-info'; ?>">
                        <?php echo $statusMap[$m['status']] ?? $m['status']; ?>
                    </span>
                </td>
                <td><?php echo formatDate($m['create_time']); ?></td>
                <td>
                    <a href="messages.php?view=<?php echo $m['id']; ?>&page=<?php echo $page; ?><?php echo $type ? '&type=' . $type : ''; ?>" class="btn-small btn-default">查看</a>
                    <?php if ($m['status'] == 0): ?>
                    <a href="messages.php?action=approve&id=<?php echo $m['id']; ?>&page=<?php echo $page; ?><?php echo $type ? '&type=' . $type : ''; ?>" class="btn-small btn-primary">通过</a>
                    <?php endif; ?>
                    <a href="messages.php?action=delete&id=<?php echo $m['id']; ?>&page=<?php echo $page; ?><?php echo $type ? '&type=' . $type : ''; ?>" onclick="return confirm('确定删除？');" class="btn-small btn-danger">删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$messages): ?>
            <tr><td colspan="7" style="text-align:center; color:#999; padding:30px 0;">暂无数据</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php
        $url = 'messages.php?' . ($type ? 'type=' . $type . '&' : '');
        echo paginate($total, $page, $perPage, $url);
        ?>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
