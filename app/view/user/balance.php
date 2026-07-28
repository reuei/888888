<div class="user-breadcrumb">
    <span>用户中心</span> / <span>余额管理</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">余额管理</h1>

<div class="card" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <div style="font-size: 14px; color: #687690; margin-bottom: 8px;">当前余额</div>
        <div style="font-size: 36px; font-weight: 700; color: #4f8cff;">¥<?= number_format($user['balance'] ?? 0, 2) ?></div>
    </div>
    <button class="btn btn-primary" onclick="alert('充值功能开发中')">
        <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-wallet"/></svg>充值
    </button>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-log"/></svg>交易记录
        </h3>
    </div>
    <?php if (!empty($logs)): ?>
    <div class="table-wrap">
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
                        <span class="badge <?= $isIncome ? 'badge-success' : 'badge-danger' ?>"><?= $isIncome ? '收入' : '支出' ?></span>
                    </td>
                    <td style="color: <?= $isIncome ? '#52c41a' : '#ff4d4f' ?>; font-weight: 500;">
                        <?= $isIncome ? '+' : '-' ?>¥<?= number_format(abs($log['amount'] ?? 0), 2) ?>
                    </td>
                    <td><?= htmlspecialchars($log['description'] ?? '') ?></td>
                    <td><?= htmlspecialchars($log['created_at'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align: center; padding: 40px; color: #687690;">暂无交易记录</div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>