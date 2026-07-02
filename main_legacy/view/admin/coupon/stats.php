<div class="breadcrumb">营销 / 优惠券 / 统计报表</div>
<div class="page-header">
    <h2>优惠券统计报表</h2>
    <a href="<?php echo url('admin/coupon'); ?>" class="btn btn-outline">返回优惠券</a>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card" style="text-align: center;">
        <div style="font-size: 28px; font-weight: 700; color: #2563EB;"><?php echo $overview['total'] ?? 0; ?></div>
        <div style="color: #64748B; font-size: 13px; margin-top: 6px;">优惠券总数</div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 28px; font-weight: 700; color: #059669;"><?php echo $overview['active'] ?? 0; ?></div>
        <div style="color: #64748B; font-size: 13px; margin-top: 6px;">启用中</div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 28px; font-weight: 700; color: #F59E0B;"><?php echo $overview['total_received'] ?? 0; ?></div>
        <div style="color: #64748B; font-size: 13px; margin-top: 6px;">累计领取</div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 28px; font-weight: 700; color: #EF4444;"><?php echo $overview['total_used'] ?? 0; ?></div>
        <div style="color: #64748B; font-size: 13px; margin-top: 6px;">累计使用</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card">
        <div class="section-title" style="margin-top: 0;">用户券状态分布</div>
        <div style="display: flex; justify-content: space-around; text-align: center;">
            <div>
                <div style="font-size: 22px; font-weight: 700; color: #2563EB;"><?php echo $userCouponStats['unused'] ?? 0; ?></div>
                <div style="color: #64748B; font-size: 12px;">未使用</div>
            </div>
            <div>
                <div style="font-size: 22px; font-weight: 700; color: #059669;"><?php echo $userCouponStats['used'] ?? 0; ?></div>
                <div style="color: #64748B; font-size: 12px;">已使用</div>
            </div>
            <div>
                <div style="font-size: 22px; font-weight: 700; color: #64748B;"><?php echo $userCouponStats['expired'] ?? 0; ?></div>
                <div style="color: #64748B; font-size: 12px;">已过期</div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="section-title" style="margin-top: 0;">领取 / 使用趋势</div>
        <table>
            <tr>
                <th>时间</th>
                <th>领取</th>
                <th>使用</th>
            </tr>
            <tr>
                <td>今日</td>
                <td><?php echo $todayStats['receive_count'] ?? 0; ?></td>
                <td><?php echo $todayStats['use_count'] ?? 0; ?></td>
            </tr>
            <tr>
                <td>昨日</td>
                <td><?php echo $yesterdayStats['receive_count'] ?? 0; ?></td>
                <td><?php echo $yesterdayStats['use_count'] ?? 0; ?></td>
            </tr>
            <tr>
                <td>本月</td>
                <td><?php echo $monthStats['receive_count'] ?? 0; ?></td>
                <td><?php echo $monthStats['use_count'] ?? 0; ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="card">
    <div class="section-title" style="margin-top: 0;">优惠券领取排行 TOP10</div>
    <table>
        <tr>
            <th>排名</th>
            <th>优惠券</th>
            <th>领取</th>
            <th>使用</th>
            <th>使用率</th>
        </tr>
        <?php if (empty($rank)): ?>
        <tr><td colspan="5" style="text-align: center; color: #64748B; padding: 40px;">暂无数据</td></tr>
        <?php else: ?>
        <?php foreach ($rank as $i => $item): ?>
        <tr>
            <td><?php echo $i + 1; ?></td>
            <td>
                <div style="font-weight: 500;"><?php echo h($item['name']); ?></div>
                <div style="color: #94A3B8; font-size: 12px;"><?php echo $item['code'] ? '券码：' . h($item['code']) : '领取券'; ?></div>
            </td>
            <td><?php echo $item['receive_count']; ?></td>
            <td><?php echo $item['used_count']; ?></td>
            <td><?php echo $item['use_rate']; ?>%</td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>
