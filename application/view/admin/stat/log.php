<div class="breadcrumb">数据统计 / 操作日志</div>
<div class="page-header">
    <h2>操作日志</h2>
    <div>
        <a href="<?php echo url('admin/stat/report'); ?>" class="btn btn-outline">经营报表</a>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/stat/log'); ?>" style="flex-wrap: wrap;">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="关键字 / 操作 / 内容">
        <select name="action" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部操作</option>
            <?php foreach ($actions as $a): ?>
            <option value="<?php echo h($a['action']); ?>" <?php echo $action === $a['action'] ? 'selected' : ''; ?>><?php echo h($a['action']); ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="admin_name" value="<?php echo h($adminName); ?>" placeholder="管理员账号" style="max-width: 160px;">
        <input type="date" name="start_time" value="<?php echo h($startTime); ?>" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
        <input type="date" name="end_time" value="<?php echo h($endTime); ?>" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>管理员</th>
            <th>操作</th>
            <th>内容</th>
            <th>IP</th>
            <th>时间</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="6" style="text-align: center; color: #64748B; padding: 40px;">暂无操作日志</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['admin_name'] ?: '系统'); ?></td>
            <td><span class="tag tag-blue"><?php echo h($item['action']); ?></span></td>
            <td>
                <div style="max-width: 400px; word-break: break-all; color: #475569;">
                    <?php echo nl2br(h($item['content'])); ?>
                </div>
            </td>
            <td><?php echo h($item['ip']); ?></td>
            <td><?php echo $item['create_time']; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('admin/stat/log') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&action=' . urlencode($action) . '&admin_name=' . urlencode($adminName) . '&start_time=' . urlencode($startTime) . '&end_time=' . urlencode($endTime); ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('admin/stat/log') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&action=' . urlencode($action) . '&admin_name=' . urlencode($adminName) . '&start_time=' . urlencode($startTime) . '&end_time=' . urlencode($endTime); ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
