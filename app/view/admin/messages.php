<div class="page-header">
    <div>
        <h1 class="page-title">消息管理</h1>
        <div class="page-subtitle">发送系统公告与通知消息给用户。</div>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openMessageModal()">
            <svg width="14" height="14" style="vertical-align:middle;margin-right:4px;"><use href="#i-plus"/></svg>
            发送消息
        </button>
    </div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>标题</th>
                    <th>内容</th>
                    <th>目标用户</th>
                    <th>邮件通知</th>
                    <th>时间</th>
                    <th style="width:180px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><?= $msg['id'] ?? '' ?></td>
                        <td style="font-weight:500;"><?= htmlspecialchars($msg['title'] ?? '') ?></td>
                        <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-secondary);"><?= htmlspecialchars(mb_substr(strip_tags($msg['content'] ?? ''), 0, 50)) ?></td>
                        <td><?= ($msg['target'] ?? '') === 'all' ? '<span class="badge badge-info">全部用户</span>' : htmlspecialchars($msg['target_user'] ?? '—') ?></td>
                        <td>
                            <?php if (($msg['send_email'] ?? 0) == 1): ?>
                                <span class="badge badge-success"><span class="tag-dot"></span>已启用</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><span class="tag-dot"></span>未启用</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($msg['created_at'] ?? '') ?></td>
                        <td>
                            <div class="admin-actions-cell">
                                <button class="btn btn-outline btn-sm" onclick="editMessage(<?= $msg['id'] ?? 0 ?>, '<?= htmlspecialchars($msg['title'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($msg['content'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($msg['target'] ?? 'all', ENT_QUOTES) ?>', <?= $msg['send_email'] ?? 0 ?>)">
                                    <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-edit"/></svg>
                                    编辑
                                </button>
                                <form method="POST" action="/admin/deleteMessage" data-ajax="true" style="display:inline;" onsubmit="return confirm('确认删除该消息？');">
                                    <input type="hidden" name="id" value="<?= $msg['id'] ?? 0 ?>">
                                    <button type="submit" class="btn btn-sm" style="background:var(--danger);color:#fff;">
                                        <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-trash"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><svg><use href="#i-message"/></svg></div><div class="empty-text">暂无消息数据</div></div></td></tr>
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

<div class="admin-modal-overlay" id="messageModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h3 id="messageModalTitle">发送消息</h3>
            <button class="admin-modal-close" onclick="closeMessageModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="admin-modal-body">
            <form method="POST" action="/admin/sendMessage" data-ajax="true" id="messageForm">
                <input type="hidden" name="id" id="msg_id">
                <div class="form-group">
                    <label class="form-label">消息标题</label>
                    <input type="text" class="form-control" name="title" id="msg_title" required>
                </div>
                <div class="form-group">
                    <label class="form-label">消息内容</label>
                    <textarea class="form-control" name="content" id="msg_content" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">目标用户</label>
                    <select class="form-control" name="target" id="msg_target">
                        <option value="all">全部用户</option>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username'] ?? $u['email'] ?? '') ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                        <input type="checkbox" name="send_email" value="1" id="msg_send_email" style="width:auto;">
                        同时发送邮件通知
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-block" id="messageSubmitBtn">发送消息</button>
            </form>
        </div>
    </div>
</div>

<script>
function openMessageModal(){
    document.getElementById('msg_id').value = '';
    document.getElementById('msg_title').value = '';
    document.getElementById('msg_content').value = '';
    document.getElementById('msg_target').value = 'all';
    document.getElementById('msg_send_email').checked = false;
    document.getElementById('messageModalTitle').textContent = '发送消息';
    document.getElementById('messageForm').action = '/admin/sendMessage';
    document.getElementById('messageSubmitBtn').textContent = '发送消息';
    document.getElementById('messageModal').classList.add('show');
}
function editMessage(id, title, content, target, sendEmail){
    document.getElementById('msg_id').value = id;
    document.getElementById('msg_title').value = title;
    document.getElementById('msg_content').value = content;
    document.getElementById('msg_target').value = target;
    document.getElementById('msg_send_email').checked = sendEmail == 1;
    document.getElementById('messageModalTitle').textContent = '编辑消息';
    document.getElementById('messageForm').action = '/admin/editMessage';
    document.getElementById('messageSubmitBtn').textContent = '保存修改';
    document.getElementById('messageModal').classList.add('show');
}
function closeMessageModal(){ document.getElementById('messageModal').classList.remove('show'); }
</script>