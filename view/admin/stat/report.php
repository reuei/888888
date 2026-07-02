<div class="breadcrumb">数据统计 / 经营报表</div>
<div class="page-header">
    <h2>经营报表</h2>
    <div>
        <a href="<?php echo url('admin/stat/log'); ?>" class="btn btn-outline">操作日志</a>
    </div>
</div>

<div class="card" style="margin-bottom: 16px;">
    <form class="search-bar" method="get" action="<?php echo url('admin/stat/report'); ?>" style="flex-wrap: wrap;">
        <select name="period" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="today" <?php echo $period === 'today' ? 'selected' : ''; ?>>今日</option>
            <option value="7d" <?php echo $period === '7d' ? 'selected' : ''; ?>>近7天</option>
            <option value="30d" <?php echo $period === '30d' ? 'selected' : ''; ?>>近30天</option>
            <option value="90d" <?php echo $period === '90d' ? 'selected' : ''; ?>>近90天</option>
        </select>
        <select name="subsite_id" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部分站</option>
            <?php foreach ($subsites as $s): ?>
            <option value="<?php echo $s['id']; ?>" <?php echo $subsiteId === (string)$s['id'] ? 'selected' : ''; ?>><?php echo h($s['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="merchant_id" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部商户</option>
            <?php foreach ($merchants as $m): ?>
            <option value="<?php echo $m['id']; ?>" <?php echo $merchantId === (string)$m['id'] ? 'selected' : ''; ?>><?php echo h($m['shop_name']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">刷新</button>
    </form>
</div>

<div class="card" style="margin-bottom: 16px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px;">
        <div style="text-align: center; padding: 16px; background: #F8FAFC; border-radius: 8px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">今日订单数</div>
            <div style="font-size: 24px; font-weight: 600; color: #2563EB;"><?php echo $overview['total_orders'] ?? 0; ?></div>
        </div>
        <div style="text-align: center; padding: 16px; background: #F8FAFC; border-radius: 8px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">今日成交额</div>
            <div style="font-size: 24px; font-weight: 600; color: #10B981;">¥ <?php echo number_format($overview['total_amount'] ?? 0, 2); ?></div>
        </div>
        <div style="text-align: center; padding: 16px; background: #F8FAFC; border-radius: 8px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">今日实付金额</div>
            <div style="font-size: 24px; font-weight: 600; color: #8B5CF6;">¥ <?php echo number_format($overview['pay_amount'] ?? 0, 2); ?></div>
        </div>
        <div style="text-align: center; padding: 16px; background: #F8FAFC; border-radius: 8px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">客单价</div>
            <div style="font-size: 24px; font-weight: 600; color: #F59E0B;">¥ <?php echo number_format($overview['avg_amount'] ?? 0, 2); ?></div>
        </div>
        <div style="text-align: center; padding: 16px; background: #F8FAFC; border-radius: 8px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">今日新增用户</div>
            <div style="font-size: 24px; font-weight: 600; color: #1F2937;"><?php echo $todayUsers; ?></div>
        </div>
        <div style="text-align: center; padding: 16px; background: #F8FAFC; border-radius: 8px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">今日新增商户</div>
            <div style="font-size: 24px; font-weight: 600; color: #1F2937;"><?php echo $todayMerchants; ?></div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">订单 / 成交额趋势</h3>
    <div id="trendChart" style="height: 320px; position: relative;">
        <canvas id="trendCanvas" style="width: 100%; height: 100%;"></canvas>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 16px;">
    <div class="card">
        <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">分站成交额 TOP10</h3>
        <table>
            <tr>
                <th>排名</th>
                <th>分站</th>
                <th>订单数</th>
                <th>成交额</th>
            </tr>
            <?php if (empty($subsiteRank)): ?>
            <tr><td colspan="4" style="text-align: center; color: #64748B; padding: 40px;">暂无数据</td></tr>
            <?php else: ?>
            <?php foreach ($subsiteRank as $index => $item): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo h($item['name'] ?? '-'); ?></td>
                <td><?php echo $item['order_count']; ?></td>
                <td>¥ <?php echo number_format($item['total_amount'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

    <div class="card">
        <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">商品成交额 TOP10</h3>
        <table>
            <tr>
                <th>排名</th>
                <th>商品</th>
                <th>订单数</th>
                <th>成交额</th>
            </tr>
            <?php if (empty($goodsRank)): ?>
            <tr><td colspan="4" style="text-align: center; color: #64748B; padding: 40px;">暂无数据</td></tr>
            <?php else: ?>
            <?php foreach ($goodsRank as $index => $item): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td><?php echo h($item['name'] ?? '-'); ?></td>
                <td><?php echo $item['order_count']; ?></td>
                <td>¥ <?php echo number_format($item['total_amount'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</div>

<script>
const trendData = <?php echo json_encode($trend); ?>;
const dates = trendData.map(d => d.date.slice(5));
const amounts = trendData.map(d => parseFloat(d.total_amount));
const counts = trendData.map(d => parseInt(d.order_count));

function drawChart() {
    const canvas = document.getElementById('trendCanvas');
    const ctx = canvas.getContext('2d');
    const rect = canvas.getBoundingClientRect();
    canvas.width = rect.width * 2;
    canvas.height = rect.height * 2;
    ctx.scale(2, 2);

    const width = rect.width;
    const height = rect.height;
    const padding = { top: 30, right: 50, bottom: 40, left: 50 };
    const chartW = width - padding.left - padding.right;
    const chartH = height - padding.top - padding.bottom;

    ctx.clearRect(0, 0, width, height);

    if (amounts.length === 0) {
        ctx.fillStyle = '#94A3B8';
        ctx.font = '14px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('暂无数据', width / 2, height / 2);
        return;
    }

    const maxAmount = Math.max(...amounts, 0.01);
    const maxCount = Math.max(...counts, 1);
    const barWidth = Math.min(32, chartW / amounts.length * 0.5);
    const stepX = chartW / amounts.length;

    // 绘制网格线
    ctx.strokeStyle = '#E2E8F0';
    ctx.lineWidth = 1;
    for (let i = 0; i <= 5; i++) {
        const y = padding.top + chartH - (chartH / 5) * i;
        ctx.beginPath();
        ctx.moveTo(padding.left, y);
        ctx.lineTo(width - padding.right, y);
        ctx.stroke();

        // 左侧金额刻度
        ctx.fillStyle = '#64748B';
        ctx.font = '11px sans-serif';
        ctx.textAlign = 'right';
        ctx.fillText('¥' + (maxAmount / 5 * i).toFixed(0), padding.left - 8, y + 4);
    }

    // 绘制柱状图（金额）
    trendData.forEach((d, i) => {
        const x = padding.left + stepX * i + stepX / 2;
        const barH = (parseFloat(d.total_amount) / maxAmount) * chartH;
        const y = padding.top + chartH - barH;

        ctx.fillStyle = '#3B82F6';
        ctx.fillRect(x - barWidth / 2, y, barWidth, barH);

        // 日期
        ctx.fillStyle = '#64748B';
        ctx.font = '11px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(dates[i], x, height - padding.bottom + 16);
    });

    // 绘制订单数折线
    ctx.strokeStyle = '#10B981';
    ctx.lineWidth = 2;
    ctx.beginPath();
    trendData.forEach((d, i) => {
        const x = padding.left + stepX * i + stepX / 2;
        const y = padding.top + chartH - (parseInt(d.order_count) / maxCount) * chartH;
        if (i === 0) ctx.moveTo(x, y);
        else ctx.lineTo(x, y);
    });
    ctx.stroke();

    // 右侧订单数刻度
    ctx.fillStyle = '#10B981';
    ctx.font = '11px sans-serif';
    ctx.textAlign = 'left';
    ctx.fillText('订单: ' + maxCount, width - padding.right + 6, padding.top + 4);
    ctx.fillText('0', width - padding.right + 6, padding.top + chartH + 4);
}

window.addEventListener('load', drawChart);
window.addEventListener('resize', drawChart);
</script>
