<div class="breadcrumb">首页 / 店铺概览</div>
<div class="page-header">
    <h2>店铺概览</h2>
    <div>
        <a href="<?php echo url('merchant/goods/create'); ?>" class="btn">新增商品</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
    <div class="card" style="border-left: 4px solid #10B981;">
        <div style="color: #64748B; font-size: 13px; margin-bottom: 8px;">今日成交额</div>
        <div style="font-size: 24px; font-weight: 600; color: #1F2937;">¥ <?php echo $kpi['today_amount']; ?></div>
    </div>
    <div class="card" style="border-left: 4px solid #2563EB;">
        <div style="color: #64748B; font-size: 13px; margin-bottom: 8px;">今日订单</div>
        <div style="font-size: 24px; font-weight: 600; color: #1F2937;"><?php echo $kpi['today_orders']; ?></div>
    </div>
    <div class="card" style="border-left: 4px solid #F59E0B;">
        <div style="color: #64748B; font-size: 13px; margin-bottom: 8px;">待处理订单</div>
        <div style="font-size: 24px; font-weight: 600; color: #1F2937;"><?php echo $kpi['pending_orders']; ?></div>
    </div>
    <div class="card" style="border-left: 4px solid #EF4444;">
        <div style="color: #64748B; font-size: 13px; margin-bottom: 8px;">账户余额</div>
        <div style="font-size: 24px; font-weight: 600; color: #1F2937;">¥ <?php echo $kpi['balance']; ?></div>
        <div style="color: #64748B; font-size: 12px; margin-top: 4px;">冻结 ¥ <?php echo $kpi['frozen_balance']; ?></div>
    </div>
</div>

<div class="card">
    <h3 style="font-size: 16px; margin-bottom: 16px;">近 7 天销售趋势</h3>
    <div style="height: 240px; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #64748B;">
        店铺销售趋势图占位
    </div>
</div>
