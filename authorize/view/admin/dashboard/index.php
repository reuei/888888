<?php
/** @var array $stats */
/** @var array $recentOrders */
/** @var array $pendingPlugins */
/** @var array $pendingRecharges */
$recentOrders      = $recentOrders      ?? [];
$pendingPlugins    = $pendingPlugins    ?? [];
$pendingRecharges  = $pendingRecharges  ?? [];
$stats             = $stats             ?? [];

$pendingPluginCount   = count($pendingPlugins);
$pendingRechargeCount = count($pendingRecharges);

$quickActions = [
    ['icon' => 'product',  'name' => '发布产品',   'url' => 'admin/product/edit'],
    ['icon' => 'plugin',   'name' => '审核插件',   'url' => 'admin/plugin'],
    ['icon' => 'version',  'name' => '发版',       'url' => 'admin/version'],
    ['icon' => 'user',     'name' => '用户管理',   'url' => 'admin/user'],
    ['icon' => 'recharge', 'name' => '充值审核',   'url' => 'admin/recharge'],
    ['icon' => 'setting',  'name' => '系统设置',   'url' => 'admin/setting'],
];
?>
<div class="page-header fade-in-up">
    <div>
        <h2><i data-icon="dashboard" class="svg-icon-lg" style="vertical-align:-4px;margin-right:6px;"></i>仪表盘</h2>
        <div class="page-sub">平台运营数据概览</div>
    </div>
</div>

<!-- 6 个统计卡 -->
<div class="grid-stats fade-in-up">
    <div class="stat-card">
        <div class="stat-icon purple"><i data-icon="dollar"></i></div>
        <div class="stat-value" data-count="<?php echo (float)($stats['total_amount'] ?? 0); ?>" data-prefix="<?php echo h(site_config('currency_unit', '¥')); ?>">0</div>
        <div class="stat-label">销售总额</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i data-icon="order"></i></div>
        <div class="stat-value" data-count="<?php echo (int)($stats['total_orders'] ?? 0); ?>">0</div>
        <div class="stat-label">订单数</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i data-icon="user"></i></div>
        <div class="stat-value" data-count="<?php echo (int)($stats['total_users'] ?? 0); ?>">0</div>
        <div class="stat-label">用户数</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i data-icon="license"></i></div>
        <div class="stat-value" data-count="<?php echo (int)($stats['total_licenses'] ?? 0); ?>">0</div>
        <div class="stat-label">授权码数</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i data-icon="plugin"></i></div>
        <div class="stat-value" data-count="<?php echo $pendingPluginCount; ?>">0</div>
        <div class="stat-label">待审核插件</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i data-icon="recharge"></i></div>
        <div class="stat-value" data-count="<?php echo $pendingRechargeCount; ?>">0</div>
        <div class="stat-label">待处理充值</div>
    </div>
</div>

