<div class="breadcrumb">首页 / 仪表盘</div>
<div class="page-header">
    <h2>仪表盘</h2>
    <div>
        <a href="#" class="btn btn-outline">导出报表</a>
        <a href="#" class="btn" style="margin-left: 8px;">快捷查单</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 16px;">
    <div class="card" style="border-left: 4px solid #2563EB;">
        <div style="color: #64748B; font-size: 13px; margin-bottom: 8px;">总交易额</div>
        <div style="font-size: 28px; font-weight: 600; color: #1F2937;">¥ <?php echo $kpi['total_amount']; ?></div>
        <div style="color: #10B981; font-size: 12px; margin-top: 4px;">↑ 12.5% 环比</div>
    </div>
    <div class="card" style="border-left: 4px solid #10B981;">
        <div style="color: #64748B; font-size: 13px; margin-bottom: 8px;">总订单量</div>
        <div style="font-size: 28px; font-weight: 600; color: #1F2937;"><?php echo $kpi['total_orders']; ?></div>
        <div style="color: #10B981; font-size: 12px; margin-top: 4px;">↑ 8.3% 环比</div>
    </div>
    <div class="card" style="border-left: 4px solid #F59E0B;">
        <div style="color: #64748B; font-size: 13px; margin-bottom: 8px;">平台抽成收入</div>
        <div style="font-size: 28px; font-weight: 600; color: #1F2937;">¥ <?php echo $kpi['platform_income']; ?></div>
        <div style="color: #EF4444; font-size: 12px; margin-top: 4px;">↓ 2.1% 环比</div>
    </div>
    <div class="card" style="border-left: 4px solid #EF4444;">
        <div style="color: #64748B; font-size: 13px; margin-bottom: 8px;">商户 / 用户</div>
        <div style="font-size: 28px; font-weight: 600; color: #1F2937;"><?php echo $kpi['merchant_count']; ?> / <?php echo $kpi['user_count']; ?></div>
        <div style="color: #64748B; font-size: 12px; margin-top: 4px;">累计入驻</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 16px;">交易趋势</h3>
            <div>
                <span class="tag tag-blue">近 7 天</span>
                <span class="tag" style="margin-left: 8px;">近 30 天</span>
            </div>
        </div>
        <div style="height: 260px; background: #F8FAFC; border: 1px dashed #CBD5E1; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #64748B;">
            折线图占位（后续接入 ECharts 数据）
        </div>
    </div>
    <div class="card">
        <h3 style="font-size: 16px; margin-bottom: 16px;">商家交易额 Top 5</h3>
        <table>
            <tr><th>店铺</th><th>交易额</th></tr>
            <tr><td>极速卡密店</td><td>¥ 128,400</td></tr>
            <tr><td>游戏点卡专卖</td><td>¥ 96,200</td></tr>
            <tr><td>会员共享站</td><td>¥ 74,500</td></tr>
            <tr><td>软件授权中心</td><td>¥ 62,100</td></tr>
            <tr><td>影视会员店</td><td>¥ 48,900</td></tr>
        </table>
    </div>
</div>
