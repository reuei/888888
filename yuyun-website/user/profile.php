<?php
/**
 * 语云科技 - 个人资料页面
 */
session_start();
require_once __DIR__ . '/../core/Functions.php';

// 要求登录
require_login();

$page_title = '个人资料 - ' . (get_config('site_name') ?: '语云科技');
$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_username'] ?? '用户';
$email = $_SESSION['user_email'] ?? '';
$avatar = $_SESSION['user_avatar'] ?? '';

$error = '';
$success = '';

// 获取用户完整信息
$users_data = get_content('users');
$current_user = null;
foreach ($users_data as $key => $u) {
    if (($u['id'] ?? '') == $user_id) {
        $current_user = $u;
        $user_key = $key;
        break;
    }
}

if (!$current_user) {
    // 如果找不到用户数据，使用Session中的信息
    $current_user = [
        'username' => $username,
        'email' => $email,
        'avatar' => $avatar,
        'created_at' => date('Y-m-d H:i:s')
    ];
}

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = '安全验证失败，请刷新页面重试';
    } else {
        $action = $_POST['action'] ?? 'profile';

        // 更新个人资料
        if ($action === 'profile') {
            $new_username = trim($_POST['username'] ?? '');
            $new_avatar_url = trim($_POST['avatar_url'] ?? '');

            if (empty($new_username)) {
                $error = '用户名不能为空';
            } elseif (mb_strlen($new_username) < 2 || mb_strlen($new_username) > 20) {
                $error = '用户名长度应为2-20个字符';
            } else {
                // 更新数据
                if ($user_key !== null && isset($users_data[$user_key])) {
                    $users_data[$user_key]['username'] = $new_username;
                    if ($new_avatar_url) {
                        $users_data[$user_key]['avatar'] = $new_avatar_url;
                    }
                    $users_data[$user_key]['updated_at'] = date('Y-m-d H:i:s');
                    save_content('users', $users_data);
                }

                // 更新Session
                $_SESSION['user_username'] = $new_username;
                if ($new_avatar_url) {
                    $_SESSION['user_avatar'] = $new_avatar_url;
                }

                $username = $new_username;
                $avatar = $new_avatar_url ?: $avatar;
                $success = '个人资料更新成功！';

                // 重新加载当前用户数据
                foreach ($users_data as $u) {
                    if (($u['id'] ?? '') == $user_id) {
                        $current_user = $u;
                        break;
                    }
                }
            }
        }

        // 修改密码
        elseif ($action === 'password') {
            $old_password = $_POST['old_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($old_password) || empty($new_password)) {
                $error = '请填写完整密码信息';
            } elseif (strlen($new_password) < 6) {
                $error = '新密码长度至少为6位';
            } elseif ($new_password !== $confirm_password) {
                $error = '两次输入的新密码不一致';
            } else {
                // 验证旧密码（这里简化处理，实际应调用API）
                // 假设验证通过，更新密码
                if ($user_key !== null && isset($users_data[$user_key])) {
                    // 实际项目中应该用 password_hash() 加密密码
                    $users_data[$user_key]['password'] = password_hash($new_password, PASSWORD_DEFAULT);
                    $users_data[$user_key]['updated_at'] = date('Y-m-d H:i:s');
                    save_content('users', $users_data);
                }

                $success = '密码修改成功！下次登录时生效。';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <style>
        /* 用户中心布局 */
        .user-layout {
            display: flex;
            min-height: calc(100vh - var(--nav-height));
            background: var(--gray-50);
        }

        .user-sidebar {
            width: 260px;
            background: var(--white);
            border-right: 1px solid var(--gray-200);
            padding: 24px 0;
            position: fixed;
            top: var(--nav-height);
            left: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 100;
        }

        .sidebar-user-info {
            padding: 0 20px 24px;
            border-bottom: 1px solid var(--gray-100);
            margin-bottom: 16px;
        }

        .sidebar-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-bg);
            margin-bottom: 12px;
        }

        .sidebar-username {
            font-size: 16px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 4px;
        }

        .sidebar-email {
            font-size: 13px;
            color: var(--gray-500);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 12px;
        }

        .sidebar-menu li { margin-bottom: 4px; }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-600);
            border-radius: var(--radius-md);
            transition: all var(--transition-fast);
            text-decoration: none;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: var(--primary-bg);
            color: var(--primary);
        }

        .sidebar-link i { width: 20px; text-align: center; font-size: 16px; }

        .sidebar-divider {
            height: 1px;
            background: var(--gray-100);
            margin: 16px 20px;
        }

        .user-main {
            flex: 1;
            margin-left: 260px;
            padding: 32px;
            min-width: 0;
        }

        /* 页面标题 */
        .page-header {
            margin-bottom: 28px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 8px;
        }

        .page-subtitle {
            font-size: 14px;
            color: var(--gray-500);
        }

        /* 内容卡片 */
        .profile-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 32px;
            margin-bottom: 24px;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--gray-100);
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            font-size: 22px;
            color: var(--primary);
        }

        /* 头像区域 */
        .avatar-section {
            display: flex;
            align-items: center;
            gap: 24px;
            margin-bottom: 32px;
            padding-bottom: 28px;
            border-bottom: 1px solid var(--gray-100);
        }

        .avatar-preview {
            position: relative;
        }

        .avatar-preview img,
        .avatar-preview .avatar-placeholder {
            width: 96px;
            height: 96px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--gray-200);
        }

        .avatar-placeholder {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 36px;
            font-weight: 700;
        }

        .avatar-info h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 6px;
        }

        .avatar-info p {
            font-size: 14px;
            color: var(--gray-500);
            margin-bottom: 12px;
        }

        /* 表单布局 */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-grid .full-width {
            grid-column: 1 / -1;
        }

        /* 只读输入框 */
        .form-input[readonly] {
            background: var(--gray-50);
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* 提示消息 */
        .alert-message {
            padding: 14px 18px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-message.error {
            background: #FEF2F2;
            color: var(--error);
            border: 1px solid #FECACA;
        }

        .alert-message.success {
            background: #F0FDF4;
            color: var(--success);
            border: 1px solid #BBF7D0;
        }

        /* 按钮组 */
        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--gray-100);
        }

        /* 移动端适配 */
        @media (max-width: 768px) {
            .user-sidebar {
                transform: translateX(-100%);
            }

            .user-sidebar.show {
                transform: translateX(0);
            }

            .user-main {
                margin-left: 0;
                padding: 20px 16px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .avatar-section {
                flex-direction: column;
                text-align: center;
            }

            .mobile-menu-btn {
                display: block !important;
            }
        }

        .mobile-menu-btn {
            display: none;
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 56px;
            height: 56px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            box-shadow: var(--shadow-lg);
            z-index: 101;
            font-size: 24px;
            cursor: pointer;
            border: none;
        }
    </style>
</head>
<body>
    <!-- 移动端菜单按钮 -->
    <button class="mobile-menu-btn" onclick="toggleSidebar()">&#9776;</button>

    <div class="user-layout">
        <!-- 侧边栏 -->
        <aside class="user-sidebar" id="userSidebar">
            <div class="sidebar-user-info">
                <?php if ($avatar): ?>
                <img src="<?php echo e($avatar); ?>" alt="头像" class="sidebar-avatar">
                <?php else: ?>
                <div class="sidebar-avatar" style="background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;color:white;font-size:24px;font-weight:700;">
                    <?php echo mb_substr($username, 0, 1); ?>
                </div>
                <?php endif; ?>
                <div class="sidebar-username"><?php echo e($username); ?></div>
                <div class="sidebar-email"><?php echo e($email); ?></div>
            </div>

            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="sidebar-link"><i>&#127968;</i> 仪表盘</a></li>
                <li><a href="profile.php" class="sidebar-link active"><i>&#128100;</i> 个人资料</a></li>
                <li><a href="tickets.php" class="sidebar-link"><i>&#128221;</i> 我的工单</a></li>
                <li><a href="feedback.php" class="sidebar-link"><i>&#128172;</i> 建议举报</a></li>
                <li class="sidebar-divider"></li>
                <li><a href="logout.php" class="sidebar-link" style="color:var(--error);"><i>&#128682;</i> 退出登录</a></li>
            </ul>
        </aside>

        <!-- 主内容区 -->
        <main class="user-main">
            <!-- 页面头部 -->
            <div class="page-header">
                <h1 class="page-title">个人资料</h1>
                <p class="page-subtitle">管理您的账户信息和安全设置</p>
            </div>

            <!-- 消息提示 -->
            <?php if ($error): ?>
            <div class="alert-message error">
                <span>&#9888;</span>
                <span><?php echo e($error); ?></span>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert-message success">
                <span>&#10003;</span>
                <span><?php echo e($success); ?></span>
            </div>
            <?php endif; ?>

            <!-- 基本信息 -->
            <div class="profile-card">
                <div class="card-header">
                    <h2 class="card-title"><i>&#128100;</i> 基本信息</h2>
                </div>

                <!-- 头像区域 -->
                <div class="avatar-section">
                    <div class="avatar-preview">
                        <?php if ($avatar || ($current_user['avatar'] ?? '')): ?>
                        <img src="<?php echo e($avatar ?: ($current_user['avatar'] ?? '')); ?>" alt="头像" id="avatarPreview">
                        <?php else: ?>
                        <div class="avatar-placeholder"><?php echo mb_substr($username, 0, 1); ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="avatar-info">
                        <h3><?php echo e($username); ?></h3>
                        <p><?php echo e($email); ?></p>
                        <p style="font-size:13px;color:var(--gray-400);">
                            注册时间：<?php echo format_date($current_user['created_at'] ?? '', 'Y-m-d'); ?>
                        </p>
                    </div>
                </div>

                <!-- 编辑表单 -->
                <form method="POST" action="" id="profileForm">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="action" value="profile">

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">头像URL</label>
                            <input type="url" name="avatar_url" class="form-input"
                                   placeholder="请输入头像图片地址（可选）"
                                   value="<?php echo e($current_user['avatar'] ?? ''); ?>"
                                   onchange="document.getElementById('avatarPreview').src=this.value||''">
                            <p class="form-hint">支持外部图片链接或上传后的相对路径</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label">用户名</label>
                            <input type="text" name="username" class="form-input"
                                   placeholder="请输入用户名" required
                                   minlength="2" maxlength="20"
                                   value="<?php echo e($current_user['username'] ?? $username); ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">邮箱地址</label>
                            <input type="email" class="form-input"
                                   value="<?php echo e($email); ?>" readonly>
                            <p class="form-hint">邮箱地址不可更改，如需修改请联系管理员</p>
                        </div>
                    </div>

                    <div class="btn-group">
                        <button type="reset" class="btn btn-outline-dark">重置</button>
                        <button type="submit" class="btn btn-primary">保存修改</button>
                    </div>
                </form>
            </div>

            <!-- 修改密码 -->
            <div class="profile-card" id="password">
                <div class="card-header">
                    <h2 class="card-title"><i>&#128274;</i> 修改密码</h2>
                </div>

                <form method="POST" action="" id="passwordForm">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                    <input type="hidden" name="action" value="password">

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">当前密码</label>
                            <input type="password" name="old_password" class="form-input"
                                   placeholder="请输入当前密码" required>
                        </div>

                        <div class="form-group"></div>

                        <div class="form-group">
                            <label class="form-label">新密码</label>
                            <input type="password" name="new_password" id="newPassword" class="form-input"
                                   placeholder="请输入新密码(至少6位)" required
                                   minlength="6">
                        </div>

                        <div class="form-group">
                            <label class="form-label">确认新密码</label>
                            <input type="password" name="confirm_password" id="confirmNewPassword" class="form-input"
                                   placeholder="请再次输入新密码" required
                                   minlength="6"
                                   oninput="checkPwdMatch()">
                        </div>
                    </div>

                    <p class="form-error" id="pwdMatchError" style="display:none;"></p>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-accent">修改密码</button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/modal.js"></script>
    <script>
        // 移动端侧边栏切换
        function toggleSidebar() {
            document.getElementById('userSidebar').classList.toggle('show');
        }

        // 密码匹配检查
        function checkPwdMatch() {
            var newPwd = document.getElementById('newPassword').value;
            var confirmPwd = document.getElementById('confirmNewPassword').value;
            var errorEl = document.getElementById('pwdMatchError');

            if (confirmPwd && newPwd !== confirmPwd) {
                errorEl.textContent = '两次输入的密码不一致';
                errorEl.style.display = 'block';
            } else {
                errorEl.style.display = 'none';
            }
        }

        // 表单验证
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            var username = this.querySelector('[name="username"]').value.trim();
            if (username.length < 2 || username.length > 20) {
                e.preventDefault();
                showToast('用户名长度应为2-20个字符', 'warning');
                return;
            }
        });

        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            var oldPwd = this.querySelector('[name="old_password"]').value;
            var newPwd = this.querySelector('[name="new_password"]').value;
            var confirmPwd = this.querySelector('[name="confirm_password"]').value;

            if (!oldPwd || !newPwd || !confirmPwd) {
                e.preventDefault();
                showToast('请填写完整的密码信息', 'warning');
                return;
            }

            if (newPwd.length < 6) {
                e.preventDefault();
                showToast('新密码长度至少为6位', 'warning');
                return;
            }

            if (newPwd !== confirmPwd) {
                e.preventDefault();
                showToast('两次输入的新密码不一致', 'warning');
                return;
            }

            if (oldPwd === newPwd) {
                e.preventDefault();
                showToast('新密码不能与当前密码相同', 'warning');
                return;
            }
        });
    </script>
</body>
</html>
