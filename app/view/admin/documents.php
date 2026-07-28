<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 24px;">文档管理</h1>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-plus"/></svg><?= isset($editDoc) ? '编辑文档' : '添加文档' ?>
        </h3>
    </div>
    <form method="POST" action="/admin/saveDocument" style="max-width: 700px;">
        <?php if (isset($editDoc)): ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($editDoc['id'] ?? '') ?>">
        <?php endif; ?>
        <div class="form-group">
            <label class="form-label" for="title">文档标题</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($editDoc['title'] ?? '') ?>" placeholder="请输入文档标题" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="category">文档分类</label>
            <select class="form-control" id="category" name="category" required>
                <option value="">请选择分类</option>
                <option value="platform" <?= (isset($editDoc) && ($editDoc['category'] ?? '') === 'platform') ? 'selected' : '' ?>>平台介绍</option>
                <option value="usage" <?= (isset($editDoc) && ($editDoc['category'] ?? '') === 'usage') ? 'selected' : '' ?>>使用教程</option>
                <option value="faq" <?= (isset($editDoc) && ($editDoc['category'] ?? '') === 'faq') ? 'selected' : '' ?>>常见问题</option>
                <option value="api" <?= (isset($editDoc) && ($editDoc['category'] ?? '') === 'api') ? 'selected' : '' ?>>API文档</option>
                <option value="other" <?= (isset($editDoc) && ($editDoc['category'] ?? '') === 'other') ? 'selected' : '' ?>>其他</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" for="content">文档内容</label>
            <textarea class="form-control textarea" id="content" name="content" rows="12" placeholder="请输入文档内容" required><?= htmlspecialchars($editDoc['content'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-check"/></svg><?= isset($editDoc) ? '保存修改' : '添加文档' ?>
        </button>
        <?php if (isset($editDoc)): ?>
        <a href="/admin/documents" class="btn" style="display: inline-block; padding: 10px 24px; font-size: 14px; background: #e8e8e8; color: #333; margin-left: 8px;">取消</a>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-doc"/></svg>文档列表
        </h3>
    </div>
    <?php if (!empty($documents)): ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>文档标题</th>
                    <th>分类</th>
                    <th>创建时间</th>
                    <th>更新时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                <tr>
                    <td><?= $doc['id'] ?? '' ?></td>
                    <td><?= htmlspecialchars($doc['title'] ?? '') ?></td>
                    <td>
                        <?php
                        $categoryLabels = ['platform' => '平台介绍', 'usage' => '使用教程', 'faq' => '常见问题', 'api' => 'API文档', 'other' => '其他'];
                        $cat = $doc['category'] ?? '';
                        echo '<span class="badge badge-info">' . htmlspecialchars($categoryLabels[$cat] ?? $cat) . '</span>';
                        ?>
                    </td>
                    <td><?= htmlspecialchars($doc['created_at'] ?? '') ?></td>
                    <td><?= htmlspecialchars($doc['updated_at'] ?? '') ?></td>
                    <td>
                        <a href="/admin/documents?edit=<?= $doc['id'] ?? 0 ?>" class="btn btn-primary btn-sm">
                            <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-edit"/></svg> 编辑
                        </a>
                        <form method="POST" action="/admin/deleteDocument" data-ajax="true" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $doc['id'] ?? 0 ?>">
                            <button type="submit" class="btn btn-sm" style="background: #ff4d4f; color: #fff;" onclick="return confirm('确认删除?')">
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
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">暂无文档数据</div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>