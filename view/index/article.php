<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h1 style="font-size: 22px; margin-bottom: 12px;"><?php echo h($article['title']); ?></h1>
    <div style="color: #94A3B8; font-size: 13px; margin-bottom: 20px;"><?php echo $article['create_time']; ?></div>
    <div style="line-height: 1.8; color: #475569;">
        <?php echo nl2br(h($article['content'])); ?>
    </div>
    <div style="margin-top: 24px;">
        <a href="<?php echo url('/'); ?>" class="btn btn-outline">返回首页</a>
    </div>
</div>
