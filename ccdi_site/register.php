<?php
/**
 * 用户注册 v6.0.0
 * 中央纪委国家监委网站 CMS 系统
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
        <div class="auth-icon">
            <i class="fas fa-user-plus"></i>
        </div>
        <h2 class="auth-card__title">注册</h2>
        <p class="auth-card__desc">创建中央纪委国家监委网站账号</p>

        <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form" id="registerForm" novalidate>
            <?php echo csrf_field(); ?>

            <div class="auth-form__group">
                <label class="auth-form__label auth-form__label--required" for="username">用户名</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-wrapper__icon"></i>
                    <input type="text" id="username" name="username" class="form-input" placeholder="3-20位，字母、数字、下划线、中文" required>
                </div>
                <span class="form-feedback" id="usernameFeedback"></span>
            </div>

            <div class="auth-form__group">
                <label class="auth-form__label" for="email">邮箱</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-wrapper__icon"></i>
                    <input type="email" id="email" name="email" class="form-input" placeholder="选填，用于密码找回">
                </div>
                <span class="form-feedback" id="emailFeedback"></span>
            </div>

            <div class="auth-form__group">
                <label class="auth-form__label auth-form__label--required" for="password">密码</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-wrapper__icon"></i>
                    <input type="password" id="password" name="password" class="form-input" placeholder="至少6位密码" required>
                    <button type="button" class="password-toggle" data-target="password"><i class="far fa-eye"></i></button>
                </div>
                <span class="form-feedback" id="passwordFeedback"></span>
            </div>

            <div class="auth-form__group">
                <label class="auth-form__label auth-form__label--required" for="password2">确认密码</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-wrapper__icon"></i>
                    <input type="password" id="password2" name="password2" class="form-input" placeholder="再次输入密码" required>
                    <button type="button" class="password-toggle" data-target="password2"><i class="far fa-eye"></i></button>
                </div>
                <span class="form-feedback" id="password2Feedback"></span>
            </div>

            <button type="submit" class="btn btn-primary btn-block">注 册</button>
        </form>

        <div class="auth-card__footer">
            已有账号？<a href="<?php echo site_url('login.php'); ?>">立即登录</a>
        </div>
    </div>
</div>

<script>
(function(){
    var u=document.getElementById('username'),e=document.getElementById('email'),
        p=document.getElementById('password'),p2=document.getElementById('password2');
    function fb(el,msg,ok){el.classList.remove('form-input--error','form-input--success');el.classList.add(ok?'form-input--success':'form-input--error');return msg;}
    function vU(){var v=u.value.trim(),f=document.getElementById('usernameFeedback'),msg='';
        if(!v)msg='请输入用户名';else if(v.length<3||v.length>20)msg='用户名长度需在3-20位之间';else if(!/^[a-zA-Z0-9_\u4e00-\u9fa5]+$/.test(v))msg='用户名只能包含字母、数字、下划线、中文';
        if(msg){f.textContent=msg;f.className='form-feedback form-feedback--error';fb(u,msg,false);return false;}
        f.textContent='';f.className='form-feedback form-feedback--success';fb(u,'',true);return true;}
    function vE(){var v=e.value.trim(),f=document.getElementById('emailFeedback');
        if(!v){e.classList.remove('form-input--error','form-input--success');f.textContent='';f.className='form-feedback';return true;}
        if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)){f.textContent='邮箱格式不正确';f.className='form-feedback form-feedback--error';fb(e,'',false);return false;}
        f.textContent='';f.className='form-feedback form-feedback--success';fb(e,'',true);return true;}
    function vP(){var v=p.value,f=document.getElementById('passwordFeedback');
        if(!v){f.textContent='请输入密码';f.className='form-feedback form-feedback--error';fb(p,'',false);return false;}
        if(v.length<6){f.textContent='密码至少6位';f.className='form-feedback form-feedback--error';fb(p,'',false);return false;}
        f.textContent='';f.className='form-feedback form-feedback--success';fb(p,'',true);return true;}
    function vP2(){var v=p2.value,f=document.getElementById('password2Feedback');
        if(!v){f.textContent='请再次输入密码';f.className='form-feedback form-feedback--error';fb(p2,'',false);return false;}
        if(v!==p.value){f.textContent='两次输入的密码不一致';f.className='form-feedback form-feedback--error';fb(p2,'',false);return false;}
        f.textContent='';f.className='form-feedback form-feedback--success';fb(p2,'',true);return true;}
    u.addEventListener('input',vU);e.addEventListener('input',vE);p.addEventListener('input',function(){vP();if(p2.value)vP2();});p2.addEventListener('input',vP2);
    document.getElementById('registerForm').addEventListener('submit',function(e){var ok=true;if(!vU())ok=false;if(!vE())ok=false;if(!vP())ok=false;if(!vP2())ok=false;if(!ok)e.preventDefault();});
    document.querySelectorAll('.password-toggle').forEach(function(b){b.addEventListener('click',function(){var t=document.getElementById(this.dataset.target);t.type=t.type==='password'?'text':'password';this.querySelector('i').className=t.type==='password'?'far fa-eye':'far fa-eye-slash';});});
})();
</script>

<?php include TEMPLATES_PATH . 'footer.php'; ?>