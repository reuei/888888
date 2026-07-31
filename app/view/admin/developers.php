<div class="page-header">
    <div>
        <h1 class="page-title">开发者管理</h1>
        <div class="page-subtitle">管理平台开发者账户与API权限。</div>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openDeveloperModal()">
            <svg width="14" height="14" style="vertical-align:middle;margin-right:4px;"><use href="#i-plus"/></svg>
            添加开发者
        </button>
    </div>
</div>

<div class="admin-card">
    <div class="admin-search-bar">
        <div class="search-input">
            <svg><use href="#i-search"/></svg>
            <input type="text" class="form-control" placeholder="搜索开发者名称、邮箱...">
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>开发者</th>
                    <th>邮箱</th>
                    <th>API Key</th>
                    <th>状态</th>
                    <th>创建时间</th>
                    <th style="width:180px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($developers)): ?>
                    <?php foreach ($developers as $dev): ?>
                    <tr>
                        <td><?= $dev['id'] ?? '' ?></td>
                        <td>
                            <div class="admin-user-cell">
                                <div class="user-avatar" style="background:linear-gradient(135deg,#13c2c2,#08979c);">
                                    <?= mb_substr($dev['name'] ?? 'D', 0, 1) ?>
                                </div>
                                <div class="user-info">
                                    <div class="user-name"><?= htmlspecialchars($dev['name'] ?? '') ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--text-secondary);"><?= htmlspecialchars($dev['email'] ?? '') ?></td>
                        <td style="font-family:monospace;font-size:12px;background:var(--bg-tertiary);padding:4px 10px;border-radius:var(--radius-sm);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($dev['api_key'] ?? '') ?>"><?= htmlspecialchars(mb_substr($dev['api_key'] ?? '', 0, 12)) ?>********</td>
                        <td>
                            <?php if (($dev['status'] ?? 0) == 1): ?>
                                <span class="badge badge-success"><span class="tag-dot"></span>正常</span>
                            <?php else: ?>
                                <span class="badge badge-danger"><span class="tag-dot"></span>禁用</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($dev['created_at'] ?? '') ?></td>
                        <td>
                            <div class="admin-actions-cell">
                                <button class="btn btn-outline btn-sm" onclick="editDeveloper(<?= $dev['id'] ?? 0 ?>, '<?= htmlspecialchars($dev['name'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($dev['email'] ?? '', ENT_QUOTES) ?>', <?= $dev['status'] ?? 1 ?>)">
                                    <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-edit"/></svg>
                                    编辑
                                </button>
                                <form method="POST" action="/admin/deleteDeveloper" data-ajax="true" style="display:inline;" onsubmit="return confirm('确认删除该开发者？');">
                                    <input type="hidden" name="id" value="<?= $dev['id'] ?? 0 ?>">
                                    <button type="submit" class="btn btn-sm" style="background:var(--danger);color:#fff;">
                                        <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-trash"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><svg><use href="#i-user"/></svg></div><div class="empty-text">暂无开发者数据</div></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-modal-overlay" id="developerModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h3 id="developerModalTitle">添加开发者</h3>
            <button class="admin-modal-close" onclick="closeDeveloperModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="admin-modal-body">
            <form method="POST" action="/admin/addDeveloper" data-ajax="true" id="developerForm">
                <input type="hidden" name="id" id="dev_id">
                <div class="admin-form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">开发者名称</label>
                        <input type="text" class="form-control" name="name" id="dev_name" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">邮箱</label>
                        <input type="email" class="form-control" name="email" id="dev_email" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">状态</label>
                        <select class="form-control" name="status" id="dev_status">
                            <option value="1">正常</option>
                            <option value="0">禁用</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">保存</button>
            </form>
        </div>
    </div>
</div>

<script>
function openDeveloperModal(){
    document.getElementById('dev_id').value = '';
    document.getElementById('dev_name').value = '';
    document.getElementById('dev_email').value = '';
    document.getElementById('dev_status').value = '1';
    document.getElementById('developerModalTitle').textContent = '添加开发者';
    document.getElementById('developerForm').action = '/admin/addDeveloper';
    document.getElementById('developerModal').classList.add('show');
}
function editDeveloper(id, name, email, status){
    document.getElementById('dev_id').value = id;
    document.getElementById('dev_name').value = name;
    document.getElementById('dev_email').value = email;
    document.getElementById('dev_status').value = status;
    document.getElementById('developerModalTitle').textContent = '编辑开发者';
    document.getElementById('developerForm').action = '/admin/editDeveloper';
    document.getElementById('developerModal').classList.add('show');
}
function closeDeveloperModal(){ document.getElementById('developerModal').classList.remove('show'); }
</script>