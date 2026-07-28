<div class="user-breadcrumb">
    <span>用户中心</span> / <span>站点日志</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">站点日志</h1>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-log"/></svg>操作日志
        </h3>
    </div>
    <?php if (!empty($logs)): ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>类型</th>
                    <th>金额</th>
                    <th>备注</th>
                    <th>时间</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['type'] ?? '') ?></td>
                    <td>¥<?= number_format($log['amount'] ?? 0, 2) ?></td>
                    <td><?= htmlspecialchars($log['description'] ?? '') ?></td>
                    <td><?= htmlspecialchars($log['created_at'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align: center; padding: 40px; color: #687690;">暂无操作日志</div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>