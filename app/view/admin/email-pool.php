<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 24px;">邮箱池配置</h1>

<!-- Settings Section -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-settings"/></svg>发送设置
        </h3>
    </div>
    <form method="POST" action="/admin/saveEmailPoolSettings" data-ajax="true" style="max-width: 500px;">
        <div class="form-group">
            <label class="form-label">发送模式</label>
            <div style="display: flex; gap: 24px; margin-top: 4px;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                    <input type="radio" name="send_mode" value="random" <?= ($emailPoolSettings['send_mode'] ?? 'random') === 'random' ? 'checked' : '' ?> style="width: auto;">
                    随机发送
                </label>
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer;">
                    <input type="radio" name="send_mode" value="sequential" <?= ($emailPoolSettings['send_mode'] ?? '') === 'sequential' ? 'checked' : '' ?> style="width: auto;">
                    顺序发送
                </label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-check"/></svg>保存设置
        </button>
    </form>
</div>

<!-- Test Email Section -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-send"/></svg>测试发送
        </h3>
    </div>
    <form method="POST" action="/admin/testEmailPool" data-ajax="true" style="max-width: 500px;">
        <div class="form-group">
            <label class="form-label" for="test_email">测试接收邮箱</label>
            <div style="display: flex; gap: 8px;">
                <input type="email" class="form-control" id="test_email" name="test_email" placeholder="请输入测试邮箱地址" required style="flex: 1;">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-send"/></svg>测试发送
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Email Pool List -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-mail"/></svg>邮箱列表
        </h3>
        <button class="btn btn-primary" onclick="openAddEmailModal()">
            <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-plus"/></svg>添加邮箱
        </button>
    </div>
    <?php if (!empty($emailPool)): ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>邮箱</th>
                    <th>SMTP 主机</th>
                    <th>端口</th>
                    <th>加密</th>
                    <th>状态</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emailPool as $email): ?>
                <tr>
                    <td><?= $email['id'] ?? '' ?></td>
                    <td><?= htmlspecialchars($email['email'] ?? '') ?></td>
                    <td><?= htmlspecialchars($email['smtp_host'] ?? '') ?></td>
                    <td><?= $email['smtp_port'] ?? '' ?></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars(strtoupper($email['encryption'] ?? 'tls')) ?></span></td>
                    <td>
                        <span class="badge <?= ($email['status'] ?? 1) == 1 ? 'badge-success' : 'badge-danger' ?>">
                            <?= ($email['status'] ?? 1) == 1 ? '启用' : '禁用' ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($email['created_at'] ?? '') ?></td>
                    <td>
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="openEditEmailModal(<?= $email['id'] ?? 0 ?>, '<?= htmlspecialchars($email['email'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($email['password'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($email['smtp_host'] ?? '', ENT_QUOTES) ?>', '<?= $email['smtp_port'] ?? 465 ?>', '<?= htmlspecialchars($email['encryption'] ?? 'tls', ENT_QUOTES) ?>')">
                            <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-edit"/></svg> 编辑
                        </a>
                        <form method="POST" action="/admin/deleteEmailPool" data-ajax="true" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $email['id'] ?? 0 ?>">
                            <button type="submit" class="btn btn-sm" style="background: #ff4d4f; color: #fff;" onclick="return confirm('确认删除该邮箱?')">
                                <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-trash"/></svg> 删除
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">暂无邮箱数据</div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Add/Edit Email Modal -->
<div class="announcement-modal" id="emailModal">
    <div class="am-overlay" onclick="closeEmailModal()"></div>
    <div class="am-dialog" style="max-width: 500px;">
        <div class="am-header">
            <h3 id="emailModalTitle">添加邮箱</h3>
            <button class="am-close" onclick="closeEmailModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="am-body">
            <form method="POST" action="/admin/addEmailPool" data-ajax="true" id="emailForm">
                <input type="hidden" name="id" id="email_id">
                <div class="form-group">
                    <label class="form-label" for="email_addr">邮箱地址</label>
                    <input type="email" class="form-control" id="email_addr" name="email" placeholder="example@domain.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email_password">密码</label>
                    <input type="password" class="form-control" id="email_password" name="password" placeholder="请输入邮箱密码或授权码" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email_smtp_host">SMTP 主机</label>
                    <input type="text" class="form-control" id="email_smtp_host" name="smtp_host" placeholder="smtp.example.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="email_smtp_port">端口</label>
                    <input type="number" class="form-control" id="email_smtp_port" name="smtp_port" value="465" placeholder="465" required style="max-width: 150px;">
                </div>
                <div class="form-group">
                    <label class="form-label" for="email_encryption">加密方式</label>
                    <select class="form-control" id="email_encryption" name="encryption">
                        <option value="tls">TLS</option>
                        <option value="ssl">SSL</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <span id="emailSubmitText">添加邮箱</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openAddEmailModal() {
    document.getElementById('email_id').value = '';
    document.getElementById('email_addr').value = '';
    document.getElementById('email_password').value = '';
    document.getElementById('email_smtp_host').value = '';
    document.getElementById('email_smtp_port').value = '465';
    document.getElementById('email_encryption').value = 'tls';
    document.getElementById('emailModalTitle').textContent = '添加邮箱';
    document.getElementById('emailSubmitText').textContent = '添加邮箱';
    document.getElementById('emailForm').action = '/admin/addEmailPool';
    document.getElementById('emailModal').classList.add('show');
}

function openEditEmailModal(id, email, password, host, port, encryption) {
    document.getElementById('email_id').value = id;
    document.getElementById('email_addr').value = email;
    document.getElementById('email_password').value = password;
    document.getElementById('email_smtp_host').value = host;
    document.getElementById('email_smtp_port').value = port;
    document.getElementById('email_encryption').value = encryption;
    document.getElementById('emailModalTitle').textContent = '编辑邮箱';
    document.getElementById('emailSubmitText').textContent = '保存修改';
    document.getElementById('emailForm').action = '/admin/editEmailPool';
    document.getElementById('emailModal').classList.add('show');
}

function closeEmailModal() {
    document.getElementById('emailModal').classList.remove('show');
}
</script>