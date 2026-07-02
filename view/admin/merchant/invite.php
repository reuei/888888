<div class="breadcrumb">商户管理 / 邀请码管理</div>
<div class="page-header">
    <h2>邀请码管理</h2>
    <div>
        <a href="<?php echo url('admin/merchant'); ?>" class="btn btn-outline">商户列表</a>
        <a href="<?php echo url('admin/merchant/audit'); ?>" class="btn" style="margin-left: 8px;">入驻审核</a>
    </div>
</div>

<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;">批量生成邀请码</h3>
    <form id="inviteForm" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">生成数量</label>
            <input type="number" name="quantity" value="10" min="1" max="100" style="width: 100px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">归属分站</label>
            <select name="subsite_id" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; min-width: 140px;">
                <option value="0">总站全局</option>
                <?php foreach ($subsites as $s): ?>
                <option value="<?php echo $s['id']; ?>"><?php echo h($s['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">默认费率分组</label>
            <select name="rate_group_id" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; min-width: 140px;">
                <?php foreach ($rateGroups as $rg): ?>
                <option value="<?php echo $rg['id']; ?>"><?php echo h($rg['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">最大使用次数（0 不限）</label>
            <input type="number" name="max_uses" value="0" min="0" style="width: 120px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">过期时间</label>
            <input type="datetime-local" name="expire_time" style="padding: 7px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <button type="submit" class="btn" id="createBtn">立即生成</button>
    </form>

    <div id="resultBox" style="display: none; margin-top: 16px; padding: 12px; background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 6px;">
        <div style="font-weight: 500; margin-bottom: 8px;">生成结果</div>
        <textarea id="resultCodes" rows="3" readonly style="width: 100%; padding: 8px; border: 1px solid #CBD5E1; border-radius: 6px; font-family: monospace; font-size: 13px;"></textarea>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/merchant/invite'); ?>">
        <select name="subsite_id" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部分站</option>
            <?php foreach ($subsites as $s): ?>
            <option value="<?php echo $s['id']; ?>" <?php echo $subsiteId === (string)$s['id'] ? 'selected' : ''; ?>><?php echo h($s['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>有效</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>失效</option>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>邀请码</th>
            <th>归属分站</th>
            <th>费率分组</th>
            <th>使用次数</th>
            <th>上限</th>
            <th>过期时间</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="9" style="text-align: center; color: #64748B; padding: 40px;">暂无邀请码</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><code style="font-family: monospace; background: #F1F5F9; padding: 2px 6px; border-radius: 4px;"><?php echo h($item['code']); ?></code></td>
            <td><?php echo h($item['subsite_name'] ?? '总站全局'); ?></td>
            <td><?php echo h($item['rate_group_name'] ?? '-'); ?></td>
            <td>
                <?php echo $item['used_count_real']; ?>
                <?php if ($item['max_uses'] > 0): ?>
                / <?php echo $item['max_uses']; ?>
                <?php else: ?>
                <span style="color: #94A3B8;">/ 不限</span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['max_uses'] > 0 ? $item['max_uses'] : '不限'; ?></td>
            <td><?php echo $item['expire_time'] ? date('Y-m-d H:i', strtotime($item['expire_time'])) : '永久'; ?></td>
            <td>
                <?php if ($item['status'] == 1): ?>
                <span class="tag tag-green">有效</span>
                <?php else: ?>
                <span class="tag">失效</span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['create_time']; ?></td>
            <td>
                <?php if ($item['status'] == 1): ?>
                <a href="javascript:;" class="btn btn-sm btn-warning" onclick="toggleInvite(<?php echo $item['id']; ?>, 0)">禁用</a>
                <?php else: ?>
                <a href="javascript:;" class="btn btn-sm btn-success" onclick="toggleInvite(<?php echo $item['id']; ?>, 1)">启用</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('admin/merchant/invite') . '?page=' . ($page - 1) . '&subsite_id=' . $subsiteId . '&status=' . $status; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('admin/merchant/invite') . '?page=' . ($page + 1) . '&subsite_id=' . $subsiteId . '&status=' . $status; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('inviteForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('createBtn');
    btn.disabled = true;
    btn.textContent = '生成中...';

    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('admin/merchant/inviteCreate'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.code === 0) {
            document.getElementById('resultBox').style.display = 'block';
            document.getElementById('resultCodes').value = data.data.codes.join('\n');
            setTimeout(() => location.reload(), 1500);
        } else {
            alert(data.msg);
        }
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '立即生成';
    }
});

async function toggleInvite(id, status) {
    if (!confirm(status ? '确认启用该邀请码？' : '确认禁用该邀请码？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('status', status);
    const res = await fetch('<?php echo url('admin/merchant/inviteToggle'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
