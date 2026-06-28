<div class="breadcrumb">首页 / 店铺概览</div>
<div class="page-header">
    <h2>店铺概览</h2>
    <div>
        <a href="<?php echo url('merchant/goods/create'); ?>" class="btn">新增商品</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 16px;">
    <div class="card" style="border-left: 4px solid #10B981;">
        <div style="color: #64748B; font-size: 13px; margin-bottom: 8px;">今日成交额</div>
        <div style="font-size: 22px; font-weight: 600; color: #1F2937;">¥ <?php echo $kpi['today_amount']; ?></div>
    </div>
    <div class="card" style="border-left: 4px solid #2563EB;">
        <div style="color: #64748B; font-size: 13px; margin-bottom: 8px;">今日订单</div>
        <div style="font-size: 22px; font-weight: 600; color: #1F2937;"><?php echo $kpi['today_orders']; ?></div>
    </div>
    <div class="card" style="border-left: 4px solid #F59E0B;">
        <div style="color: #64748B; font-size: 13px; margin-bottom: 8px;">待发货订单</div>
        <div style="font-size: 22px; font-weight: 600; color: #1F2937;"><?php echo $kpi['pending_orders']; ?></div>
    </div>
    <div class="card" style="border-left: 4px solid #8B5CF6;">
        <div style="color: #64748B; font-size: 13px; margin-bottom: 8px;">商品总数</div>
        <div style="font-size: 22px; font-weight: 600; color: #1F2937;"><?php echo $kpi['goods_total']; ?></div>
        <div style="color: #EF4444; font-size: 12px; margin-top: 4px;"><?php echo $kpi['low_stock']; ?> 个库存紧张</div>
    </div>
    <div class="card" style="border-left: 4px solid #EF4444;">
        <div style="color: #64748B; font-size: 13px; margin-bottom: 8px;">账户余额</div>
        <div style="font-size: 22px; font-weight: 600; color: #1F2937;">¥ <?php echo $kpi['balance']; ?></div>
        <div style="color: #64748B; font-size: 12px; margin-top: 4px;">冻结 ¥ <?php echo $kpi['frozen_balance']; ?></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
    <div class="card">
        <h3 style="font-size: 16px; margin-bottom: 16px;">近 7 天销售趋势</h3>
        <?php if (empty($trend)): ?>
        <div style="height: 220px; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #64748B;">
            暂无销售数据
        </div>
        <?php else: ?>
        <table>
            <tr>
                <th>日期</th>
                <th>订单数</th>
                <th>成交额</th>
            </tr>
            <?php foreach ($trend as $row): ?>
            <tr>
                <td><?php echo $row['day']; ?></td>
                <td><?php echo $row['orders']; ?></td>
                <td>¥ <?php echo number_format($row['amount'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3 style="font-size: 16px; margin-bottom: 16px;">最近订单</h3>
        <?php if (empty($latestOrders)): ?>
        <div style="color: #64748B; text-align: center; padding: 40px 0;">暂无订单</div>
        <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($latestOrders as $order): ?>
            <div style="border-bottom: 1px solid #F1F5F9; padding-bottom: 10px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                    <span style="color: #64748B;"><?php echo h($order['order_no']); ?></span>
                    <span style="color: #EF4444; font-weight: 500;">¥<?php echo $order['pay_amount']; ?></span>
                </div>
                <div style="font-size: 13px; margin-top: 4px;" class="order-status-text" data-status="<?php echo $order['status']; ?>"></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
const statusMap = {
    0: ['待支付', '#D97706'],
    1: ['待发货', '#2563EB'],
    2: ['已发货', '#059669'],
    3: ['已完成', '#059669'],
    4: ['退款中', '#EF4444'],
    5: ['已关闭', '#64748B'],
};
document.querySelectorAll('.order-status-text').forEach(el => {
    const s = statusMap[el.dataset.status] || ['未知', '#475569'];
    el.textContent = s[0];
    el.style.color = s[1];
});
</script>
