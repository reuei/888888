<div class="page-header">
    <div>
        <h1 class="page-title">插件管理</h1>
        <div class="page-subtitle">管理系统中安装的插件与扩展模块。</div>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openPluginModal()">
            <svg width="14" height="14" style="vertical-align:middle;margin-right:4px;"><use href="#i-plus"/></svg>
            安装插件
        </button>
    </div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>插件名称</th>
                    <th>版本</th>
                    <th>描述</th>
                    <th>状态</th>
                    <th>安装时间</th>
                    <th style="width:180px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($plugins)): ?>
                    <?php foreach ($plugins as $plugin): ?>
                    <tr>
                        <td><?= $plugin['id'] ?? '' ?></td>
                        <td>
                            <div class="admin-user-cell">
                                <div class="user-avatar" style="background:linear-gradient(135deg,#722ed1,#531dab);">
                                    <svg width="14" height="14" style="color:#fff;"><use href="#i-box"/></svg>
                                </div>
                                <div class="user-info">
                                    <div class="user-name"><?= htmlspecialchars($plugin['name'] ?? '') ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge badge-info"><?= htmlspecialchars($plugin['version'] ?? '') ?></span></td>
                        <td style="color:var(--text-secondary);max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($plugin['description'] ?? '') ?></td>
                        <td>
                            <?php if (($plugin['status'] ?? 0) == 1): ?>
                                <span class="badge badge-success"><span class="tag-dot"></span>已启用</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><span class="tag-dot"></span>已禁用</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($plugin['created_at'] ?? '') ?></td>
                        <td>
                            <div class="admin-actions-cell">
                                <form method="POST" action="/admin/togglePlugin" data-ajax="true" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $plugin['id'] ?? 0 ?>">
                                    <input type="hidden" name="status" value="<?= ($plugin['status'] ?? 0) == 1 ? 0 : 1 ?>">
                                    <button type="submit" class="btn btn-outline btn-sm"><?= ($plugin['status'] ?? 0) == 1 ? '禁用' : '启用' ?></button>
                                </form>
                                <form method="POST" action="/admin/deletePlugin" data-ajax="true" style="display:inline;" onsubmit="return confirm('确认删除该插件？');">
                                    <input type="hidden" name="id" value="<?= $plugin['id'] ?? 0 ?>">
                                    <button type="submit" class="btn btn-sm" style="background:var(--danger);color:#fff;">
                                        <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-trash"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><svg><use href="#i-box"/></svg></div><div class="empty-text">暂无插件数据</div></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-modal-overlay" id="pluginModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h3>安装插件</h3>
            <button class="admin-modal-close" onclick="closePluginModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="admin-modal-body">
            <form method="POST" action="/admin/addPlugin" data-ajax="true">
                <div class="admin-form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">插件名称</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">版本号</label>
                        <input type="text" class="form-control" name="version" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">描述</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">插件目录</label>
                        <input type="text" class="form-control" name="path" placeholder="如 plugins/my-plugin" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">安装插件</button>
            </form>
        </div>
    </div>
</div>

<script>
function openPluginModal(){ document.getElementById('pluginModal').classList.add('show'); }
function closePluginModal(){ document.getElementById('pluginModal').classList.remove('show'); }
</script>