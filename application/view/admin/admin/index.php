<div class="breadcrumb">系统设置 / 管理员账号</div>
<div class="page-header">
    <h2>管理员账号</h2>
</div>

<div class="card" style="max-width: 720px;">
    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;" id="formTitle">添加管理员</h3>
    <form id="adminForm">
        <input type="hidden" name="id" id="adminId" value="0">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
            <div class="form-group">
                <label>账号</label>
                <input type="text" name="username" id="adminUsername" placeholder="4-20 位字母/数字/下划线" required>
            </div>
            <div class="form-group">
                <label>密码</label>
                <input type="password" name="password" id="adminPassword" placeholder="新增必填，修改留空不更改">
            </div>
            <div class="form-group">
                <label>角色</label>
                <select name="role" id="adminRole" required>
                    <option value="">请选择</option>
                    <?php foreach ($roleMap as $key => $name): ?>
                    <option value="<?php echo h($key); ?>"><?php echo h($name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>所属分站</label>
                <select name="subsite_id" id="adminSubsite">
                    <option value="0">总站</option>
                    <?php foreach ($subsites as $s): ?>
                    <option value="<?php echo $s['id']; ?>"><?php echo h($s['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>姓名</label>
                <input type="text" name="real_name" id="adminRealName" placeholder="真实姓名">
            </div>
            <div class="form-group">
                <label>手机号</label>
                <input type="text" name="mobile" id="adminMobile" placeholder="手机号">
            </div>
            <div class="form-group">
                <label>状态</label>
                <select name="status" id="adminStatus">
                    <option value="1">启用</option>
                    <option value="0">禁用</option>
                </select>
            </div>
        </div>
        <div style="display: flex; gap: 8px; margin-top: 8px;">
            <button type="submit" class="btn" id="saveBtn">保存</button>
            <button type="button" class="btn btn-outline" id="cancelBtn" style="display: none;">取消</button>
        </div>
    </form>
</div>

<div class="card">
    <form method="get" class="search-bar">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="账号 / 姓名 / 手机号">
        <select name="role" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部角色</option>
            <?php foreach ($roleMap as $key => $name): ?>
            <option value="<?php echo h($key); ?>" <?php echo $role === $key ? 'selected' : ''; ?>><?php echo h($name); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>启用</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>禁用</option>
        </select>
        <button type="submit" class="btn btn-sm">搜索</button>
        <a href="<?php echo url('admin/admin'); ?>" class="btn btn-sm btn-outline">重置</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>账号</th>
                <th>角色</th>
                <th>分站</th>
                <th>姓名</th>
                <th>手机号</th>
                <th>状态</th>
                <th>最后登录</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list)): ?>
            <tr>
                <td colspan="9" style="text-align: center; color: #64748B;">暂无管理员</td>
            </tr>
            <?php else: ?>
            <?php foreach ($list as $item): ?>
            <tr data-id="<?php echo $item['id']; ?>" data-username="<?php echo h($item['username']); ?>" data-role="<?php echo h($item['role']); ?>" data-subsite="<?php echo $item['subsite_id']; ?>" data-realname="<?php echo h($item['real_name']); ?>" data-mobile="<?php echo h($item['mobile']); ?>" data-status="<?php echo $item['status']; ?>">
                <td><?php echo $item['id']; ?></td>
                <td><?php echo h($item['username']); ?></td>
                <td><?php echo h($roleMap[$item['role']] ?? $item['role']); ?></td>
                <td><?php echo $item['subsite_id'] > 0 ? h($item['subsite_name']) : '-'; ?></td>
                <td><?php echo h($item['real_name']); ?></td>
                <td><?php echo h($item['mobile']); ?></td>
                <td>
                    <span class="tag <?php echo $item['status'] ? 'tag-green' : 'tag-red'; ?>">
                        <?php echo $item['status'] ? '启用' : '禁用'; ?>
                    </span>
                </td>
                <td><?php echo $item['last_login_time'] ?: '未登录'; ?></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline edit-btn">编辑</button>
                    <?php if ($item['role'] !== 'super'): ?>
                    <button type="button" class="btn btn-sm btn-warning toggle-btn"><?php echo $item['status'] ? '禁用' : '启用'; ?></button>
                    <button type="button" class="btn btn-sm btn-danger delete-btn">删除</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: center; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('admin/admin', ['keyword' => $keyword, 'role' => $role, 'status' => $status, 'page' => $page - 1]); ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 6px 12px; color: #64748B;"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('admin/admin', ['keyword' => $keyword, 'role' => $role, 'status' => $status, 'page' => $page + 1]); ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
const adminForm = document.getElementById('adminForm');
const formTitle = document.getElementById('formTitle');
const adminId = document.getElementById('adminId');
const adminUsername = document.getElementById('adminUsername');
const adminPassword = document.getElementById('adminPassword');
const adminRole = document.getElementById('adminRole');
const adminSubsite = document.getElementById('adminSubsite');
const adminRealName = document.getElementById('adminRealName');
const adminMobile = document.getElementById('adminMobile');
const adminStatus = document.getElementById('adminStatus');
const saveBtn = document.getElementById('saveBtn');
const cancelBtn = document.getElementById('cancelBtn');

function toggleSubsiteRequired() {
    const needSubsite = ['subsite_super', 'subsite_admin'].includes(adminRole.value);
    adminSubsite.required = needSubsite;
}
adminRole.addEventListener('change', toggleSubsiteRequired);

adminForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (adminId.value > 0) {
        adminUsername.disabled = true;
    }
    saveBtn.disabled = true;
    saveBtn.textContent = '保存中...';
    const formData = new FormData(adminForm);
    if (adminId.value > 0) {
        formData.append('username', adminUsername.value);
    }
    try {
        const res = await fetch('<?php echo url('admin/admin/save'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        adminUsername.disabled = false;
        saveBtn.disabled = false;
        saveBtn.textContent = adminId.value > 0 ? '保存' : '添加';
    }
});

