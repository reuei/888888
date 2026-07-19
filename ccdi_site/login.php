<?php
/**
 * 用户登录 v3.0.0
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';

$page_title = '登录';
$error = '';

if (is_logged_in()) redirect(site_url());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify(post(CSRF_TOKEN_NAME))) {
        $error = '表单验证失败，请刷新重试';
    } else {
        $result = login_user(post('username'), $_POST['password'] ?? '', isset($_POST['remember']));
        if ($result['success']) {
            $redirect = $_SESSION['redirect_after_login'] ?? site_url();
            unset($_SESSION['redirect_after_login']);
            redirect($redirect);
        }
        $error = $result['message'];
    }
}

include TEMPLATES_PATH . 'header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon"><i class="fas fa-lock"></i></div>
            <h2>登录</h2>
            <p>中央纪委国家监委网站</p>
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
                    <input type="text" id="username" name="username" required placeholder="请输入用户名" autocomplete="username">
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

            <div class="form-check">
                <label class="checkbox-label"><input type="checkbox" name="remember" value="1"> 记住登录</label>
                <a href="#" class="forgot-link">忘记密码</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block">登 录</button>
        </form>

        <div class="auth-footer">
            还没有账号？<a href="<?php echo site_url('register.php'); ?>">立即注册</a>
        </div>
    </div>
</div>

<script>
(function(){
    var u = document.getElementById('username'), p = document.getElementById('password');
    var uf = document.getElementById('usernameFeedback'), pf = document.getElementById('passwordFeedback');

    function vUser(){
        var v = u.value.trim();
        if(!v){ u.classList.add('invalid');u.classList.remove('valid');uf.textContent='请输入用户名';uf.className='form-feedback error';return false; }
        if(v.length<3){ u.classList.add('invalid');u.classList.remove('valid');uf.textContent='用户名至少3个字符';uf.className='form-feedback error';return false; }
        u.classList.remove('invalid');u.classList.add('valid');uf.textContent='';uf.className='form-feedback success';return true;
    }
    function vPass(){
        var v = p.value;
        if(!v){ p.classList.add('invalid');p.classList.remove('valid');pf.textContent='请输入密码';pf.className='form-feedback error';return false; }
        if(v.length<6){ p.classList.add('invalid');p.classList.remove('valid');pf.textContent='密码至少6位';pf.className='form-feedback error';return false; }
        p.classList.remove('invalid');p.classList.add('valid');pf.textContent='';pf.className='form-feedback success';return true;
    }
    u.addEventListener('input', vUser);
    p.addEventListener('input', vPass);
    document.getElementById('loginForm').addEventListener('submit', function(e){ if(!vUser()||!vPass()) e.preventDefault(); });
    document.querySelectorAll('.toggle-password').forEach(function(b){
        b.addEventListener('click',function(){ var t=document.getElementById(this.dataset.target); t.type=t.type==='password'?'text':'password'; this.querySelector('i').className=t.type==='password'?'far fa-eye':'far fa-eye-slash'; });
    });
})();
</script>

<?php include TEMPLATES_PATH . 'footer.php'; ?>