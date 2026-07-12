<?php
if (!defined('IN_ADMIN')) define('IN_ADMIN', true);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn() || !isSuperAdmin()) {
    header('Location: login.php');
    exit;
}

$activeMenu = 'admins';
$pageTitle = '管理员管理';
include __DIR__ . '/header.php';

$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action == 'add') {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $nickname = trim($_POST['nickname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'admin';

        if (empty($username) || empty($password)) {
            $msg = '用户名和密码不能为空';
            $msgType = 'error';
        } elseif (strlen($password) < 6) {
            $msg = '密码长度不能少于6位';
            $msgType = 'error';
        } elseif ($password !== $password2) {
            $msg = '两次输入的密码不一致';
            $msgType = 'error';
        } elseif (!in_array($role, ['admin', 'super_admin'])) {
            $msg = '无效的角色';
            $msgType = 'error';
        } else {
            $exists = DB::fetchOne("SELECT id FROM users WHERE username=?", [$username]);
            if ($exists) {
                $msg = '该用户名已存在';
                $msgType = 'error';
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                DB::insert('users', [
                    'username' => $username,
                    'password' => $hashed,
                    'nickname' => $nickname,
                    'email' => $email,
                    'role' => $role,
                    'status' => 1,
                ]);
                $msg = '管理员添加成功';
                $msgType = 'success';
            }
        }
    } elseif ($action == 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $nickname = trim($_POST['nickname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'admin';
        $password = $_POST['password'] ?? '';

        $data = [
            'nickname' => $nickname,
            'email' => $email,
            'role' => in_array($role, ['admin', 'super_admin']) ? $role : 'admin',
        ];

        if (!empty($password)) {
            if (strlen($password) < 6) {
                $msg = '密码长度不能少于6位';
                $msgType = 'error';
            } else {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
        }

        if (!$msg) {
            DB::update('users', $data, 'id=?', [$id]);
            $msg = '管理员信息更新成功';
            $msgType = 'success';
        }
    }
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = intval($_GET['id'] ?? 0);
    $current = currentUser();

    if ($action == 'delete') {
        if ($id == $current['id']) {
            $msg = '不能删除自己';
            $msgType = 'error';
        } else {
            DB::delete('users', 'id=?', [$id]);
            $msg = '删除成功';
            $msgType = 'success';
        }
    }
}

$admins = DB::fetchAll("SELECT * FROM users WHERE role IN ('admin','super_admin') ORDER BY id ASC");
$editAdmin = null;
if (isset($_GET['edit'])) {
    $editAdmin = DB::fetchOne("SELECT * FROM users WHERE id=?", [intval($_GET['edit'])]);
}
?>

<div class="admin-card">
    <h3><?php echo $editAdmin ? '编辑管理员' : '添加管理员'; ?></h3>

    <?php if ($msg): ?>
    <div class="alert alert-<?php echo $msgType; ?>"><?php echo e($msg); ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="action" value="<?php echo $editAdmin ? 'edit' : 'add'; ?>">
        <?php if ($editAdmin): ?>
        <input type="hidden" name="id" value="<?php echo $editAdmin['id']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-item">
                <label>用户名 <?php echo $editAdmin ? '' : '*'; ?></label>
                <input type="text" name="username" value="<?php echo e($editAdmin['username'] ?? ''); ?>" <?php echo $editAdmin ? 'disabled' : 'required'; ?>>
                <?php if ($editAdmin): ?>
                <input type="hidden" name="username" value="<?php echo e($editAdmin['username']); ?>">
                <?php endif; ?>
            </div>
            <div class="form-item">
                <label>角色</label>
                <select name="role">
                    <option value="admin" <?php echo ($editAdmin['role'] ?? 'admin') == 'admin' ? 'selected' : ''; ?>>管理员</option>
                    <option value="super_admin" <?php echo ($editAdmin['role'] ?? '') == 'super_admin' ? 'selected' : ''; ?>>超级管理员</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-item">
                <label>昵称</label>
                <input type="text" name="nickname" value="<?php echo e($editAdmin['nickname'] ?? ''); ?>">
            </div>
            <div class="form-item">
                <label>邮箱</label>
                <input type="email" name="email" value="<?php echo e($editAdmin['email'] ?? ''); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-item">
                <label>密码 <?php echo $editAdmin ? '（留空则不修改）' : '*'; ?></label>
                <input type="password" name="password" <?php echo $editAdmin ? '' : 'required'; ?>>
            </div>
            <div class="form-item">
                <label>确认密码 <?php echo $editAdmin ? '（留空则不修改）' : '*'; ?></label>
                <input type="password" name="password2" <?php echo $editAdmin ? '' : 'required'; ?>>
            </div>
        </div>

        <div style="margin-top:10px;">
            <button type="submit" class="btn btn-primary">
                <?php echo $editAdmin ? '更新管理员' : '添加管理员'; ?>
            </button>
            <?php if ($editAdmin): ?>
            <a href="admins.php" class="btn btn-secondary" style="margin-left:10px;">取消编辑</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="admin-card">
    <h3>管理员列表</h3>
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
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($admins as $a): ?>
            <tr>
                <td><?php echo $a['id']; ?></td>
                <td><?php echo e($a['username']); ?></td>
                <td><?php echo e($a['nickname'] ?: '-'); ?></td>
                <td><?php echo e($a['email'] ?: '-'); ?></td>
                <td>
                    <span class="badge <?php echo $a['role'] == 'super_admin' ? 'badge-danger' : 'badge-info'; ?>">
                        <?php echo $a['role'] == 'super_admin' ? '超级管理员' : '管理员'; ?>
                    </span>
                </td>
                <td>
                    <span class="badge <?php echo $a['status'] == 1 ? 'badge-success' : 'badge-danger'; ?>">
                        <?php echo $a['status'] == 1 ? '正常' : '禁用'; ?>
                    </span>
                </td>
                <td><?php echo formatDate($a['reg_time']); ?></td>
                <td>
                    <a href="admins.php?edit=<?php echo $a['id']; ?>" class="btn-small btn-default">编辑</a>
                    <?php if ($a['id'] != currentUser()['id']): ?>
                    <a href="admins.php?action=delete&id=<?php echo $a['id']; ?>" onclick="return confirm('确定删除该管理员？');" class="btn-small btn-danger">删除</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/footer.php'; ?>
