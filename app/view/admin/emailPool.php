<div class="page-header">
    <div>
        <h1 class="page-title">邮箱池配置</h1>
        <div class="page-subtitle">配置用于发送邮件的邮箱池，支持多邮箱轮换。</div>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openEmailModal()">
            <svg width="14" height="14" style="vertical-align:middle;margin-right:4px;"><use href="#i-plus"/></svg>
            添加邮箱
        </button>
    </div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>邮箱地址</th>
                    <th>SMTP 服务器</th>
                    <th>端口</th>
                    <th>使用次数</th>
                    <th>状态</th>
                    <th>更新时间</th>
                    <th style="width:180px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($emails)): ?>
                    <?php foreach ($emails as $email): ?>
                    <tr>
                        <td><?= $email['id'] ?? '' ?></td>
                        <td><?= htmlspecialchars($email['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($email['smtp_host'] ?? '') ?></td>
                        <td><?= $email['smtp_port'] ?? 465 ?></td>
                        <td><?= $email['used_count'] ?? 0 ?></td>
                        <td>
                            <?php if (($email['status'] ?? 0) == 1): ?>
                                <span class="badge badge-success"><span class="tag-dot"></span>正常</span>
                            <?php else: ?>
                                <span class="badge badge-danger"><span class="tag-dot"></span>禁用</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($email['updated_at'] ?? '') ?></td>
                        <td>
                            <div class="admin-actions-cell">
                                <button class="btn btn-outline btn-sm" onclick="editEmail(<?= $email['id'] ?? 0 ?>, '<?= htmlspecialchars($email['email'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($email['smtp_host'] ?? '', ENT_QUOTES) ?>', <?= $email['smtp_port'] ?? 465 ?>, '<?= htmlspecialchars($email['smtp_username'] ?? '', ENT_QUOTES) ?>', <?= $email['status'] ?? 1 ?>)">
                                    <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-edit"/></svg>
                                    编辑
                                </button>
                                <form method="POST" action="/admin/deleteEmail" data-ajax="true" style="display:inline;" onsubmit="return confirm('确认删除该邮箱？');">
                                    <input type="hidden" name="id" value="<?= $email['id'] ?? 0 ?>">
                                    <button type="submit" class="btn btn-sm" style="background:var(--danger);color:#fff;">
                                        <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-trash"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8"><div class="empty-state"><div class="empty-icon"><svg><use href="#i-key"/></svg></div><div class="empty-text">暂无邮箱数据</div></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-modal-overlay" id="emailModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h3 id="emailModalTitle">添加邮箱</h3>
            <button class="admin-modal-close" onclick="closeEmailModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="admin-modal-body">
            <form method="POST" action="/admin/addEmail" data-ajax="true" id="emailForm">
                <input type="hidden" name="id" id="email_id">
                <div class="admin-form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">邮箱地址</label>
                        <input type="email" class="form-control" name="email" id="email_addr" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">SMTP 服务器</label>
                        <input type="text" class="form-control" name="smtp_host" id="email_host" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">端口</label>
                        <input type="number" class="form-control" name="smtp_port" id="email_port" value="465" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">SMTP 用户名</label>
                        <input type="text" class="form-control" name="smtp_username" id="email_username" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">SMTP 密码</label>
                        <input type="password" class="form-control" name="smtp_password" id="email_password">
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">状态</label>
                        <select class="form-control" name="status" id="email_status">
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
function openEmailModal(){
    document.getElementById('email_id').value = '';
    document.getElementById('email_addr').value = '';
    document.getElementById('email_host').value = '';
    document.getElementById('email_port').value = '465';
    document.getElementById('email_username').value = '';
    document.getElementById('email_password').value = '';
    document.getElementById('email_status').value = '1';
    document.getElementById('emailModalTitle').textContent = '添加邮箱';
    document.getElementById('emailForm').action = '/admin/addEmail';
    document.getElementById('emailModal').classList.add('show');
}
function editEmail(id, addr, host, port, username, status){
    document.getElementById('email_id').value = id;
    document.getElementById('email_addr').value = addr;
    document.getElementById('email_host').value = host;
    document.getElementById('email_port').value = port;
    document.getElementById('email_username').value = username;
    document.getElementById('email_password').value = '';
    document.getElementById('email_status').value = status;
    document.getElementById('emailModalTitle').textContent = '编辑邮箱';
    document.getElementById('emailForm').action = '/admin/editEmail';
    document.getElementById('emailModal').classList.add('show');
}
function closeEmailModal(){ document.getElementById('emailModal').classList.remove('show'); }
</script>