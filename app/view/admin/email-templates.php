<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 24px;">邮件模板</h1>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-file-text"/></svg>模板列表
        </h3>
    </div>
    <?php if (!empty($templates)): ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>模板名称</th>
                    <th>模板代码</th>
                    <th>邮件主题</th>
                    <th>更新时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($templates as $tpl): ?>
                <tr>
                    <td><?= $tpl['id'] ?? '' ?></td>
                    <td><?= htmlspecialchars($tpl['name'] ?? '') ?></td>
                    <td><code style="background: var(--bg-tertiary); padding: 2px 8px; border-radius: 4px; font-size: 12px;"><?= htmlspecialchars($tpl['code'] ?? '') ?></code></td>
                    <td><?= htmlspecialchars($tpl['subject'] ?? '') ?></td>
                    <td><?= htmlspecialchars($tpl['updated_at'] ?? '') ?></td>
                    <td>
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="toggleEdit(<?= $tpl['id'] ?? 0 ?>)">
                            <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-edit"/></svg> 编辑
                        </a>
                    </td>
                </tr>
                <tr id="editRow<?= $tpl['id'] ?? 0 ?>" style="display: none;">
                    <td colspan="6" style="padding: 20px 24px; background: var(--bg-tertiary);">
                        <form method="POST" action="/admin/saveEmailTemplate" data-ajax="true">
                            <input type="hidden" name="id" value="<?= $tpl['id'] ?? 0 ?>">
                            <div class="form-group">
                                <label class="form-label" for="tpl_subject_<?= $tpl['id'] ?? 0 ?>">邮件主题</label>
                                <input type="text" class="form-control" id="tpl_subject_<?= $tpl['id'] ?? 0 ?>" name="subject" value="<?= htmlspecialchars($tpl['subject'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="tpl_content_<?= $tpl['id'] ?? 0 ?>">邮件内容 (HTML)</label>
                                <textarea class="form-control textarea" id="tpl_content_<?= $tpl['id'] ?? 0 ?>" name="content" rows="12" style="font-family: monospace; font-size: 13px;" required><?= htmlspecialchars($tpl['content'] ?? '') ?></textarea>
                            </div>
                            <div style="display: flex; gap: 8px;">
                                <button type="submit" class="btn btn-primary">
                                    <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-check"/></svg>保存
                                </button>
                                <button type="button" class="btn btn-ghost" onclick="toggleEdit(<?= $tpl['id'] ?? 0 ?>)">取消</button>
                            </div>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">暂无邮件模板数据</div>
    <?php endif; ?>
</div>

<script>
function toggleEdit(id) {
    var row = document.getElementById('editRow' + id);
    if (row.style.display === 'none' || row.style.display === '') {
        row.style.display = 'table-row';
    } else {
        row.style.display = 'none';
    }
}
</script>