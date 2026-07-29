<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 24px;">消息管理</h1>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-message"/></svg>消息列表
        </h3>
        <button class="btn btn-primary" onclick="openSendModal()">
            <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-plus"/></svg>发送消息
        </button>
    </div>
    <?php if (!empty($messages)): ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>标题</th>
                    <th>内容预览</th>
                    <th>目标用户</th>
                    <th>邮件</th>
                    <th>时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                <tr>
                    <td><?= $msg['id'] ?? '' ?></td>
                    <td><?= htmlspecialchars($msg['title'] ?? '') ?></td>
                    <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars(mb_substr(strip_tags($msg['content'] ?? ''), 0, 40)) ?></td>
                    <td><?= ($msg['target'] ?? '') === 'all' ? '全部用户' : htmlspecialchars($msg['target_user'] ?? '—') ?></td>
                    <td>
                        <span class="badge <?= ($msg['send_email'] ?? 0) == 1 ? 'badge-success' : 'badge-info' ?>">
                            <?= ($msg['send_email'] ?? 0) == 1 ? '是' : '否' ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($msg['created_at'] ?? '') ?></td>
                    <td>
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="openEditModal(<?= $msg['id'] ?? 0 ?>, '<?= htmlspecialchars($msg['title'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($msg['content'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($msg['target'] ?? 'all', ENT_QUOTES) ?>', <?= $msg['send_email'] ?? 0 ?>)">
                            <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-edit"/></svg> 编辑
                        </a>
                        <form method="POST" action="/admin/deleteMessage" data-ajax="true" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $msg['id'] ?? 0 ?>">
                            <button type="submit" class="btn btn-sm" style="background: #ff4d4f; color: #fff;" onclick="return confirm('确认删除该消息?')">
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
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">暂无消息数据</div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Send/Edit Message Modal -->
<div class="announcement-modal" id="messageModal">
    <div class="am-overlay" onclick="closeMessageModal()"></div>
    <div class="am-dialog" style="max-width: 560px;">
        <div class="am-header">
            <h3 id="messageModalTitle">发送消息</h3>
            <button class="am-close" onclick="closeMessageModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="am-body">
            <form method="POST" action="/admin/sendMessage" data-ajax="true" id="messageForm">
                <input type="hidden" name="id" id="msg_id">
                <div class="form-group">
                    <label class="form-label" for="msg_title">标题</label>
                    <input type="text" class="form-control" id="msg_title" name="title" placeholder="请输入消息标题" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="msg_content">内容</label>
                    <textarea class="form-control textarea" id="msg_content" name="content" rows="5" placeholder="请输入消息内容" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="msg_target">目标用户</label>
                    <select class="form-control" id="msg_target" name="target">
                        <option value="all">全部用户</option>
                        <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?? 0 ?>"><?= htmlspecialchars($u['username'] ?? $u['email'] ?? '') ?></option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="send_email" value="1" id="msg_send_email" style="width: auto;">
                        同时发送邮件通知
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-send"/></svg>
                    <span id="messageSubmitText">发送消息</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openSendModal() {
    document.getElementById('msg_id').value = '';
    document.getElementById('msg_title').value = '';
    document.getElementById('msg_content').value = '';
    document.getElementById('msg_target').value = 'all';
    document.getElementById('msg_send_email').checked = false;
    document.getElementById('messageModalTitle').textContent = '发送消息';
    document.getElementById('messageSubmitText').textContent = '发送消息';
    document.getElementById('messageForm').action = '/admin/sendMessage';
    document.getElementById('messageModal').classList.add('show');
}

function openEditModal(id, title, content, target, sendEmail) {
    document.getElementById('msg_id').value = id;
    document.getElementById('msg_title').value = title;
    document.getElementById('msg_content').value = content;
    document.getElementById('msg_target').value = target;
    document.getElementById('msg_send_email').checked = sendEmail == 1;
    document.getElementById('messageModalTitle').textContent = '编辑消息';
    document.getElementById('messageSubmitText').textContent = '保存修改';
    document.getElementById('messageForm').action = '/admin/editMessage';
    document.getElementById('messageModal').classList.add('show');
}

function closeMessageModal() {
    document.getElementById('messageModal').classList.remove('show');
}
</script>