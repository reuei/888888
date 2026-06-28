<div class="breadcrumb">客服管理 / 咨询列表</div>
<div class="page-header">
    <h2>咨询列表</h2>
</div>

<div class="card">
    <form method="GET" action="<?php echo url('merchant/chat'); ?>" class="search-bar">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
            <option value="">全部状态</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>进行中</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>已关闭</option>
        </select>
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="搜索访客昵称/联系方式/最后消息">
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>访客</th>
            <th>联系方式</th>
            <th>最后消息</th>
            <th>未读</th>
            <th>状态</th>
            <th>更新时间</th>
            <th>操作</th>
        </tr>
        <?php foreach ($sessions as $item): ?>
        <tr>
            <td><?php echo h($item['user_name'] ?: '游客' . $item['id']); ?></td>
            <td><?php echo h($item['contact'] ?: '-'); ?></td>
            <td><?php echo h(mb_substr($item['last_message'] ?? '', 0, 30)); ?></td>
            <td>
                <?php if ($item['unread_count'] > 0): ?>
                    <span class="tag tag-red"><?php echo $item['unread_count']; ?></span>
                <?php else: ?>
                    0
                <?php endif; ?>
            </td>
            <td>
                <?php if ($item['status'] == 1): ?>
                    <span class="tag tag-green">进行中</span>
                <?php else: ?>
                    <span class="tag tag-orange">已关闭</span>
                <?php endif; ?>
            </td>
            <td><?php echo h($item['update_time']); ?></td>
            <td>
                <a href="<?php echo url('merchant/chat/session?id=' . $item['id']); ?>" class="btn btn-sm">回复</a>
                <?php if ($item['status'] == 1): ?>
                    <button class="btn btn-sm btn-danger" onclick="closeSession(<?php echo $item['id']; ?>)">关闭</button>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($sessions)): ?>
        <tr>
            <td colspan="7" style="text-align: center; color: #64748B;">暂无咨询记录</td>
        </tr>
        <?php endif; ?>
    </table>

    <?php if ($total > $pageSize): ?>
    <div style="margin-top: 16px; text-align: right;">
        <?php
        $totalPages = (int) ceil($total / $pageSize);
        $baseUrl = url('merchant/chat') . '?status=' . urlencode($status) . '&keyword=' . urlencode($keyword) . '&page=';
        ?>
        <?php if ($page > 1): ?>
            <a href="<?php echo $baseUrl . ($page - 1); ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="margin: 0 12px; color: #64748B;"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
        <?php if ($page < $totalPages): ?>
            <a href="<?php echo $baseUrl . ($page + 1); ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
async function closeSession(id) {
    if (!confirm('确定要关闭该会话吗？')) return;
    try {
        const res = await fetch('<?php echo url('merchant/chat/close'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id
        });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    }
}
</script>
