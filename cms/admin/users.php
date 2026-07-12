<?php
$activeMenu = 'users';
$pageTitle = '用户管理';
include __DIR__ . '/header.php';

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$keyword = trim($_GET['keyword'] ?? '');
$role = $_GET['role'] ?? '';

$where = ['1=1'];
$params = [];
if ($keyword) {
    $where[] = '(username LIKE ? OR nickname LIKE ? OR email LIKE ?)';
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}
if ($role) {
    $where[] = 'role=?';
    $params[] = $role;
}
$whereStr = implode(' AND ', $where);

$total = DB::fetchOne("SELECT COUNT(*) as cnt FROM users WHERE $whereStr", $params)['cnt'];
$users = DB::fetchAll("SELECT * FROM users WHERE $whereStr ORDER BY id DESC LIMIT $offset, $perPage", $params);

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id'] ?? 0);
    if ($action == 'ban') {
        DB::update('users', ['status' => 0], 'id=?', [$id]);
    } elseif ($action == 'unban') {
        DB::update('users', ['status' => 1], 'id=?', [$id]);
    } elseif ($action == 'delete') {
        $current = currentUser();
        if ($id == $current['id']) {
            echo '<script>alert("不能删除自己");</script>';
        } else {
            DB::delete('users', 'id=?', [$id]);
        }
    }
    header('Location: users.php?page=' . $page . ($keyword ? '&keyword=' . urlencode($keyword) : '') . ($role ? '&role=' . $role : ''));
    exit;
}
?>

<div class="admin-card">
    <h3>用户管理</h3>
    <div style="margin-bottom:15px; display:flex; gap:10px;">
        <form method="get" style="display:flex; gap:10px;">
            <select name="role" style="padding:6px 10px; border:1px solid #ddd; border-radius:4px;">
                <option value="">全部角色</option>
                <option value="super_admin" <?php echo $role == 'super_admin' ? 'selected' : ''; ?>>超级管理员</option>
                <option value="admin" <?php echo $role == 'admin' ? 'selected' : ''; ?>>管理员</option>
                <option value="subscriber" <?php echo $role == 'subscriber' ? 'selected' : ''; ?>>普通用户</option>
            </select>
            <input type="text" name="keyword" value="<?php echo e($keyword); ?>" placeholder="搜索用户名/昵称/邮箱..." style="padding:6px 10px; border:1px solid #ddd; border-radius:4px; width:250px;">
            <button type="submit" class="btn-small btn-primary">搜索</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>用户名</th>
                <th>昵称</th>
                <th>邮箱</th>
                <th>角色</th>
                <th>状态</th>
                <th>注册时间</th>
                <th>最后登录</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo e($u['username']); ?></td>
                <td><?php echo e($u['nickname'] ?: '-'); ?></td>
                <td><?php echo e($u['email'] ?: '-'); ?></td>
                <td>
                    <?php
                    $roleMap = ['super_admin' => '超级管理员', 'admin' => '管理员', 'subscriber' => '普通用户'];
                    $roleClass = ['super_admin' => 'badge-danger', 'admin' => 'badge-info', 'subscriber' => 'badge-success'];
                    ?>
                    <span class="badge <?php echo $roleClass[$u['role']] ?? 'badge-info'; ?>">
                        <?php echo e($roleMap[$u['role']] ?? $u['role']); ?>
                    </span>
                </td>
                <td>
                    <span class="badge <?php echo $u['status'] == 1 ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo $u['status'] == 1 ? '正常' : '禁用'; ?>
                    </span>
                </td>
                <td><?php echo formatDate($u['reg_time']); ?></td>
                <td><?php echo $u['last_login'] ? formatDate($u['last_login']) : '-'; ?></td>
                <td>
                    <?php if ($u['status'] == 1): ?>
                    <a href="users.php?action=ban&id=<?php echo $u['id']; ?>&page=<?php echo $page; ?>" onclick="return confirm('确定禁用？');" class="btn-small btn-default">禁用</a>
                    <?php else: ?>
                    <a href="users.php?action=unban&id=<?php echo $u['id']; ?>&page=<?php echo $page; ?>" class="btn-small btn-primary">启用</a>
                    <?php endif; ?>
                    <a href="users.php?action=delete&id=<?php echo $u['id']; ?>&page=<?php echo $page; ?>" onclick="return confirm('确定删除该用户？');" class="btn-small btn-danger">删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$users): ?>
            <tr><td colspan="9" style="text-align:center; color:#999; padding:30px 0;">暂无用户</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php
        $url = 'users.php?' . ($keyword ? 'keyword=' . urlencode($keyword) . '&' : '') . ($role ? 'role=' . $role . '&' : '');
        echo paginate($total, $page, $perPage, $url);
        ?>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
