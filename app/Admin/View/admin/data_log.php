<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">操作日志</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>管理员</th>
                <th>操作</th>
                <th>详情</th>
                <th>IP</th>
                <th>时间</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $l): ?>
            <tr>
                <td><?= (int) $l['id'] ?></td>
                <td><?= h($l['admin_name']) ?></td>
                <td><?= h($l['action']) ?></td>
                <td class="muted"><?= h($l['content']) ?></td>
                <td><code><?= h($l['ip']) ?></code></td>
                <td class="muted"><?= format_time($l['create_time'] ?? null) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
