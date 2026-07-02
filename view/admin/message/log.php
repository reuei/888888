<div class="breadcrumb">系统设置 / 消息模板 / 发送日志</div>
<div class="page-header">
    <h2>发送日志</h2>
    <a href="<?php echo url('admin/message'); ?>" class="btn btn-outline">返回模板列表</a>
</div>

<div class="card" style="margin-bottom: 16px;">
    <form method="get" action="<?php echo url('admin/message/log'); ?>" style="display: flex; gap: 12px; align-items: center;">
        <select name="type" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
            <option value="email" <?php echo $type === 'email' ? 'selected' : ''; ?>>邮件日志</option>
            <option value="sms" <?php echo $type === 'sms' ? 'selected' : ''; ?>>短信日志</option>
        </select>
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="搜索收件人/手机号" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; min-width: 220px;">
        <button type="submit" class="btn btn-outline">筛选</button>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <?php if ($type === 'sms'): ?>
                <th>手机号</th>
                <?php else: ?>
                <th>收件人</th>
                <?php endif; ?>
                <th>模板编码</th>
                <th>内容/主题</th>
                <th>结果</th>
                <th>状态</th>
                <th>时间</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo h($type === 'sms' ? $item['mobile'] : $item['recipient']); ?></td>
                <td><?php echo h($item['template_code']); ?></td>
                <td>
                    <?php if ($type === 'email'): ?>
                    <div style="font-weight: 500;"><?php echo h($item['subject']); ?></div>
                    <div style="font-size: 12px; color: #64748B; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo h(strip_tags($item['content'] ?? '')); ?></div>
                    <?php else: ?>
                    <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo h($item['content']); ?></div>
                    <?php endif; ?>
                </td>
                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo h($item['result']); ?></td>
                <td>
                    <?php if ($item['status'] == 1): ?>
                    <span class="tag tag-green">成功</span>
                    <?php else: ?>
                    <span class="tag tag-orange">失败/调试</span>
                    <?php endif; ?>
                </td>
                <td><?php echo $item['create_time']; ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($list)): ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #64748B;">暂无发送记录</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
<div style="display: flex; justify-content: center; gap: 8px; margin-top: 24px;">
    <?php if ($page > 1): ?>
    <a href="<?php echo url('admin/message/log') . '?page=' . ($page - 1) . '&type=' . $type . '&keyword=' . urlencode($keyword); ?>" class="btn btn-outline">上一页</a>
    <?php endif; ?>
    <span style="padding: 8px 16px; color: #64748B;"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
    <?php if ($page < $totalPages): ?>
    <a href="<?php echo url('admin/message/log') . '?page=' . ($page + 1) . '&type=' . $type . '&keyword=' . urlencode($keyword); ?>" class="btn btn-outline">下一页</a>
    <?php endif; ?>
</div>
<?php endif; ?>
