<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">调用日志</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>授权码</th>
                <th>域名</th>
                <th>操作</th>
                <th>IP</th>
                <th>状态</th>
                <th>时间</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $l): ?>
            <tr>
                <td><code><?= h($l['license']) ?></code></td>
                <td><?= h($l['domain']) ?></td>
                <td><span class="tag"><?= h($l['action']) ?></span></td>
                <td><code><?= h($l['ip']) ?></code></td>
                <td>
                    <?php if ((int) $l['status'] === 1): ?>
                        <span class="badge badge-success">成功</span>
                    <?php else: ?>
                        <span class="badge badge-danger">失败</span>
                    <?php endif; ?>
                </td>
                <td class="muted"><?= h($l['create_time']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
