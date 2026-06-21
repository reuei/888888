<?php
/**
 * 语云科技 - 建议举报页面
 */
session_start();
require_once __DIR__ . '/../core/Functions.php';

// 要求登录
require_login();

$page_title = '建议举报 - ' . (get_config('site_name') ?: '语云科技');
$user_id = $_SESSION['user_id'];
$username = $_SESSION['user_username'] ?? '用户';
$email = $_SESSION['user_email'] ?? '';
$avatar = $_SESSION['user_avatar'] ?? '';

$error = '';
$success = '';

// 获取反馈数据
$feedbacks_data = get_content('feedbacks');

// 处理提交反馈
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = '安全验证失败';
    } else {
        $type = $_POST['feedback_type'] ?? 'suggestion';
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($title) || empty($description)) {
            $error = '请填写标题和详细描述';
        } elseif (mb_strlen($title) > 100) {
            $error = '标题不能超过100个字符';
        } else {
            // 处理附件上传（如果有）
            $attachment_path = '';
            if (!empty($_FILES['attachment']['name'])) {
                $upload_result = handle_upload($_FILES['attachment']);
                if ($upload_result['success']) {
                    $attachment_path = $upload_result['path'];
                }
            }

            // 创建新反馈
            $new_feedback = [
                'id' => uniqid('fb_'),
                'user_id' => $user_id,
                'username' => $username,
                'type' => $type,
                'title' => $title,
                'description' => $description,
                'attachment' => $attachment_path,
                'status' => 'pending',
                'reply' => '',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $feedbacks_data[] = $new_feedback;
            save_content('feedbacks', $feedbacks_data);
            $success = ($type === 'report') ? '举报提交成功，我们会尽快处理！' : '感谢您的建议，我们会认真考虑！';

            // 刷新页面
            header('Location: feedback.php');
            exit;
        }
    }
}

// 筛选当前用户的反馈
$my_feedbacks = array_filter($feedbacks_data, function($f) use ($user_id) {
    return ($f['user_id'] ?? '') == $user_id;
});

// 按时间倒序排序
usort($my_feedbacks, function($a, $b) {
    return strtotime($b['created_at'] ?? 0) - strtotime($a['created_at'] ?? 0);
});

// 状态映射
$status_map = [
    'pending' => ['text' => '待处理', 'class' => 'warning', 'bg' => '#FEF3C7', 'color' => '#D97706'],
    'processing' => ['text' => '处理中', 'class' => 'info', 'bg' => '#DBEAFE', 'color' => '#2563EB'],
    'resolved' => ['text' => '已解决', 'class' => 'success', 'bg' => '#D1FAE5', 'color' => '#059669']
];

