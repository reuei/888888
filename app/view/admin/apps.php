<div class="page-header">
    <div>
        <h1 class="page-title">APP下载管理</h1>
        <div class="page-subtitle">管理 APP 下载页面中的软件、版本号、图片、下载地址等配置。</div>
    </div>
    <div class="page-actions">
        <a href="/app-download" target="_blank" class="btn btn-outline">
            <svg width="14" height="14" style="vertical-align:middle;margin-right:4px;"><use href="#i-eye"/></svg>
            预览页面
        </a>
        <button class="btn btn-primary" onclick="openAppModal()">
            <svg width="14" height="14" style="vertical-align:middle;margin-right:4px;"><use href="#i-plus"/></svg>
            添加APP
        </button>
    </div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width:60px;">ID</th>
                    <th>软件名称</th>
                    <th>版本号</th>
                    <th>标语</th>
                    <th>Android</th>
                    <th>iOS</th>
                    <th>排序</th>
                    <th>状态</th>
                    <th>更新时间</th>
                    <th style="width:180px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($apps)): ?>
                    <?php foreach ($apps as $app): ?>
                    <tr>
                        <td><?= $app['id'] ?? 0 ?></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <?php if (!empty($app['app_logo'])): ?>
                                <img src="<?= htmlspecialchars($app['app_logo']) ?>" style="width:32px;height:32px;border-radius:8px;object-fit:cover;" alt="logo">
                                <?php else: ?>
                                <div style="width:32px;height:32px;border-radius:8px;background:var(--primary-gradient);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:14px;"><?= htmlspecialchars(mb_substr($app['app_name'] ?? 'A', 0, 1)) ?></div>
                                <?php endif; ?>
                                <span style="font-weight:500;"><?= htmlspecialchars($app['app_name'] ?? '') ?></span>
                            </div>
                        </td>
                        <td><span class="badge badge-info"><?= htmlspecialchars($app['app_version'] ?? '-') ?></span></td>
                        <td style="font-size:12px;color:var(--text-secondary);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($app['app_slogan'] ?? '-') ?></td>
                        <td style="font-size:12px;">
                            <?php if (!empty($app['android_url'])): ?>
                            <span style="color:var(--success);">✓ <?= htmlspecialchars($app['android_version'] ?? $app['app_version'] ?? '') ?></span>
                            <?php else: ?>
                            <span style="color:var(--text-muted);">未配置</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;">
                            <?php if (!empty($app['ios_url'])): ?>
                            <span style="color:var(--success);">✓ <?= htmlspecialchars($app['ios_version'] ?? $app['app_version'] ?? '') ?></span>
                            <?php else: ?>
                            <span style="color:var(--text-muted);">未配置</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $app['sort_order'] ?? 0 ?></td>
                        <td>
                            <?php if (($app['status'] ?? 0) == 1): ?>
                                <span class="badge badge-success"><span class="tag-dot"></span>上架</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><span class="tag-dot"></span>下架</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($app['updated_at'] ?? '') ?></td>
                        <td>
                            <div class="admin-actions-cell">
                                <button class="btn btn-outline btn-sm" onclick='editApp(<?= json_encode($app, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS) ?>)'>
                                    <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-edit"/></svg>
                                    编辑
                                </button>
                                <form method="POST" action="/admin/deleteApp" data-ajax="true" style="display:inline;" onsubmit="return confirm('确认删除该APP？');">
                                    <input type="hidden" name="id" value="<?= $app['id'] ?? 0 ?>">
                                    <button type="submit" class="btn btn-sm" style="background:var(--danger);color:#fff;">
                                        <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-trash"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="10"><div class="empty-state"><div class="empty-icon"><svg><use href="#i-box"/></svg></div><div class="empty-text">暂无APP配置，点击右上角添加</div></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-modal-overlay" id="appModal">
    <div class="admin-modal" style="max-width:760px;">
        <div class="admin-modal-header">
            <h3 id="appModalTitle">添加APP</h3>
            <button class="admin-modal-close" onclick="closeAppModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="admin-modal-body">
            <form method="POST" action="/admin/saveApp" data-ajax="true" id="appForm">
                <input type="hidden" name="id" id="app_id">
                <div class="admin-form-grid">
                    <div class="form-group">
                        <label class="form-label">软件名称 <span style="color:var(--danger);">*</span></label>
                        <input type="text" class="form-control" name="app_name" id="app_name" placeholder="例如：商家工作台" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">版本号</label>
                        <input type="text" class="form-control" name="app_version" id="app_version" placeholder="例如：v1.0.2">
                    </div>
                    <div class="form-group">
                        <label class="form-label">排序</label>
                        <input type="number" class="form-control" name="sort_order" id="app_sort" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">状态</label>
                        <select class="form-control" name="status" id="app_status">
                            <option value="1">上架</option>
                            <option value="0">下架</option>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">宣传语</label>
                        <input type="text" class="form-control" name="app_slogan" id="app_slogan" placeholder="例如：高效运营 · 安全管理 · 随时掌握">
                    </div>
                    <div class="form-group">
                        <label class="form-label">图标URL</label>
                        <input type="text" class="form-control" name="app_logo" id="app_logo" placeholder="https://.../icon.png">
                    </div>
                    <div class="form-group">
                        <label class="form-label">截图URL</label>
                        <input type="text" class="form-control" name="app_screenshot" id="app_screenshot" placeholder="https://.../screenshot.png">
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">软件描述</label>
                        <textarea class="form-control" name="app_description" id="app_description" rows="3" placeholder="软件功能介绍..."></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Android 下载地址</label>
                        <input type="text" class="form-control" name="android_url" id="app_android_url" placeholder="https://.../app.apk">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Android 版本号</label>
                        <input type="text" class="form-control" name="android_version" id="app_android_version" placeholder="例如：v1.0.2">
                    </div>
                    <div class="form-group">
                        <label class="form-label">iOS 下载地址</label>
                        <input type="text" class="form-control" name="ios_url" id="app_ios_url" placeholder="https://apps.apple.com/...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">iOS 版本号</label>
                        <input type="text" class="form-control" name="ios_version" id="app_ios_version" placeholder="例如：v1.0.2">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top:12px;">保存配置</button>
            </form>
        </div>
    </div>
