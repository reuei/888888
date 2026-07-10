<div class="page-head">
    <h1 class="page-title">消息详情</h1>
    <a href="/user/messages" class="page-link">返回列表</a>
</div>
<div class="panel">
    <h2 class="article-title"><?= h($message['title'] ?? '') ?></h2>
    <div class="article-meta">发布于：<?= format_time($message['create_time'] ?? null) ?></div>
    <div class="article-content"><?= nl2br(h($message['content'] ?? '')) ?></div>
</div>
