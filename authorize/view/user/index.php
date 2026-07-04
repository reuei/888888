<?php
/** @var array $currentUser */
$currentUser  = $currentUser ?? $user ?? [];
$licenses      = $licenses      ?? [];
$orders        = $orders        ?? [];
$plugins       = $plugins       ?? [];
$licenseCount  = $licenseCount  ?? count($licenses);
$pluginCount   = $pluginCount   ?? count($plugins);
$orderCount    = $orderCount    ?? count($orders);

$userName   = $currentUser['nickname'] ?: ($currentUser['username'] ?? '');
$userAvatar = mb_substr($userName ?: 'U', 0, 1);
$balance    = (float)($currentUser['balance'] ?? 0);
$createTime = $currentUser['create_time'] ?? '';
$userLevel  = $currentUser['level'] ?? 0;

$sideNav = [
    ['icon' => 'dashboard', 'name' => '我的资料',  'url' => 'user/profile',   'active' => false],
    ['icon' => 'license',   'name' => '授权码管理', 'url' => 'user/license',   'active' => false],
    ['icon' => 'order',     'name' => '订单记录',   'url' => 'user/order',     'active' => false],
    ['icon' => 'recharge',  'name' => '充值记录',   'url' => 'user/recharge',  'active' => false],
    ['icon' => 'plugin',   'name' => '我的插件',   'url' => 'user/plugin',    'active' => false],
    ['icon' => 'key',      'name' => '修改密码',   'url' => 'user/password',  'active' => false],
];
$quickActions = [
    ['icon' => 'product',  'name' => '购买授权',   'desc' => '浏览授权产品',    'url' => 'product'],
    ['icon' => 'plugin',   'name' => '获取插件',   'desc' => '探索插件市场',    'url' => 'plugin'],
    ['icon' => 'recharge', 'name' => '余额充值',   'desc' => '为账户充值',      'url' => 'user/recharge'],
    ['icon' => 'license',  'name' => '我的授权',   'desc' => '管理授权码',      'url' => 'user/license'],
];
?>
<div class="user-layout">
    <!-- 左侧侧边导航 -->
    <aside class="user-sidebar fade-in-up">
        <div class="user-side-header">
            <div class="user-side-avatar"><?php echo h($userAvatar); ?></div>
            <div style="min-width:0;">
                <div style="font-weight:600;font-size:13px;color:#1e1b2e;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo h($userName); ?></div>
                <div style="font-size:11px;color:#9ca3af;">个人中心</div>
            </div>
        </div>
        <a href="<?php echo url('user'); ?>" class="active">
            <i data-icon="dashboard"></i><span>个人中心</span>
        </a>
        <?php foreach ($sideNav as $nav): ?>
        <a href="<?php echo url($nav['url']); ?>">
            <i data-icon="<?php echo h($nav['icon']); ?>"></i><span><?php echo h($nav['name']); ?></span>
        </a>
        <?php endforeach; ?>
        <a href="<?php echo url('login/logout'); ?>" class="user-side-logout">
            <i data-icon="logout"></i><span>退出登录</span>
        </a>
    </aside>

    <!-- 右侧内容区 -->
    <div class="user-content">
        <!-- 顶部渐变欢迎卡 -->
        <div class="user-welcome fade-in-up user-welcome-v2">
            <div class="user-welcome-main">
                <div class="user-welcome-avatar"><?php echo h($userAvatar); ?></div>
                <div class="user-welcome-info">
                    <div class="user-welcome-name">
                        欢迎回来，<?php echo h($userName); ?> 👋
                        <?php if ($userLevel): ?>
                        <span class="user-level-badge">Lv.<?php echo (int)$userLevel; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="user-welcome-meta">
                        <span><i data-icon="user" class="svg-icon-sm"></i><?php echo h($currentUser['username'] ?? ''); ?></span>
                        <?php if ($createTime): ?>
                        <span><i data-icon="clock" class="svg-icon-sm"></i>注册于 <?php echo h(date('Y-m-d', strtotime($createTime))); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="user-welcome-balance">
                <div class="balance-label">账户余额</div>
                <div class="balance-value">
                    <span class="balance-prefix"><?php echo h(site_config('currency_unit', '¥')); ?></span>
                    <span class="stat-value" data-count="<?php echo $balance; ?>"><?php echo number_format($balance, 2); ?></span>
                </div>
                <a href="<?php echo url('user/recharge'); ?>" class="btn btn-sm btn-primary-hero">充值</a>
            </div>
        </div>

        <!-- 4 个统计卡 -->
        <div class="grid-stats fade-in-up">
            <div class="stat-card">
                <div class="stat-icon purple"><i data-icon="license"></i></div>
                <div class="stat-value" data-count="<?php echo (int)$licenseCount; ?>">0</div>
                <div class="stat-label">授权码数量</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue"><i data-icon="order"></i></div>
                <div class="stat-value" data-count="<?php echo (int)$orderCount; ?>">0</div>
                <div class="stat-label">订单总数</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i data-icon="dollar"></i></div>
                <div class="stat-value" data-count="<?php echo $balance; ?>" data-prefix="<?php echo h(site_config('currency_unit', '¥')); ?>">0</div>
                <div class="stat-label">账户余额</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i data-icon="plugin"></i></div>
                <div class="stat-value" data-count="<?php echo (int)$pluginCount; ?>">0</div>
                <div class="stat-label">插件数量</div>
            </div>
        </div>

        <!-- 最近授权码列表 -->
        <div class="card fade-in-up">
            <div class="section-title">
                <span><i data-icon="license" class="svg-icon-sm" style="vertical-align:-2px;margin-right:6px;"></i>最近授权码</span>
                <a href="<?php echo url('user/license'); ?>">查看全部 <i data-icon="chevron-right" class="svg-icon-sm"></i></a>
            </div>
            <?php if (empty($licenses)): ?>
            <div class="empty-tip">暂无授权码</div>
            <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>产品</th><th>授权码</th><th>类型</th><th>状态</th><th>过期</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($licenses, 0, 5) as $item): ?>
                        <tr>
                            <td><?php echo h($item['product_name'] ?? ($item['name'] ?? '-')); ?></td>
                            <td><span style="font-family:monospace;font-size:12px;"><?php echo h($item['auth_code'] ?? '-'); ?></span></td>
                            <td>
                                <span class="tag <?php echo ($item['license_type'] ?? '') === 'domain' ? 'tag-blue' : 'tag-green'; ?>">
                                    <?php echo ($item['license_type'] ?? '') === 'domain' ? '域名授权' : '授权码'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="tag <?php echo ($item['status'] ?? 0) == 1 ? 'tag-green' : 'tag-orange'; ?>">
                                    <?php echo ($item['status'] ?? 0) == 1 ? '正常' : '禁用'; ?>
                                </span>
                            </td>
                            <td><span style="color:#9ca3af;font-size:12px;"><?php echo !empty($item['expire_time']) ? h($item['expire_time']) : '永久'; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- 快捷操作网格 -->
        <div class="card fade-in-up">
            <div class="section-title">
                <span><i data-icon="cart" class="svg-icon-sm" style="vertical-align:-2px;margin-right:6px;"></i>快捷操作</span>
            </div>
            <div class="grid-features">
                <?php foreach ($quickActions as $qa): ?>
                <a class="feature-card" href="<?php echo url($qa['url']); ?>">
                    <div class="feature-icon"><i data-icon="<?php echo h($qa['icon']); ?>"></i></div>
                    <h3><?php echo h($qa['name']); ?></h3>
                    <p><?php echo h($qa['desc']); ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* 欢迎卡 V2 */
