<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">登录日志</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>账号</th>
                <th>类型</th>
                <th>IP</th>
                <th>状态</th>
                <th>时间</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $l): ?>
            <tr>
                <td><?= h($l['username']) ?></td>
                <td><span class="tag tag-<?= h($l['type']) ?>"><?= h($l['type']) ?></span></td>
                <td><code><?= h($l['ip']) ?></code></td>
                <td>
                    <?php if ((int) $l['status'] === 1): ?>
                        <span class="badge badge-success">成功</span>
                    <?php else: ?>
                        <span class="badge badge-danger">失败</span>
                    <?php endif; ?>
                </td>
                <td class="muted"><?= format_time($l['create_time'] ?? null) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
