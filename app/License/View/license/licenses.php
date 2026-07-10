<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">授权列表</h3>
        <button class="btn btn-primary btn-sm">新增授权</button>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>授权码</th>
                <th>产品</th>
                <th>版本</th>
                <th>域名数</th>
                <th>过期时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $l): ?>
            <tr>
                <td><code><?= h($l['code']) ?></code></td>
                <td><?= h($l['product']) ?></td>
                <td>v<?= h($l['version']) ?></td>
                <td><?= (int) $l['max_domains'] ?></td>
                <td class="muted"><?= h($l['expire_time']) ?></td>
                <td>
                    <?php if ((int) $l['status'] === 1): ?>
                        <span class="badge badge-success">正常</span>
                    <?php else: ?>
                        <span class="badge badge-danger">禁用</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="#" class="link">编辑</a>
                    <a href="#" class="link link-danger">禁用</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
