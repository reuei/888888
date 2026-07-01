<div class="card">
    <h1 style="font-size: 20px; margin-bottom: 16px;"><?php echo h($article['title']); ?></h1>
    <div class="detail-meta">发布时间：<?php echo $article['create_time']; ?></div>
    <div class="content-body" style="line-height: 1.8;">
        <?php echo $article['content']; ?>
    </div>
    <div style="margin-top: 24px;">
        <a href="<?php echo url('/'); ?>" class="btn btn-outline">返回首页</a>
    </div>
</div>