.user-welcome-v2 {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
    padding: 28px 32px;
}
.user-welcome-main { display: flex; align-items: center; gap: 18px; min-width: 0; }
.user-welcome-avatar {
    width: 64px; height: 64px;
    border-radius: 50%;
    background: rgba(255,255,255,0.28);
    border: 2px solid rgba(255,255,255,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 26px;
    font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 8px 24px rgba(91,33,182,0.25);
}
.user-welcome-info { min-width: 0; }
.user-welcome-name {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}
.user-level-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 10px;
    background: rgba(255,255,255,0.24);
    border: 1px solid rgba(255,255,255,0.4);
    border-radius: var(--radius-pill);
    font-size: 12px;
    font-weight: 600;
}
.user-welcome-meta {
    display: flex;
    gap: 18px;
    flex-wrap: wrap;
    font-size: 13px;
    opacity: 0.92;
}
.user-welcome-meta span { display: inline-flex; align-items: center; gap: 4px; }
.user-welcome-balance {
    text-align: right;
    background: rgba(255,255,255,0.14);
    border: 1px solid rgba(255,255,255,0.28);
    border-radius: var(--radius-lg);
    padding: 16px 22px;
    backdrop-filter: blur(6px);
}
.balance-label { font-size: 12px; opacity: 0.85; margin-bottom: 4px; }
.balance-value {
    font-size: 26px;
    font-weight: 800;
    margin-bottom: 10px;
    font-variant-numeric: tabular-nums;
}
.balance-prefix { font-size: 16px; opacity: 0.9; margin-right: 2px; }
.user-welcome-balance .btn-primary-hero { padding: 6px 16px; font-size: 13px; }

/* 退出登录链接 */
.user-side-logout {
    margin-top: 12px;
    border-top: 1px solid var(--color-border-light);
    padding-top: 12px;
    color: var(--color-danger) !important;
}
.user-side-logout:hover {
    background: #fee2e2 !important;
    color: var(--color-danger) !important;
}

@media (max-width: 768px) {
    .user-welcome-v2 { padding: 22px 20px; }
    .user-welcome-avatar { width: 52px; height: 52px; font-size: 22px; }
    .user-welcome-name { font-size: 18px; }
    .user-welcome-balance { width: 100%; text-align: left; }
}
</style>
