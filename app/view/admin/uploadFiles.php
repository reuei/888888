<div class="page-header">
    <div>
        <h1 class="page-title">文件管理</h1>
        <div class="page-subtitle">管理系统上传的文件与资源。</div>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openUploadModal()">
            <svg width="14" height="14" style="vertical-align:middle;margin-right:4px;"><use href="#i-plus"/></svg>
            上传文件
        </button>
    </div>
</div>

<div class="admin-card">
    <div class="admin-search-bar">
        <div class="search-input">
            <svg><use href="#i-search"/></svg>
            <input type="text" class="form-control" placeholder="搜索文件名...">
        </div>
        <div class="admin-filters">
            <select class="form-control" style="max-width:140px;">
                <option value="">全部类型</option>
                <option value="image">图片</option>
                <option value="document">文档</option>
                <option value="other">其他</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>文件名</th>
                    <th>类型</th>
                    <th>大小</th>
                    <th>上传者</th>
                    <th>上传时间</th>
                    <th style="width:180px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($files)): ?>
                    <?php foreach ($files as $file): ?>
                    <tr>
                        <td><?= $file['id'] ?? '' ?></td>
                        <td>
                            <div class="admin-user-cell">
                                <div class="user-avatar" style="background:linear-gradient(135deg,#fa8c16,#d46b08);">
                                    <svg width="14" height="14" style="color:#fff;"><use href="#i-box"/></svg>
                                </div>
                                <div class="user-info">
                                    <div class="user-name"><?= htmlspecialchars($file['filename'] ?? '') ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge badge-info"><?= htmlspecialchars($file['type'] ?? 'other') ?></span></td>
                        <td style="font-size:13px;"><?= htmlspecialchars($file['size'] ?? '0 B') ?></td>
                        <td><?= htmlspecialchars($file['username'] ?? '') ?></td>
                        <td style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($file['created_at'] ?? '') ?></td>
                        <td>
                            <div class="admin-actions-cell">
                                <a href="<?= htmlspecialchars($file['url'] ?? '#') ?>" class="btn btn-outline btn-sm" target="_blank">
                                    <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-box"/></svg>
                                    查看
                                </a>
                                <form method="POST" action="/admin/deleteFile" data-ajax="true" style="display:inline;" onsubmit="return confirm('确认删除该文件？');">
                                    <input type="hidden" name="id" value="<?= $file['id'] ?? 0 ?>">
                                    <button type="submit" class="btn btn-sm" style="background:var(--danger);color:#fff;">
                                        <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-trash"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><svg><use href="#i-box"/></svg></div><div class="empty-text">暂无文件数据</div></div></td></tr>
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

<div class="admin-modal-overlay" id="uploadModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h3>上传文件</h3>
            <button class="admin-modal-close" onclick="closeUploadModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="admin-modal-body">
            <form method="POST" action="/admin/uploadFile" data-ajax="true" enctype="multipart/form-data" id="uploadForm">
                <div class="form-group full-width">
                    <label class="form-label">选择文件</label>
                    <input type="file" class="form-control" name="file" required>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">文件类型</label>
                    <select class="form-control" name="type">
                        <option value="image">图片</option>
                        <option value="document">文档</option>
                        <option value="other">其他</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">上传文件</button>
            </form>
        </div>
    </div>
</div>

<script>
function openUploadModal(){ document.getElementById('uploadModal').classList.add('show'); }
function closeUploadModal(){ document.getElementById('uploadModal').classList.remove('show'); }
</script>