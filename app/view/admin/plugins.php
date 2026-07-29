<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 24px;">插件管理</h1>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-puzzle"/></svg>插件列表
        </h3>
    </div>
    <?php if (!empty($plugins)): ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>名称</th>
                    <th>提交者</th>
                    <th>描述</th>
                    <th>价格</th>
                    <th>状态</th>
                    <th>下载次数</th>
                    <th>时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($plugins as $plugin): ?>
                <tr>
                    <td><?= $plugin['id'] ?? '' ?></td>
                    <td><?= htmlspecialchars($plugin['name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($plugin['submitter'] ?? '') ?></td>
                    <td style="max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($plugin['description'] ?? '') ?></td>
                    <td>¥<?= number_format($plugin['price'] ?? 0, 2) ?></td>
                    <td>
                        <?php $status = $plugin['status'] ?? 0; ?>
                        <span class="badge <?= $status === 1 ? 'badge-success' : ($status === -1 ? 'badge-danger' : 'badge-warning') ?>">
                            <?= $status === 1 ? '已通过' : ($status === -1 ? '已驳回' : '待审核') ?>
                        </span>
                    </td>
                    <td><?= $plugin['downloads'] ?? 0 ?></td>
                    <td><?= htmlspecialchars($plugin['created_at'] ?? '') ?></td>
                    <td>
                        <?php if (($plugin['status'] ?? 0) === 0): ?>
                        <form method="POST" action="/admin/approvePlugin" data-ajax="true" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $plugin['id'] ?? 0 ?>">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-check"/></svg> 通过
                            </button>
                        </form>
                        <form method="POST" action="/admin/rejectPlugin" data-ajax="true" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $plugin['id'] ?? 0 ?>">
                            <button type="submit" class="btn btn-sm" style="background: #ff4d4f; color: #fff;">
                                <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-close"/></svg> 驳回
                            </button>
                        </form>
                        <?php endif; ?>
                        <a href="javascript:void(0)" class="btn btn-outline btn-sm" onclick="openEditPluginModal(<?= $plugin['id'] ?? 0 ?>, '<?= htmlspecialchars($plugin['name'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($plugin['description'] ?? '', ENT_QUOTES) ?>', <?= $plugin['price'] ?? 0 ?>, '<?= htmlspecialchars($plugin['version'] ?? '', ENT_QUOTES) ?>')">
                            <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-edit"/></svg> 编辑
                        </a>
                        <form method="POST" action="/admin/deletePlugin" data-ajax="true" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $plugin['id'] ?? 0 ?>">
                            <button type="submit" class="btn btn-sm" style="background: #ff4d4f; color: #fff;" onclick="return confirm('确认删除该插件?')">
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
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">暂无插件数据</div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Edit Plugin Modal -->
<div class="announcement-modal" id="editPluginModal">
    <div class="am-overlay" onclick="closeEditPluginModal()"></div>
    <div class="am-dialog" style="max-width: 500px;">
        <div class="am-header">
            <h3>编辑插件</h3>
            <button class="am-close" onclick="closeEditPluginModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="am-body">
            <form method="POST" action="/admin/editPlugin" data-ajax="true" id="editPluginForm">
                <input type="hidden" name="id" id="plugin_id">
                <div class="form-group">
                    <label class="form-label" for="plugin_name">名称</label>
                    <input type="text" class="form-control" id="plugin_name" name="name" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="plugin_desc">描述</label>
                    <textarea class="form-control textarea" id="plugin_desc" name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="plugin_price">价格</label>
                    <input type="number" class="form-control" id="plugin_price" name="price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="plugin_version">版本</label>
                    <input type="text" class="form-control" id="plugin_version" name="version" placeholder="1.0.0">
                </div>
                <button type="submit" class="btn btn-primary btn-block">保存修改</button>
            </form>
        </div>
    </div>
</div>

<script>
function openEditPluginModal(id, name, desc, price, version) {
    document.getElementById('plugin_id').value = id;
    document.getElementById('plugin_name').value = name;
    document.getElementById('plugin_desc').value = desc;
    document.getElementById('plugin_price').value = price;
    document.getElementById('plugin_version').value = version;
    document.getElementById('editPluginModal').classList.add('show');
}
function closeEditPluginModal() {
    document.getElementById('editPluginModal').classList.remove('show');
}
</script>