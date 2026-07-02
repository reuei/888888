<div class="breadcrumb">开放 API / API 请求日志</div>
<div class="page-header">
    <h2>API 请求日志</h2>
    <a href="<?php echo url('admin/api'); ?>" class="btn btn-outline">返回密钥列表</a>
</div>

<div class="card" style="margin-bottom: 16px;">
    <form method="get" action="<?php echo url('admin/api/log'); ?>" style="display: flex; gap: 12px; align-items: center;">
        <input type="text" name="app_id" value="<?php echo h($appId); ?>" placeholder="输入 App ID" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; min-width: 240px;">
        <button type="submit" class="btn btn-outline">筛选</button>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>App ID</th>
                <th>动作</th>
                <th>参数</th>
                <th>结果</th>
                <th>IP</th>
                <th>状态</th>
                <th>时间</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo h($item['app_id']); ?></td>
                <td><?php echo h($item['action']); ?></td>
                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo h($item['params']); ?></td>
                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo h($item['result']); ?></td>
                <td><?php echo h($item['ip']); ?></td>
                <td><?php echo $item['status'] ? '<span class="tag tag-green">成功</span>' : '<span class="tag tag-orange">失败</span>'; ?></td>
                <td><?php echo $item['create_time']; ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($list)): ?>
            <tr><td colspan="8" style="text-align: center; color: #64748B;">暂无日志</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
<div style="display: flex; justify-content: center; gap: 8px; margin-top: 24px;">
    <?php if ($page > 1): ?>
    <a href="<?php echo url('admin/api/log') . '?page=' . ($page - 1) . '&app_id=' . urlencode($appId); ?>" class="btn btn-outline">上一页</a>
    <?php endif; ?>
    <span style="padding: 8px 16px; color: #64748B;"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
    <?php if ($page < $totalPages): ?>
    <a href="<?php echo url('admin/api/log') . '?page=' . ($page + 1) . '&app_id=' . urlencode($appId); ?>" class="btn btn-outline">下一页</a>
    <?php endif; ?>
</div>
<?php endif; ?>
