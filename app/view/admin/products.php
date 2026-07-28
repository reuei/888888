<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 24px;">产品管理</h1>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-plus"/></svg>添加产品
        </h3>
    </div>
    <form method="POST" action="/admin/addProduct" data-ajax="true" style="max-width: 600px;">
        <div class="form-group">
            <label class="form-label" for="name">产品名称</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="请输入产品名称" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="description">产品描述</label>
            <textarea class="form-control textarea" id="description" name="description" rows="3" placeholder="请输入产品描述"></textarea>
        </div>
        <div class="form-group">
            <label class="form-label" for="type">产品类型</label>
            <select class="form-control" id="type" name="type">
                <option value="software">软件</option>
                <option value="service">服务</option>
                <option value="other">其他</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label" for="price">价格</label>
            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" placeholder="0.00" required>
        </div>
        <div class="form-group">
            <label class="form-label" for="duration">有效期（天）</label>
            <input type="number" class="form-control" id="duration" name="duration" value="0" min="0" style="max-width: 120px;" placeholder="0=永久">
        </div>
        <div class="form-group">
            <label class="form-label" for="sort">排序</label>
            <input type="number" class="form-control" id="sort" name="sort" value="0" min="0" style="max-width: 120px;">
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-plus"/></svg>添加产品
        </button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-box"/></svg>产品列表
        </h3>
    </div>
    <?php if (!empty($products)): ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>产品名称</th>
                    <th>价格</th>
                    <th>状态</th>
                    <th>排序</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?? '' ?></td>
                    <td><?= htmlspecialchars($product['name'] ?? '') ?></td>
                    <td>¥<?= number_format($product['price'] ?? 0, 2) ?></td>
                    <td><span class="badge <?= ($product['status'] ?? 0) == 1 ? 'badge-success' : 'badge-warning' ?>"><?= ($product['status'] ?? 0) == 1 ? '上架' : '下架' ?></span></td>
                    <td><?= $product['sort'] ?? 0 ?></td>
                    <td><?= htmlspecialchars($product['created_at'] ?? '') ?></td>
                    <td>
                        <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="editProduct(<?= $product['id'] ?? 0 ?>, '<?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($product['type'] ?? 'software') ?>', <?= $product['price'] ?? 0 ?>, <?= $product['duration'] ?? 0 ?>, <?= $product['sort'] ?? 0 ?>, <?= $product['status'] ?? 1 ?>)">
                            <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-edit"/></svg> 编辑
                        </a>
                        <form method="POST" action="/admin/deleteProduct" data-ajax="true" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $product['id'] ?? 0 ?>">
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
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">暂无产品数据</div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Edit Product Modal -->
<div class="announcement-modal" id="editProductModal">
    <div class="am-overlay" onclick="closeEditModal()"></div>
    <div class="am-dialog" style="max-width: 500px;">
        <div class="am-header">
            <h3>编辑产品</h3>
            <button class="am-close" onclick="closeEditModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="am-body">
            <form method="POST" action="/admin/editProduct" data-ajax="true" id="editProductForm">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label class="form-label" for="edit_name">产品名称</label>
                    <input type="text" class="form-control" id="edit_name" name="name" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_description">产品描述</label>
                    <textarea class="form-control textarea" id="edit_description" name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_type">产品类型</label>
                    <select class="form-control" id="edit_type" name="type">
                        <option value="software">软件</option>
                        <option value="service">服务</option>
                        <option value="other">其他</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_price">价格</label>
                    <input type="number" class="form-control" id="edit_price" name="price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_duration">有效期（天）</label>
                    <input type="number" class="form-control" id="edit_duration" name="duration" value="0" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_sort">排序</label>
                    <input type="number" class="form-control" id="edit_sort" name="sort" value="0" min="0">
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_status">状态</label>
                    <select class="form-control" id="edit_status" name="status">
                        <option value="1">上架</option>
                        <option value="0">下架</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">保存修改</button>
            </form>
        </div>
    </div>
</div>

<script>
function editProduct(id, name, desc, type, price, duration, sort, status) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = desc;
    document.getElementById('edit_type').value = type;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_duration').value = duration;
    document.getElementById('edit_sort').value = sort;
    document.getElementById('edit_status').value = status;
    document.getElementById('editProductModal').classList.add('show');
}
function closeEditModal() {
    document.getElementById('editProductModal').classList.remove('show');
}
</script>