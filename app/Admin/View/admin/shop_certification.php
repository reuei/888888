<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">店铺认证</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>店铺</th>
                <th>认证类型</th>
                <th>认证编号</th>
                <th>状态</th>
                <th>提交时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $s): ?>
            <tr>
                <td><?= h($s['shop_name']) ?></td>
                <td><?= h($s['cert_type'] ?? '品牌认证') ?></td>
                <td><?= h($s['cert_no'] ?? '-') ?></td>
                <td>
                    <?php $st = (int) ($s['cert_status'] ?? 0); ?>
                    <span class="badge badge-<?= $st === 2 ? 'success' : ($st === 1 ? 'warning' : 'default') ?>">
                        <?= $st === 2 ? '已认证' : ($st === 1 ? '待审核' : '未认证') ?>
                    </span>
                </td>
                <td class="muted"><?= format_time($s['create_time'] ?? null) ?></td>
                <td>
                    <a href="#" class="link link-primary">通过</a>
                    <a href="#" class="link link-danger">驳回</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
