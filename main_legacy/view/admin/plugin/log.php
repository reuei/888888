<div class="breadcrumb">开放 API / 插件执行日志</div>
<div class="page-header">
    <h2>插件执行日志</h2>
    <a href="<?php echo url('admin/plugin'); ?>" class="btn btn-outline">返回插件列表</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>插件</th>
                <th>事件</th>
                <th>Payload</th>
                <th>响应</th>
                <th>状态</th>
                <th>时间</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo h($item['plugin_name'] ?: $item['code']); ?></td>
                <td><?php echo h($item['event_type']); ?></td>
                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo h($item['payload']); ?></td>
                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo h($item['response']); ?></td>
                <td><?php echo $item['status'] ? '<span class="tag tag-green">成功</span>' : '<span class="tag tag-orange">失败</span>'; ?></td>
                <td><?php echo $item['create_time']; ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($list)): ?>
            <tr><td colspan="7" style="text-align: center; color: #64748B;">暂无日志</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
<div style="display: flex; justify-content: center; gap: 8px; margin-top: 24px;">
    <?php if ($page > 1): ?>
    <a href="<?php echo url('admin/plugin/log') . '?page=' . ($page - 1) . '&plugin_id=' . $pluginId; ?>" class="btn btn-outline">上一页</a>
    <?php endif; ?>
    <span style="padding: 8px 16px; color: #64748B;"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
    <?php if ($page < $totalPages): ?>
    <a href="<?php echo url('admin/plugin/log') . '?page=' . ($page + 1) . '&plugin_id=' . $pluginId; ?>" class="btn btn-outline">下一页</a>
    <?php endif; ?>
</div>
<?php endif; ?>
