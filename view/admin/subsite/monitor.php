<div class="breadcrumb">分站管理 / 分站监控</div>
<div class="page-header">
    <h2>分站监控</h2>
    <div>
        <a href="<?php echo url('admin/subsite'); ?>" class="btn btn-outline">分站列表</a>
    </div>
</div>

<div class="card" style="margin-bottom: 16px;">
    <form method="get" action="<?php echo url('admin/subsite/monitor'); ?>" style="display: flex; gap: 12px; align-items: center;">
        <label style="font-weight: 500;">排序维度：</label>
        <select name="sort" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="amount" <?php echo $sort === 'amount' ? 'selected' : ''; ?>>交易额</option>
            <option value="order" <?php echo $sort === 'order' ? 'selected' : ''; ?>>订单量</option>
            <option value="complaint" <?php echo $sort === 'complaint' ? 'selected' : ''; ?>>投诉率</option>
        </select>
        <select name="period" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="7d" <?php echo $period === '7d' ? 'selected' : ''; ?>>近 7 天</option>
            <option value="30d" <?php echo $period === '30d' ? 'selected' : ''; ?>>近 30 天</option>
        </select>
        <button type="submit" class="btn">刷新</button>
    </form>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
    <div class="card" style="border-left: 4px solid #2563EB;">
        <div style="color: #64748B; font-size: 13px;">监控分站数</div>
        <div style="font-size: 24px; font-weight: 600; margin-top: 4px;"><?php echo count($list); ?></div>
    </div>
    <div class="card" style="border-left: 4px solid #10B981;">
        <div style="color: #64748B; font-size: 13px;">总订单数</div>
        <div style="font-size: 24px; font-weight: 600; margin-top: 4px;"><?php echo array_sum(array_column($list, 'order_count')); ?></div>
    </div>
    <div class="card" style="border-left: 4px solid #F59E0B;">
        <div style="color: #64748B; font-size: 13px;">总交易额</div>
        <div style="font-size: 24px; font-weight: 600; margin-top: 4px;">¥ <?php echo number_format(array_sum(array_column($list, 'total_amount')), 2); ?></div>
    </div>
    <div class="card" style="border-left: 4px solid #EF4444;">
        <div style="color: #64748B; font-size: 13px;">异常订单数</div>
        <div style="font-size: 24px; font-weight: 600; margin-top: 4px;"><?php echo array_sum(array_column($list, 'risk_count')); ?></div>
    </div>
</div>

<div class="card">
    <h3 style="font-size: 16px; margin-bottom: 16px;">分站 KPI 排行</h3>
    <table>
        <tr>
            <th>排名</th>
            <th>分站名称</th>
            <th>域名前缀</th>
            <th>商户数</th>
            <th>订单量</th>
            <th>交易额</th>
            <th>异常订单</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="8" style="text-align: center; color: #64748B; padding: 40px;">暂无正常分站</td></tr>
        <?php else: ?>
        <?php foreach ($list as $index => $item): ?>
        <tr>
            <td><?php echo $index + 1; ?></td>
            <td><?php echo h($item['name']); ?></td>
            <td><?php echo h($item['domain_prefix']); ?></td>
            <td><?php echo $item['merchant_count']; ?></td>
            <td><?php echo $item['order_count']; ?></td>
            <td>¥ <?php echo number_format($item['total_amount'], 2); ?></td>
            <td><?php echo $item['risk_count']; ?></td>
            <td>
                <a href="<?php echo url('admin/subsite/detail') . '?id=' . $item['id']; ?>" class="btn btn-sm">下钻详情</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>