// 类型映射
$type_map = [
    'suggestion' => ['text' => '建议', 'icon' => '&#128161;', 'color' => 'var(--primary)'],
    'report' => ['text' => '举报', 'icon' => '&#9888;', 'color' => 'var(--error)']
];
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
        .user-layout { display: flex; min-height: calc(100vh - var(--nav-height)); background: var(--gray-50); }

        .user-sidebar {
            width: 260px; background: var(--white); border-right: 1px solid var(--gray-200);
            padding: 24px 0; position: fixed; top: var(--nav-height); left: 0; bottom: 0;
            overflow-y: auto; z-index: 100;
        }

        .sidebar-user-info { padding: 0 20px 24px; border-bottom: 1px solid var(--gray-100); margin-bottom: 16px; }
        .sidebar-avatar { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-bg); margin-bottom: 12px; }
        .sidebar-username { font-size: 16px; font-weight: 700; color: var(--gray-900); margin-bottom: 4px; }
        .sidebar-email { font-size: 13px; color: var(--gray-500); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        .sidebar-menu { list-style: none; padding: 0 12px; }
        .sidebar-menu li { margin-bottom: 4px; }
        .sidebar-link {
            display: flex; align-items: center; gap: 12px; padding: 12px 16px;
            font-size: 14px; font-weight: 500; color: var(--gray-600);
            border-radius: var(--radius-md); transition: all var(--transition-fast); text-decoration: none;
        }
        .sidebar-link:hover, .sidebar-link.active { background: var(--primary-bg); color: var(--primary); }
        .sidebar-link i { width: 20px; text-align: center; font-size: 16px; }
        .sidebar-divider { height: 1px; background: var(--gray-100); margin: 16px 20px; }

        .user-main { flex: 1; margin-left: 260px; padding: 32px; min-width: 0; }

        /* 页面头部 */
        .page-header { margin-bottom: 28px; }
        .page-title { font-size: 24px; font-weight: 800; color: var(--gray-900); margin-bottom: 8px; }
        .page-subtitle { font-size: 14px; color: var(--gray-500); }

        /* Tab切换 */
        .feedback-tabs {
            display: flex; gap: 8px; margin-bottom: 28px;
            border-bottom: 2px solid var(--gray-200); padding-bottom: 0;
        }

        .feedback-tab {
            padding: 14px 24px; font-size: 15px; font-weight: 600;
            color: var(--gray-500); cursor: pointer; border: none;
            background: transparent; position: relative;
            transition: all var(--transition-fast);
        }

        .feedback-tab:hover { color: var(--gray-700); }

        .feedback-tab.active {
            color: var(--primary);
        }

        .feedback-tab.active::after {
            content: ''; position: absolute; bottom: -2px; left: 0; right: 0;
            height: 3px; background: var(--primary); border-radius: 3px 3px 0 0;
        }

        /* 内容区 */
        .tab-content-panel { display: none; }
        .tab-content-panel.active { display: block; }

        /* 表单卡片 */
        .form-card {
            background: var(--white); border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg); padding: 32px; max-width: 700px;
        }

        .form-header {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid var(--gray-100);
        }

        .form-header-icon {
            width: 48px; height: 48px; border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }

        .form-header-icon.suggestion {
            background: var(--primary-bg); color: var(--primary);
        }

        .form-header-icon.report {
            background: #FEF2F2; color: var(--error);
        }

        .form-header h3 { font-size: 18px; font-weight: 700; color: var(--gray-900); }
        .form-header p { font-size: 13px; color: var(--gray-500); margin-top: 4px; }

        /* 类型标签 */
        .type-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: var(--radius-full);
            font-size: 13px; font-weight: 600; margin-bottom: 20px;
        }

        .type-badge.suggestion { background: #EFF6FF; color: var(--primary); }
        .type-badge.report { background: #FEF2F2; color: var(--error); }

        /* 文件上传 */
        .file-upload-wrapper {
            position: relative; border: 2px dashed var(--gray-300);
            border-radius: var(--radius-md); padding: 32px; text-align: center;
            transition: all var(--transition-fast); cursor: pointer;
        }

        .file-upload-wrapper:hover { border-color: var(--primary); background: var(--primary-bg); }

        .file-upload-wrapper input[type="file"] {
            position: absolute; inset: 0; opacity: 0; cursor: pointer;
        }

        .upload-icon { font-size: 36px; color: var(--gray-400); margin-bottom: 10px; }
        .upload-text { font-size: 14px; color: var(--gray-500); }
        .upload-hint { font-size: 12px; color: var(--gray-400); margin-top: 6px; }

        /* 反馈列表 */
        .feedback-list { display: flex; flex-direction: column; gap: 16px; }

        .feedback-item {
            background: var(--white); border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg); padding: 24px; transition: all var(--transition-normal);
        }

        .feedback-item:hover { box-shadow: var(--shadow-md); border-color: transparent; }

        .feedback-item-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 14px; flex-wrap: wrap; gap: 12px;
        }

        .feedback-item-left { display: flex; align-items: center; gap: 12px; }

        .feedback-type-icon {
            width: 36px; height: 36px; border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
        }

        .feedback-type-icon.suggestion { background: #EFF6FF; color: var(--primary); }
        .feedback-type-icon.report { background: #FEF2F2; color: var(--error); }

        .feedback-item-title {
            font-size: 16px; font-weight: 700; color: var(--gray-900);
        }

        .feedback-item-desc {
            font-size: 14px; color: var(--gray-600); line-height: 1.7;
            margin-bottom: 14px; max-height: 60px; overflow: hidden;
            position: relative;
        }

        .feedback-item-desc.expanded { max-height: none; }

        .feedback-item-footer {
            display: flex; align-items: center; justify-content: space-between;
            font-size: 13px; color: var(--gray-400);
            padding-top: 14px; border-top: 1px solid var(--gray-100);
        }

        .expand-btn {
            color: var(--primary); cursor: pointer; font-weight: 500;
            background: none; border: none; font-size: 13px;
        }
        .expand-btn:hover { text-decoration: underline; }

        /* 状态Badge */
        .badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 20px; font-size: 12px;
            font-weight: 600;
        }

        .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }

        .badge-warning { background: #FEF3C7; color: #D97706; }
        .badge-warning::before { background: #D97706; }

        .badge-info { background: #DBEAFE; color: #2563EB; }
        .badge-info::before { background: #2563EB; }

        .badge-success { background: #D1FAE5; color: #059669; }
        .badge-success::before { background: #059669; }

        /* 提示消息 */
        .alert-message {
            padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 20px;
            font-size: 14px; display: flex; align-items: center; gap: 10px;
        }
        .alert-message.error { background: #FEF2F2; color: var(--error); border: 1px solid #FECACA; }
        .alert-message.success { background: #F0FDF4; color: var(--success); border: 1px solid #BBF7D0; }

        /* 空状态 */
        .empty-state { text-align: center; padding: 60px 20px; color: var(--gray-400); }
        .empty-state i { font-size: 48px; margin-bottom: 16px; display: block; }
        .empty-state p { font-size: 15px; margin-bottom: 16px; }

        /* 移动端适配 */
        @media (max-width: 768px) {
            .user-sidebar { transform: translateX(-100%); }
            .user-sidebar.show { transform: translateX(0); }
            .user-main { margin-left: 0; padding: 20px 16px; }
            .mobile-menu-btn { display: block !important; }
            .feedback-tabs { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .form-card { padding: 24px 20px; }
        }

        .mobile-menu-btn {
            display: none; position: fixed; bottom: 24px; right: 24px;
            width: 56px; height: 56px; background: var(--primary); color: white;
            border-radius: 50%; box-shadow: var(--shadow-lg); z-index: 101;
            font-size: 24px; cursor: pointer; border: none;
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
                <li><a href="profile.php" class="sidebar-link"><i>&#128100;</i> 个人资料</a></li>
                <li><a href="tickets.php" class="sidebar-link"><i>&#128221;</i> 我的工单</a></li>
                <li><a href="feedback.php" class="sidebar-link active"><i>&#128172;</i> 建议举报</a></li>
                <li class="sidebar-divider"></li>
                <li><a href="logout.php" class="sidebar-link" style="color:var(--error);"><i>&#128682;</i> 退出登录</a></li>
            </ul>
        </aside>

        <!-- 主内容区 -->
        <main class="user-main">
            <!-- 页面头部 -->
            <div class="page-header">
                <h1 class="page-title">建议与举报</h1>
                <p class="page-subtitle">我们重视每一条反馈，共同打造更好的服务体验</p>
            </div>

            <!-- 消息提示 -->
            <?php if ($error): ?>
            <div class="alert-message error"><span>&#9888;</span><span><?php echo e($error); ?></span></div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert-message success"><span>&#10003;</span><span><?php echo e($success); ?></span></div>
            <?php endif; ?>

            <!-- Tab切换 -->
            <div class="feedback-tabs">
                <button type="button" class="feedback-tab active" data-tab="submit">提交反馈</button>
                <button type="button" class="feedback-tab" data-tab="history">我的反馈记录</button>
            </div>

            <!-- 提交反馈面板 -->
            <div class="tab-content-panel active" id="panel-submit">
                <div class="form-card">
                    <form method="POST" action="" enctype="multipart/form-data" id="feedbackForm">
                        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                        <input type="hidden" name="action" value="submit">

                        <!-- 表单头 -->
                        <div class="form-header">
                            <div class="form-header-icon suggestion" id="formIcon">
                                <?php echo $type_map['suggestion']['icon']; ?>
                            </div>
                            <div>
                                <h3 id="formTitle">提交建议</h3>
                                <p id="formDesc">您的建议是我们改进的动力</p>
                            </div>
                        </div>

                        <!-- 类型选择 -->
                        <div class="form-group">
                            <label class="form-label">反馈类型</label>
                            <select name="feedback_type" id="feedbackType" class="form-select"
                                    onchange="updateFeedbackType()">
                                <option value="suggestion">功能建议</option>
                                <option value="report">问题举报</option>
                            </select>
                        </div>

                        <div class="type-badge suggestion" id="typeBadge">
                            <?php echo $type_map['suggestion']['icon']; ?> 功能建议
                        </div>

                        <!-- 标题 -->
                        <div class="form-group">
                            <label class="form-label">标题 <span style="color:var(--error)">*</span></label>
                            <input type="text" name="title" class="form-input"
                                   placeholder="简要描述您的建议或问题"
                                   required maxlength="100">
                        </div>

                        <!-- 详细描述 -->
                        <div class="form-group">
                            <label class="form-label">详细描述 <span style="color:var(--error)">*</span></label>
                            <textarea name="description" class="form-textarea"
                                      placeholder="请详细描述您的内容，以便我们更好地理解和处理..."
                                      required minlength="10"></textarea>
                        </div>

                        <!-- 附件上传 -->
                        <div class="form-group">
                            <label class="form-label">附件（可选）</label>
                            <div class="file-upload-wrapper">
                                <input type="file" name="attachment"
                                       accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.zip">
                                <div class="upload-icon">&#128194;</div>
                                <div class="upload-text">点击或拖拽文件到此处上传</div>
                                <div class="upload-hint">支持 JPG、PNG、PDF、DOC 等格式，最大 5MB</div>
                            </div>
                        </div>

                        <!-- 提交按钮 -->
                        <button type="submit" class="btn btn-primary btn-lg" style="width:100%;margin-top:8px;" id="submitBtn">
                            提交建议
                        </button>
                    </form>
                </div>
            </div>

            <!-- 历史记录面板 -->
            <div class="tab-content-panel" id="panel-history">
                <?php if (!empty($my_feedbacks)): ?>
                <div class="feedback-list">
                    <?php foreach ($my_feedbacks as $fb):
                        $type_info = $type_map[$fb['type']] ?? $type_map['suggestion'];
                        $status_info = $status_map[$fb['status']] ?? $status_map['pending'];
                    ?>
                    <div class="feedback-item">
                        <div class="feedback-item-header">
                            <div class="feedback-item-left">
                                <div class="feedback-type-icon <?php echo $fb['type']; ?>">
                                    <?php echo $type_info['icon']; ?>
                                </div>
                                <span class="feedback-item-title"><?php echo e($fb['title']); ?></span>
                            </div>
                            <span class="badge badge-<?php echo $status_info['class']; ?>">
                                <?php echo $status_info['text']; ?>
                            </span>
                        </div>

                        <div class="feedback-item-desc" id="desc-<?php echo e(substr($fb['id'], -8)); ?>">
                            <?php echo e(truncate($fb['description'], 150)); ?>
                        </div>

                        <div class="feedback-item-footer">
                            <span>
                                <?php echo $type_info['text']; ?> ·
                                <?php echo format_date($fb['created_at'], 'Y-m-d H:i'); ?>
                            </span>
                            <?php if (strlen($fb['description'] ?? '') > 150): ?>
                            <button class="expand-btn" onclick="toggleExpand('<?php echo e(substr($fb['id'], -8)); ?>')">
                                展开
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <span>&#128172;</span>
                    <p>暂无反馈记录</p>
                    <button class="btn btn-primary" onclick="switchTab('submit')">立即提交反馈</button>
                </div>
                <?php endif; ?>
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

        // Tab切换
        document.querySelectorAll('.feedback-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                var targetTab = this.getAttribute('data-tab');

                // 更新Tab样式
                document.querySelectorAll('.feedback-tab').forEach(function(t) {
                    t.classList.remove('active');
                });
                this.classList.add('active');

                // 切换面板
                document.querySelectorAll('.tab-content-panel').forEach(function(panel) {
                    panel.classList.remove('active');
                });
                document.getElementById('panel-' + targetTab).classList.add('active');
            });
        });

        // 更新反馈类型显示
        function updateFeedbackType() {
            var type = document.getElementById('feedbackType').value;
            var iconEl = document.getElementById('formIcon');
            var titleEl = document.getElementById('formTitle');
            var descEl = document.getElementById('formDesc');
            var badgeEl = document.getElementById('typeBadge');
            var submitBtn = document.getElementById('submitBtn');

            if (type === 'report') {
                iconEl.className = 'form-header-icon report';
                iconEl.innerHTML = '&#9888;';
                titleEl.textContent = '提交举报';
                descEl.textContent = '发现问题？告诉我们，我们将及时处理';
                badgeEl.className = 'type-badge report';
                badgeEl.innerHTML = '&#9888; 问题举报';
                submitBtn.textContent = '提交举报';
            } else {
                iconEl.className = 'form-header-icon suggestion';
                iconEl.innerHTML = '&#128161;';
                titleEl.textContent = '提交建议';
                descEl.textContent = '您的建议是我们改进的动力';
                badgeEl.className = 'type-badge suggestion';
                badgeEl.innerHTML = '&#128161; 功能建议';
                submitBtn.textContent = '提交建议';
            }
        }

        // 展开描述
        function toggleExpand(id) {
            var el = document.getElementById('desc-' + id);
            el.classList.toggle('expanded');

            var btn = el.parentElement.querySelector('.expand-btn');
            if (el.classList.contains('expanded')) {
                btn.textContent = '收起';
            } else {
                btn.textContent = '展开';
            }
        }

        // 切换到指定Tab
        function switchTab(tabName) {
            document.querySelectorAll('.feedback-tab').forEach(function(t) {
                t.classList.remove('active');
                if (t.getAttribute('data-tab') === tabName) t.classList.add('active');
            });
            document.querySelectorAll('.tab-content-panel').forEach(function(p) {
                p.classList.remove('active');
            });
            document.getElementById('panel-' + tabName).classList.add('active');
        }

        // 表单验证
        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            var title = this.querySelector('[name="title"]').value.trim();
            var description = this.querySelector('[name="description"]').value.trim();

            if (!title || !description) {
                e.preventDefault();
                showToast('请填写完整的表单信息', 'warning');
                return;
            }

            if (description.length < 10) {
                e.preventDefault();
                showToast('详细描述至少需要10个字符', 'warning');
                return;
            }
        });
    </script>
</body>
</html>
