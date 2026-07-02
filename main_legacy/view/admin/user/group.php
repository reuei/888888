<div class="breadcrumb">用户管理 / 等级分组</div>
<div class="page-header">
    <h2>用户等级分组</h2>
    <a href="<?php echo url('admin/user'); ?>" class="btn btn-outline">用户列表</a>
</div>

<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;">新增/编辑分组</h3>
    <form id="groupForm" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
        <input type="hidden" name="id" id="groupId" value="0">
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">分组名称</label>
            <input type="text" name="name" id="groupName" required style="width: 160px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">等级</label>
            <input type="number" name="level" id="groupLevel" value="1" min="1" style="width: 100px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">折扣率（0-1）</label>
            <input type="number" name="discount" id="groupDiscount" value="1.0000" step="0.0001" min="0" max="1" style="width: 120px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">排序</label>
            <input type="number" name="sort" id="groupSort" value="0" style="width: 100px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">状态</label>
            <select name="status" id="groupStatus" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
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
            <th>分组名称</th>
            <th>等级</th>
            <th>折扣率</th>
            <th>排序</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="7" style="text-align: center; color: #64748B; padding: 40px;">暂无分组</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['name']); ?></td>
            <td><?php echo $item['level']; ?></td>
            <td><?php echo $item['discount']; ?></td>
            <td><?php echo $item['sort']; ?></td>
            <td>
                <?php if ($item['status'] == 1): ?>
                <span class="tag tag-green">启用</span>
                <?php else: ?>
                <span class="tag">禁用</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="javascript:;" class="btn btn-sm btn-outline" onclick="editGroup(<?php echo $item['id']; ?>, '<?php echo h(addslashes($item['name'])); ?>', <?php echo $item['level']; ?>, '<?php echo $item['discount']; ?>', <?php echo $item['sort']; ?>, <?php echo $item['status']; ?>)">编辑</a>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="deleteGroup(<?php echo $item['id']; ?>)">删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<script>
const form = document.getElementById('groupForm');
const saveBtn = document.getElementById('saveBtn');
const resetBtn = document.getElementById('resetBtn');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    saveBtn.disabled = true;
    saveBtn.textContent = '保存中...';
    const formData = new FormData(form);
    try {
        const res = await fetch('<?php echo url('admin/user/groupSave'); ?>', { method: 'POST', body: formData });
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

function editGroup(id, name, level, discount, sort, status) {
    document.getElementById('groupId').value = id;
    document.getElementById('groupName').value = name;
    document.getElementById('groupLevel').value = level;
    document.getElementById('groupDiscount').value = discount;
    document.getElementById('groupSort').value = sort;
    document.getElementById('groupStatus').value = status;
    saveBtn.textContent = '更新';
    resetBtn.style.display = 'inline-block';
}

resetBtn.addEventListener('click', () => {
    form.reset();
    document.getElementById('groupId').value = 0;
    saveBtn.textContent = '保存';
    resetBtn.style.display = 'none';
});

async function deleteGroup(id) {
    if (!confirm('确认删除该分组？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('admin/user/groupDelete'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
