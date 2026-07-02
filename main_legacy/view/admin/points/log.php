<div class="page-header">
    <h2>用户积分流水</h2>
</div>

<div class="search-bar">
    <form method="get" action="<?php echo url('admin/points/log'); ?>" style="display:flex; gap:12px; flex:1;">
        <select name="type" style="padding:8px 12px; border:1px solid #CBD5E1; border-radius:6px;">
            <option value="">全部类型</option>
            <?php foreach ($typeMap as $k => $v): ?>
            <option value="<?php echo $k; ?>" <?php echo $type === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm">筛选</button>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>用户</th>
                <th>类型</th>
                <th>积分变动</th>
                <th>变动前</th>
                <th>变动后</th>
                <th>备注</th>
                <th>时间</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo h($item['nickname'] ?: $item['mobile']); ?></td>
                <td><?php echo $typeMap[$item['type']] ?? $item['type']; ?></td>
                <td style="color: <?php echo $item['points'] >= 0 ? '#059669' : '#DC2626'; ?>"><?php echo ($item['points'] >= 0 ? '+' : '') . $item['points']; ?></td>
                <td><?php echo $item['before_points']; ?></td>
                <td><?php echo $item['after_points']; ?></td>
                <td><?php echo h($item['remark']); ?></td>
                <td><?php echo $item['create_time']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination" style="display:flex; justify-content:center; gap:8px; margin-top:16px;">
    <a href="<?php echo url('admin/points/log') . '?page=' . ($page - 1) . '&type=' . $type; ?>" class="btn btn-sm btn-outline <?php echo $page <= 1 ? 'disabled' : ''; ?>">上一页</a>
    <span style="padding:5px 10px; color:#64748B;"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
    <a href="<?php echo url('admin/points/log') . '?page=' . ($page + 1) . '&type=' . $type; ?>" class="btn btn-sm btn-outline <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">下一页</a>
</div>
<?php endif; ?>
