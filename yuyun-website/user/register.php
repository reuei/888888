<?php
/**
 * 语云科技 - 用户注册页面
 */
session_start();
require_once __DIR__ . '/../core/Functions.php';

// 已登录则跳转到仪表盘
if (is_logged_in()) {
    redirect('dashboard.php');
}

$page_title = '用户注册 - ' . (get_config('site_name') ?: '语云科技');
$error = '';
$success = '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 验证CSRF
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = '安全验证失败，请刷新页面重试';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // 前端验证（双重保障）
        if (empty($username) || empty($email) || empty($password)) {
            $error = '请填写所有必填字段';
        } elseif (mb_strlen($username) < 2 || mb_strlen($username) > 20) {
            $error = '用户名长度应为2-20个字符';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = '请输入有效的邮箱地址';
        } elseif (strlen($password) < 6) {
            $error = '密码长度至少为6位';
        } elseif ($password !== $confirm_password) {
            $error = '两次输入的密码不一致';
        } else {
            // 提交到API处理
            $api_url = '../api/auth.php';
            $post_data = http_build_query([
                'action' => 'register',
                'username' => $username,
                'email' => $email,
                'password' => $password,
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
                // 注册成功，跳转到登录页或直接登录
                $success = '注册成功！正在跳转到登录页...';
                echo '<script>
                    setTimeout(function() {
                        window.location.href = "login.php' . ($_GET['redirect'] ? '?redirect=' . urlencode($_GET['redirect']) : '') . '";
                    }, 1500);
                </script>';
            } else {
                $error = $result['message'] ?? '注册失败，请稍后重试';
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
        .register-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f7ff 0%, #e8f4fd 50%, #fff5eb 100%);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .register-page::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0,102,204,0.08) 0%, transparent 70%);
            top: -200px;
            right: -200px;
            border-radius: 50%;
        }

        .register-page::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,107,0,0.06) 0%, transparent 70%);
            bottom: -100px;
            left: -100px;
            border-radius: 50%;
        }

        .register-card {
            background: var(--white);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 480px;
            padding: 48px 40px;
            position: relative;
            z-index: 1;
        }

        .register-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .register-logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--accent), #FF8C00);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
            color: white;
        }

        .register-title {
            font-size: 26px;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 8px;
        }

        .register-subtitle {
            font-size: 15px;
            color: var(--gray-500);
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

        /* 密码强度指示器 */
        .password-strength {
            display: flex;
            gap: 4px;
            margin-top: 8px;
        }

        .strength-bar {
            flex: 1;
            height: 4px;
            background: var(--gray-200);
            border-radius: 2px;
            transition: all var(--transition-fast);
        }

        .strength-bar.weak { background: var(--error); }
        .strength-bar.medium { background: var(--warning); }
        .strength-bar.strong { background: var(--success); }

        .strength-text {
            font-size: 12px;
            margin-top: 4px;
            color: var(--gray-500);
        }

        /* 注册按钮 */
        .btn-register {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            font-weight: 700;
            margin-top: 8px;
        }

        /* 协议同意 */
        .agreement-wrapper {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 24px;
            font-size: 13px;
            color: var(--gray-500);
            line-height: 1.5;
        }

        .agreement-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            margin-top: 2px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .agreement-wrapper a {
            color: var(--primary);
        }

        /* 底部链接 */
        .register-footer {
            text-align: center;
            font-size: 14px;
            color: var(--gray-500);
            margin-top: 28px;
        }

        .register-footer a {
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
            .register-card {
                padding: 32px 24px;
                border-radius: var(--radius-lg);
            }

            .register-title {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="register-page">
        <div class="register-card">
            <!-- 头部 -->
            <div class="register-header">
                <div class="register-logo">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                </div>
                <h1 class="register-title">创建账户</h1>
                <p class="register-subtitle">注册新账号以享受完整服务</p>
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

            <!-- 注册表单 -->
            <form id="registerForm" method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">

                <div class="form-group">
                    <label class="form-label">用户名 <span style="color:var(--error)">*</span></label>
                    <div class="form-input-icon">
                        <i>&#128100;</i>
                        <input type="text" name="username" id="username" class="form-input"
                               placeholder="请输入用户名(2-20个字符)" required
                               minlength="2" maxlength="20"
                               value="<?php echo e($_POST['username'] ?? ''); ?>"
                               pattern="^[a-zA-Z0-9_\u4e00-\u9fa5]+$">
                    </div>
                    <p class="form-hint">支持中文、字母、数字和下划线</p>
                </div>

                <div class="form-group">
                    <label class="form-label">邮箱地址 <span style="color:var(--error)">*</span></label>
                    <div class="form-input-icon">
                        <i>&#9993;</i>
                        <input type="email" name="email" id="regEmail" class="form-input"
                               placeholder="请输入邮箱地址" required
                               value="<?php echo e($_POST['email'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">密码 <span style="color:var(--error)">*</span></label>
                    <div class="form-input-icon">
                        <i>&#128274;</i>
                        <input type="password" name="password" id="regPassword" class="form-input"
                               placeholder="请输入密码(至少6位)" required
                               minlength="6" oninput="checkPasswordStrength(this.value)">
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                        <div class="strength-bar"></div>
                    </div>
                    <p class="strength-text" id="strengthText"></p>
                </div>

                <div class="form-group">
                    <label class="form-label">确认密码 <span style="color:var(--error)">*</span></label>
                    <div class="form-input-icon">
                        <i>&#128274;</i>
                        <input type="password" name="confirm_password" id="confirmPassword" class="form-input"
                               placeholder="请再次输入密码" required
                               minlength="6" oninput="checkPasswordMatch()">
                    </div>
                    <p class="form-error" id="matchError" style="display:none;"></p>
                </div>

                <!-- 协议 -->
                <div class="agreement-wrapper">
                    <input type="checkbox" name="agree" id="agreeTerms" required>
                    <label for="agreeTerms">
                        我已阅读并同意 <a href="#">《用户服务协议》</a> 和 <a href="#">《隐私政策》</a>
                    </label>
                </div>

                <!-- 注册按钮 -->
                <button type="submit" class="btn btn-accent btn-register" id="submitBtn">
                    注 册
                </button>
            </form>

            <!-- 底部链接 -->
            <div class="register-footer">
                已有账号？<a href="login.php<?php echo $_GET['redirect'] ? '?redirect='.urlencode($_GET['redirect']) : ''; ?>">立即登录</a>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/modal.js"></script>
    <script>
        // 密码强度检测
        function checkPasswordStrength(password) {
            var bars = document.querySelectorAll('#passwordStrength .strength-bar');
            var text = document.getElementById('strengthText');

            // 重置
            bars.forEach(function(bar) {
                bar.className = 'strength-bar';
            });

            if (!password) {
                text.textContent = '';
                return;
            }

            var strength = 0;

            // 长度检查
            if (password.length >= 6) strength++;
            if (password.length >= 10) strength++;

            // 复杂度检查
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            // 映射到4级
            var level = Math.min(4, Math.ceil(strength / 1.5));
            var levels = ['weak', 'medium', 'strong', 'strong'];
            var texts = ['弱', '一般', '强', '非常强'];

            for (var i = 0; i < level; i++) {
                bars[i].className = 'strength-bar ' + levels[level - 1];
            }

            text.textContent = '密码强度：' + texts[level - 1];
            text.style.color = level === 1 ? 'var(--error)' :
                              level === 2 ? 'var(--warning)' : 'var(--success)';
        }

        // 密码匹配检查
        function checkPasswordMatch() {
            var password = document.getElementById('regPassword').value;
            var confirm = document.getElementById('confirmPassword').value;
            var errorEl = document.getElementById('matchError');

            if (confirm && password !== confirm) {
                errorEl.textContent = '两次输入的密码不一致';
                errorEl.style.display = 'block';
            } else {
                errorEl.style.display = 'none';
            }
        }

        // 表单验证
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            var username = document.getElementById('username').value.trim();
            var email = document.getElementById('regEmail').value.trim();
            var password = document.getElementById('regPassword').value;
            var confirm = document.getElementById('confirmPassword').value;
            var agree = document.getElementById('agreeTerms').checked;

            // 用户名验证
            if (username.length < 2 || username.length > 20) {
                e.preventDefault();
                showToast('用户名长度应为2-20个字符', 'warning');
                return;
            }

            // 邮箱验证
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                showToast('请输入有效的邮箱地址', 'warning');
                return;
            }

            // 密码验证
            if (password.length < 6) {
                e.preventDefault();
                showToast('密码长度至少为6位', 'warning');
                return;
            }

            // 确认密码
            if (password !== confirm) {
                e.preventDefault();
                showToast('两次输入的密码不一致', 'warning');
                return;
            }

            // 同意协议
            if (!agree) {
                e.preventDefault();
                showToast('请先阅读并同意服务协议', 'warning');
                return;
            }
        });
    </script>
</body>
</html>
