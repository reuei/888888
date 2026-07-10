<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">实名认证</h3>
        <span class="panel-sub">待审核：<?= count($items) ?></span>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>店铺</th>
                <th>真实姓名</th>
                <th>身份证号</th>
                <th>状态</th>
                <th>提交时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $s): ?>
            <tr>
                <td><?= h($s['shop_name']) ?></td>
                <td><?= h($s['real_name']) ?></td>
                <td><?= h(substr($s['id_card_no'] ?? '', 0, 6)) ?>********</td>
                <td>
                    <?php $st = (int) ($s['realname_status'] ?? 0); ?>
                    <span class="badge badge-<?= $st === 2 ? 'success' : ($st === 1 ? 'warning' : 'default') ?>">
                        <?= $st === 2 ? '已通过' : ($st === 1 ? '待审核' : '未提交') ?>
                    </span>
                </td>
                <td class="muted"><?= format_time($s['create_time'] ?? null) ?></td>
                <td>
                    <a href="#" class="link">查看</a>
                    <a href="#" class="link link-primary">通过</a>
                    <a href="#" class="link link-danger">驳回</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
