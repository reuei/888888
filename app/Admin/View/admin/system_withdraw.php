<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">提现管理</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>单号</th>
                <th>金额</th>
                <th>渠道</th>
                <th>账号</th>
                <th>状态</th>
                <th>时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $w): ?>
            <tr>
                <td><?= h($w['withdraw_no']) ?></td>
                <td>¥<?= format_money($w['amount']) ?></td>
                <td><?= h($w['channel']) ?></td>
                <td><?= h($w['account_name']) ?></td>
                <td>
                    <?php $st = (int) $w['status']; ?>
                    <span class="badge badge-<?= $st === 2 ? 'success' : ($st === 0 ? 'warning' : 'danger') ?>">
                        <?= $st === 2 ? '已打款' : ($st === 0 ? '待处理' : '已拒绝') ?>
                    </span>
                </td>
                <td class="muted"><?= format_time($w['create_time'] ?? null) ?></td>
                <td>
                    <?php if ((int) $w['status'] === 0): ?>
                        <a href="#" class="link link-primary" data-action="approve" data-id="<?= (int) $w['id'] ?>">通过</a>
                        <a href="#" class="link link-danger" data-action="reject" data-id="<?= (int) $w['id'] ?>">拒绝</a>
                    <?php else: ?>
                        <span class="muted">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