<!-- 销售趋势图占位 -->
<div class="card fade-in-up trend-card">
    <div class="section-title">
        <span><i data-icon="dashboard" class="svg-icon-sm" style="vertical-align:-2px;margin-right:6px;"></i>近 30 天销售趋势</span>
    </div>
    <div class="trend-chart">
        <svg viewBox="0 0 720 220" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <defs>
                <linearGradient id="trendFill" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0" stop-color="#7c3aed" stop-opacity="0.32"/>
                    <stop offset="1" stop-color="#7c3aed" stop-opacity="0"/>
                </linearGradient>
                <linearGradient id="trendStroke" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="0" stop-color="#7c3aed"/>
                    <stop offset="1" stop-color="#a855f7"/>
                </linearGradient>
            </defs>
            <!-- 网格线 -->
            <line x1="0" y1="44"  x2="720" y2="44"  stroke="#f3f4f6" stroke-width="1"/>
            <line x1="0" y1="88"  x2="720" y2="88"  stroke="#f3f4f6" stroke-width="1"/>
            <line x1="0" y1="132" x2="720" y2="132" stroke="#f3f4f6" stroke-width="1"/>
            <line x1="0" y1="176" x2="720" y2="176" stroke="#f3f4f6" stroke-width="1"/>
            <!-- 区域填充 -->
            <path d="M0 160 L40 140 L80 150 L120 110 L160 120 L200 90 L240 100 L280 70 L320 85 L360 60 L400 75 L440 50 L480 65 L520 40 L560 55 L600 35 L640 45 L680 30 L720 40 L720 220 L0 220 Z" fill="url(#trendFill)"/>
            <!-- 折线 -->
            <path d="M0 160 L40 140 L80 150 L120 110 L160 120 L200 90 L240 100 L280 70 L320 85 L360 60 L400 75 L440 50 L480 65 L520 40 L560 55 L600 35 L640 45 L680 30 L720 40" fill="none" stroke="url(#trendStroke)" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/>
            <!-- 数据点 -->
            <circle cx="120" cy="110" r="3.5" fill="#7c3aed"/>
            <circle cx="280" cy="70"  r="3.5" fill="#7c3aed"/>
            <circle cx="440" cy="50"  r="3.5" fill="#7c3aed"/>
            <circle cx="600" cy="35"  r="3.5" fill="#7c3aed"/>
            <circle cx="720" cy="40"  r="3.5" fill="#7c3aed"/>
        </svg>
        <div class="trend-legend">
            <span class="legend-dot"></span>销售额（<?php echo h(site_config('currency_unit', '¥')); ?>）
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- 最近订单 -->
    <div class="card fade-in-up">
        <div class="section-title">
            <span><i data-icon="order" class="svg-icon-sm" style="vertical-align:-2px;margin-right:6px;"></i>最近订单</span>
            <a href="<?php echo url('admin/order'); ?>">查看全部 <i data-icon="chevron-right" class="svg-icon-sm"></i></a>
        </div>
        <?php if (empty($recentOrders)): ?>
        <div class="empty-tip">暂无订单</div>
        <?php else: ?>
        <div class="table-wrap">
        <table>
            <thead>
                <tr><th>订单号</th><th>用户</th><th>金额</th><th>状态</th><th>时间</th></tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($recentOrders, 0, 8) as $item): ?>
                <tr>
                    <td><span style="font-family:monospace;font-size:12px;"><?php echo h($item['order_no']); ?></span></td>
                    <td><?php echo h($item['username'] ?: ($item['nickname'] ?? '')); ?></td>
                    <td><span class="item-price" style="font-size:14px;"><?php echo format_price($item['pay_amount']); ?></span></td>
                    <td><span class="tag <?php echo ($item['status'] ?? 0) == 1 ? 'tag-green' : 'tag-orange'; ?>"><?php echo ($item['status'] ?? 0) == 1 ? '已支付' : '待支付'; ?></span></td>
                    <td><span style="color:#9ca3af;font-size:12px;"><?php echo h($item['create_time'] ?? ''); ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- 待审核插件 -->
    <div class="card fade-in-up">
        <div class="section-title">
            <span><i data-icon="plugin" class="svg-icon-sm" style="vertical-align:-2px;margin-right:6px;"></i>待审核插件</span>
            <a href="<?php echo url('admin/plugin'); ?>">查看全部 <i data-icon="chevron-right" class="svg-icon-sm"></i></a>
        </div>
        <?php if (empty($pendingPlugins)): ?>
        <div class="empty-tip">暂无待审核插件</div>
        <?php else: ?>
        <div class="table-wrap">
        <table>
            <thead>
                <tr><th>插件名</th><th>开发者</th><th>版本</th><th>操作</th></tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($pendingPlugins, 0, 5) as $item): ?>
                <tr>
                    <td><?php echo h($item['name']); ?></td>
                    <td><?php echo h($item['author'] ?? '-'); ?></td>
                    <td><span class="tag tag-purple"><?php echo h($item['version']); ?></span></td>
                    <td><a href="<?php echo url('admin/plugin/review', ['id' => $item['id']]); ?>" class="btn btn-sm"><i data-icon="check" class="svg-icon-sm"></i>审核</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- 待处理充值 -->
    <div class="card fade-in-up">
        <div class="section-title">
            <span><i data-icon="recharge" class="svg-icon-sm" style="vertical-align:-2px;margin-right:6px;"></i>待处理充值</span>
            <a href="<?php echo url('admin/recharge'); ?>">查看全部 <i data-icon="chevron-right" class="svg-icon-sm"></i></a>
        </div>
        <?php if (empty($pendingRecharges)): ?>
        <div class="empty-tip">暂无待处理充值</div>
        <?php else: ?>
        <div class="table-wrap">
        <table>
            <thead>
                <tr><th>用户</th><th>金额</th><th>方式</th><th>操作</th></tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($pendingRecharges, 0, 5) as $item): ?>
                <tr>
                    <td><?php echo h($item['username'] ?: ($item['nickname'] ?? '-')); ?></td>
                    <td><span class="item-price" style="font-size:14px;"><?php echo format_price($item['amount']); ?></span></td>
                    <td><span class="tag tag-blue"><?php echo h($item['pay_channel'] ?? '线下'); ?></span></td>
                    <td>
                        <button class="btn btn-sm btn-success approve" data-id="<?php echo (int)$item['id']; ?>"><i data-icon="check" class="svg-icon-sm"></i>到账</button>
                        <button class="btn btn-sm btn-outline reject" data-id="<?php echo (int)$item['id']; ?>"><i data-icon="close" class="svg-icon-sm"></i>拒绝</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- 6 个快捷操作网格 -->
