<?php
/**
 * 用户注册 v4.0.0
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/includes/init.php';

$page_title = '注册';
$error = '';

if (is_logged_in()) redirect(site_url());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify(post(CSRF_TOKEN_NAME))) {
        $error = '表单验证失败，请刷新重试';
    } else {
        $p1 = $_POST['password'] ?? '';
        $p2 = $_POST['password2'] ?? '';
        if ($p1 !== $p2) {
            $error = '两次输入的密码不一致';
        } else {
            $result = register_user(post('username'), $p1, post('email'));
            if ($result['success']) redirect(site_url());
            $error = $result['message'];
        }
    }
}

include TEMPLATES_PATH . 'header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon"><i class="fas fa-user-plus"></i></div>
            <h2>注册</h2>
            <p>创建中央纪委国家监委网站账号</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
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

            <button type="submit" class="btn btn-primary btn-block">注 册</button>
        </form>

        <div class="auth-footer">
            已有账号？<a href="<?php echo site_url('login.php'); ?>">立即登录</a>
        </div>
    </div>
</div>

<script>
(function(){
    var u=document.getElementById('username'),e=document.getElementById('email'),p=document.getElementById('password'),p2=document.getElementById('password2');
    function vU(){var v=u.value.trim(),f=document.getElementById('usernameFeedback');
        if(!v){u.classList.add('invalid');u.classList.remove('valid');f.textContent='请输入用户名';f.className='form-feedback error';return false;}
        if(v.length<3||v.length>20){u.classList.add('invalid');u.classList.remove('valid');f.textContent='用户名长度需在3-20位之间';f.className='form-feedback error';return false;}
        if(!/^[a-zA-Z0-9_\u4e00-\u9fa5]+$/.test(v)){u.classList.add('invalid');u.classList.remove('valid');f.textContent='用户名只能包含字母、数字、下划线、中文';f.className='form-feedback error';return false;}
        u.classList.remove('invalid');u.classList.add('valid');f.textContent='';f.className='form-feedback success';return true;}
    function vE(){var v=e.value.trim(),f=document.getElementById('emailFeedback');
        if(!v){e.classList.remove('invalid','valid');f.textContent='';f.className='form-feedback';return true;}
        if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)){e.classList.add('invalid');e.classList.remove('valid');f.textContent='邮箱格式不正确';f.className='form-feedback error';return false;}
        e.classList.remove('invalid');e.classList.add('valid');f.textContent='';f.className='form-feedback success';return true;}
    function vP(){var v=p.value,f=document.getElementById('passwordFeedback');
        if(!v){p.classList.add('invalid');p.classList.remove('valid');f.textContent='请输入密码';f.className='form-feedback error';return false;}
        if(v.length<6){p.classList.add('invalid');p.classList.remove('valid');f.textContent='密码至少6位';f.className='form-feedback error';return false;}
        p.classList.remove('invalid');p.classList.add('valid');f.textContent='';f.className='form-feedback success';return true;}
    function vP2(){var v=p2.value,f=document.getElementById('password2Feedback');
        if(!v){p2.classList.add('invalid');p2.classList.remove('valid');f.textContent='请再次输入密码';f.className='form-feedback error';return false;}
        if(v!==p.value){p2.classList.add('invalid');p2.classList.remove('valid');f.textContent='两次输入的密码不一致';f.className='form-feedback error';return false;}
        p2.classList.remove('invalid');p2.classList.add('valid');f.textContent='';f.className='form-feedback success';return true;}
    u.addEventListener('input',vU);e.addEventListener('input',vE);p.addEventListener('input',function(){vP();if(p2.value)vP2();});p2.addEventListener('input',vP2);
    document.getElementById('registerForm').addEventListener('submit',function(e){var ok=true;if(!vU())ok=false;if(!vE())ok=false;if(!vP())ok=false;if(!vP2())ok=false;if(!ok)e.preventDefault();});
    document.querySelectorAll('.toggle-password').forEach(function(b){b.addEventListener('click',function(){var t=document.getElementById(this.dataset.target);t.type=t.type==='password'?'text':'password';this.querySelector('i').className=t.type==='password'?'far fa-eye':'far fa-eye-slash';});});
})();
</script>

<?php include TEMPLATES_PATH . 'footer.php'; ?>