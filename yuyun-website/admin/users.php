<?php
/**
 * 语云科技 - 用户管理页面
 * 用户列表、搜索筛选、状态管理等
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/core/Functions.php';
require_once YUYUN_ROOT . '/core/Auth.php';
require_admin();

$auth = new Auth();
$users = get_content('users') ?: [];

// 处理操作
$action = $_GET['action'] ?? '';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = intval($_POST['user_id'] ?? 0);

    switch ($_POST['operation'] ?? '') {
        case 'toggle_status':
            foreach ($users as &$u) {
                if ((int)$u['id'] === $userId) {
                    $u['status'] = ($u['status'] ?? 'active') === 'active' ? 'banned' : 'active';
                    $msg = $u['status'] === 'banned' ? '用户已被禁用' : '用户已启用';
                    break;
                }
            }
            unset($u);
            save_content('users', $users);
            log_message("管理员修改了用户 #{$userId} 状态");
            break;

        case 'set_admin':
            foreach ($users as &$u) {
                if ((int)$u['id'] === $userId) {
                    $u['role'] = 'admin';
                    $msg = '已设为管理员';
                    break;
                }
            }
            unset($u);
            save_content('users', $users);
            log_message("管理员将用户 #{$userId} 设为管理员");
            break;

        case 'delete':
            $users = array_values(array_filter($users, fn($u) => (int)$u['id'] !== $userId));
            save_content('users', $users);
            $msg = '用户已删除';
            log_message("管理员删除了用户 #{$userId}");
            break;
    }

    // 重新加载
    $users = get_content('users') ?: [];
}

// 筛选和搜索
$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? 'all';

if ($search) {
    $users = array_filter($users, function($u) use ($search) {
        return stripos($u['email'] ?? '', $search) !== false ||
               stripos($u['name'] ?? '', $search) !== false;
    });
}

if ($statusFilter !== 'all') {
    $users = array_filter($users, fn($u) => ($u['status'] ?? '') === $statusFilter);
}

$users = array_values($users);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户管理 - 语云科技后台</title>
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
                <span>用户管理</span>
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
            <div style="background:rgba(40,167,69,0.12); border:1px solid rgba(40,167,69,0.3); color:#51cf66; padding:12px 16px; border-radius:8px; margin-bottom:20px;">
                <i class="fas fa-check-circle"></i> <?php echo e($msg); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-user-friends"></i> 用户管理</h3>
                <span style="color:var(--text-muted); font-size:13px;">共 <?php echo count($users); ?> 名用户</span>
            </div>

            <!-- 筛选栏 -->
            <div class="filter-bar">
                <div class="search-box">
                    <input type="text" placeholder="搜索邮箱或姓名..." value="<?php echo e($search); ?>" data-search>
                    <i class="fas fa-search"></i>
                </div>
                <select class="filter-select" data-filter onchange="location.href='?status='+this.value+(this.value==='all'?'':'&search=<?php echo urlencode($search); ?>')">
                    <option value="all" <?php echo $statusFilter === 'all' ? 'selected' : ''; ?>>全部状态</option>
                    <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>正常</option>
                    <option value="banned" <?php echo $statusFilter === 'banned' ? 'selected' : ''; ?>>已禁用</option>
                </select>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>邮箱</th>
                            <th>姓名</th>
                            <th width="80">角色</th>
                            <th width="80">状态</th>
                            <th width="150">注册时间</th>
                            <th width="180">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr><td colspan="7" class="text-center text-muted" style="padding:40px;">没有找到匹配的用户</td></tr>
                        <?php else:
                            foreach ($users as $user):
                                $roleBadge = ($user['role'] ?? 'user') === 'admin'
                                    ? '<span class="badge badge-danger">管理员</span>'
                                    : '<span class="badge badge-secondary">普通用户</span>';
                                $statusBadge = ($user['status'] ?? 'active') === 'active'
                                    ? '<span class="badge badge-success">正常</span>'
                                    : '<span class="badge badge-danger">已禁用</span>';
                        ?>
                            <tr data-id="<?php echo $user['id']; ?>">
                                <td><?php echo $user['id']; ?></td>
                                <td><strong><?php echo e($user['email'] ?? '-'); ?></strong></td>
                                <td><?php echo e($user['name'] ?? '-'); ?></td>
                                <td><?php echo $roleBadge; ?></td>
                                <td><?php echo $statusBadge; ?></td>
                                <td style="white-space:nowrap;"><?php echo format_date($user['created_at'] ?? ''); ?></td>
                                <td>
                                    <div class="table-actions">
                                        <button class="action-btn view" title="查看详情"
                                            onclick="showUserDetail(<?php echo htmlspecialchars(json_encode($user), ENT_QUOTES); ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if (($user['role'] ?? 'user') !== 'admin'): ?>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('确定要执行此操作吗？')">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="operation" value="toggle_status">
                                                <button type="submit" class="action-btn edit" title="<?php echo ($user['status'] ?? '') === 'active' ? '禁用' : '启用'; ?>">
                                                    <i class="fas fa-<?php echo ($user['status'] ?? '') === 'active' ? 'ban' : 'check'; ?>"></i>
                                                </button>
                                            </form>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('确定要将此用户设为管理员吗？')">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="operation" value="set_admin">
                                                <button type="submit" class="action-btn view" title="设为管理员">
                                                    <i class="fas fa-shield-alt"></i>
                                                </button>
                                            </form>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('确定要删除此用户吗？此操作不可恢复！')">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="operation" value="delete">
                                                <button type="submit" class="action-btn delete" title="删除">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted" title="无法操作管理员账号" style="font-size:12px;">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- 用户详情模态框 -->
    <div class="modal-overlay" id="userDetailModal">
        <div class="modal modal-lg">
            <div class="modal-header">
                <h3>用户详情</h3>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <div class="modal-body" id="userDetailContent">
                <!-- 动态填充 -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('userDetailModal').classList.remove('show')">关闭</button>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
    <script>
        function showUserDetail(user) {
            const html = `
                <div style="text-align:center; margin-bottom:24px;">
                    <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,var(--primary-color),var(--accent-color));display:inline-flex;align-items:center;justify-content:center;font-size:28px;color:white;font-weight:bold;margin-bottom:12px;">
                        ${(user.name || '?').charAt(0)}
                    </div>
                    <h3 style="margin-bottom:4px;">${user.name || '未设置姓名'}</h3>
                    <p style="color:var(--text-secondary);">${user.email}</p>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div style="background:var(--table-header); padding:16px; border-radius:8px;">
                        <p style="color:var(--text-muted); font-size:12px; margin-bottom:4px;">用户ID</p>
                        <strong>#${user.id}</strong>
                    </div>
                    <div style="background:var(--table-header); padding:16px; border-radius:8px;">
                        <p style="color:var(--text-muted); font-size:12px; margin-bottom:4px;">角色</p>
                        <strong>${(user.role || 'user') === 'admin' ? '管理员' : '普通用户'}</strong>
                    </div>
                    <div style="background:var(--table-header); padding:16px; border-radius:8px;">
                        <p style="color:var(--text-muted); font-size:12px; margin-bottom:4px;">状态</p>
                        <strong>${(user.status || 'active') === 'active' ? '<span class="badge badge-success">正常</span>' : '<span class="badge badge-danger">禁用</span>'}</strong>
                    </div>
                    <div style="background:var(--table-header); padding:16px; border-radius:8px;">
                        <p style="color:var(--text-muted); font-size:12px; margin-bottom:4px;">注册时间</p>
                        <strong>${user.created_at || '-'}</strong>
                    </div>
                </div>
            `;
            document.getElementById('userDetailContent').innerHTML = html;
            Modal.open('userDetailModal');
        }
    </script>
</body>
</html>
