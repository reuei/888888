<div class="filter-bar">
    <div class="filter-tabs">
        <a href="/user/balance-logs" class="filter-tab<?= !isset($filter) || $filter === 'all' ? ' active' : '' ?>">全部</a>
        <a href="/user/balance-logs?filter=income" class="filter-tab<?= ($filter ?? '') === 'income' ? ' active' : '' ?>">收入</a>
        <a href="/user/balance-logs?filter=expense" class="filter-tab<?= ($filter ?? '') === 'expense' ? ' active' : '' ?>">支出</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="18" height="18" style="margin-right: 6px; color: var(--primary);"><use href="#i-wallet"/></svg>
            余额变动记录
        </h3>
        <a href="/user/balance" style="font-size: 13px; color: var(--primary); text-decoration: none;">返回余额管理</a>
    </div>
    <?php if (!empty($logs)): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>时间</th>
                    <th>类型</th>
                    <th>金额</th>
                    <th>变动前</th>
                    <th>变动后</th>
                    <th>描述</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td style="font-size: 13px;"><?= htmlspecialchars($log['created_at'] ?? '') ?></td>
                    <td>
                        <?php $isIncome = ($log['type'] ?? '') === 'income'; ?>
                        <span class="badge <?= $isIncome ? 'badge-success' : 'badge-danger' ?>"><?= $isIncome ? '收入' : '支出' ?></span>
                    </td>
                    <td style="font-weight: 600; color: <?= $isIncome ? 'var(--success)' : 'var(--danger)' ?>;">
                        <?= $isIncome ? '+' : '-' ?>¥<?= number_format(abs($log['amount'] ?? 0), 2) ?>
                    </td>
                    <td style="color: var(--text-secondary);">¥<?= number_format($log['balance_before'] ?? 0, 2) ?></td>
                    <td style="font-weight: 600;">¥<?= number_format($log['balance_after'] ?? 0, 2) ?></td>
                    <td style="color: var(--text-secondary);"><?= htmlspecialchars($log['description'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg width="24" height="24"><use href="#i-log"/></svg>
        </div>
        <div class="empty-state-text">暂无余额变动记录</div>
    </div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination" style="display: flex; justify-content: center; gap: 8px; margin-top: 24px;">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" style="padding: 8px 14px; border: 1px solid var(--border); border-radius: var(--radius); text-decoration: none; color: var(--text); font-size: 13px; transition: all 0.15s; <?= ($page ?? 1) == $i ? 'background: var(--primary); color: #fff; border-color: var(--primary);' : 'background: var(--bg-card);' ?>">
        <?= $i ?>
    </a>
    <?php endfor; ?>
</div>
<?php endif; ?>
