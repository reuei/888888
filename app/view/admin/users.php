<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 24px;">用户管理</h1>

<div class="card">
    <?php if (!empty($users)): ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>用户名</th>
                    <th>邮箱</th>
                    <th>余额</th>
                    <th>状态</th>
                    <th>注册时间</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?? '' ?></td>
                    <td><?= htmlspecialchars($u['username'] ?? '') ?></td>
                    <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
                    <td>¥<?= number_format($u['balance'] ?? 0, 2) ?></td>
                    <td><span class="badge <?= ($u['status'] ?? 0) == 1 ? 'badge-success' : 'badge-danger' ?>"><?= ($u['status'] ?? 0) == 1 ? '正常' : '禁用' ?></span></td>
                    <td><?= htmlspecialchars($u['created_at'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">暂无用户数据</div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>