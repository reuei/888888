<div class="breadcrumb">用户管理 / 用户列表</div>
<div class="page-header">
    <h2>用户列表</h2>
    <a href="<?php echo url('admin/user/group'); ?>" class="btn btn-outline">等级分组</a>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/user'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="昵称 / 手机号 / ID">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>正常</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>禁用</option>
        </select>
        <select name="subsite_id" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部分站</option>
            <?php foreach ($subsites as $s): ?>
            <option value="<?php echo $s['id']; ?>" <?php echo $subsiteId === (string)$s['id'] ? 'selected' : ''; ?>><?php echo h($s['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="group_id" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部分组</option>
            <?php foreach ($groups as $g): ?>
            <option value="<?php echo $g['id']; ?>" <?php echo $groupId === (string)$g['id'] ? 'selected' : ''; ?>><?php echo h($g['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>用户ID</th>
            <th>用户信息</th>
            <th>所属分站</th>
            <th>等级分组</th>
            <th>余额</th>
            <th>状态</th>
            <th>注册时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="8" style="text-align: center; color: #64748B; padding: 40px;">暂无用户数据</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td>
                <div style="font-weight: 500;"><?php echo h($item['nickname'] ?: '-'); ?></div>
                <div style="color: #94A3B8; font-size: 12px;"><?php echo h($item['mobile'] ?: '-'); ?></div>
            </td>
            <td><?php echo h($item['subsite_name'] ?? '-'); ?></td>
            <td><?php echo h($item['group_name'] ?? '-'); ?></td>
            <td>¥ <?php echo $item['balance']; ?></td>
            <td>
                <?php if ($item['status'] == 1): ?>
                <span class="tag tag-green">正常</span>
                <?php else: ?>
                <span class="tag tag-red">禁用</span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['create_time']; ?></td>
            <td>
                <a href="<?php echo url('admin/user/detail') . '?id=' . $item['id']; ?>" class="btn btn-sm">详情</a>
                <?php if ($item['status'] == 1): ?>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="toggleStatus(<?php echo $item['id']; ?>, 0)">禁用</a>
                <?php else: ?>
                <a href="javascript:;" class="btn btn-sm btn-success" onclick="toggleStatus(<?php echo $item['id']; ?>, 1)">启用</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('admin/user') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status . '&subsite_id=' . $subsiteId . '&group_id=' . $groupId; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('admin/user') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status . '&subsite_id=' . $subsiteId . '&group_id=' . $groupId; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
async function toggleStatus(id, status) {
    const label = status ? '启用' : '禁用';
    if (!confirm('确认' + label + '该用户？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('status', status);
    const res = await fetch('<?php echo url('admin/user/toggleStatus'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
