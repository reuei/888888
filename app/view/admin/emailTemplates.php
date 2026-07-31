<div class="page-header">
    <div>
        <h1 class="page-title">邮件模板</h1>
        <div class="page-subtitle">管理系统邮件通知的模板内容与变量。</div>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openTemplateModal()">
            <svg width="14" height="14" style="vertical-align:middle;margin-right:4px;"><use href="#i-plus"/></svg>
            添加模板
        </button>
    </div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>模板名称</th>
                    <th>模板代码</th>
                    <th>主题</th>
                    <th>状态</th>
                    <th>更新时间</th>
                    <th style="width:180px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($templates)): ?>
                    <?php foreach ($templates as $tpl): ?>
                    <tr>
                        <td><?= $tpl['id'] ?? '' ?></td>
                        <td style="font-weight:500;"><?= htmlspecialchars($tpl['name'] ?? '') ?></td>
                        <td style="font-family:monospace;font-size:12px;background:var(--bg-tertiary);padding:4px 10px;border-radius:var(--radius-sm);"><?= htmlspecialchars($tpl['code'] ?? '') ?></td>
                        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text-secondary);"><?= htmlspecialchars($tpl['subject'] ?? '') ?></td>
                        <td>
                            <?php if (($tpl['status'] ?? 0) == 1): ?>
                                <span class="badge badge-success"><span class="tag-dot"></span>启用</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><span class="tag-dot"></span>禁用</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($tpl['updated_at'] ?? '') ?></td>
                        <td>
                            <div class="admin-actions-cell">
                                <button class="btn btn-outline btn-sm" onclick="editTemplate(<?= $tpl['id'] ?? 0 ?>, '<?= htmlspecialchars($tpl['name'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($tpl['code'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($tpl['subject'] ?? '', ENT_QUOTES) ?>', <?= $tpl['status'] ?? 1 ?>)">
                                    <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-edit"/></svg>
                                    编辑
                                </button>
                                <form method="POST" action="/admin/deleteTemplate" data-ajax="true" style="display:inline;" onsubmit="return confirm('确认删除该模板？');">
                                    <input type="hidden" name="id" value="<?= $tpl['id'] ?? 0 ?>">
                                    <button type="submit" class="btn btn-sm" style="background:var(--danger);color:#fff;">
                                        <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-trash"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><svg><use href="#i-message"/></svg></div><div class="empty-text">暂无模板数据</div></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-modal-overlay" id="templateModal">
    <div class="admin-modal" style="max-width:720px;">
        <div class="admin-modal-header">
            <h3 id="templateModalTitle">添加模板</h3>
            <button class="admin-modal-close" onclick="closeTemplateModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="admin-modal-body">
            <form method="POST" action="/admin/addTemplate" data-ajax="true" id="templateForm">
                <input type="hidden" name="id" id="tpl_id">
                <div class="admin-form-grid">
                    <div class="form-group">
                        <label class="form-label">模板名称</label>
                        <input type="text" class="form-control" name="name" id="tpl_name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">模板代码</label>
                        <input type="text" class="form-control" name="code" id="tpl_code" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">邮件主题</label>
                        <input type="text" class="form-control" name="subject" id="tpl_subject" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">模板内容 (支持变量如 {{username}}, {{email}})</label>
                        <textarea class="form-control" name="content" id="tpl_content" rows="8" required></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">状态</label>
                        <select class="form-control" name="status" id="tpl_status">
                            <option value="1">启用</option>
                            <option value="0">禁用</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">保存模板</button>
            </form>
        </div>
    </div>
</div>

<script>
function openTemplateModal(){
    document.getElementById('tpl_id').value = '';
    document.getElementById('tpl_name').value = '';
    document.getElementById('tpl_code').value = '';
    document.getElementById('tpl_subject').value = '';
    document.getElementById('tpl_content').value = '';
    document.getElementById('tpl_status').value = '1';
    document.getElementById('templateModalTitle').textContent = '添加模板';
    document.getElementById('templateForm').action = '/admin/addTemplate';
    document.getElementById('templateModal').classList.add('show');
}
function editTemplate(id, name, code, subject, status){
    document.getElementById('tpl_id').value = id;
    document.getElementById('tpl_name').value = name;
    document.getElementById('tpl_code').value = code;
    document.getElementById('tpl_subject').value = subject;
    document.getElementById('tpl_status').value = status;
    document.getElementById('templateModalTitle').textContent = '编辑模板';
    document.getElementById('templateForm').action = '/admin/editTemplate';
    document.getElementById('templateModal').classList.add('show');
}
function closeTemplateModal(){ document.getElementById('templateModal').classList.remove('show'); }
</script>