</div>

<script>
function openAppModal(){
    document.getElementById('appModalTitle').innerText = '添加APP';
    ['app_id','app_name','app_version','app_slogan','app_logo','app_screenshot','app_description','app_android_url','app_android_version','app_ios_url','app_ios_version'].forEach(function(id){
        document.getElementById(id).value = '';
    });
    document.getElementById('app_sort').value = 0;
    document.getElementById('app_status').value = 1;
    document.getElementById('appModal').classList.add('show');
}
function closeAppModal(){
    document.getElementById('appModal').classList.remove('show');
}
function editApp(app){
    document.getElementById('appModalTitle').innerText = '编辑APP - ' + (app.app_name || '');
    document.getElementById('app_id').value = app.id || '';
    document.getElementById('app_name').value = app.app_name || '';
    document.getElementById('app_version').value = app.app_version || '';
    document.getElementById('app_slogan').value = app.app_slogan || '';
    document.getElementById('app_logo').value = app.app_logo || '';
    document.getElementById('app_screenshot').value = app.app_screenshot || '';
    document.getElementById('app_description').value = app.app_description || '';
    document.getElementById('app_android_url').value = app.android_url || '';
    document.getElementById('app_android_version').value = app.android_version || '';
    document.getElementById('app_ios_url').value = app.ios_url || '';
    document.getElementById('app_ios_version').value = app.ios_version || '';
    document.getElementById('app_sort').value = app.sort_order ?? 0;
    document.getElementById('app_status').value = app.status ?? 1;
    document.getElementById('appModal').classList.add('show');
}
document.getElementById('appModal').addEventListener('click', function(e){
    if (e.target === this) closeAppModal();
});
</script>
