<div class="page-header">
    <h2>积分商品</h2>
    <button type="button" class="btn" onclick="openModal()">+ 新增商品</button>
</div>

<div class="search-bar">
    <form method="get" action="<?php echo url('admin/points/goods'); ?>" style="display:flex; gap:12px; flex:1;">
        <input type="text" name="keyword" placeholder="搜索商品标题" value="<?php echo h($keyword); ?>">
        <button type="submit" class="btn btn-sm">搜索</button>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>商品</th>
                <th>积分</th>
                <th>库存/已兑</th>
                <th>排序</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <?php if ($item['image']): ?>
                        <img src="<?php echo base_url($item['image']); ?>" style="width:40px; height:40px; object-fit:cover; border-radius:4px;">
                        <?php endif; ?>
                        <div><?php echo h($item['title']); ?></div>
                    </div>
                </td>
                <td><?php echo $item['points']; ?></td>
                <td><?php echo $item['stock']; ?> / <?php echo $item['sold']; ?></td>
                <td><?php echo $item['sort']; ?></td>
                <td><?php echo $item['status'] ? '<span class="tag tag-green">上架</span>' : '<span class="tag tag-orange">下架</span>'; ?></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline" onclick="editGoods(<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES); ?>)">编辑</button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="toggleGoods(<?php echo $item['id']; ?>, <?php echo $item['status'] ? 0 : 1; ?>)"><?php echo $item['status'] ? '下架' : '上架'; ?></button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteGoods(<?php echo $item['id']; ?>, '<?php echo h($item['title']); ?>')">删除</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination" style="display:flex; justify-content:center; gap:8px; margin-top:16px;">
    <a href="<?php echo url('admin/points/goods') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword); ?>" class="btn btn-sm btn-outline <?php echo $page <= 1 ? 'disabled' : ''; ?>">上一页</a>
    <span style="padding:5px 10px; color:#64748B;"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
    <a href="<?php echo url('admin/points/goods') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword); ?>" class="btn btn-sm btn-outline <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">下一页</a>
</div>
<?php endif; ?>

<div id="goodsModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:200; align-items:center; justify-content:center;">
    <div style="background:#fff; width:520px; max-width:90%; border-radius:8px; padding:24px; max-height:90vh; overflow-y:auto;">
        <h3 style="margin-bottom:16px;">积分商品</h3>
        <form id="goodsForm">
            <input type="hidden" name="id" id="goodsId">
            <div style="margin-bottom:12px;">
                <label>商品标题</label>
                <input type="text" name="title" id="goodsTitle" required style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="margin-bottom:12px;">
                <label>商品图片 URL</label>
                <input type="text" name="image" id="goodsImage" placeholder="uploads/..." style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="display:flex; gap:12px; margin-bottom:12px;">
                <div style="flex:1;">
                    <label>兑换积分</label>
                    <input type="number" name="points" id="goodsPoints" value="0" required min="1" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                </div>
                <div style="flex:1;">
                    <label>库存</label>
                    <input type="number" name="stock" id="goodsStock" value="0" required min="0" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label>商品详情</label>
                <textarea name="description" id="goodsDesc" rows="4" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;"></textarea>
            </div>
            <div style="display:flex; gap:12px; margin-bottom:12px;">
                <div style="flex:1;">
                    <label>排序</label>
                    <input type="number" name="sort" id="goodsSort" value="0" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                </div>
                <div style="flex:1;">
                    <label>状态</label>
                    <select name="status" id="goodsStatus" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                        <option value="1">上架</option>
                        <option value="0">下架</option>
                    </select>
                </div>
            </div>
            <div style="text-align:right;">
                <button type="button" class="btn btn-outline" onclick="closeModal()" style="margin-right:8px;">取消</button>
                <button type="submit" class="btn">保存</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('goodsForm').reset();
    document.getElementById('goodsId').value = '';
    document.getElementById('goodsModal').style.display = 'flex';
}
function closeModal() {
    document.getElementById('goodsModal').style.display = 'none';
}
function editGoods(item) {
    openModal();
    document.getElementById('goodsId').value = item.id;
    document.getElementById('goodsTitle').value = item.title;
    document.getElementById('goodsImage').value = item.image;
    document.getElementById('goodsPoints').value = item.points;
    document.getElementById('goodsStock').value = item.stock;
    document.getElementById('goodsDesc').value = item.description;
    document.getElementById('goodsSort').value = item.sort;
    document.getElementById('goodsStatus').value = item.status;
}
function toggleGoods(id, status) {
    fetch('<?php echo url("admin/points/toggleGoods"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&status=' + status
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) location.reload();
    });
}
function deleteGoods(id, title) {
    if (!confirm('确认删除商品：' + title + '？')) return;
    fetch('<?php echo url("admin/points/deleteGoods"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) location.reload();
    });
}
document.getElementById('goodsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = new FormData(this);
    fetch('<?php echo url("admin/points/saveGoods"); ?>', {
        method: 'POST',
        body: new URLSearchParams(form)
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) location.reload();
    });
});
</script>
