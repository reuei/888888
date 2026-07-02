<div class="breadcrumb">商品管理 / 分类管理</div>
<div class="page-header">
    <h2>商品分类管理</h2>
    <a href="<?php echo url('admin/goods'); ?>" class="btn btn-outline">商品列表</a>
</div>

<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;"><?php echo input('edit') ? '编辑分类' : '新增分类'; ?></h3>
    <form id="categoryForm" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
        <input type="hidden" name="id" id="catId" value="0">
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">分类名称</label>
            <input type="text" name="name" id="catName" required style="width: 180px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">父级分类</label>
            <select name="parent_id" id="catParent" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; min-width: 140px;">
                <option value="0">无（顶级）</option>
                <?php foreach ($list as $c): ?>
                <?php if ($c['parent_id'] == 0): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo h($c['name']); ?></option>
                <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">排序</label>
            <input type="number" name="sort" id="catSort" value="0" style="width: 100px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">导航显示</label>
            <select name="is_nav" id="catNav" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
                <option value="1">显示</option>
                <option value="0">隐藏</option>
            </select>
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">状态</label>
            <select name="status" id="catStatus" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
                <option value="1">启用</option>
                <option value="0">禁用</option>
            </select>
        </div>
        <button type="submit" class="btn" id="saveBtn">保存</button>
        <button type="button" class="btn btn-outline" id="resetBtn" style="display: none;">取消</button>
    </form>
</div>

<div class="card">
    <table>
        <tr>
            <th>ID</th>
            <th>分类名称</th>
            <th>父级</th>
            <th>排序</th>
            <th>导航显示</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="7" style="text-align: center; color: #64748B; padding: 40px;">暂无分类</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo str_repeat('　　', (int)$item['level'] ?? 0) . h($item['name']); ?></td>
            <td><?php echo h($item['parent_name'] ?? '顶级'); ?></td>
            <td><?php echo $item['sort']; ?></td>
            <td><?php echo $item['is_nav'] ? '显示' : '隐藏'; ?></td>
            <td>
                <?php if ($item['status']): ?>
                <span class="tag tag-green">启用</span>
                <?php else: ?>
                <span class="tag">禁用</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="javascript:;" class="btn btn-sm btn-outline" onclick="editCat(<?php echo $item['id']; ?>, '<?php echo h(addslashes($item['name'])); ?>', <?php echo $item['parent_id']; ?>, <?php echo $item['sort']; ?>, <?php echo $item['is_nav']; ?>, <?php echo $item['status']; ?>)">编辑</a>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="deleteCat(<?php echo $item['id']; ?>)">删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<script>
const form = document.getElementById('categoryForm');
const saveBtn = document.getElementById('saveBtn');
const resetBtn = document.getElementById('resetBtn');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    saveBtn.disabled = true;
    saveBtn.textContent = '保存中...';
    const formData = new FormData(form);
    try {
        const res = await fetch('<?php echo url('admin/goods/categorySave'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = '保存';
    }
});

function editCat(id, name, parentId, sort, isNav, status) {
    document.getElementById('catId').value = id;
    document.getElementById('catName').value = name;
    document.getElementById('catParent').value = parentId;
    document.getElementById('catSort').value = sort;
    document.getElementById('catNav').value = isNav;
    document.getElementById('catStatus').value = status;
    saveBtn.textContent = '更新';
    resetBtn.style.display = 'inline-block';
}

resetBtn.addEventListener('click', () => {
    form.reset();
    document.getElementById('catId').value = 0;
    saveBtn.textContent = '保存';
    resetBtn.style.display = 'none';
});

async function deleteCat(id) {
    if (!confirm('确认删除该分类？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('admin/goods/categoryDelete'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
