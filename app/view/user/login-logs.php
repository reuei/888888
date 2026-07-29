<div class="user-breadcrumb">
    <span>用户中心</span> / <span>站点日志</span> / <span>登录日志</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">登录日志</h1>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-log"/></svg>登录记录
        </h3>
    </div>
    <?php if (!empty($logs)): ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>时间</th>
                    <th>IP</th>
                    <th>状态</th>
                    <th>消息</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['created_at'] ?? '') ?></td>
                    <td style="font-family: monospace; font-size: 13px;"><?= htmlspecialchars($log['ip'] ?? '') ?></td>
                    <td>
                        <?php $success = ($log['status'] ?? '') === 'success'; ?>
                        <span class="badge <?= $success ? 'badge-success' : 'badge-danger' ?>"><?= $success ? '成功' : '失败' ?></span>
                    </td>
                    <td><?= htmlspecialchars($log['message'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">暂无登录记录</div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>