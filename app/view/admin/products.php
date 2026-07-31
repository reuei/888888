<div class="page-header">
    <div>
        <h1 class="page-title">产品管理</h1>
        <div class="page-subtitle">管理产品信息、价格与上下架状态。</div>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openProductModal()">
            <svg width="14" height="14" style="vertical-align:middle;margin-right:4px;"><use href="#i-plus"/></svg>
            添加产品
        </button>
    </div>
</div>

<div class="admin-card">
    <div class="admin-search-bar">
        <div class="search-input">
            <svg><use href="#i-search"/></svg>
            <input type="text" class="form-control" placeholder="搜索产品名称...">
        </div>
        <div class="admin-filters">
            <select class="form-control" style="max-width:140px;">
                <option value="">全部类型</option>
                <option value="software">软件</option>
                <option value="service">服务</option>
                <option value="other">其他</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>产品名称</th>
                    <th>类型</th>
                    <th>价格</th>
                    <th>有效期</th>
                    <th>排序</th>
                    <th>状态</th>
                    <th>创建时间</th>
                    <th style="width:180px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?? '' ?></td>
                        <td>
                            <div class="admin-user-cell">
                                <div class="user-avatar" style="background:linear-gradient(135deg,#52c41a,#389e0d);">
                                    <svg width="14" height="14" style="color:#fff;"><use href="#i-box"/></svg>
                                </div>
                                <div class="user-info">
                                    <div class="user-name"><?= htmlspecialchars($product['name'] ?? '') ?></div>
                                    <div class="user-email" style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($product['description'] ?? '') ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span class="badge badge-info"><?= htmlspecialchars($product['type'] ?? 'software') ?></span></td>
                        <td style="font-weight:600;color:var(--primary);">¥<?= number_format($product['price'] ?? 0, 2) ?></td>
                        <td style="font-size:13px;color:var(--text-secondary);"><?= ($product['duration'] ?? 0) > 0 ? ($product['duration'] . ' 天') : '永久' ?></td>
                        <td><?= $product['sort'] ?? 0 ?></td>
                        <td>
                            <?php if (($product['status'] ?? 0) == 1): ?>
                                <span class="badge badge-success"><span class="tag-dot"></span>上架</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><span class="tag-dot"></span>下架</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($product['created_at'] ?? '') ?></td>
                        <td>
                            <div class="admin-actions-cell">
                                <button class="btn btn-outline btn-sm" onclick="editProduct(<?= $product['id'] ?? 0 ?>, '<?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($product['type'] ?? 'software', ENT_QUOTES) ?>', <?= $product['price'] ?? 0 ?>, <?= $product['duration'] ?? 0 ?>, <?= $product['sort'] ?? 0 ?>, <?= $product['status'] ?? 1 ?>)">
                                    <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-edit"/></svg>
                                    编辑
                                </button>
                                <form method="POST" action="/admin/deleteProduct" data-ajax="true" style="display:inline;" onsubmit="return confirm('确认删除该产品？');">
                                    <input type="hidden" name="id" value="<?= $product['id'] ?? 0 ?>">
                                    <button type="submit" class="btn btn-sm" style="background:var(--danger);color:#fff;">
                                        <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-trash"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9"><div class="empty-state"><div class="empty-icon"><svg><use href="#i-box"/></svg></div><div class="empty-text">暂无产品数据</div></div></td></tr>
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

<div class="admin-modal-overlay" id="productModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h3 id="productModalTitle">添加产品</h3>
            <button class="admin-modal-close" onclick="closeProductModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="admin-modal-body">
            <form method="POST" action="/admin/addProduct" data-ajax="true" id="productForm">
                <input type="hidden" name="id" id="product_id">
                <div class="admin-form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">产品名称</label>
                        <input type="text" class="form-control" name="name" id="product_name" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">产品描述</label>
                        <textarea class="form-control" name="description" id="product_desc" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">产品类型</label>
                        <select class="form-control" name="type" id="product_type">
                            <option value="software">软件</option>
                            <option value="service">服务</option>
                            <option value="other">其他</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">价格</label>
                        <input type="number" class="form-control" name="price" id="product_price" step="0.01" min="0" value="0" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">有效期（天，0为永久）</label>
                        <input type="number" class="form-control" name="duration" id="product_duration" min="0" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">排序</label>
                        <input type="number" class="form-control" name="sort" id="product_sort" min="0" value="0">
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">状态</label>
                        <select class="form-control" name="status" id="product_status">
                            <option value="1">上架</option>
                            <option value="0">下架</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">保存</button>
            </form>
        </div>
    </div>
</div>

<script>
function openProductModal(){
    document.getElementById('product_id').value = '';
    document.getElementById('product_name').value = '';
    document.getElementById('product_desc').value = '';
    document.getElementById('product_type').value = 'software';
    document.getElementById('product_price').value = '0';
    document.getElementById('product_duration').value = '0';
    document.getElementById('product_sort').value = '0';
    document.getElementById('product_status').value = '1';
    document.getElementById('productModalTitle').textContent = '添加产品';
    document.getElementById('productForm').action = '/admin/addProduct';
    document.getElementById('productModal').classList.add('show');
}
function editProduct(id, name, desc, type, price, duration, sort, status){
    document.getElementById('product_id').value = id;
    document.getElementById('product_name').value = name;
    document.getElementById('product_desc').value = desc;
    document.getElementById('product_type').value = type;
    document.getElementById('product_price').value = price;
    document.getElementById('product_duration').value = duration;
    document.getElementById('product_sort').value = sort;
    document.getElementById('product_status').value = status;
    document.getElementById('productModalTitle').textContent = '编辑产品';
    document.getElementById('productForm').action = '/admin/editProduct';
    document.getElementById('productModal').classList.add('show');
}
function closeProductModal(){ document.getElementById('productModal').classList.remove('show'); }
</script>