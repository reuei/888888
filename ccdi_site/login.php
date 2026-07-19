<?php
/**
 * 用户登录页面
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';

$page_title = '用户登录';
$error = '';
$username = '';

if (is_logged_in()) {
    redirect(site_url());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify(post(CSRF_TOKEN_NAME))) {
        $error = '表单验证失败，请刷新页面重试';
    } else {
        $username = post('username');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        $result = login_user($username, $password, $remember);
        if ($result['success']) {
            $redirect = $_SESSION['redirect_after_login'] ?? site_url();
            unset($_SESSION['redirect_after_login']);
            redirect($redirect);
        } else {
            $error = $result['message'];
        }
    }
}

include TEMPLATES_PATH . 'header.php';
?>

<div class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">
                    <i class="fas fa-shield-haltered"></i>
                </div>
                <h2>用户登录</h2>
                <p>登录中央纪委国家监委网站</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="post" class="auth-form" id="loginForm" novalidate>
                <?php echo csrf_field(); ?>
                
                <div class="form-group">
                    <label for="username">用户名</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required placeholder="请输入用户名" autocomplete="username">
                    </div>
                    <span class="form-feedback" id="usernameFeedback"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">密码</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" required placeholder="请输入密码" autocomplete="current-password">
                        <button type="button" class="toggle-password" data-target="password"><i class="far fa-eye"></i></button>
                    </div>
                    <span class="form-feedback" id="passwordFeedback"></span>
                </div>
                
                <div class="form-group form-check">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" value="1"> 记住登录
                    </label>
                    <a href="#" class="forgot-link">忘记密码？</a>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">登 录</button>
            </form>
            
            <div class="auth-footer">
                <p>还没有账号？<a href="<?php echo site_url('register.php'); ?>">立即注册</a></p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('loginForm');
    var username = document.getElementById('username');
    var password = document.getElementById('password');
    var usernameFb = document.getElementById('usernameFeedback');
    var passwordFb = document.getElementById('passwordFeedback');
    
    function validateUsername() {
        var val = username.value.trim();
        if (val === '') {
            username.classList.add('invalid');
            username.classList.remove('valid');
            usernameFb.textContent = '请输入用户名';
            usernameFb.className = 'form-feedback error';
            return false;
        }
        if (val.length < 3) {
            username.classList.add('invalid');
            username.classList.remove('valid');
            usernameFb.textContent = '用户名至少3个字符';
            usernameFb.className = 'form-feedback error';
            return false;
        }
        username.classList.remove('invalid');
        username.classList.add('valid');
        usernameFb.textContent = '用户名格式正确';
        usernameFb.className = 'form-feedback success';
        return true;
    }
    
    function validatePassword() {
        var val = password.value;
        if (val === '') {
            password.classList.add('invalid');
            password.classList.remove('valid');
            passwordFb.textContent = '请输入密码';
            passwordFb.className = 'form-feedback error';
            return false;
        }
        if (val.length < 6) {
            password.classList.add('invalid');
            password.classList.remove('valid');
            passwordFb.textContent = '密码至少6位';
            passwordFb.className = 'form-feedback error';
            return false;
        }
        password.classList.remove('invalid');
        password.classList.add('valid');
        passwordFb.textContent = '密码格式正确';
        passwordFb.className = 'form-feedback success';
        return true;
    }
    
    username.addEventListener('input', validateUsername);
    password.addEventListener('input', validatePassword);
    
    form.addEventListener('submit', function(e) {
        var vu = validateUsername();
        var vp = validatePassword();
        if (!vu || !vp) {
            e.preventDefault();
        }
    });
    
    // 密码显示切换
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var target = document.getElementById(this.dataset.target);
            var icon = this.querySelector('i');
            if (target.type === 'password') {
                target.type = 'text';
                icon.className = 'far fa-eye-slash';
            } else {
                target.type = 'password';
                icon.className = 'far fa-eye';
            }
        });
    });
});
</script>

<?php include TEMPLATES_PATH . 'footer.php'; ?>