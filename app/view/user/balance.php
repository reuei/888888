<div class="welcome-banner" style="background: linear-gradient(135deg, #10b981, #059669);">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-size: 14px; opacity: 0.9; margin-bottom: 8px;">账户余额</div>
            <div style="font-size: 36px; font-weight: 700;">¥<?= number_format($user['balance'] ?? 0, 2) ?></div>
            <div style="font-size: 13px; opacity: 0.8; margin-top: 8px;">充值后可用于购买产品和插件</div>
        </div>
        <button class="btn btn-primary btn-lg" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3);" onclick="showRechargeModal()">
            <svg width="18" height="18" style="vertical-align: middle; margin-right: 6px;"><use href="#i-wallet"/></svg>
            立即充值
        </button>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="stat-card accent-green">
        <div class="stat-icon-box">
            <svg width="20" height="20"><use href="#i-arrow-up"/></svg>
        </div>
        <div class="stat-value" style="color: var(--success);">+¥<?= number_format($stats['total_income'] ?? 0, 2) ?></div>
        <div class="stat-label">累计收入</div>
    </div>
    <div class="stat-card accent-orange">
        <div class="stat-icon-box">
            <svg width="20" height="20"><use href="#i-arrow-down"/></svg>
        </div>
        <div class="stat-value" style="color: var(--danger);">-¥<?= number_format($stats['total_expense'] ?? 0, 2) ?></div>
        <div class="stat-label">累计支出</div>
    </div>
    <div class="stat-card accent-blue">
        <div class="stat-icon-box">
            <svg width="20" height="20"><use href="#i-orders"/></svg>
        </div>
        <div class="stat-value"><?= $stats['transaction_count'] ?? 0 ?></div>
        <div class="stat-label">交易次数</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="18" height="18" style="margin-right: 6px; color: var(--primary);"><use href="#i-log"/></svg>
            交易记录
        </h3>
        <a href="/user/balance-logs" style="font-size: 13px; color: var(--primary); text-decoration: none;">查看明细</a>
    </div>
    <?php if (!empty($logs)): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>类型</th>
                    <th>金额</th>
                    <th>说明</th>
                    <th>时间</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td>
                        <?php $isIncome = ($log['type'] ?? '') === 'income'; ?>
                        <span class="badge <?= $isIncome ? 'badge-success' : 'badge-danger' ?>">
                            <svg width="12" height="12" style="vertical-align: middle; margin-right: 4px;"><use href="#i-arrow-<?= $isIncome ? 'up' : 'down' ?>"/></svg>
                            <?= $isIncome ? '收入' : '支出' ?>
                        </span>
                    </td>
                    <td style="font-weight: 600; color: <?= $isIncome ? 'var(--success)' : 'var(--danger)' ?>;">
                        <?= $isIncome ? '+' : '-' ?>¥<?= number_format(abs($log['amount'] ?? 0), 2) ?>
                    </td>
                    <td style="color: var(--text-secondary);"><?= htmlspecialchars($log['description'] ?? '') ?></td>
                    <td style="font-size: 13px;"><?= htmlspecialchars($log['created_at'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg width="24" height="24"><use href="#i-wallet"/></svg>
        </div>
        <div class="empty-state-text">暂无交易记录</div>
        <button class="btn btn-primary" onclick="showRechargeModal()">去充值</button>
    </div>
    <?php endif; ?>
</div>

<div id="rechargeModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;" onclick="if(event.target===this)hideRechargeModal()">
    <div style="background: var(--bg-card); border-radius: var(--radius-lg); padding: 32px; width: 90%; max-width: 400px;">
        <h3 style="font-size: 18px; font-weight: 600; color: var(--text); margin-bottom: 20px;">账户充值</h3>
        <div style="text-align: center; padding: 20px; background: var(--primary-50); border-radius: var(--radius); margin-bottom: 20px;">
            <div style="font-size: 14px; color: var(--text-secondary); margin-bottom: 8px;">当前余额</div>
            <div style="font-size: 28px; font-weight: 700; color: var(--primary);">¥<?= number_format($user['balance'] ?? 0, 2) ?></div>
        </div>
        <div style="text-align: center; padding: 40px 20px; background: var(--bg-tertiary); border-radius: var(--radius); color: var(--text-muted);">
            <svg width="32" height="32" style="margin: 0 auto 12px; display: block; color: var(--warning);"><use href="#i-warn"/></svg>
            <div style="font-size: 14px;">充值功能正在开发中</div>
            <div style="font-size: 12px; margin-top: 4px;">请耐心等待，或联系客服获取帮助</div>
        </div>
        <button class="btn btn-primary btn-block" style="margin-top: 20px;" onclick="hideRechargeModal()">我知道了</button>
    </div>
</div>

<script>
    function showRechargeModal() {
        document.getElementById('rechargeModal').style.display = 'flex';
    }
    function hideRechargeModal() {
        document.getElementById('rechargeModal').style.display = 'none';
    }
</script>
