<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">风控管理</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>店铺</th>
                <th>风险等级</th>
                <th>风险分数</th>
                <th>预警次数</th>
                <th>注册时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $s): ?>
            <tr>
                <td><?= h($s['shop_name']) ?></td>
                <td>
                    <?php $lv = (int) ($s['risk_level'] ?? 0); ?>
                    <span class="badge badge-<?= $lv === 2 ? 'danger' : ($lv === 1 ? 'warning' : 'success') ?>">
                        <?= $lv === 2 ? '高' : ($lv === 1 ? '中' : '低') ?>
                    </span>
                </td>
                <td><?= (int) ($s['risk_score'] ?? 0) ?></td>
                <td><?= (int) ($s['warn_count'] ?? 0) ?></td>
                <td class="muted"><?= format_time($s['create_time'] ?? null) ?></td>
                <td>
                    <a href="#" class="link">查看</a>
                    <a href="#" class="link link-primary">处理</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
