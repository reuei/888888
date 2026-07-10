<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">消息列表</h3>
        <a href="/admin/message/publish" class="btn btn-primary btn-sm">发布新消息</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>类型</th>
                <th>标题</th>
                <th>内容</th>
                <th>时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $m): ?>
            <tr>
                <td><?= (int) $m['id'] ?></td>
                <td><span class="tag tag-<?= h($m['type'] ?? 'system') ?>"><?= h($m['type'] ?? '系统') ?></span></td>
                <td><?= h($m['title']) ?></td>
                <td class="muted"><?= h(mb_substr($m['content'], 0, 30)) ?>...</td>
                <td class="muted"><?= format_time($m['create_time'] ?? null) ?></td>
                <td>
                    <a href="#" class="link link-danger" data-action="delete" data-id="<?= (int) $m['id'] ?>">删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
