<div class="auth-layout">
    <div class="auth-card fade-in-up">
        <!-- 左侧 3D 插画 -->
        <div class="auth-illustration">
            <svg class="auth-illust-svg" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <!-- 盾牌主体 -->
                <path d="M100 30 L160 52 V100 C160 138 132 162 100 174 C68 162 40 138 40 100 V52 Z" fill="rgba(255,255,255,0.96)"/>
                <path d="M100 30 L160 52 V100 C160 138 132 162 100 174 C68 162 40 138 40 100 V52 Z" fill="url(#lg-shield)" opacity="0.2"/>
                <!-- 盾牌高光 -->
                <path d="M100 30 L160 52 V78 C160 84 152 86 146 80 L100 44 Z" fill="rgba(255,255,255,0.5)"/>
                <!-- 钥匙圆环 -->
                <circle cx="100" cy="92" r="20" stroke="#7c3aed" stroke-width="6" fill="none"/>
                <!-- 钥匙杆 -->
                <path d="M100 112 L100 148 M100 130 L116 130 M100 140 L110 140" stroke="#7c3aed" stroke-width="6" stroke-linecap="round"/>
                <!-- 漂浮装饰 -->
                <circle cx="172" cy="60" r="8" fill="rgba(255,255,255,0.6)"/>
                <circle cx="28" cy="80" r="5" fill="rgba(255,255,255,0.5)"/>
                <rect x="170" y="110" width="14" height="14" rx="3" fill="rgba(255,255,255,0.5)" transform="rotate(15 177 117)"/>
                <defs>
                    <linearGradient id="lg-shield" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="#a855f7"/>
                        <stop offset="1" stop-color="#7c3aed"/>
                    </linearGradient>
                </defs>
            </svg>
            <h2>欢迎回来</h2>
            <p>登录以管理您的授权码、订单与插件</p>
        </div>

        <!-- 右侧登录表单 -->
        <div class="auth-form-side">
            <h2>用户登录</h2>
            <p class="auth-sub">请输入您的账号和密码</p>
            <form id="loginForm" data-async>
                <div class="form-group">
                    <label><i data-icon="user" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>账号</label>
                    <input type="text" name="username" placeholder="请输入账号" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label><i data-icon="key" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>密码</label>
                    <input type="password" name="password" placeholder="请输入密码" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn btn-block btn-lg">
                    <i data-icon="logout"></i><span>登录</span>
                </button>
            </form>
            <p class="auth-alt">
                还没有账号？<a href="<?php echo url('login/register'); ?>">立即注册 <i data-icon="chevron-right" class="svg-icon-sm" style="vertical-align:-2px;"></i></a>
            </p>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = e.target;
    var btn = form.querySelector('[type="submit"]');
    if (window.QEEFG) QEEFG.setLoading(btn, true);
    fetch('<?php echo url('login/doLogin'); ?>', {
        method: 'POST',
        body: new FormData(form)
    }).then(function(r) { return r.json(); }).then(function(res) {
        if (window.QEEFG) { QEEFG.setLoading(btn, false); QEEFG.toast(res.msg, res.code === 0 ? 'success' : 'error'); }
        else { alert(res.msg); }
        if (res.code === 0 && res.data.redirect) {
            setTimeout(function() { location.href = res.data.redirect; }, 600);
        }
    }).catch(function() {
        if (window.QEEFG) { QEEFG.setLoading(btn, false); QEEFG.toastError('网络异常，请稍后重试'); }
        else { alert('网络异常，请稍后重试'); }
    });
});
</script>