cancelBtn.addEventListener('click', () => {
    adminForm.reset();
    adminId.value = '0';
    adminUsername.disabled = false;
    formTitle.textContent = '添加管理员';
    saveBtn.textContent = '保存';
    cancelBtn.style.display = 'none';
    toggleSubsiteRequired();
});

document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.closest('tr');
        adminId.value = row.dataset.id;
        adminUsername.value = row.dataset.username;
        adminUsername.disabled = true;
        adminPassword.value = '';
        adminRole.value = row.dataset.role;
        adminSubsite.value = row.dataset.subsite;
        adminRealName.value = row.dataset.realname;
        adminMobile.value = row.dataset.mobile;
        adminStatus.value = row.dataset.status;
        formTitle.textContent = '编辑管理员';
        saveBtn.textContent = '保存';
        cancelBtn.style.display = 'inline-block';
        toggleSubsiteRequired();
        adminUsername.focus();
    });
});

document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('确定切换该账号状态？')) return;
        const id = btn.closest('tr').dataset.id;
        try {
            const formData = new FormData();
            formData.append('id', id);
            const res = await fetch('<?php echo url('admin/admin/toggle'); ?>', { method: 'POST', body: formData });
            const data = await res.json();
            alert(data.msg);
            if (data.code === 0) location.reload();
        } catch (err) {
            alert('请求失败');
        }
    });
});

document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('确定删除该管理员？')) return;
        const id = btn.closest('tr').dataset.id;
        try {
            const formData = new FormData();
            formData.append('id', id);
            const res = await fetch('<?php echo url('admin/admin/delete'); ?>', { method: 'POST', body: formData });
            const data = await res.json();
            alert(data.msg);
            if (data.code === 0) location.reload();
        } catch (err) {
            alert('请求失败');
        }
    });
});
</script>
