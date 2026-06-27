<style>
.hero {
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
    color: #fff;
    border-radius: 12px;
    padding: 48px 32px;
    text-align: center;
    margin-bottom: 24px;
}
.hero h1 { font-size: 32px; margin-bottom: 12px; }
.hero p { font-size: 15px; opacity: 0.9; margin-bottom: 24px; }
.hero .btn {
    background: #fff;
    color: #2563EB;
    border-color: #fff;
    font-weight: 500;
}
.category-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 24px;
}
.category-list a {
    display: inline-block;
    padding: 8px 16px;
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 20px;
    font-size: 13px;
    color: #475569;
    transition: all 0.2s;
}
.category-list a:hover {
    border-color: #2563EB;
    color: #2563EB;
}
.notice-bar {
    background: #FFFBEB;
    border: 1px solid #FDE68A;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 13px;
}
.notice-bar strong { color: #D97706; }
.notice-bar a { color: #2563EB; }
</style>

<div class="hero">
    <h1>鲸商城 Pro</h1>
    <p>安全、便捷的卡密自动发货平台，汇聚海量优质商品</p>
    <a href="<?php echo url('index/category'); ?>" class="btn btn-lg">立即购卡</a>
</div>

<?php if (!empty($articles)): ?>
<div class="notice-bar">
    <strong>公告</strong>
    <div style="flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
        <?php foreach ($articles as $article): ?>
        <a href="<?php echo url('index/article', ['id' => $article['id']]); ?>"><?php echo h($article['title']); ?></a>
        <span style="color:#CBD5E1; margin:0 8px;">|</span>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($categories)): ?>
<div class="category-list">
    <?php foreach ($categories as $c): ?>
    <a href="<?php echo url('index/category', ['id' => $c['id']]); ?>"><?php echo h($c['name']); ?></a>
    <?php endforeach; ?>
    <a href="<?php echo url('index/category'); ?>" style="color:#2563EB; border-color:#2563EB;">全部商品 →</a>
</div>
<?php endif; ?>

<div class="section-title">
    <span>热门商品</span>
    <a href="<?php echo url('index/category'); ?>">查看更多</a>
</div>

<?php if (empty($goods)): ?>
<div class="card empty-tip">暂无上架商品</div>
<?php else: ?>
<div class="grid">
    <?php foreach ($goods as $item): ?>
    <div class="goods-card">
        <a href="<?php echo url('index/goods', ['id' => $item['id']]); ?>">
            <div class="goods-cover">
                <?php if ($item['cover']): ?>
                <img src="<?php echo h($item['cover']); ?>" alt="<?php echo h($item['name']); ?>">
                <?php else: ?>
                暂无图片
                <?php endif; ?>
            </div>
            <div class="goods-info">
                <div class="goods-name"><?php echo h($item['name']); ?></div>
                <div class="goods-meta">
                    <span class="goods-price">¥<?php echo $item['price']; ?></span>
                    <span class="goods-sold">已售 <?php echo $item['sold']; ?></span>
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
