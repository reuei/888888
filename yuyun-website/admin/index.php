<?php
/**
 * 语云科技 - 后台管理系统登录页
 * 独立的后台登录界面，深色专业风格
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));

// 如果已登录，直接跳转到仪表盘
if (is_admin()) {
    header('Location: dashboard.php');
    exit;
}

require_once YUYUN_ROOT . '/core/Functions.php';

$error = '';
$success = '';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once YUYUN_ROOT . '/core/Auth.php';
    $auth = new Auth();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = '请填写用户名和密码';
    } else {
        $result = $auth->adminLogin($username, $password);

        if ($result['success']) {
            // 登录成功，跳转到仪表盘
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台管理 - 语云科技</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0D1117 0%, #1a1a2e 50%, #0f3460 100%);
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* 背景装饰 */
        .login-bg-decoration {
            position: absolute;
            border-radius: 50%;
            opacity: 0.4;
            pointer-events: none;
        }

        .login-bg-1 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0, 102, 204, 0.12), transparent);
            top: -200px;
            right: -150px;
        }

        .login-bg-2 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(255, 107, 0, 0.08), transparent);
            bottom: -150px;
            left: -100px;
        }

        .login-bg-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(111, 66, 193, 0.06), transparent);
            top: 40%;
            left: 10%;
        }

        .login-container {
            background: rgba(22, 27, 34, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid #30363d;
            border-radius: 20px;
            padding: 48px 42px;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
            box-shadow:
                0 24px 80px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.03) inset;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 36px;
        }

        .logo-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #0066CC 0%, #FF6B00 100%);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(0, 102, 204, 0.35);
        }

        .login-logo h1 {
            font-size: 26px;
            font-weight: 700;
            color: #e6edf3;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .login-logo p {
            font-size: 14px;
            color: #6e7681;
        }

        .login-form .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6e7681;
            font-size: 15px;
            transition: color 0.3s ease;
            z-index: 1;
        }

        .input-wrapper .form-control {
            padding-left: 46px;
            padding: 14px 16px 14px 46px;
            font-size: 15px;
            background: #0D1117;
            border: 1px solid #30363d;
            border-radius: 10px;
            color: #e6edf3;
            width: 100%;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-wrapper .form-control:focus {
            border-color: #0066CC;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.15);
        }

        .input-wrapper .form-control:focus + i,
        .input-wrapper .form-control:focus ~ i {
            color: #0066CC;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6e7681;
            cursor: pointer;
            font-size: 15px;
            padding: 4px;
            z-index: 1;
        }

        .password-toggle:hover {
            color: #8b949e;
        }

        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            font-size: 13px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #8b949e;
            cursor: pointer;
        }

        .remember-label input[type="checkbox"] {
            accent-color: #0066CC;
            width: 16px;
            height: 16px;
        }

        .forgot-link {
            color: #0066CC;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            font-size: 16px;
            font-weight: 600;
            background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 102, 204, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* 错误和成功提示 */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shakeAlert 0.4s ease;
        }

        @keyframes shakeAlert {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }

        .alert-error {
            background: rgba(220, 53, 69, 0.12);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #ff6b6b;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.12);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #51cf66;
        }

        .alert i {
            font-size: 16px;
        }

        .login-footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #21262d;
            font-size: 13px;
            color: #6e7681;
        }

        .login-footer a {
            color: #0066CC;
        }

        /* 加载动画 */
        .loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(13, 17, 23, 0.85);
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .loading-overlay.show {
            opacity: 1;
            pointer-events: auto;
        }

        .spinner {
            width: 44px;
            height: 44px;
            border: 3px solid #30363d;
            border-top-color: #0066CC;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-bottom: 12px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 16px;
                padding: 36px 28px;
            }
        }
    </style>
</head>
<body>
    <div class="login-page">
        <!-- 背景装饰 -->
        <div class="login-bg-decoration login-bg-1"></div>
        <div class="login-bg-decoration login-bg-2"></div>
        <div class="login-bg-decoration login-bg-3"></div>

        <div class="login-container">
            <!-- Logo区域 -->
            <div class="login-logo">
                <div class="logo-icon">
                    <i class="fas fa-cloud"></i>
                </div>
                <h1>语云科技</h1>
                <p>企业官网管理后台</p>
            </div>

            <!-- 登录表单 -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo e($error); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?php echo e($success); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="login-form" id="loginForm">
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text"
                               name="username"
                               class="form-control"
                               placeholder="请输入管理员账号 / 邮箱"
                               value="<?php echo e($_POST['username'] ?? ''); ?>"
                               required
                               autofocus
                               autocomplete="username">
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="password"
                               name="password"
                               id="passwordInput"
                               class="form-control"
                               placeholder="请输入密码"
                               required
                               autocomplete="current-password">
                        <i class="fas fa-lock"></i>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="remember-row">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" value="1">
                        <span>记住登录状态</span>
                    </label>
                    <a href="#" class="forgot-link">忘记密码？</a>
                </div>

                <button type="submit" class="btn-login" id="submitBtn">
                    <i class="fas fa-sign-in-alt"></i> 登录后台
                </button>
            </form>

            <!-- 底部信息 -->
            <div class="login-footer">
                <p>&copy; <?php echo date('Y'); ?> 语云科技有限公司 版权所有</p>
                <p style="margin-top: 6px;">
                    <a href="<?php echo get_config('site_url') ?: '/'; ?>">返回前台</a>
                </p>
            </div>

            <!-- 加载遮罩 -->
            <div class="loading-overlay" id="loadingOverlay">
                <div class="spinner"></div>
                <span style="color: #8b949e;">正在验证身份...</span>
            </div>
        </div>
    </div>

    <script>
        // 密码显示/隐藏切换
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon = document.getElementById('toggleIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // 表单提交处理
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const overlay = document.getElementById('loadingOverlay');
            const btn = document.getElementById('submitBtn');

            overlay.classList.add('show');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 验证中...';

            // 表单正常提交（非AJAX）
            setTimeout(() => {
                this.submit();
            }, 500);
        });

        // 回车键聚焦
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && document.activeElement.tagName !== 'BUTTON') {
                const form = document.getElementById('loginForm');
                form.dispatchEvent(new Event('submit'));
            }
        });
    </script>
</body>
</html>
