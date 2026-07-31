<div class="page-header">
    <div>
        <h1 class="page-title">控制台</h1>
        <div class="page-subtitle">欢迎回来，<?= htmlspecialchars($admin['username'] ?? 'admin') ?>。以下是系统运行概况。</div>
    </div>
    <div class="page-actions">
        <a href="/admin/users" class="btn btn-primary">
            <svg width="14" height="14" style="vertical-align:middle;margin-right:4px;"><use href="#i-user"/></svg>
            管理用户
        </a>
    </div>
</div>

<div class="admin-stats-row">
    <div class="admin-stat-mini">
        <div class="asm-label">
            <span class="admin-tag badge-info" style="font-size:10px;">用户</span>
            注册用户
        </div>
        <div class="asm-value"><?= $stats['users'] ?? 0 ?></div>
        <div class="asm-trend up">↑ 活跃增长</div>
    </div>
    <div class="admin-stat-mini">
        <div class="asm-label">
            <span class="admin-tag badge-success" style="font-size:10px;">产品</span>
            产品数量
        </div>
        <div class="asm-value"><?= $stats['products'] ?? 0 ?></div>
        <div class="asm-trend">全部产品</div>
    </div>
    <div class="admin-stat-mini">
        <div class="asm-label">
            <span class="admin-tag badge-warning" style="font-size:10px;">授权</span>
            授权总数
        </div>
        <div class="asm-value"><?= $stats['licenses'] ?? 0 ?></div>
        <div class="asm-trend">有效授权</div>
    </div>
    <div class="admin-stat-mini">
        <div class="asm-label">
            <span class="admin-tag badge-danger" style="font-size:10px;">订单</span>
            订单总数
        </div>
        <div class="asm-value"><?= $stats['orders'] ?? 0 ?></div>
        <div class="asm-trend up">↑ 总营业额 ¥<?= number_format($stats['revenue'] ?? 0, 2) ?></div>
    </div>
</div>

<div class="admin-quick-actions">
    <a href="/admin/users" class="quick-action-btn">
        <span class="qa-icon" style="background:linear-gradient(135deg,#4f8cff,#3868ff);">
            <svg><use href="#i-user"/></svg>
        </span>
        <div>
            <div class="qa-text">用户管理</div>
            <div class="qa-hint">查看与管理用户</div>
        </div>
    </a>
    <a href="/admin/products" class="quick-action-btn">
        <span class="qa-icon" style="background:linear-gradient(135deg,#52c41a,#389e0d);">
            <svg><use href="#i-box"/></svg>
        </span>
        <div>
            <div class="qa-text">产品管理</div>
            <div class="qa-hint">添加与编辑产品</div>
        </div>
    </a>
    <a href="/admin/licenses" class="quick-action-btn">
        <span class="qa-icon" style="background:linear-gradient(135deg,#fa8c16,#d46b08);">
            <svg><use href="#i-key"/></svg>
        </span>
        <div>
            <div class="qa-text">授权管理</div>
            <div class="qa-hint">生成与管理授权</div>
        </div>
    </a>
    <a href="/admin/orders" class="quick-action-btn">
        <span class="qa-icon" style="background:linear-gradient(135deg,#722ed1,#531dab);">
            <svg><use href="#i-orders"/></svg>
        </span>
        <div>
            <div class="qa-text">订单管理</div>
            <div class="qa-hint">查看交易记录</div>
        </div>
    </a>
    <a href="/admin/messages" class="quick-action-btn">
        <span class="qa-icon" style="background:linear-gradient(135deg,#eb2f96,#c41d7f);">
            <svg><use href="#i-message"/></svg>
        </span>
        <div>
            <div class="qa-text">消息管理</div>
            <div class="qa-hint">发送系统消息</div>
        </div>
    </a>
    <a href="/admin/settings" class="quick-action-btn">
        <span class="qa-icon" style="background:linear-gradient(135deg,#13c2c2,#08979c);">
            <svg><use href="#i-settings"/></svg>
        </span>
        <div>
            <div class="qa-text">系统设置</div>
            <div class="qa-hint">配置站点信息</div>
        </div>
    </a>
</div>

<div class="admin-grid-2">
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">
                <svg width="16" height="16"><use href="#i-orders"/></svg>
                最新订单
            </h3>
            <a href="/admin/orders" class="btn btn-outline btn-sm">查看全部</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>订单号</th>
                        <th>产品</th>
                        <th>金额</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentOrders)): ?>
                        <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td style="font-family:monospace;font-size:12px;"><?= htmlspecialchars($order['order_no'] ?? '') ?></td>
                            <td><?= htmlspecialchars($order['product_name'] ?? '') ?></td>
                            <td>¥<?= number_format($order['amount'] ?? 0, 2) ?></td>
                            <td>
                                <?php if (($order['status'] ?? 0) == 1): ?>
                                    <span class="badge badge-success">已完成</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">待处理</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center;padding:32px;color:var(--text-muted);">暂无订单数据</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">
                <svg width="16" height="16"><use href="#i-message"/></svg>
                最新消息
            </h3>
            <a href="/admin/messages" class="btn btn-outline btn-sm">查看全部</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>标题</th>
                        <th>目标</th>
                        <th>时间</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recentMessages)): ?>
                        <?php foreach ($recentMessages as $msg): ?>
                        <tr>
                            <td><?= htmlspecialchars($msg['title'] ?? '') ?></td>
                            <td><?= ($msg['target'] ?? '') === 'all' ? '全部用户' : htmlspecialchars($msg['target_user'] ?? '—') ?></td>
                            <td style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($msg['created_at'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align:center;padding:32px;color:var(--text-muted);">暂无消息数据</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>