<div class="card fade-in-up">
    <div class="section-title">
        <span><i data-icon="dashboard" class="svg-icon-sm" style="vertical-align:-2px;margin-right:6px;"></i>快捷操作</span>
    </div>
    <div class="grid-features">
        <?php foreach ($quickActions as $qa): ?>
        <a class="feature-card" href="<?php echo url($qa['url']); ?>">
            <div class="feature-icon"><i data-icon="<?php echo h($qa['icon']); ?>"></i></div>
            <h3><?php echo h($qa['name']); ?></h3>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<style>
.trend-card { padding: 24px 28px; }
.trend-chart { position: relative; }
.trend-chart svg { width: 100%; height: 220px; display: block; }
.trend-legend {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
    font-size: 13px;
    color: var(--color-text-secondary);
}
.legend-dot {
    width: 10px; height: 10px;
    border-radius: 50%;
    background: var(--gradient-primary);
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: var(--space-md);
    margin-bottom: var(--space-md);
}
.dashboard-grid .card:nth-child(3) { grid-column: 1 / -1; }

@media (max-width: 1024px) {
    .dashboard-grid { grid-template-columns: 1fr; }
    .dashboard-grid .card:nth-child(3) { grid-column: auto; }
}
</style>

<script>
(function () {
    function bindAction(selector, url, confirmMsg) {
        document.querySelectorAll(selector).forEach(function (btn) {
            btn.addEventListener('click', async function () {
                if (!window.confirm(confirmMsg)) return;
                var id = this.dataset.id;
                var reset = (typeof btnLoading === 'function') ? btnLoading(this) : null;
                try {
                    var res = await ajax.post(url, { id: id });
                    if (res.code === 0) {
                        Toast.success(res.msg || '操作成功');
                        setTimeout(function () { location.reload(); }, 700);
                    } else {
                        Toast.error(res.msg || '操作失败');
                    }
                } catch (e) {
                    Toast.error('网络异常，请稍后重试');
                } finally {
                    if (reset) reset();
                }
            });
        });
    }
    bindAction('.approve', '<?php echo url('admin/recharge/approve'); ?>', '确认已到账？');
    bindAction('.reject', '<?php echo url('admin/recharge/reject'); ?>', '确认拒绝该充值申请？');
})();
</script>
