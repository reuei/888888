<?php
/**
 * 在线更新 / 授权管理
 * 对标主站设计系统（蓝紫渐变 + 玻璃拟态 + SVG 图标 + Toast）
 */
?>
<style>
.license-hero{
    background:linear-gradient(135deg,var(--primary-600),#7c3aed 60%,var(--primary-700));
    border-radius:18px;padding:28px 30px;color:#fff;position:relative;overflow:hidden;
    box-shadow:0 18px 40px -18px rgba(37,99,235,.55);margin-bottom:24px;
}
.license-hero::before{
    content:"";position:absolute;right:-60px;top:-60px;width:240px;height:240px;
    background:radial-gradient(circle,rgba(255,255,255,.25),transparent 70%);pointer-events:none;
}
.license-hero::after{
    content:"";position:absolute;left:-40px;bottom:-80px;width:200px;height:200px;
    background:radial-gradient(circle,rgba(124,58,237,.4),transparent 70%);pointer-events:none;
}
.license-hero .illust{position:absolute;right:30px;top:50%;transform:translateY(-50%);opacity:.55;pointer-events:none}
.license-hero .illust svg{width:150px;height:150px}
.license-hero h2{font-size:22px;font-weight:700;margin-bottom:8px;position:relative;z-index:1}
.license-hero p{font-size:13px;opacity:.92;position:relative;z-index:1;max-width:520px;line-height:1.6}

.upd-card{background:#fff;border:1px solid var(--border-soft);border-radius:14px;padding:24px;margin-bottom:20px;box-shadow:var(--shadow-sm)}
.upd-card h3{display:flex;align-items:center;gap:10px;font-size:16px;font-weight:600;margin-bottom:18px;color:var(--text)}
.upd-card h3 .icon{width:20px;height:20px;color:var(--primary)}
.upd-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px}
.upd-grid .form-group{margin-bottom:0}
.upd-grid .hint{margin-top:6px;font-size:12px;color:var(--text-muted)}
.upd-actions{margin-top:20px;display:flex;gap:12px;flex-wrap:wrap}

.lic-status{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600}
.lic-status svg{width:14px;height:14px}
.lic-ok{background:var(--success-50);color:#047857}
.lic-no{background:#FEF2F2;color:#B91C1C}
.lic-wait{background:var(--warning-50);color:#B45309}

.ver-row{display:flex;align-items:flex-start;gap:28px;flex-wrap:wrap;margin-bottom:18px}
.ver-item{min-width:120px}
.ver-item .lbl{font-size:12px;color:var(--text-muted);margin-bottom:4px;display:flex;align-items:center;gap:6px}
.ver-item .lbl svg{width:14px;height:14px}
.ver-item .val{font-size:20px;font-weight:700;color:var(--text)}

.update-banner{display:flex;align-items:center;gap:14px;padding:16px 18px;border-radius:12px;background:linear-gradient(135deg,#EEF2FF,#F5F3FF);border:1px solid #C7D2FE;margin-bottom:16px}
.update-banner .banner-icon{flex-shrink:0;width:44px;height:44px;border-radius:12px;background:var(--gradient-primary);display:flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 8px 20px -8px rgba(37,99,235,.6)}
.update-banner .banner-icon svg{width:22px;height:22px}
.update-banner .banner-body{flex:1;min-width:0}
.update-banner .banner-title{font-weight:600;color:var(--text);margin-bottom:2px}
.update-banner .banner-meta{font-size:12px;color:var(--text-muted)}
.update-banner .banner-desc{font-size:13px;color:var(--text-muted);margin-top:6px;line-height:1.5}

.log-list{list-style:none;padding:0;margin:0}
.log-list li{padding:12px 0;border-bottom:1px dashed var(--border-soft);font-size:13px}
.log-list li:last-child{border-bottom:none}
.log-list .log-ver{display:inline-flex;align-items:center;gap:6px;font-weight:600;color:var(--primary);margin-right:10px}
.log-list .log-date{font-size:12px;color:var(--text-muted)}
.log-list .log-desc{display:block;margin-top:4px;color:var(--text-muted);line-height:1.5}

.api-input-wrap{position:relative}
.api-input-wrap .lead-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);pointer-events:none}
.api-input-wrap .lead-icon svg{width:16px;height:16px}
.api-input-wrap input{padding-left:38px}
@media(max-width:640px){.license-hero .illust{display:none}.ver-row{gap:18px}}
</style>

<!-- 顶部 Hero -->
<div class="license-hero">
    <div class="illust">
        <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <defs>
                <linearGradient id="lhShield" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="#fff" stop-opacity=".95"/>
                    <stop offset="1" stop-color="#fff" stop-opacity=".6"/>
                </linearGradient>
            </defs>
            <!-- 3D 盾牌 -->
            <path d="M100 22 L168 46 V104 C168 144 138 172 100 184 C62 172 32 144 32 104 V46 Z" fill="url(#lhShield)" stroke="#fff" stroke-width="2" opacity=".92"/>
            <!-- 内部钥匙 -->
            <circle cx="86" cy="96" r="16" fill="none" stroke="#7c3aed" stroke-width="4"/>
            <path d="M96 104 L124 132" stroke="#7c3aed" stroke-width="4" stroke-linecap="round"/>
            <path d="M116 124 L128 112 M120 138 L132 126" stroke="#7c3aed" stroke-width="4" stroke-linecap="round"/>
            <!-- 浮动圆点 -->
            <circle cx="160" cy="60" r="4" fill="#fff" opacity=".8"/>
            <circle cx="44" cy="150" r="3" fill="#fff" opacity=".6"/>
            <circle cx="150" cy="150" r="2.5" fill="#fff" opacity=".5"/>
        </svg>
    </div>
    <h2>在线授权与更新中心</h2>
    <p>主站通过授权站 <strong>qeefg.duziemd.cn</strong> 进行在线授权校验与版本更新。请填入在授权站购买的授权码以解锁完整功能，授权站负责发布新版本与安全补丁。</p>
</div>

<!-- 授权配置 -->
<div class="upd-card">
    <h3><svg class="icon" aria-hidden="true"><use href="#icon-shield"></use></svg>授权配置</h3>
    <form id="licenseForm">
        <div class="upd-grid">
            <div class="form-group">
                <label>授权站 API 地址</label>
                <div class="api-input-wrap">
                    <span class="lead-icon"><svg class="icon" aria-hidden="true"><use href="#icon-server"></use></svg></span>
                    <input type="text" name="api_url" value="<?php echo h($license['api_url']); ?>" placeholder="https://qeefg.duziemd.cn">
                </div>
                <p class="hint">授权站根地址，默认已指向 qeefg.duziemd.cn</p>
            </div>
            <div class="form-group">
                <label>授权码</label>
                <div class="api-input-wrap">
                    <span class="lead-icon"><svg class="icon" aria-hidden="true"><use href="#icon-lock"></use></svg></span>
                    <input type="text" name="auth_code" value="<?php echo h($license['auth_code']); ?>" placeholder="请输入购买的授权码" autocomplete="off">
                </div>
                <p class="hint">在授权站购买商品后获得的授权码</p>
            </div>
            <div class="form-group">
                <label>授权域名</label>
                <div class="api-input-wrap">
                    <span class="lead-icon"><svg class="icon" aria-hidden="true"><use href="#icon-globe"></use></svg></span>
                    <input type="text" name="auth_domain" value="<?php echo h($license['auth_domain'] ?: ($_SERVER['HTTP_HOST'] ?? '')); ?>" placeholder="当前站点域名">
                </div>
                <p class="hint">需要授权绑定的域名，留空使用当前域名</p>
            </div>
            <div class="form-group">
                <label>API 通信密钥（可选）</label>
                <div class="api-input-wrap">
                    <span class="lead-icon"><svg class="icon" aria-hidden="true"><use href="#icon-key"></use></svg></span>
                    <input type="text" name="api_key" value="<?php echo h($license['api_key']); ?>" placeholder="用于接口签名">
                </div>
                <p class="hint">授权站提供的 API 密钥，用于接口签名验证</p>
            </div>
        </div>
        <div class="upd-actions">
            <button type="submit" class="btn btn-primary" id="saveLicenseBtn">
                <svg class="icon" aria-hidden="true"><use href="#icon-check"></use></svg>
                保存并验证
            </button>
        </div>
    </form>
</div>

<!-- 版本与更新 -->
<div class="upd-card">
    <h3><svg class="icon" aria-hidden="true"><use href="#icon-update"></use></svg>版本与更新</h3>
    <div class="ver-row">
        <div class="ver-item">
            <div class="lbl"><svg class="icon" aria-hidden="true"><use href="#icon-tag"></use></svg>当前版本</div>
            <div class="val">v<?php echo h($currentVersion); ?></div>
        </div>
        <div class="ver-item">
            <div class="lbl"><svg class="icon" aria-hidden="true"><use href="#icon-shield"></use></svg>授权状态</div>
            <div class="val">
                <?php if ($remoteInfo && !empty($remoteInfo['license_valid'])): ?>
                    <span class="lic-status lic-ok"><svg class="icon" aria-hidden="true"><use href="#icon-check-circle"></use></svg>已授权</span>
                <?php elseif ($remoteInfo): ?>
                    <span class="lic-status lic-no"><svg class="icon" aria-hidden="true"><use href="#icon-x-circle"></use></svg>未授权</span>
                <?php else: ?>
                    <span class="lic-status lic-wait"><svg class="icon" aria-hidden="true"><use href="#icon-info"></use></svg>未检测</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="ver-item">
            <div class="lbl"><svg class="icon" aria-hidden="true"><use href="#icon-trending-up"></use></svg>最新版本</div>
            <div class="val"><?php echo h($remoteInfo['latest_version'] ?? '—'); ?></div>
        </div>
        <div class="ver-item">
            <div class="lbl"><svg class="icon" aria-hidden="true"><use href="#icon-globe"></use></svg>授权域名</div>
            <div class="val" style="font-size:15px"><?php echo h($remoteInfo['auth_domain'] ?? $license['auth_domain'] ?: '—'); ?></div>
        </div>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error show">
        <svg class="icon" aria-hidden="true"><use href="#icon-alert"></use></svg>
        <?php echo h($error); ?>
    </div>
    <?php endif; ?>

    <?php if ($remoteInfo && !empty($remoteInfo['license_msg'])): ?>
    <div class="alert alert-warning show" style="margin-bottom:16px">
        <svg class="icon" aria-hidden="true"><use href="#icon-alert"></use></svg>
        <?php echo h($remoteInfo['license_msg']); ?>
    </div>
    <?php endif; ?>

    <?php if ($remoteInfo && !empty($remoteInfo['has_update'])): ?>
    <div class="update-banner">
        <div class="banner-icon">
            <svg class="icon" aria-hidden="true"><use href="#icon-download"></use></svg>
        </div>
        <div class="banner-body">
            <div class="banner-title">发现新版本 v<?php echo h($remoteInfo['latest_version']); ?></div>
            <div class="banner-meta">发布时间：<?php echo h($remoteInfo['release_date'] ?? '—'); ?><?php if (!empty($remoteInfo['force_update'])): ?> · <span style="color:#DC2626;font-weight:600">强制更新</span><?php endif; ?></div>
            <?php if (!empty($remoteInfo['update_desc'])): ?>
            <div class="banner-desc"><?php echo h($remoteInfo['update_desc']); ?></div>
            <?php endif; ?>
        </div>
        <button type="button" class="btn btn-success" id="upgradeBtn" data-version="<?php echo h($remoteInfo['latest_version']); ?>">
            <svg class="icon" aria-hidden="true"><use href="#icon-download"></use></svg>
            立即更新
        </button>
    </div>
    <?php elseif ($remoteInfo && empty($remoteInfo['has_update'])): ?>
    <div class="alert alert-success show" style="margin-bottom:16px">
        <svg class="icon" aria-hidden="true"><use href="#icon-check-circle"></use></svg>
        当前已是最新版本，无需更新
    </div>
    <?php endif; ?>

    <div class="upd-actions">
        <button type="button" class="btn btn-outline" id="checkUpdateBtn">
            <svg class="icon" aria-hidden="true"><use href="#icon-refresh"></use></svg>
            检查更新
        </button>
    </div>
</div>

<?php if ($remoteInfo && !empty($remoteInfo['changelog'])): ?>
<div class="upd-card">
    <h3><svg class="icon" aria-hidden="true"><use href="#icon-article"></use></svg>更新日志</h3>
    <ul class="log-list">
        <?php foreach ($remoteInfo['changelog'] as $log): ?>
        <li>
            <span class="log-ver"><svg class="icon" aria-hidden="true"><use href="#icon-tag"></use></svg>v<?php echo h($log['version'] ?? ''); ?></span>
            <span class="log-date"><?php echo h($log['date'] ?? ''); ?></span>
            <span class="log-desc"><?php echo h($log['desc'] ?? ''); ?></span>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<script>
(function(){
    var form = document.getElementById('licenseForm');
    if(form){
        form.addEventListener('submit', function(e){
            e.preventDefault();
            var btn = document.getElementById('saveLicenseBtn');
            var reset = (typeof btnLoading === 'function') ? btnLoading(btn) : function(){};
            var formData = new FormData(form);
            ajax.post('<?php echo url('admin/update/saveLicense'); ?>', formData)
                .then(function(data){
                    if(data.code === 0){
                        Toast.success(data.msg || '保存并验证成功');
                        setTimeout(function(){ location.reload(); }, 900);
                    } else {
                        Toast.error(data.msg || '验证未通过');
                    }
                })
                .catch(function(err){ Toast.error('请求失败：' + (err.message || err)); })
                .finally(function(){ reset(); });
        });
    }

    var checkBtn = document.getElementById('checkUpdateBtn');
    if(checkBtn){
        checkBtn.addEventListener('click', function(){
            var reset = (typeof btnLoading === 'function') ? btnLoading(checkBtn) : function(){};
            ajax.get('<?php echo url('admin/update/check'); ?>')
                .then(function(data){
                    if(data.code === 0){
                        Toast.success(data.msg || '检查成功');
                        setTimeout(function(){ location.reload(); }, 800);
                    } else {
                        Toast.error(data.msg || '检查失败');
                    }
                })
                .catch(function(err){ Toast.error('请求失败：' + (err.message || err)); })
                .finally(function(){ reset(); });
        });
    }

    var upBtn = document.getElementById('upgradeBtn');
    if(upBtn){
        upBtn.addEventListener('click', function(){
            var version = upBtn.dataset.version;
            if(!confirm('确定要更新到 v' + version + ' 吗？\n更新前建议先备份数据库和代码。')) return;
            var reset = (typeof btnLoading === 'function') ? btnLoading(upBtn) : function(){};
            var formData = new FormData();
            formData.append('version', version);
            ajax.post('<?php echo url('admin/update/upgrade'); ?>', formData)
                .then(function(data){
                    if(data.code === 0){
                        Toast.success(data.msg || '更新成功');
                        setTimeout(function(){ location.reload(); }, 1200);
                    } else {
                        Toast.error(data.msg || '更新失败');
                    }
                })
                .catch(function(err){ Toast.error('请求失败：' + (err.message || err)); })
                .finally(function(){ reset(); });
        });
    }
})();
</script>
