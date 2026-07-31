<div class="page-header">
    <div>
        <h1 class="page-title">文档管理</h1>
        <div class="page-subtitle">管理帮助文档与使用说明内容。</div>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openDocumentModal()">
            <svg width="14" height="14" style="vertical-align:middle;margin-right:4px;"><use href="#i-plus"/></svg>
            添加文档
        </button>
    </div>
</div>

<div class="admin-card">
    <div class="admin-search-bar">
        <div class="search-input">
            <svg><use href="#i-search"/></svg>
            <input type="text" class="form-control" placeholder="搜索文档标题...">
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>标题</th>
                    <th>分类</th>
                    <th>排序</th>
                    <th>状态</th>
                    <th>更新时间</th>
                    <th style="width:180px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($documents)): ?>
                    <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td><?= $doc['id'] ?? '' ?></td>
                        <td style="font-weight:500;"><?= htmlspecialchars($doc['title'] ?? '') ?></td>
                        <td><span class="badge badge-info"><?= htmlspecialchars($doc['category'] ?? '默认') ?></span></td>
                        <td><?= $doc['sort'] ?? 0 ?></td>
                        <td>
                            <?php if (($doc['status'] ?? 0) == 1): ?>
                                <span class="badge badge-success"><span class="tag-dot"></span>已发布</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><span class="tag-dot"></span>草稿</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($doc['updated_at'] ?? '') ?></td>
                        <td>
                            <div class="admin-actions-cell">
                                <button class="btn btn-outline btn-sm" onclick="editDocument(<?= $doc['id'] ?? 0 ?>, '<?= htmlspecialchars($doc['title'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($doc['category'] ?? '默认', ENT_QUOTES) ?>', <?= $doc['sort'] ?? 0 ?>, <?= $doc['status'] ?? 1 ?>)">
                                    <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-edit"/></svg>
                                    编辑
                                </button>
                                <form method="POST" action="/admin/deleteDocument" data-ajax="true" style="display:inline;" onsubmit="return confirm('确认删除该文档？');">
                                    <input type="hidden" name="id" value="<?= $doc['id'] ?? 0 ?>">
                                    <button type="submit" class="btn btn-sm" style="background:var(--danger);color:#fff;">
                                        <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-trash"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><svg><use href="#i-message"/></svg></div><div class="empty-text">暂无文档数据</div></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-modal-overlay" id="documentModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h3 id="documentModalTitle">添加文档</h3>
            <button class="admin-modal-close" onclick="closeDocumentModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="admin-modal-body">
            <form method="POST" action="/admin/addDocument" data-ajax="true" id="documentForm">
                <input type="hidden" name="id" id="doc_id">
                <div class="admin-form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">文档标题</label>
                        <input type="text" class="form-control" name="title" id="doc_title" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">分类</label>
                        <input type="text" class="form-control" name="category" id="doc_category" value="默认">
                    </div>
                    <div class="form-group">
                        <label class="form-label">排序</label>
                        <input type="number" class="form-control" name="sort" id="doc_sort" min="0" value="0">
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">文档内容</label>
                        <textarea class="form-control" name="content" id="doc_content" rows="8" placeholder="支持 Markdown 格式"></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">状态</label>
                        <select class="form-control" name="status" id="doc_status">
                            <option value="1">已发布</option>
                            <option value="0">草稿</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">保存</button>
            </form>
        </div>
    </div>
</div>

<script>
function openDocumentModal(){
    document.getElementById('doc_id').value = '';
    document.getElementById('doc_title').value = '';
    document.getElementById('doc_category').value = '默认';
    document.getElementById('doc_sort').value = '0';
    document.getElementById('doc_content').value = '';
    document.getElementById('doc_status').value = '1';
    document.getElementById('documentModalTitle').textContent = '添加文档';
    document.getElementById('documentForm').action = '/admin/addDocument';
    document.getElementById('documentModal').classList.add('show');
}
function editDocument(id, title, category, sort, status){
    document.getElementById('doc_id').value = id;
    document.getElementById('doc_title').value = title;
    document.getElementById('doc_category').value = category;
    document.getElementById('doc_sort').value = sort;
    document.getElementById('doc_status').value = status;
    document.getElementById('documentModalTitle').textContent = '编辑文档';
    document.getElementById('documentForm').action = '/admin/editDocument';
    document.getElementById('documentModal').classList.add('show');
}
function closeDocumentModal(){ document.getElementById('documentModal').classList.remove('show'); }
</script>