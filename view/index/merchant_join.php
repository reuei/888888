<style>
.join-card {
    max-width: 520px;
    margin: 0 auto;
}
.join-header {
    text-align: center;
    margin-bottom: 24px;
}
.join-header h2 { font-size: 22px; font-weight: 600; margin-bottom: 8px; }
.join-header p { color: #64748B; font-size: 13px; }
.join-tips {
    background: #F0F9FF;
    border: 1px solid #BAE6FD;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 20px;
    font-size: 13px;
    color: #0369A1;
}
.join-tips ul { margin: 8px 0 0 18px; }
.join-tips li { margin-bottom: 4px; }
.success-box {
    text-align: center;
    padding: 40px 20px;
}
.success-box .icon { font-size: 48px; margin-bottom: 16px; }
.success-box h3 { font-size: 18px; font-weight: 600; margin-bottom: 8px; }
.success-box p { color: #64748B; font-size: 13px; }
</style>

<div class="card join-card">
    <div class="join-header">
        <h2>商户入驻</h2>
        <p>提交资料后，平台将在 1-3 个工作日内完成审核</p>
    </div>

    <?php if (input('success', '') === '1'): ?>
    <div class="success-box">
        <div class="icon">✅</div>
        <h3>入驻申请已提交</h3>
        <p>请耐心等待审核，审核结果将通过短信通知</p>
        <div style="margin-top: 20px;">
            <a href="<?php echo url('/'); ?>" class="btn btn-outline">返回首页</a>
        </div>
    </div>
    <?php else: ?>
    <div class="join-tips">
        <strong>入驻须知</strong>
        <ul>
            <li>账号为 4-20 位字母/数字/下划线</li>
            <li>密码长度不少于 6 位</li>
            <li>请填写真实有效的手机号</li>
            <li>提交后账号状态为“待审核”，审核通过后方可登录</li>
        </ul>
    </div>

    <form id="merchantJoinForm">
        <div class="form-group">
            <label>登录账号</label>
            <input type="text" name="username" placeholder="4-20 位字母/数字/下划线" required>
        </div>
        <div class="form-group">
            <label>登录密码</label>
            <input type="password" name="password" placeholder="不少于 6 位" required>
        </div>
        <div class="form-group">
            <label>确认密码</label>
            <input type="password" name="password_confirm" placeholder="再次输入密码" required>
        </div>
        <div class="form-group">
            <label>店铺名称</label>
            <input type="text" name="shop_name" placeholder="请输入店铺名称" required>
        </div>
        <div class="form-group">
            <label>联系手机号</label>
            <input type="tel" name="mobile" placeholder="请输入手机号" maxlength="11" required>
        </div>

        <?php if ($currentSubsite): ?>
        <div class="form-group">
            <label>所属分站</label>
            <input type="text" value="<?php echo h($currentSubsite['name']); ?>" disabled>
            <input type="hidden" name="subsite_id" value="<?php echo $currentSubsite['id']; ?>">
        </div>
        <?php elseif (!empty($subsites)): ?>
        <div class="form-group">
            <label>所属分站</label>
            <select name="subsite_id">
                <option value="0">总站</option>
                <?php foreach ($subsites as $s): ?>
                <option value="<?php echo $s['id']; ?>" <?php echo $subsiteId == $s['id'] ? 'selected' : ''; ?>><?php echo h($s['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label>邀请码（选填）</label>
            <input type="text" name="invite_code" value="<?php echo h($inviteCode); ?>" placeholder="如有邀请码请填写">
        </div>

        <?php if (captcha_required('join')): ?>
        <div class="form-group">
            <label>验证码</label>
            <div class="captcha-row" style="display: flex; gap: 10px; align-items: center;">
                <input type="text" name="captcha" placeholder="请输入验证码" maxlength="4" required style="flex: 1;">
                <img src="<?php echo url('login/captcha', ['key' => 'join']); ?>" alt="验证码" id="joinCaptchaImg" title="点击刷新" style="height: 40px; border-radius: 6px; cursor: pointer; border: 1px solid #CBD5E1;">
            </div>
        </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-lg btn-block" id="submitBtn">提交入驻申请</button>
        <div style="text-align: center; margin-top: 16px;">
            <span style="color: #64748B; font-size: 13px;">已有账号？</span>
            <a href="<?php echo url('login'); ?>?type=merchant" style="font-size: 13px;">立即登录</a>
        </div>
    </form>
    <?php endif; ?>
</div>

<script>
const joinCaptchaImg = document.getElementById('joinCaptchaImg');
function refreshJoinCaptcha() {
    if (joinCaptchaImg) {
        joinCaptchaImg.src = '<?php echo url('login/captcha', ['key' => 'join']); ?>&' + Date.now();
    }
}
if (joinCaptchaImg) {
    joinCaptchaImg.addEventListener('click', refreshJoinCaptcha);
}

document.getElementById('merchantJoinForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.textContent = '提交中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('index/doMerchantJoin'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.code === 0) {
            location.href = '<?php echo url('index/merchantJoin'); ?>?success=1';
        } else {
            alert(data.msg);
            refreshJoinCaptcha();
        }
    } catch (err) {
        alert('请求失败');
        refreshJoinCaptcha();
    } finally {
        btn.disabled = false;
        btn.textContent = '提交入驻申请';
    }
});
</script>
