<div class="page-header">
    <div>
        <h1 class="page-title">用户管理</h1>
        <div class="page-subtitle">管理平台所有注册用户，查看用户详情与账户状态。</div>
    </div>
</div>

<div class="admin-card">
    <div class="admin-search-bar">
        <div class="search-input">
            <svg><use href="#i-search"/></svg>
            <input type="text" class="form-control" id="searchUser" placeholder="搜索用户名、邮箱...">
        </div>
        <div class="admin-filters">
            <select class="form-control" id="filterStatus" style="max-width:140px;">
                <option value="">全部状态</option>
                <option value="1">正常</option>
                <option value="0">禁用</option>
            </select>
            <button class="btn btn-primary" onclick="openAddUserModal()">
                <svg width="14" height="14" style="vertical-align:middle;margin-right:4px;"><use href="#i-plus"/></svg>
                添加用户
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>用户</th>
                    <th>邮箱</th>
                    <th>余额</th>
                    <th>状态</th>
                    <th>注册时间</th>
                    <th style="width:140px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?? '' ?></td>
                        <td>
                            <div class="admin-user-cell">
                                <div class="user-avatar"><?= mb_substr($u['username'] ?? 'U', 0, 1) ?></div>
                                <div class="user-info">
                                    <div class="user-name"><?= htmlspecialchars($u['username'] ?? '') ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="color:var(--text-secondary);"><?= htmlspecialchars($u['email'] ?? '') ?></td>
                        <td>¥<?= number_format($u['balance'] ?? 0, 2) ?></td>
                        <td>
                            <?php if (($u['status'] ?? 0) == 1): ?>
                                <span class="badge badge-success"><span class="tag-dot"></span>正常</span>
                            <?php else: ?>
                                <span class="badge badge-danger"><span class="tag-dot"></span>禁用</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:13px;color:var(--text-secondary);"><?= htmlspecialchars($u['created_at'] ?? '') ?></td>
                        <td>
                            <div class="admin-actions-cell">
                                <button class="btn btn-outline btn-sm" onclick="editUser(<?= $u['id'] ?? 0 ?>, '<?= htmlspecialchars($u['username'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES) ?>', <?= $u['balance'] ?? 0 ?>, <?= $u['status'] ?? 1 ?>)">
                                    <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-edit"/></svg>
                                    编辑
                                </button>
                                <form method="POST" action="/admin/deleteUser" data-ajax="true" style="display:inline;" onsubmit="return confirm('确认删除该用户？');">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?? 0 ?>">
                                    <button type="submit" class="btn btn-sm" style="background:var(--danger);color:#fff;">
                                        <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-trash"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><svg><use href="#i-user"/></svg></div><div class="empty-text">暂无用户数据</div></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (($totalPages ?? 1) > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<div class="admin-modal-overlay" id="userModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h3 id="userModalTitle">添加用户</h3>
            <button class="admin-modal-close" onclick="closeUserModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="admin-modal-body">
            <form method="POST" action="/admin/addUser" data-ajax="true" id="userForm">
                <input type="hidden" name="id" id="user_id">
                <div class="admin-form-grid">
                    <div class="form-group">
                        <label class="form-label">用户名</label>
                        <input type="text" class="form-control" name="username" id="user_username" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">邮箱</label>
                        <input type="email" class="form-control" name="email" id="user_email" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">密码</label>
                        <input type="password" class="form-control" name="password" id="user_password" placeholder="留空则不修改">
                    </div>
                    <div class="form-group">
                        <label class="form-label">余额</label>
                        <input type="number" class="form-control" name="balance" id="user_balance" step="0.01" value="0">
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">状态</label>
                        <select class="form-control" name="status" id="user_status">
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
function openAddUserModal(){
    document.getElementById('user_id').value = '';
    document.getElementById('user_username').value = '';
    document.getElementById('user_email').value = '';
    document.getElementById('user_password').value = '';
    document.getElementById('user_balance').value = '0';
    document.getElementById('user_status').value = '1';
    document.getElementById('userModalTitle').textContent = '添加用户';
    document.getElementById('userForm').action = '/admin/addUser';
    document.getElementById('userModal').classList.add('show');
}
function editUser(id, username, email, balance, status){
    document.getElementById('user_id').value = id;
    document.getElementById('user_username').value = username;
    document.getElementById('user_email').value = email;
    document.getElementById('user_balance').value = balance;
    document.getElementById('user_status').value = status;
    document.getElementById('userModalTitle').textContent = '编辑用户';
    document.getElementById('userForm').action = '/admin/editUser';
    document.getElementById('userModal').classList.add('show');
}
function closeUserModal(){ document.getElementById('userModal').classList.remove('show'); }
</script>