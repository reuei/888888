<?php
/**
 * 用户登录 v4.0.0
 * 中央纪委国家监委网站 CMS 系统
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';

$page_title = '登录';
$error = '';

if (is_logged_in()) redirect(site_url());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify(post(CSRF_TOKEN_NAME))) {
        $error = '表单验证失败，请刷新后重试';
    } else {
        $username = post('username');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if (empty($username) || empty($password)) {
            $error = '请输入用户名和密码';
        } else {
            $result = login_user($username, $password, $remember);
            if ($result['success']) {
                $redirect = $_SESSION['redirect_after_login'] ?? site_url();
                unset($_SESSION['redirect_after_login']);
                redirect($redirect);
            }
            $error = $result['message'];
        }
    }
}

include TEMPLATES_PATH . 'header.php';
?>

<style>
.auth-page {
    min-height: calc(100vh - 280px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: #f5f6fa;
}
.auth-card {
    width: 100%;
    max-width: 420px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
    padding: 40px 36px;
}
.auth-icon-square {
    width: 56px;
    height: 56px;
    background: #c62828;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}
.auth-icon-square i {
    color: #fff;
    font-size: 24px;
}
.auth-card h2 {
    text-align: center;
    font-size: 22px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 6px;
}
.auth-subtitle {
    text-align: center;
    font-size: 13px;
    color: #999;
    margin: 0 0 28px;
}
.alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 13px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.alert-error::before {
    content: '\f071';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    flex-shrink: 0;
}
.auth-form .form-group {
    margin-bottom: 18px;
}
.auth-form label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
}
.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.input-wrapper .input-icon {
    position: absolute;
    left: 14px;
    color: #9ca3af;
    font-size: 15px;
    pointer-events: none;
    z-index: 1;
    transition: color 0.2s;
}
.input-wrapper input {
    width: 100%;
    height: 46px;
    padding: 0 14px 0 40px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    color: #1a1a1a;
    background: #fff;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.input-wrapper input:focus {
    border-color: #c62828;
    box-shadow: 0 0 0 3px rgba(198,40,40,0.1);
}
.input-wrapper input.valid {
    border-color: #16a34a;
}
.input-wrapper input.valid:focus {
    box-shadow: 0 0 0 3px rgba(22,163,74,0.1);
}
.input-wrapper input.invalid {
    border-color: #dc2626;
}
.input-wrapper input.invalid:focus {
    box-shadow: 0 0 0 3px rgba(220,38,38,0.1);
}
.input-wrapper input.valid ~ .input-icon { color: #16a34a; }
.input-wrapper input.invalid ~ .input-icon { color: #dc2626; }
.toggle-password {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 4px 6px;
    font-size: 15px;
    line-height: 1;
    transition: color 0.2s;
}
.toggle-password:hover { color: #6b7280; }
.form-feedback {
    display: block;
    font-size: 12px;
    margin-top: 4px;
    min-height: 18px;
}
.form-feedback.error { color: #dc2626; }
.form-feedback.success { color: #16a34a; }
.form-check {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
    font-size: 13px;
}
.checkbox-label {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #6b7280;
    cursor: pointer;
    user-select: none;
}
.checkbox-label input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #c62828;
    cursor: pointer;
}
.forgot-link {
    color: #c62828;
    text-decoration: none;
    font-weight: 500;
    transition: opacity 0.2s;
}
.forgot-link:hover { opacity: 0.8; }
.btn-block {
    width: 100%;
    height: 46px;
    background: #c62828;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
    letter-spacing: 2px;
}
.btn-block:hover { background: #b71c1c; }
.btn-block:active { transform: scale(0.98); }
.btn-block:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.auth-footer {
    text-align: center;
    margin-top: 24px;
    font-size: 13px;
    color: #9ca3af;
}
.auth-footer a {
    color: #c62828;
    text-decoration: none;
    font-weight: 600;
    transition: opacity 0.2s;
}
.auth-footer a:hover { opacity: 0.8; }
</style>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-icon-square">
            <i class="fas fa-shield-haltered"></i>
        </div>
        <h2>登录</h2>
        <p class="auth-subtitle">中央纪委国家监委网站后台管理系统</p>

        <?php if ($error): ?>
        <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form" id="loginForm" novalidate autocomplete="off">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="username">用户名</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" id="username" name="username" required placeholder="请输入用户名" autocomplete="username">
                </div>
                <span class="form-feedback" id="usernameFeedback"></span>
            </div>

            <div class="form-group">
                <label for="password">密码</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" required placeholder="请输入密码" autocomplete="current-password">
                    <button type="button" class="toggle-password" data-target="password" tabindex="-1">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
                <span class="form-feedback" id="passwordFeedback"></span>
            </div>

            <div class="form-check">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember" value="1"> 记住登录
                </label>
                <a href="#" class="forgot-link">忘记密码？</a>
            </div>

            <button type="submit" class="btn-block" id="submitBtn">登 录</button>
        </form>

        <div class="auth-footer">
            还没有账号？<a href="<?php echo site_url('register.php'); ?>">立即注册</a>
        </div>
    </div>
</div>

<script>
(function(){
    var u = document.getElementById('username'),
        p = document.getElementById('password'),
        uf = document.getElementById('usernameFeedback'),
        pf = document.getElementById('passwordFeedback'),
        btn = document.getElementById('submitBtn'),
        form = document.getElementById('loginForm');

    function vUser() {
        var v = u.value.trim();
        if (!v) {
            u.classList.add('invalid'); u.classList.remove('valid');
            uf.textContent = '请输入用户名'; uf.className = 'form-feedback error';
            return false;
        }
        if (v.length < 2) {
            u.classList.add('invalid'); u.classList.remove('valid');
            uf.textContent = '用户名至少2个字符'; uf.className = 'form-feedback error';
            return false;
        }
        u.classList.remove('invalid'); u.classList.add('valid');
        uf.textContent = ''; uf.className = 'form-feedback success';
        return true;
    }

    function vPass() {
        var v = p.value;
        if (!v) {
            p.classList.add('invalid'); p.classList.remove('valid');
            pf.textContent = '请输入密码'; pf.className = 'form-feedback error';
            return false;
        }
        if (v.length < 6) {
            p.classList.add('invalid'); p.classList.remove('valid');
            pf.textContent = '密码至少6个字符'; pf.className = 'form-feedback error';
            return false;
        }
        p.classList.remove('invalid'); p.classList.add('valid');
        pf.textContent = ''; pf.className = 'form-feedback success';
        return true;
    }

    function validateAll() {
        var vu = vUser(), vp = vPass();
        return vu && vp;
    }

    u.addEventListener('input', vUser);
    p.addEventListener('input', vPass);

    // 切换密码可见性
    document.querySelectorAll('.toggle-password').forEach(function(b) {
        b.addEventListener('click', function() {
            var t = document.getElementById(this.dataset.target);
            var icon = this.querySelector('i');
            if (t.type === 'password') {
                t.type = 'text';
                icon.className = 'far fa-eye-slash';
            } else {
                t.type = 'password';
                icon.className = 'far fa-eye';
            }
        });
    });

    // 表单提交
    form.addEventListener('submit', function(e) {
        if (!validateAll()) {
            e.preventDefault();
            return;
        }
        btn.disabled = true;
        btn.textContent = '登录中...';
    });

    // 如果页面通过错误消息返回，显示 toast
    <?php if ($error): ?>
    if (typeof showToast === 'function') {
        showToast('<?php echo addslashes($error); ?>', 'error');
    }
    <?php endif; ?>
})();
</script>

<?php include TEMPLATES_PATH . 'footer.php'; ?>