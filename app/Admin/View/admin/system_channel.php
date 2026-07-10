<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">支付通道</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>编码</th>
                <th>名称</th>
                <th>类型</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $c): ?>
            <tr>
                <td><code><?= h($c['code']) ?></code></td>
                <td><?= h($c['name']) ?></td>
                <td><?= h($c['type']) ?></td>
                <td>
                    <?php if ((int) $c['status'] === 1): ?>
                        <span class="badge badge-success">启用</span>
                    <?php else: ?>
                        <span class="badge">禁用</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="#" class="link link-primary" data-action="toggle" data-id="<?= (int) $c['id'] ?>" data-status="<?= (int) $c['status'] === 1 ? 0 : 1 ?>">
                        <?= (int) $c['status'] === 1 ? '禁用' : '启用' ?>
                    </a>
                    <a href="#" class="link">配置</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
