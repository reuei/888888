<?php
/**
 * 用户注册页面
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';

$page_title = '用户注册';
$error = '';
$success = '';

if (is_logged_in()) {
    redirect(site_url());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify(post(CSRF_TOKEN_NAME))) {
        $error = '表单验证失败，请刷新页面重试';
    } else {
        $username = post('username');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $email = post('email');
        
        if ($password !== $password2) {
            $error = '两次输入的密码不一致';
        } else {
            $result = register_user($username, $password, $email);
            if ($result['success']) {
                $success = $result['message'];
                redirect(site_url());
            } else {
                $error = $result['message'];
            }
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
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2>用户注册</h2>
                <p>注册中央纪委国家监委网站账号</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="post" class="auth-form" id="registerForm" novalidate>
                <?php echo csrf_field(); ?>
                
                <div class="form-group">
                    <label for="username">用户名 <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="username" name="username" required placeholder="3-20位，字母、数字、下划线、中文">
                    </div>
                    <span class="form-feedback" id="usernameFeedback"></span>
                </div>
                
                <div class="form-group">
                    <label for="email">邮箱</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" placeholder="选填，用于密码找回">
                    </div>
                    <span class="form-feedback" id="emailFeedback"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">密码 <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" required placeholder="至少6位密码">
                        <button type="button" class="toggle-password" data-target="password"><i class="far fa-eye"></i></button>
                    </div>
                    <span class="form-feedback" id="passwordFeedback"></span>
                </div>
                
                <div class="form-group">
                    <label for="password2">确认密码 <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password2" name="password2" required placeholder="再次输入密码">
                        <button type="button" class="toggle-password" data-target="password2"><i class="far fa-eye"></i></button>
                    </div>
                    <span class="form-feedback" id="password2Feedback"></span>
                </div>
                
                <div class="form-group form-check">
                    <label class="checkbox-label">
                        <input type="checkbox" required> 我已阅读并同意相关条款
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">注 册</button>
            </form>
            
            <div class="auth-footer">
                <p>已有账号？<a href="<?php echo site_url('login.php'); ?>">立即登录</a></p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('registerForm');
    var username = document.getElementById('username');
    var email = document.getElementById('email');
    var password = document.getElementById('password');
    var password2 = document.getElementById('password2');
    
    function validateUsername() {
        var val = username.value.trim();
        var fb = document.getElementById('usernameFeedback');
        if (val === '') {
            username.classList.add('invalid'); username.classList.remove('valid');
            fb.textContent = '请输入用户名'; fb.className = 'form-feedback error';
            return false;
        }
        if (val.length < 3 || val.length > 20) {
            username.classList.add('invalid'); username.classList.remove('valid');
            fb.textContent = '用户名长度需在3-20位之间'; fb.className = 'form-feedback error';
            return false;
        }
        if (!/^[a-zA-Z0-9_\u4e00-\u9fa5]+$/.test(val)) {
            username.classList.add('invalid'); username.classList.remove('valid');
            fb.textContent = '用户名只能包含字母、数字、下划线、中文'; fb.className = 'form-feedback error';
            return false;
        }
        username.classList.remove('invalid'); username.classList.add('valid');
        fb.textContent = '用户名格式正确'; fb.className = 'form-feedback success';
        return true;
    }
    
    function validateEmail() {
        var val = email.value.trim();
        var fb = document.getElementById('emailFeedback');
        if (val === '') {
            email.classList.remove('invalid'); email.classList.remove('valid');
            fb.textContent = ''; fb.className = 'form-feedback';
            return true;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
            email.classList.add('invalid'); email.classList.remove('valid');
            fb.textContent = '邮箱格式不正确'; fb.className = 'form-feedback error';
            return false;
        }
        email.classList.remove('invalid'); email.classList.add('valid');
        fb.textContent = '邮箱格式正确'; fb.className = 'form-feedback success';
        return true;
    }
    
    function validatePassword() {
        var val = password.value;
        var fb = document.getElementById('passwordFeedback');
        if (val === '') {
            password.classList.add('invalid'); password.classList.remove('valid');
            fb.textContent = '请输入密码'; fb.className = 'form-feedback error';
            return false;
        }
        if (val.length < 6) {
            password.classList.add('invalid'); password.classList.remove('valid');
            fb.textContent = '密码至少6位'; fb.className = 'form-feedback error';
            return false;
        }
        if (val.length > 50) {
            password.classList.add('invalid'); password.classList.remove('valid');
            fb.textContent = '密码不能超过50位'; fb.className = 'form-feedback error';
            return false;
        }
        password.classList.remove('invalid'); password.classList.add('valid');
        fb.textContent = '密码强度合格'; fb.className = 'form-feedback success';
        return true;
    }
    
    function validatePassword2() {
        var val = password2.value;
        var fb = document.getElementById('password2Feedback');
        if (val === '') {
            password2.classList.add('invalid'); password2.classList.remove('valid');
            fb.textContent = '请再次输入密码'; fb.className = 'form-feedback error';
            return false;
        }
        if (val !== password.value) {
            password2.classList.add('invalid'); password2.classList.remove('valid');
            fb.textContent = '两次输入的密码不一致'; fb.className = 'form-feedback error';
            return false;
        }
        password2.classList.remove('invalid'); password2.classList.add('valid');
        fb.textContent = '密码一致'; fb.className = 'form-feedback success';
        return true;
    }
    
    username.addEventListener('input', validateUsername);
    email.addEventListener('input', validateEmail);
    password.addEventListener('input', function() { validatePassword(); if (password2.value) validatePassword2(); });
    password2.addEventListener('input', validatePassword2);
    
    form.addEventListener('submit', function(e) {
        var ok = true;
        if (!validateUsername()) ok = false;
        if (!validateEmail()) ok = false;
        if (!validatePassword()) ok = false;
        if (!validatePassword2()) ok = false;
        if (!ok) e.preventDefault();
    });
    
    document.querySelectorAll('.toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var target = document.getElementById(this.dataset.target);
            var icon = this.querySelector('i');
            target.type = target.type === 'password' ? 'text' : 'password';
            icon.className = target.type === 'password' ? 'far fa-eye' : 'far fa-eye-slash';
        });
    });
});
</script>

<?php include TEMPLATES_PATH . 'footer.php'; ?>