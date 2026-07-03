<div class="auth-layout">
    <div class="auth-card fade-in-up">
        <!-- 左侧 3D 插画 -->
        <div class="auth-illustration">
            <svg class="auth-illust-svg" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <!-- 主体卡片 -->
                <rect x="44" y="50" width="112" height="120" rx="14" fill="rgba(255,255,255,0.96)"/>
                <rect x="44" y="50" width="112" height="120" rx="14" fill="url(#rg-card)" opacity="0.18"/>
                <!-- 顶部条 -->
                <rect x="44" y="50" width="112" height="28" rx="14" fill="rgba(124,58,237,0.18)"/>
                <rect x="44" y="64" width="112" height="14" fill="rgba(124,58,237,0.18)"/>
                <!-- 用户图标圆 -->
                <circle cx="100" cy="100" r="18" fill="url(#rg-card)"/>
                <circle cx="100" cy="95" r="6" fill="#fff"/>
                <path d="M88 116 C88 108 112 108 112 116 V120 H88 Z" fill="#fff"/>
                <!-- 装饰线 -->
                <rect x="64" y="132" width="72" height="6" rx="3" fill="rgba(124,58,237,0.2)"/>
                <rect x="64" y="146" width="56" height="6" rx="3" fill="rgba(124,58,237,0.15)"/>
                <!-- 漂浮装饰 -->
                <circle cx="36" cy="40" r="6" fill="rgba(255,255,255,0.6)"/>
                <circle cx="170" cy="170" r="5" fill="rgba(255,255,255,0.5)"/>
                <rect x="166" y="40" width="12" height="12" rx="3" fill="rgba(255,255,255,0.5)" transform="rotate(20 172 46)"/>
                <defs>
                    <linearGradient id="rg-card" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0" stop-color="#a855f7"/>
                        <stop offset="1" stop-color="#7c3aed"/>
                    </linearGradient>
                </defs>
            </svg>
            <h2>加入我们</h2>
            <p>注册账号，开启您的数字商品授权之旅</p>
        </div>

        <!-- 右侧注册表单 -->
        <div class="auth-form-side">
            <h2>用户注册</h2>
            <p class="auth-sub">填写以下信息完成注册</p>
            <form id="registerForm" data-async>
                <div class="form-group">
                    <label><i data-icon="user" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>账号</label>
                    <input type="text" name="username" placeholder="4-20 位字母/数字/下划线" required>
                </div>
                <div class="form-group">
                    <label><i data-icon="tag" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>昵称</label>
                    <input type="text" name="nickname" placeholder="可选">
                </div>
                <div class="form-group">
                    <label><i data-icon="bell" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>邮箱</label>
                    <input type="text" name="email" placeholder="可选">
                </div>
                <div class="form-group">
                    <label><i data-icon="key" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>密码</label>
                    <input type="password" name="password" placeholder="至少 6 位" required autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label><i data-icon="shield" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>确认密码</label>
                    <input type="password" name="password_confirm" placeholder="再次输入密码" required autocomplete="new-password">
                </div>
                <button type="submit" class="btn btn-block btn-lg">
                    <i data-icon="check"></i><span>立即注册</span>
                </button>
            </form>
            <p class="auth-alt">
                已有账号？<a href="<?php echo url('login'); ?>">立即登录 <i data-icon="chevron-right" class="svg-icon-sm" style="vertical-align:-2px;"></i></a>
            </p>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    var form = e.target;
    var btn = form.querySelector('[type="submit"]');
    if (window.QEEFG) QEEFG.setLoading(btn, true);
    fetch('<?php echo url('login/doRegister'); ?>', {
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
