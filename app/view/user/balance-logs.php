<div class="user-breadcrumb">
    <span>用户中心</span> / <span>站点日志</span> / <span>余额明细</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">余额明细</h1>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-wallet"/></svg>余额变动记录
        </h3>
    </div>
    <?php if (!empty($logs)): ?>
    <div class="table-wrap">
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
                    <td><?= htmlspecialchars($log['created_at'] ?? '') ?></td>
                    <td>
                        <?php $isIncome = ($log['type'] ?? '') === 'income'; ?>
                        <span class="badge <?= $isIncome ? 'badge-success' : 'badge-danger' ?>"><?= $isIncome ? '收入' : '支出' ?></span>
                    </td>
                    <td style="color: <?= $isIncome ? '#52c41a' : '#ff4d4f' ?>; font-weight: 500;">
                        <?= $isIncome ? '+' : '-' ?>¥<?= number_format(abs($log['amount'] ?? 0), 2) ?>
                    </td>
                    <td>¥<?= number_format($log['balance_before'] ?? 0, 2) ?></td>
                    <td>¥<?= number_format($log['balance_after'] ?? 0, 2) ?></td>
                    <td><?= htmlspecialchars($log['description'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">暂无余额变动记录</div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>