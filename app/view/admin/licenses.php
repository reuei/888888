<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 24px;">授权管理</h1>

<div class="card">
    <?php if (!empty($licenses)): ?>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>授权码</th>
                    <th>产品名称</th>
                    <th>用户</th>
                    <th>绑定域名</th>
                    <th>状态</th>
                    <th>到期时间</th>
                    <th>创建时间</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($licenses as $license): ?>
                <tr>
                    <td><?= $license['id'] ?? '' ?></td>
                    <td style="font-family: monospace; font-size: 13px;"><?= htmlspecialchars($license['license_key'] ?? '') ?></td>
                    <td><?= htmlspecialchars($license['product_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($license['username'] ?? '') ?></td>
                    <td><?= htmlspecialchars($license['domain'] ?? '未绑定') ?></td>
                    <td><span class="badge <?= ($license['status'] ?? 0) == 1 ? 'badge-success' : 'badge-danger' ?>"><?= ($license['status'] ?? 0) == 1 ? '有效' : '无效' ?></span></td>
                    <td><?= htmlspecialchars($license['expires_at'] ?? '永久') ?></td>
                    <td><?= htmlspecialchars($license['created_at'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">暂无授权数据</div>
    <?php endif; ?>
</div>

<?php if (($totalPages ?? 1) > 1): ?>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?= $i ?>" class="<?= ($page ?? 1) == $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>