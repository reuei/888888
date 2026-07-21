<?php
/**
 * 用户登录 v8.0.0
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

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h2 class="auth-card__title">登录</h2>
        <p class="auth-card__desc">中央纪委国家监委网站</p>

        <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form" id="loginForm" novalidate autocomplete="off">
            <?php echo csrf_field(); ?>

            <div class="auth-form__group">
                <label class="auth-form__label" for="username">用户名</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-wrapper__icon"></i>
                    <input type="text" id="username" name="username" class="form-input" required placeholder="请输入用户名" autocomplete="username">
                </div>
                <span class="form-feedback" id="usernameFeedback"></span>
            </div>

            <div class="auth-form__group">
                <label class="auth-form__label" for="password">密码</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-wrapper__icon"></i>
                    <input type="password" id="password" name="password" class="form-input" required placeholder="请输入密码" autocomplete="current-password">
                    <button type="button" class="password-toggle" data-target="password" tabindex="-1">
                        <i class="far fa-eye"></i>
                    </button>
                </div>
                <span class="form-feedback" id="passwordFeedback"></span>
            </div>

            <div class="form-check" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;font-size:13px;">
                <label style="display:flex;align-items:center;gap:6px;color:#6b7280;cursor:pointer;">
                    <input type="checkbox" name="remember" value="1" style="accent-color:#c41230;"> 记住登录
                </label>
                <a href="#" style="color:#c41230;font-size:13px;font-weight:500;">忘记密码？</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block" id="submitBtn">登 录</button>
        </form>

        <div class="auth-card__footer">
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
            u.classList.add('form-input--error'); u.classList.remove('form-input--success');
            uf.textContent = '请输入用户名'; uf.className = 'form-feedback form-feedback--error';
            return false;
        }
        if (v.length < 2) {
            u.classList.add('form-input--error'); u.classList.remove('form-input--success');
            uf.textContent = '用户名至少2个字符'; uf.className = 'form-feedback form-feedback--error';
            return false;
        }
        u.classList.remove('form-input--error'); u.classList.add('form-input--success');
        uf.textContent = ''; uf.className = 'form-feedback form-feedback--success';
        return true;
    }

    function vPass() {
        var v = p.value;
        if (!v) {
            p.classList.add('form-input--error'); p.classList.remove('form-input--success');
            pf.textContent = '请输入密码'; pf.className = 'form-feedback form-feedback--error';
            return false;
        }
        if (v.length < 6) {
            p.classList.add('form-input--error'); p.classList.remove('form-input--success');
            pf.textContent = '密码至少6个字符'; pf.className = 'form-feedback form-feedback--error';
            return false;
        }
        p.classList.remove('form-input--error'); p.classList.add('form-input--success');
        pf.textContent = ''; pf.className = 'form-feedback form-feedback--success';
        return true;
    }

    function validateAll() { return vUser() && vPass(); }

    u.addEventListener('input', vUser);
    p.addEventListener('input', vPass);

    document.querySelectorAll('.password-toggle').forEach(function(b) {
        b.addEventListener('click', function() {
            var t = document.getElementById(this.dataset.target);
            var icon = this.querySelector('i');
            if (t.type === 'password') { t.type = 'text'; icon.className = 'far fa-eye-slash'; }
            else { t.type = 'password'; icon.className = 'far fa-eye'; }
        });
    });

    form.addEventListener('submit', function(e) {
        if (!validateAll()) { e.preventDefault(); return; }
        btn.disabled = true; btn.textContent = '登录中...';
    });

    <?php if ($error): ?>
    if (typeof showToast === 'function') { showToast('<?php echo addslashes($error); ?>', 'error'); }
    <?php endif; ?>
})();
</script>

<?php include TEMPLATES_PATH . 'footer.php'; ?>