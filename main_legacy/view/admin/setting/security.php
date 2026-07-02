<div class="breadcrumb">系统设置 / 安全防护</div>
<div class="page-header">
    <h2>安全防护</h2>
</div>

<div class="card" style="max-width: 720px;">
    <form id="settingForm">
        <input type="hidden" name="group" value="security">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
            <div class="form-group">
                <label>总站后台二次认证</label>
                <select name="admin_2fa">
                    <option value="0" <?php echo ($config['security_admin_2fa'] ?? '0') === '0' ? 'selected' : ''; ?>>关闭</option>
                    <option value="1" <?php echo ($config['security_admin_2fa'] ?? '0') === '1' ? 'selected' : ''; ?>>开启</option>
                </select>
            </div>
            <div class="form-group">
                <label>商户后台二次认证</label>
                <select name="merchant_2fa">
                    <option value="0" <?php echo ($config['security_merchant_2fa'] ?? '0') === '0' ? 'selected' : ''; ?>>关闭</option>
                    <option value="1" <?php echo ($config['security_merchant_2fa'] ?? '0') === '1' ? 'selected' : ''; ?>>开启</option>
                </select>
            </div>
            <div class="form-group">
                <label>登录失败次数限制</label>
                <input type="number" name="login_fail_limit" min="0" value="<?php echo h($config['security_login_fail_limit'] ?? '5'); ?>">
            </div>
            <div class="form-group">
                <label>登录锁定分钟数</label>
                <input type="number" name="login_lock_minutes" min="0" value="<?php echo h($config['security_login_lock_minutes'] ?? '30'); ?>">
            </div>
            <div class="form-group">
                <label>登录图形验证码</label>
                <select name="captcha_login">
                    <option value="0" <?php echo ($config['security_captcha_login'] ?? '0') === '0' ? 'selected' : ''; ?>>关闭</option>
                    <option value="1" <?php echo ($config['security_captcha_login'] ?? '0') === '1' ? 'selected' : ''; ?>>开启</option>
                </select>
            </div>
            <div class="form-group">
                <label>商户入驻图形验证码</label>
                <select name="captcha_join">
                    <option value="0" <?php echo ($config['security_captcha_join'] ?? '0') === '0' ? 'selected' : ''; ?>>关闭</option>
                    <option value="1" <?php echo ($config['security_captcha_join'] ?? '0') === '1' ? 'selected' : ''; ?>>开启</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn" id="saveBtn">保存安全设置</button>
    </form>
</div>

<div class="card">
    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">IP 黑名单</h3>

    <form id="blacklistForm" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 20px; align-items: end;">
        <input type="hidden" name="id" id="blId" value="0">
        <div class="form-group" style="margin: 0;">
            <label style="font-size: 13px; margin-bottom: 4px;">IP / IP 段</label>
            <input type="text" name="ip" id="blIp" placeholder="如 192.168.1.1 或 192.168.1.%" required>
        </div>
        <div class="form-group" style="margin: 0;">
            <label style="font-size: 13px; margin-bottom: 4px;">原因</label>
            <input type="text" name="reason" id="blReason" placeholder="封禁原因">
        </div>
        <div class="form-group" style="margin: 0;">
            <label style="font-size: 13px; margin-bottom: 4px;">过期时间（留空永久）</label>
            <input type="datetime-local" name="expire_time" id="blExpire">
        </div>
        <div class="form-group" style="margin: 0;">
            <label style="font-size: 13px; margin-bottom: 4px;">状态</label>
            <select name="status" id="blStatus">
                <option value="1">启用</option>
                <option value="0">禁用</option>
            </select>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-sm" id="blBtn">添加</button>
            <button type="button" class="btn btn-sm btn-outline" id="blCancel" style="display: none;">取消</button>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>IP / IP 段</th>
                <th>原因</th>
                <th>过期时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($blacklist)): ?>
            <tr>
                <td colspan="5" style="text-align: center; color: #64748B;">暂无黑名单记录</td>
            </tr>
            <?php else: ?>
            <?php foreach ($blacklist as $item): ?>
            <tr data-id="<?php echo $item['id']; ?>">
                <td><?php echo h($item['ip']); ?></td>
                <td><?php echo h($item['reason']); ?></td>
                <td><?php echo $item['expire_time'] ? h($item['expire_time']) : '永久'; ?></td>
                <td>
                    <span class="tag <?php echo $item['status'] ? 'tag-green' : 'tag-orange'; ?>">
                        <?php echo $item['status'] ? '启用' : '禁用'; ?>
                    </span>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline edit-btn">编辑</button>
                    <button type="button" class="btn btn-sm btn-warning toggle-btn"><?php echo $item['status'] ? '禁用' : '启用'; ?></button>
                    <button type="button" class="btn btn-sm btn-danger delete-btn">删除</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('settingForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = '保存中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('admin/setting/save'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '保存安全设置';
    }
});

const blacklistForm = document.getElementById('blacklistForm');
const blId = document.getElementById('blId');
const blIp = document.getElementById('blIp');
const blReason = document.getElementById('blReason');
const blExpire = document.getElementById('blExpire');
const blStatus = document.getElementById('blStatus');
const blBtn = document.getElementById('blBtn');
const blCancel = document.getElementById('blCancel');

function formatDateTimeLocal(value) {
    if (!value) return '';
    const d = new Date(value.replace(' ', 'T'));
    if (isNaN(d.getTime())) return '';
    const pad = n => n.toString().padStart(2, '0');
    return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
}

blacklistForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    blBtn.disabled = true;
    blBtn.textContent = '保存中...';
    const formData = new FormData(blacklistForm);
    try {
        const res = await fetch('<?php echo url('admin/setting/ipBlacklistSave'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        blBtn.disabled = false;
        blBtn.textContent = blId.value > 0 ? '保存' : '添加';
    }
});

blCancel.addEventListener('click', () => {
    blacklistForm.reset();
    blId.value = '0';
    blBtn.textContent = '添加';
    blCancel.style.display = 'none';
});

document.querySelectorAll('.edit-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.closest('tr');
        const cells = row.querySelectorAll('td');
        blId.value = row.dataset.id;
        blIp.value = cells[0].textContent.trim();
        blReason.value = cells[1].textContent.trim();
        blExpire.value = formatDateTimeLocal(cells[2].textContent.trim());
        blStatus.value = cells[3].querySelector('.tag').textContent.trim() === '启用' ? '1' : '0';
        blBtn.textContent = '保存';
        blCancel.style.display = 'inline-block';
        blIp.focus();
    });
});

document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('确定切换该黑名单状态？')) return;
        const id = btn.closest('tr').dataset.id;
        try {
            const formData = new FormData();
            formData.append('id', id);
            const res = await fetch('<?php echo url('admin/setting/ipBlacklistToggle'); ?>', { method: 'POST', body: formData });
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
        if (!confirm('确定删除该黑名单？')) return;
        const id = btn.closest('tr').dataset.id;
        try {
            const formData = new FormData();
            formData.append('id', id);
            const res = await fetch('<?php echo url('admin/setting/ipBlacklistDelete'); ?>', { method: 'POST', body: formData });
            const data = await res.json();
            alert(data.msg);
            if (data.code === 0) location.reload();
        } catch (err) {
            alert('请求失败');
        }
    });
});
</script>
