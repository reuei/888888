<div class="breadcrumb">仪表盘 / 分站概览</div>
<div class="page-header">
    <h2>分站概览</h2>
    <span style="color: #64748B; font-size: 13px;"><?php echo h($subsite['name'] ?? '-'); ?>（<?php echo h($subsite['domain_prefix'] ?? '-'); ?>）</span>
</div>

<div class="card" style="margin-bottom: 16px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px;">
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">商户总数</div>
            <div style="font-size: 20px; font-weight: 600; color: #1F2937;"><?php echo $kpi['merchant_total']; ?></div>
        </div>
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">待审核商户</div>
            <div style="font-size: 20px; font-weight: 600; color: #F59E0B;"><?php echo $kpi['merchant_pending']; ?></div>
        </div>
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">今日订单</div>
            <div style="font-size: 20px; font-weight: 600; color: #2563EB;"><?php echo $kpi['today_orders']; ?></div>
        </div>
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">今日成交额</div>
            <div style="font-size: 20px; font-weight: 600; color: #10B981;">¥ <?php echo $kpi['today_amount']; ?></div>
        </div>
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">待发货订单</div>
            <div style="font-size: 20px; font-weight: 600; color: #EF4444;"><?php echo $kpi['pending_orders']; ?></div>
        </div>
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">在售商品</div>
            <div style="font-size: 20px; font-weight: 600; color: #8B5CF6;"><?php echo $kpi['goods_onsale']; ?> / <?php echo $kpi['goods_total']; ?></div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;">近 7 天订单趋势</h3>
    <?php if (empty($trend)): ?>
    <div style="text-align: center; color: #64748B; padding: 40px;">暂无数据</div>
    <?php else: ?>
    <table>
        <tr>
            <th>日期</th>
            <th>订单数</th>
            <th>成交额</th>
        </tr>
        <?php foreach ($trend as $t): ?>
        <tr>
            <td><?php echo $t['day']; ?></td>
            <td><?php echo $t['orders']; ?></td>
            <td>¥ <?php echo number_format($t['amount'], 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>

<div class="card">
    <h3 style="font-size: 16px; margin-bottom: 16px;">最近订单</h3>
    <table>
        <tr>
            <th>订单号</th>
            <th>商品</th>
            <th>金额</th>
            <th>状态</th>
            <th>下单时间</th>
        </tr>
        <?php if (empty($latestOrders)): ?>
        <tr><td colspan="5" style="text-align: center; color: #64748B; padding: 40px;">暂无订单</td></tr>
        <?php else: ?>
        <?php foreach ($latestOrders as $o): ?>
        <tr>
            <td><?php echo h($o['order_no']); ?></td>
            <td><?php echo h($o['goods_name']); ?></td>
            <td>¥ <?php echo $o['pay_amount']; ?></td>
            <td>
                <?php
                $statusMap = [0 => '待支付', 1 => '已支付', 2 => '已发货', 3 => '已完成', 4 => '退款中', 5 => '已关闭'];
                $statusColors = [0 => 'tag-orange', 1 => 'tag-blue', 2 => 'tag-green', 3 => 'tag-green', 4 => 'tag-orange', 5 => 'tag'];
                ?>
                <span class="tag <?php echo $statusColors[$o['status']] ?? 'tag'; ?>"><?php echo $statusMap[$o['status']] ?? '未知'; ?></span>
            </td>
            <td><?php echo $o['create_time']; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>
