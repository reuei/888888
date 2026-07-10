<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">域名列表</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>域名</th>
                <th>授权码</th>
                <th>IP</th>
                <th>绑定时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $d): ?>
            <tr>
                <td><?= h($d['domain']) ?></td>
                <td><code><?= h($d['license']) ?></code></td>
                <td><code><?= h($d['ip']) ?></code></td>
                <td class="muted"><?= h($d['create_time']) ?></td>
                <td>
                    <a href="#" class="link link-danger">解绑</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
