<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">客服账号</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>店铺</th>
                <th>QQ</th>
                <th>电话</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $s): ?>
            <tr>
                <td><?= h($s['shop_name']) ?></td>
                <td><?= h($s['service_qq'] ?? '-') ?></td>
                <td><?= h($s['service_phone'] ?? '-') ?></td>
                <td>
                    <span class="badge badge-success">在线</span>
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
