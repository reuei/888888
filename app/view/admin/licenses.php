<div class="page-header">
    <div>
        <h1 class="page-title">授权管理</h1>
        <div class="page-subtitle">管理系统中所有授权码，查看授权状态与绑定信息。</div>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openLicenseModal()">
            <svg width="14" height="14" style="vertical-align:middle;margin-right:4px;"><use href="#i-plus"/></svg>
            生成授权
        </button>
    </div>
</div>

<div class="admin-card">
    <div class="admin-search-bar">
        <div class="search-input">
            <svg><use href="#i-search"/></svg>
            <input type="text" class="form-control" placeholder="搜索授权码、产品、用户...">
        </div>
        <div class="admin-filters">
            <select class="form-control" style="max-width:140px;">
                <option value="">全部状态</option>
                <option value="1">有效</option>
                <option value="0">无效</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>授权码</th>
                    <th>产品</th>
                    <th>用户</th>
                    <th>绑定域名</th>
                    <th>状态</th>
                    <th>到期时间</th>
                    <th>创建时间</th>
                    <th style="width:100px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($licenses)): ?>
                    <?php foreach ($licenses as $license): ?>
                    <tr>
                        <td><?= $license['id'] ?? '' ?></td>
                        <td style="font-family:monospace;font-size:12px;background:var(--bg-tertiary);padding:4px 10px;border-radius:var(--radius-sm);"><?= htmlspecialchars($license['license_key'] ?? '') ?></td>
                        <td><?= htmlspecialchars($license['product_name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($license['username'] ?? '') ?></td>
                        <td style="color:var(--text-secondary);"><?= htmlspecialchars($license['domain'] ?? '未绑定') ?></td>
                        <td>
                            <?php if (($license['status'] ?? 0) == 1): ?>
                                <span class="badge badge-success"><span class="tag-dot"></span>有效</span>
                            <?php else: ?>
                                <span class="badge badge-danger"><span class="tag-dot"></span>无效</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:13px;"><?= htmlspecialchars($license['expires_at'] ?? '永久') ?></td>
                        <td style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($license['created_at'] ?? '') ?></td>
                        <td>
                            <div class="admin-actions-cell">
                                <form method="POST" action="/admin/deleteLicense" data-ajax="true" style="display:inline;" onsubmit="return confirm('确认删除该授权？');">
                                    <input type="hidden" name="id" value="<?= $license['id'] ?? 0 ?>">
                                    <button type="submit" class="btn btn-sm" style="background:var(--danger);color:#fff;">
                                        <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-trash"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9"><div class="empty-state"><div class="empty-icon"><svg><use href="#i-key"/></svg></div><div class="empty-text">暂无授权数据</div></div></td></tr>
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

<div class="admin-modal-overlay" id="licenseModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h3>生成授权</h3>
            <button class="admin-modal-close" onclick="closeLicenseModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="admin-modal-body">
            <form method="POST" action="/admin/addLicense" data-ajax="true">
                <div class="admin-form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">选择产品</label>
                        <select class="form-control" name="product_id" required>
                            <option value="">请选择产品</option>
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $p): ?>
                                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name'] ?? '') ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">绑定用户</label>
                        <select class="form-control" name="user_id" required>
                            <option value="">请选择用户</option>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username'] ?? '') ?> (<?= htmlspecialchars($u['email'] ?? '') ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">有效期（天）</label>
                        <input type="number" class="form-control" name="duration" min="0" value="365">
                    </div>
                    <div class="form-group">
                        <label class="form-label">绑定域名</label>
                        <input type="text" class="form-control" name="domain" placeholder="可选">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">生成授权</button>
            </form>
        </div>
    </div>
</div>

<script>
function openLicenseModal(){ document.getElementById('licenseModal').classList.add('show'); }
function closeLicenseModal(){ document.getElementById('licenseModal').classList.remove('show'); }
</script>