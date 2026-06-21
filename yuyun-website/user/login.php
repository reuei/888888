<?php
/**
 * 语云科技 - 用户登录页面
 * 支持密码登录 + 邮箱验证码登录
 */
session_start();
require_once __DIR__ . '/../core/Functions.php';

// 已登录则跳转到仪表盘
if (is_logged_in()) {
    $redirect = $_GET['redirect'] ?: 'dashboard.php';
    redirect($redirect);
}

$page_title = '用户登录 - ' . (get_config('site_name') ?: '语云科技');
$error = '';
$success = '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'password';

    // 验证CSRF
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = '安全验证失败，请刷新页面重试';
    } else {
        // 密码登录
        if ($action === 'password') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            if (empty($email) || empty($password)) {
                $error = '请填写邮箱和密码';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = '请输入有效的邮箱地址';
            } else {
                // 提交到API处理
                $api_url = '../api/auth.php';
                $post_data = http_build_query([
                    'action' => 'login',
                    'email' => $email,
                    'password' => $password,
                    'remember' => $remember ? 1 : 0,
                    'csrf_token' => csrf_token()
                ]);

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $api_url,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $post_data,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => false,
                    CURLOPT_HTTPHEADER => ['X-Requested-With: XMLHttpRequest']
                ]);
                $response = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($response, true);

                if ($result && $result['code'] === 200) {
                    $redirect_url = $_GET['redirect'] ?: 'dashboard.php';
                    header("Location: $redirect_url");
                    exit;
                } else {
                    $error = $result['message'] ?? '登录失败，请检查账号密码';
                }
            }
        }
        // 验证码登录
        elseif ($action === 'code') {
            $email = trim($_POST['code_email'] ?? '');
            $code = $_POST['verification_code'] ?? '';

            if (empty($email)) {
                $error = '请输入邮箱地址';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = '请输入有效的邮箱地址';
            } elseif (empty($code) || strlen($code) !== 6) {
                $error = '请输入6位验证码';
            } else {
                // 验证码登录提交
                $api_url = '../api/auth.php';
                $post_data = http_build_query([
                    'action' => 'code_login',
                    'email' => $email,
                    'code' => $code,
                    'csrf_token' => csrf_token()
                ]);

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $api_url,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $post_data,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => ['X-Requested-With: XMLHttpRequest']
                ]);
                $response = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($response, true);

                if ($result && $result['code'] === 200) {
                    $redirect_url = $_GET['redirect'] ?: 'dashboard.php';
                    header("Location: $redirect_url");
                    exit;
                } else {
                    $error = $result['message'] ?? '验证码错误或已过期';
                }
            }
        }

        // 发送验证码
        if (isset($_POST['send_code'])) {
            $email = trim($_POST['send_code_email'] ?? '');

            if (empty($email)) {
                $error = '请先输入邮箱地址';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = '请输入有效的邮箱地址';
            } else {
                // 调用发送验证码接口
                $api_url = '../api/auth.php';
                $post_data = http_build_query([
                    'action' => 'send_code',
                    'email' => $email,
                    'csrf_token' => csrf_token()
                ]);

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $api_url,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $post_data,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => ['X-Requested-With: XMLHttpRequest']
                ]);
                $response = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($response, true);

                if ($result && $result['code'] === 200) {
                    $success = '验证码已发送，请注意查收';
                } else {
                    $error = $result['message'] ?? '发送失败，请稍后重试';
                }
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
        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f7ff 0%, #e8f4fd 50%, #fff5eb 100%);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .login-page::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0,102,204,0.08) 0%, transparent 70%);
            top: -200px;
            right: -200px;
            border-radius: 50%;
        }

        .login-page::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,107,0,0.06) 0%, transparent 70%);
            bottom: -100px;
            left: -100px;
            border-radius: 50%;
        }

        .login-card {
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 460px;
            padding: 48px 40px;
            position: relative;
            z-index: 1;
        }

        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .login-logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
            color: white;
        }

        .login-title {
            font-size: 26px;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 8px;
        }

        .login-subtitle {
            font-size: 15px;
            color: var(--gray-500);
        }

        /* Tab切换 */
        .login-tabs {
            display: flex;
            background: var(--gray-100);
            border-radius: var(--radius-md);
            padding: 4px;
            margin-bottom: 28px;
        }

        .login-tab {
            flex: 1;
            padding: 12px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-500);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all var(--transition-fast);
            border: none;
            background: transparent;
        }

        .login-tab.active {
            background: var(--white);
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        .login-tab:hover:not(.active) {
            color: var(--gray-700);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* 表单增强 */
        .form-input-icon {
            position: relative;
        }

        .form-input-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 16px;
        }

        .form-input-icon .form-input {
            padding-left: 44px;
        }

        /* 验证码输入框组 */
        .code-input-group {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .code-input-group .form-input {
            flex: 1;
        }

        .btn-send-code {
            padding: 12px 18px;
            background: var(--gray-100);
            color: var(--gray-700);
            font-size: 13px;
            font-weight: 600;
            border-radius: var(--radius-md);
            cursor: pointer;
            white-space: nowrap;
            transition: all var(--transition-fast);
            border: 2px solid var(--gray-200);
            min-width: 120px;
        }

        .btn-send-code:hover:not(:disabled) {
            background: var(--primary-bg);
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn-send-code:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* 记住我 & 忘记密码 */
        .login-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: var(--gray-600);
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        .forgot-link {
            color: var(--primary);
            font-weight: 500;
        }

        .forgot-link:hover {
            color: var(--primary-dark);
        }

        /* 登录按钮 */
        .btn-login {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: 700;
        }

        /* 分隔线 */
        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 24px 0;
            color: var(--gray-400);
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--gray-200);
        }

        /* 底部链接 */
        .login-footer {
            text-align: center;
            font-size: 14px;
            color: var(--gray-500);
        }

        .login-footer a {
            color: var(--primary);
            font-weight: 600;
        }

        /* 错误/成功提示 */
        .alert-message {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
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

        /* 移动端适配 */
        @media (max-width: 520px) {
            .login-card {
                padding: 32px 24px;
                border-radius: var(--radius-lg);
            }

            .login-title {
                font-size: 22px;
            }

            .code-input-group {
                flex-direction: column;
            }

            .btn-send-code {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <!-- 头部 -->
            <div class="login-header">
                <div class="login-logo">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                        <path d="M2 17l10 5 10-5"/>
                        <path d="M2 12l10 5 10-5"/>
                    </svg>
                </div>
                <h1 class="login-title">欢迎回来</h1>
                <p class="login-subtitle">登录您的账户以继续</p>
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

            <!-- 登录表单 -->
            <form id="loginForm" method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">

                <!-- Tab切换 -->
                <div class="login-tabs">
                    <button type="button" class="login-tab active" data-tab="password">密码登录</button>
                    <button type="button" class="login-tab" data-tab="code">验证码登录</button>
                </div>

                <!-- 密码登录 -->
                <div class="tab-content active" id="tab-password">
                    <div class="form-group">
                        <label class="form-label">邮箱地址</label>
                        <div class="form-input-icon">
                            <i>&#9993;</i>
                            <input type="email" name="email" class="form-input"
                                   placeholder="请输入邮箱地址" required
                                   value="<?php echo e($_POST['email'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">密码</label>
                        <div class="form-input-icon">
                            <i>&#128274;</i>
                            <input type="password" name="password" class="form-input"
                                   placeholder="请输入密码" required minlength="6">
                        </div>
                    </div>

                    <div class="login-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="remember" value="1">
                            <span>记住我</span>
                        </label>
                        <a href="#" class="forgot-link" onclick="showAlert('请联系管理员重置密码'); return false;">忘记密码？</a>
                    </div>
                </div>

                <!-- 验证码登录 -->
                <div class="tab-content" id="tab-code">
                    <div class="form-group">
                        <label class="form-label">邮箱地址</label>
                        <div class="form-input-icon">
                            <i>&#9993;</i>
                            <input type="email" name="code_email" id="codeEmail" class="form-input"
                                   placeholder="请输入邮箱地址" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">验证码</label>
                        <div class="code-input-group">
                            <input type="text" name="verification_code" class="form-input"
                                   placeholder="请输入6位验证码" maxlength="6"
                                   pattern="[0-9]{6}" inputmode="numeric">
                            <button type="button" class="btn-send-code" id="sendCodeBtn"
                                    onclick="sendVerificationCode()">发送验证码</button>
                        </div>
                        <p class="form-hint">验证码有效期为5分钟</p>
                    </div>
                </div>

                <!-- 登录按钮 -->
                <button type="submit" name="action" value="password" class="btn btn-primary btn-login" id="submitBtn">
                    登 录
                </button>
            </form>

            <!-- 底部链接 -->
            <div class="divider">或</div>
            <div class="login-footer">
                还没有账号？<a href="register.php<?php echo $_GET['redirect'] ? '?redirect='.urlencode($_GET['redirect']) : ''; ?>">立即注册</a>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/modal.js"></script>
    <script>
        // Tab切换功能
        document.querySelectorAll('.login-tab').forEach(function(tab) {
            tab.addEventListener('click', function() {
                var targetTab = this.getAttribute('data-tab');

                // 切换Tab样式
                document.querySelectorAll('.login-tab').forEach(function(t) {
                    t.classList.remove('active');
                });
                this.classList.add('active');

                // 切换内容区
                document.querySelectorAll('.tab-content').forEach(function(content) {
                    content.classList.remove('active');
                });
                document.getElementById('tab-' + targetTab).classList.add('active');

                // 更新提交按钮的action值
                document.getElementById('submitBtn').value = targetTab;
            });
        });

        // 发送验证码
        function sendVerificationCode() {
            var emailInput = document.getElementById('codeEmail');
            var email = emailInput.value.trim();

            if (!email) {
                showToast('请先输入邮箱地址', 'warning');
                emailInput.focus();
                return;
            }

            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showToast('请输入有效的邮箱地址', 'warning');
                emailInput.focus();
                return;
            }

            // 创建隐藏表单发送请求
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            form.style.display = 'none';

            var fields = {
                'action': 'code',
                'send_code': '1',
                'send_code_email': email,
                'csrf_token': document.querySelector('[name="csrf_token"]').value
            };

            for (var key in fields) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = fields[key];
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
        }

        // 表单前端验证
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            var activeTab = document.querySelector('.login-tab.active').getAttribute('data-tab');

            if (activeTab === 'password') {
                var email = this.querySelector('[name="email"]').value.trim();
                var password = this.querySelector('[name="password"]').value;

                if (!email || !password) {
                    e.preventDefault();
                    showToast('请填写完整的登录信息', 'warning');
                    return;
                }

                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    showToast('请输入有效的邮箱地址', 'warning');
                    return;
                }
            } else if (activeTab === 'code') {
                var codeEmail = this.querySelector('[name="code_email"]').value.trim();
                var code = this.querySelector('[name="verification_code"]').value;

                if (!codeEmail || !code) {
                    e.preventDefault();
                    showToast('请填写完整信息', 'warning');
                    return;
                }

                if (code.length !== 6 || !/^\d{6}$/.test(code)) {
                    e.preventDefault();
                    showToast('请输入6位数字验证码', 'warning');
                    return;
                }
            }
        });

        // 如果URL有redirect参数，保持传递
        <?php if (!empty($_GET['redirect'])): ?>
        document.getElementById('loginForm').addEventListener('submit', function() {
            var redirect = '<?php echo urlencode($_GET["redirect"]); ?>';
            this.action = '?redirect=' + redirect;
        });
        <?php endif; ?>
    </script>
</body>
</html>
