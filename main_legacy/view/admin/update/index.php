<style>
.update-card {
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    padding: 24px;
    margin-bottom: 20px;
}
.update-card h3 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 16px;
    color: #1F2937;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}
.status-success { background: #ECFDF5; color: #059669; }
.status-danger { background: #FEF2F2; color: #DC2626; }
.status-warning { background: #FFFBEB; color: #D97706; }
.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 16px;
}
.version-info {
    display: flex;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
    margin-bottom: 16px;
}
.version-info .item {
    font-size: 14px;
    color: #64748B;
}
.version-info .item strong {
    color: #1F2937;
    font-size: 18px;
}
.log-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.log-list li {
    padding: 10px 0;
    border-bottom: 1px solid #F1F5F9;
    font-size: 14px;
}
.log-list li:last-child { border-bottom: none; }
</style>

<div class="update-card">
    <h3>🔐 授权配置</h3>
    <form id="licenseForm">
        <div class="form-row">
            <div class="form-group">
                <label>授权站 API 地址</label>
                <input type="text" name="api_url" value="<?php echo h($license['api_url']); ?>" placeholder="https://auth.example.com">
                <p class="hint">授权站（QEEFG 售卖系统）的根地址</p>
            </div>
            <div class="form-group">
                <label>授权码</label>
                <input type="text" name="auth_code" value="<?php echo h($license['auth_code']); ?>" placeholder="请输入购买的授权码" autocomplete="off">
                <p class="hint">在授权站购买后获得的授权码</p>
            </div>
            <div class="form-group">
                <label>授权域名</label>
                <input type="text" name="auth_domain" value="<?php echo h($license['auth_domain'] ?: ($_SERVER['HTTP_HOST'] ?? '')); ?>" placeholder="当前站点域名">
                <p class="hint">需要授权绑定的域名，留空使用当前域名</p>
            </div>
            <div class="form-group">
                <label>API 通信密钥（可选）</label>
                <input type="text" name="api_key" value="<?php echo h($license['api_key']); ?>" placeholder="用于接口签名">
                <p class="hint">授权站提供的 API 密钥，用于接口签名验证</p>
            </div>
        </div>
        <button type="submit" class="btn" id="saveLicenseBtn">保存并验证</button>
    </form>
</div>

<div class="update-card">
    <h3>🚀 版本与更新</h3>
    <div class="version-info">
        <div class="item">
            当前版本<br>
            <strong>v<?php echo h($currentVersion); ?></strong>
        </div>
        <div class="item">
            授权状态<br>
            <?php if ($remoteInfo && !empty($remoteInfo['license_valid'])): ?>
                <span class="status-badge status-success">✓ 已授权</span>
            <?php elseif ($remoteInfo): ?>
                <span class="status-badge status-danger">✗ 未授权</span>
            <?php else: ?>
                <span class="status-badge status-warning">⏳ 未检测</span>
            <?php endif; ?>
        </div>
        <div class="item">
            最新版本<br>
            <strong><?php echo h($remoteInfo['latest_version'] ?? '—'); ?></strong>
        </div>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><?php echo h($error); ?></div>
    <?php endif; ?>

    <?php if ($remoteInfo && !empty($remoteInfo['has_update'])): ?>
    <div style="margin-bottom: 16px;">
        <p style="margin-bottom: 12px;">发现新版本 <strong>v<?php echo h($remoteInfo['latest_version']); ?></strong>，发布时间 <?php echo h($remoteInfo['release_date'] ?? '—'); ?></p>
        <p class="hint" style="margin-bottom: 12px;"><?php echo h($remoteInfo['update_desc'] ?? ''); ?></p>
        <button type="button" class="btn btn-success" id="upgradeBtn" data-version="<?php echo h($remoteInfo['latest_version']); ?>">立即更新</button>
    </div>
    <?php elseif ($remoteInfo): ?>
    <div class="alert alert-success">当前已是最新版本</div>
    <?php endif; ?>

    <button type="button" class="btn btn-outline" id="checkUpdateBtn">检查更新</button>
</div>

<?php if ($remoteInfo && !empty($remoteInfo['changelog'])): ?>
<div class="update-card">
    <h3>📋 更新日志</h3>
    <ul class="log-list">
        <?php foreach ($remoteInfo['changelog'] as $log): ?>
        <li><strong><?php echo h($log['version'] ?? ''); ?></strong> <?php echo h($log['date'] ?? ''); ?><br><span class="hint"><?php echo h($log['desc'] ?? ''); ?></span></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<script>
document.getElementById('licenseForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveLicenseBtn');
    btn.disabled = true;
    btn.textContent = '验证中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('admin/update/saveLicense'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) {
            location.reload();
        }
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '保存并验证';
    }
});

document.getElementById('checkUpdateBtn')?.addEventListener('click', async () => {
    const btn = document.getElementById('checkUpdateBtn');
    btn.disabled = true;
    btn.textContent = '检查中...';
    try {
        const res = await fetch('<?php echo url('admin/update/check'); ?>');
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) {
            location.reload();
        }
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '检查更新';
    }
});

document.getElementById('upgradeBtn')?.addEventListener('click', async () => {
    const btn = document.getElementById('upgradeBtn');
    const version = btn.dataset.version;
    if (!confirm('确定要更新到 v' + version + ' 吗？更新前建议先备份数据库和代码。')) {
        return;
    }
    btn.disabled = true;
    btn.textContent = '更新中...';
    try {
        const formData = new FormData();
        formData.append('version', version);
        const res = await fetch('<?php echo url('admin/update/upgrade'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) {
            location.reload();
        }
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '立即更新';
    }
});
</script>
