<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">用户列表</h3>
        <span class="panel-sub">共 <?= count($items) ?> 位用户</span>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>用户名</th>
                <th>昵称</th>
                <th>余额</th>
                <th>状态</th>
                <th>注册时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $u): ?>
            <tr>
                <td><?= (int) $u['id'] ?></td>
                <td><?= h($u['username']) ?></td>
                <td><?= h($u['nickname'] ?? '-') ?></td>
                <td>¥<?= format_money($u['balance'] ?? 0) ?></td>
                <td>
                    <?php if ((int) ($u['status'] ?? 1) === 1): ?>
                        <span class="badge badge-success">正常</span>
                    <?php else: ?>
                        <span class="badge badge-danger">禁用</span>
                    <?php endif; ?>
                </td>
                <td class="muted"><?= format_time($u['create_time'] ?? null) ?></td>
                <td>
                    <a href="#" class="link" data-action="edit">编辑</a>
                    <a href="#" class="link" data-action="toggle"><?= (int) ($u['status'] ?? 1) === 1 ? '禁用' : '启用' ?></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
