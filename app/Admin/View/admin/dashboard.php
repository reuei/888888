<div class="stat-grid stat-grid-4">
    <div class="stat-card stat-card-primary">
        <div class="stat-card-icon icon-order"></div>
        <div class="stat-card-body">
            <div class="stat-card-label">总订单</div>
            <div class="stat-card-num"><?= number_format($stats['total_orders']) ?></div>
            <div class="stat-card-trend up">↑ 今日 <?= $stats['today_orders'] ?></div>
        </div>
    </div>
    <div class="stat-card stat-card-success">
        <div class="stat-card-icon icon-money"></div>
        <div class="stat-card-body">
            <div class="stat-card-label">总收入</div>
            <div class="stat-card-num">¥<?= format_money($stats['total_income']) ?></div>
            <div class="stat-card-trend up">↑ 今日 ¥<?= format_money($stats['today_income']) ?></div>
        </div>
    </div>
    <div class="stat-card stat-card-warning">
        <div class="stat-card-icon icon-user"></div>
        <div class="stat-card-body">
            <div class="stat-card-label">用户数</div>
            <div class="stat-card-num"><?= number_format($stats['total_users']) ?></div>
            <div class="stat-card-trend up">↑ 今日 <?= $stats['today_users'] ?></div>
        </div>
    </div>
    <div class="stat-card stat-card-info">
        <div class="stat-card-icon icon-shop"></div>
        <div class="stat-card-body">
            <div class="stat-card-label">店铺数</div>
            <div class="stat-card-num"><?= number_format($stats['total_shops']) ?></div>
            <div class="stat-card-trend">商品 <?= $stats['total_goods'] ?></div>
        </div>
    </div>
</div>

<div class="chart-grid">
    <div class="chart-card">
        <div class="chart-head">
            <h3 class="chart-title">订单趋势</h3>
            <span class="chart-sub">最近7天</span>
        </div>
        <div class="chart-body" id="orderChart">
            <?php
            $max = max($trends['orders'] ?: [0]);
            foreach ($trends['days'] as $i => $day):
                $val = $trends['orders'][$i] ?? 0;
                $h = $max > 0 ? ($val / $max * 100) : 0;
            ?>
            <div class="chart-bar-wrap">
                <div class="chart-bar-value"><?= $val ?></div>
                <div class="chart-bar" style="height: <?= $h ?>%"></div>
                <div class="chart-bar-label"><?= h($day) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-head">
            <h3 class="chart-title">收入趋势</h3>
            <span class="chart-sub">最近7天</span>
        </div>
        <div class="chart-body" id="incomeChart">
            <?php
            $max = max($trends['income'] ?: [0]);
            foreach ($trends['days'] as $i => $day):
                $val = $trends['income'][$i] ?? 0;
                $h = $max > 0 ? ($val / $max * 100) : 0;
            ?>
            <div class="chart-bar-wrap">
                <div class="chart-bar-value">¥<?= number_format($val) ?></div>
                <div class="chart-bar chart-bar-success" style="height: <?= $h ?>%"></div>
                <div class="chart-bar-label"><?= h($day) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">最近订单</h3>
        <a href="/admin/data/log" class="panel-link">查看全部</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>订单号</th>
                <th>商品</th>
                <th>金额</th>
                <th>状态</th>
                <th>时间</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentOrders as $o): ?>
            <tr>
                <td><?= h($o['order_no']) ?></td>
                <td><?= h($o['goods_name']) ?></td>
                <td>¥<?= format_money($o['amount']) ?></td>
                <td>
                    <?php $st = (int) $o['status']; ?>
                    <span class="badge badge-<?= $st === 1 ? 'success' : ($st === 0 ? 'warning' : 'default') ?>">
                        <?= $st === 1 ? '已完成' : '待支付' ?>
                    </span>
                </td>
                <td class="muted"><?= format_time($o['create_time']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
