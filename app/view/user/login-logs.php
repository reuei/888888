<div class="filter-bar">
    <div class="filter-tabs">
        <a href="/user/login-logs" class="filter-tab active">全部记录</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="18" height="18" style="margin-right: 6px; color: var(--primary);"><use href="#i-log"/></svg>
            登录记录
        </h3>
    </div>
    <?php if (!empty($logs)): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>时间</th>
                    <th>IP 地址</th>
                    <th>状态</th>
                    <th>消息</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td style="font-size: 13px;"><?= htmlspecialchars($log['created_at'] ?? '') ?></td>
                    <td style="font-family: monospace; font-size: 13px;"><?= htmlspecialchars($log['ip'] ?? '') ?></td>
                    <td>
                        <?php $success = ($log['status'] ?? '') === 'success'; ?>
                        <span class="badge <?= $success ? 'badge-success' : 'badge-danger' ?>">
                            <?= $success ? '成功' : '失败' ?>
                        </span>
                    </td>
                    <td style="color: var(--text-secondary);"><?= htmlspecialchars($log['message'] ?? '') ?></td>
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
        <div class="empty-state-text">暂无登录记录</div>
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