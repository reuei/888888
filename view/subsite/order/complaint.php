<div class="breadcrumb">订单管理 / 分站投诉管理</div>
<div class="page-header">
    <h2>分站投诉管理</h2>
    <a href="<?php echo url('subsite/order'); ?>" class="btn btn-outline">订单列表</a>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('subsite/order/complaint'); ?>">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>待处理</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>已处理</option>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>投诉ID</th>
            <th>订单 / 商品</th>
            <th>商户</th>
            <th>投诉类型</th>
            <th>投诉内容</th>
            <th>状态</th>
            <th>投诉时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="8" style="text-align: center; color: #64748B; padding: 40px;">暂无投诉记录</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td>
                <div style="font-weight: 500;"><?php echo h($item['order_no'] ?? '-'); ?></div>
                <div style="color: #94A3B8; font-size: 12px;"><?php echo h($item['goods_name'] ?? '-'); ?></div>
            </td>
            <td><?php echo h($item['shop_name'] ?? '-'); ?></td>
            <td><?php echo h($item['type']); ?></td>
            <td style="max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo h($item['content']); ?></td>
            <td>
                <?php if ($item['status'] == 1): ?>
                <span class="tag tag-green">已处理</span>
                <?php else: ?>
                <span class="tag tag-orange">待处理</span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['create_time']; ?></td>
            <td>
                <a href="<?php echo url('subsite/order/detail') . '?id=' . $item['order_id']; ?>" class="btn btn-sm">订单详情</a>
                <?php if ($item['status'] == 0): ?>
                <a href="javascript:;" class="btn btn-sm btn-success" onclick="handleComplaint(<?php echo $item['id']; ?>)" style="margin-left: 4px;">处理</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('subsite/order/complaint') . '?page=' . ($page - 1) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('subsite/order/complaint') . '?page=' . ($page + 1) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
async function handleComplaint(id) {
    const result = prompt('请输入处理结果（如：驳回投诉 / 退款买家 / 协商解决）：');
    if (!result) return;
    const remark = prompt('请输入处理备注（可选）：') || '';
    if (!confirm('确认提交处理结果？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('result', result);
    form.append('remark', remark);
    const res = await fetch('<?php echo url('subsite/order/complaintHandle'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
