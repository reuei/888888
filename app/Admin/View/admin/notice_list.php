<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">公告列表</h3>
        <a href="/admin/notice/publish" class="btn btn-primary btn-sm">发布新公告</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>标题</th>
                <th>状态</th>
                <th>发布时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $n): ?>
            <tr>
                <td><?= (int) $n['id'] ?></td>
                <td><?= h($n['title']) ?></td>
                <td>
                    <?php if ((int) $n['status'] === 1): ?>
                        <span class="badge badge-success">已发布</span>
                    <?php else: ?>
                        <span class="badge">已下线</span>
                    <?php endif; ?>
                </td>
                <td class="muted"><?= format_time($n['create_time'] ?? null) ?></td>
                <td>
                    <a href="#" class="link">编辑</a>
                    <a href="#" class="link link-danger" data-action="delete" data-id="<?= (int) $n['id'] ?>">删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
