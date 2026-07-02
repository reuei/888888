<div class="breadcrumb">支付网关 / 渠道配置</div>
<div class="page-header">
    <h2>支付渠道配置</h2>
    <a href="<?php echo url('admin/payment/risk'); ?>" class="btn btn-outline">风控策略</a>
</div>

<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;">新增/编辑渠道</h3>
    <form id="channelForm" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
        <input type="hidden" name="id" id="channelId" value="0">
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">渠道编码</label>
            <input type="text" name="code" id="channelCode" required placeholder="如：alipay" style="width: 140px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">渠道名称</label>
            <input type="text" name="name" id="channelName" required style="width: 160px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">作用范围</label>
            <select name="scope" id="channelScope" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
                <?php foreach ($scopeMap as $k => $v): ?>
                <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">作用对象ID</label>
            <input type="number" name="scope_id" id="channelScopeId" value="0" style="width: 110px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">排序</label>
            <input type="number" name="sort" id="channelSort" value="0" style="width: 90px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">状态</label>
            <select name="status" id="channelStatus" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
                <option value="1">启用</option>
                <option value="0">禁用</option>
            </select>
        </div>
        <button type="submit" class="btn" id="saveBtn">保存</button>
        <button type="button" class="btn btn-outline" id="resetBtn" style="display: none;">取消</button>
    </form>
    <div style="margin-top: 12px;">
        <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">配置 JSON（如：{"app_id":"xxx","secret":"xxx"}）</label>
        <textarea name="config" id="channelConfig" rows="3" style="width: 100%; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;"></textarea>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/payment/channel'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="编码 / 名称">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
            <option value="">全部状态</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>启用</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>禁用</option>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>编码</th>
            <th>名称</th>
            <th>范围</th>
            <th>排序</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="8" style="text-align: center; color: #64748B; padding: 40px;">暂无渠道</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><code style="font-family: monospace; background: #F1F5F9; padding: 2px 6px; border-radius: 4px;"><?php echo h($item['code']); ?></code></td>
            <td><?php echo h($item['name']); ?></td>
            <td><?php echo $scopeMap[$item['scope']] ?? $item['scope']; ?><?php echo $item['scope_id'] ? '(' . $item['scope_id'] . ')' : ''; ?></td>
            <td><?php echo $item['sort']; ?></td>
            <td>
                <?php if ($item['status']): ?>
                <span class="tag tag-green">启用</span>
                <?php else: ?>
                <span class="tag">禁用</span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['create_time']; ?></td>
            <td>
                <a href="javascript:;" class="btn btn-sm btn-outline" onclick="editChannel(<?php echo $item['id']; ?>, '<?php echo h(addslashes($item['code'])); ?>', '<?php echo h(addslashes($item['name'])); ?>', '<?php echo $item['scope']; ?>', <?php echo $item['scope_id']; ?>, <?php echo $item['sort']; ?>, <?php echo $item['status']; ?>, '<?php echo h(addslashes($item['config'])); ?>')">编辑</a>
                <?php if ($item['status']): ?>
                <a href="javascript:;" class="btn btn-sm btn-warning" onclick="toggleStatus(<?php echo $item['id']; ?>, 0)">禁用</a>
                <?php else: ?>
                <a href="javascript:;" class="btn btn-sm btn-success" onclick="toggleStatus(<?php echo $item['id']; ?>, 1)">启用</a>
                <?php endif; ?>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="deleteChannel(<?php echo $item['id']; ?>, '<?php echo h(addslashes($item['name'])); ?>')">删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('admin/payment/channel') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('admin/payment/channel') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
const form = document.getElementById('channelForm');
const saveBtn = document.getElementById('saveBtn');
const resetBtn = document.getElementById('resetBtn');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    saveBtn.disabled = true;
    saveBtn.textContent = '保存中...';
    const formData = new FormData(form);
    formData.append('config', document.getElementById('channelConfig').value);
    try {
        const res = await fetch('<?php echo url('admin/payment/channelSave'); ?>', { method: 'POST', body: formData });
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

function editChannel(id, code, name, scope, scopeId, sort, status, config) {
    document.getElementById('channelId').value = id;
    document.getElementById('channelCode').value = code;
    document.getElementById('channelName').value = name;
    document.getElementById('channelScope').value = scope;
    document.getElementById('channelScopeId').value = scopeId;
    document.getElementById('channelSort').value = sort;
    document.getElementById('channelStatus').value = status;
    document.getElementById('channelConfig').value = config;
    saveBtn.textContent = '更新';
    resetBtn.style.display = 'inline-block';
}

resetBtn.addEventListener('click', () => {
    form.reset();
    document.getElementById('channelId').value = 0;
    document.getElementById('channelConfig').value = '';
    saveBtn.textContent = '保存';
    resetBtn.style.display = 'none';
});

async function toggleStatus(id, status) {
    const form = new FormData();
    form.append('id', id);
    form.append('status', status);
    const res = await fetch('<?php echo url('admin/payment/channelStatus'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function deleteChannel(id, name) {
    if (!confirm('确认删除渠道「' + name + '」？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('admin/payment/channelDelete'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
