<div class="breadcrumb">订单管理 / 分站订单列表</div>
<div class="page-header">
    <h2>分站订单列表</h2>
    <a href="<?php echo url('subsite/order/complaint'); ?>" class="btn btn-outline">投诉管理</a>
</div>

<div class="card" style="margin-bottom: 16px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px;">
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">订单总数</div>
            <div style="font-size: 20px; font-weight: 600; color: #1F2937;"><?php echo $stat['total_orders'] ?? 0; ?></div>
        </div>
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">成交总额</div>
            <div style="font-size: 20px; font-weight: 600; color: #10B981;">¥ <?php echo number_format($stat['total_amount'] ?? 0, 2); ?></div>
        </div>
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">待发货</div>
            <div style="font-size: 20px; font-weight: 600; color: #F59E0B;"><?php echo $stat['pending_ship'] ?? 0; ?></div>
        </div>
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">风控订单</div>
            <div style="font-size: 20px; font-weight: 600; color: #EF4444;"><?php echo $stat['risk_orders'] ?? 0; ?></div>
        </div>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('subsite/order'); ?>" style="flex-wrap: wrap;">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="订单号 / 商品名 / ID / 商户">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <?php foreach ($statusMap as $k => $v): ?>
            <option value="<?php echo $k; ?>" <?php echo $status === (string)$k ? 'selected' : ''; ?>><?php echo $v; ?></option>
            <?php endforeach; ?>
        </select>
        <select name="pay_channel" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部支付渠道</option>
            <?php foreach ($payChannels as $pc): ?>
            <option value="<?php echo h($pc['pay_channel']); ?>" <?php echo $payChannel === $pc['pay_channel'] ? 'selected' : ''; ?>><?php echo h($pc['pay_channel']); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="risk_flag" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部风险</option>
            <option value="1" <?php echo $riskFlag === '1' ? 'selected' : ''; ?>>风控</option>
            <option value="0" <?php echo $riskFlag === '0' ? 'selected' : ''; ?>>正常</option>
        </select>
        <input type="date" name="start_time" value="<?php echo h($startTime); ?>" style="padding: 7px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
        <input type="date" name="end_time" value="<?php echo h($endTime); ?>" style="padding: 7px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>订单ID</th>
            <th>订单号 / 商品</th>
            <th>商户</th>
            <th>金额</th>
            <th>支付渠道</th>
            <th>状态</th>
            <th>风险</th>
            <th>下单时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="9" style="text-align: center; color: #64748B; padding: 40px;">暂无订单数据</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td>
                <div style="font-weight: 500;"><?php echo h($item['order_no']); ?></div>
                <div style="color: #94A3B8; font-size: 12px;"><?php echo h($item['goods_name']); ?></div>
            </td>
            <td><?php echo h($item['shop_name'] ?? '-'); ?></td>
            <td>
                <div>¥ <?php echo $item['pay_amount']; ?></div>
                <div style="color: #94A3B8; font-size: 12px;">应付 ¥ <?php echo $item['total_amount']; ?></div>
            </td>
            <td><?php echo h($item['pay_channel'] ?: '-'); ?></td>
            <td>
                <?php
                $statusColors = [0 => 'tag-orange', 1 => 'tag-blue', 2 => 'tag-green', 3 => 'tag-green', 4 => 'tag-orange', 5 => 'tag'];
                $color = $statusColors[$item['status']] ?? 'tag';
                ?>
                <span class="tag <?php echo $color; ?>"><?php echo $statusMap[$item['status']] ?? '未知'; ?></span>
            </td>
            <td>
                <?php if ($item['risk_flag']): ?>
                <span class="tag tag-red">风控</span>
                <?php else: ?>
                <span class="tag">正常</span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['create_time']; ?></td>
            <td>
                <a href="<?php echo url('subsite/order/detail') . '?id=' . $item['id']; ?>" class="btn btn-sm">详情</a>
                <?php if ($item['status'] == 1): ?>
                <a href="javascript:;" class="btn btn-sm btn-success" onclick="deliver(<?php echo $item['id']; ?>)" style="margin-left: 4px;">发货</a>
                <a href="javascript:;" class="btn btn-sm btn-warning" onclick="refund(<?php echo $item['id']; ?>)" style="margin-left: 4px;">退款</a>
                <?php endif; ?>
                <?php if ($item['status'] == 0): ?>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="closeOrder(<?php echo $item['id']; ?>)" style="margin-left: 4px;">关闭</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('subsite/order') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status . '&pay_channel=' . urlencode($payChannel) . '&risk_flag=' . $riskFlag . '&start_time=' . $startTime . '&end_time=' . $endTime; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('subsite/order') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status . '&pay_channel=' . urlencode($payChannel) . '&risk_flag=' . $riskFlag . '&start_time=' . $startTime . '&end_time=' . $endTime; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
async function deliver(id) {
    if (!confirm('确认标记该订单为已发货？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('subsite/order/deliver'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function refund(id) {
    const reason = prompt('请输入退款原因：');
    if (!reason) return;
    if (!confirm('确认对该订单发起退款？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('reason', reason);
    const res = await fetch('<?php echo url('subsite/order/refund'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function closeOrder(id) {
    if (!confirm('确认关闭该订单？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('subsite/order/close'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
