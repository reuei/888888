<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/slide.php';

$canInstall = !file_exists(DB_PATH);
if ($canInstall) {
    redirect('install.php');
}

$navCategories = getCategories();
$yaowenCat = getCategoryBySlug('yaowen');
$yaowenList = $yaowenCat ? @DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY is_top DESC, publish_time DESC LIMIT 8", [$yaowenCat['id']]) : [];
$shenchaCat = getCategoryBySlug('shencha');
$shenchaList = $shenchaCat ? @DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY publish_time DESC LIMIT 6", [$shenchaCat['id']]) : [];
$xunshiCat = getCategoryBySlug('xunshi');
$xunshiList = $xunshiCat ? @DB::fetchAll("SELECT * FROM articles WHERE category_id=? AND status=1 ORDER BY publish_time DESC LIMIT 6", [$xunshiCat['id']]) : [];
$hotArticles = @DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY views DESC LIMIT 10") ?: [];
$totalArticles = @DB::fetchOne("SELECT COUNT(*) as cnt FROM articles WHERE status=1")['cnt'] ?: 0;
$totalCategories = @DB::fetchOne("SELECT COUNT(*) as cnt FROM categories")['cnt'] ?: 0;
$totalUsers = @DB::fetchOne("SELECT COUNT(*) as cnt FROM users")['cnt'] ?: 0;

$pageTitle = '';
include __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="hero-grid">
        <div>
            <?php echo getSliderHtml(); ?>
        </div>

        <div class="headline-feature">
            <div class="img">
                <svg viewBox="0 0 200 110" xmlns="http://www.w3.org/2000/svg" style="width:100%; height:100%; position:absolute; inset:0; opacity:0.4;">
                    <circle cx="160" cy="40" r="35" fill="none" stroke="#c9a227" stroke-width="0.6"/>
                    <circle cx="160" cy="40" r="22" fill="none" stroke="#c9a227" stroke-width="0.4"/>
                    <path d="M160 16 L168 30 L184 28 L180 44 L194 50 L180 56 L184 72 L168 70 L160 84 L152 70 L136 72 L140 56 L126 50 L140 44 L136 28 L152 30 Z" fill="#c9a227" opacity="0.7"/>
                </svg>
                <div style="position:absolute; left:24px; top:24px; z-index:2;">
                    <div style="color:#c9a227; font-size:11px; letter-spacing:3px; margin-bottom:8px;">PEOPLE'S PROCURATORATE</div>
                    <div style="color:#fff; font-size:24px; font-family:var(--pk-font-serif); letter-spacing:4px; line-height:1.4;">忠诚&nbsp;担当<br>公正&nbsp;清廉</div>
                </div>
            </div>
            <div class="body">
                <span class="tag">头条新闻</span>
                <h3><?php echo $yaowenList ? e($yaowenList[0]['title']) : '深入开展检察监督 维护社会公平正义'; ?></h3>
                <p>检察机关坚持总体国家安全观，依法履行法律监督职责，扎实推进平安中国、法治中国建设，以高质量检察履职服务保障经济社会高质量发展。</p>
                <div class="meta">来源：检察要闻 · <?php echo date('Y-m-d'); ?></div>
            </div>
        </div>

        <div class="theme-grid">
            <h3>主题入口</h3>
            <div class="theme-list">
                <a href="<?php echo BASE_URL; ?>report.php" class="theme-item">
                    <div class="ico">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5z"/></svg>
                    </div>
                    <span>信访举报</span>
                </a>
                <a href="<?php echo BASE_URL; ?>category.php?slug=shencha" class="theme-item">
                    <div class="ico">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="16" y1="16" x2="21" y2="21"/></svg>
                    </div>
                    <span>审查起诉</span>
                </a>
                <a href="<?php echo BASE_URL; ?>category.php?slug=xunshi" class="theme-item">
                    <div class="ico">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                    </div>
                    <span>公益诉讼</span>
                </a>
                <a href="<?php echo BASE_URL; ?>category.php?slug=fagui" class="theme-item">
                    <div class="ico">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7z"/><path d="M9 12l2 2 4-4"/></svg>
                    </div>
                    <span>法律法规</span>
                </a>
                <a href="<?php echo BASE_URL; ?>cases.php" class="theme-item">
                    <div class="ico">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    </div>
                    <span>典型案例</span>
                </a>
                <a href="<?php echo BASE_URL; ?>topic.php" class="theme-item">
                    <div class="ico">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <span>专题专栏</span>
                </a>
            </div>
        </div>
    </div>

    <div class="stat-bar">
        <div class="stat-bar-grid">
            <div class="stat-cell">
                <div class="num"><?php echo number_format($totalArticles); ?><small>条</small></div>
                <div class="label">检务公开信息</div>
            </div>
            <div class="stat-cell">
                <div class="num"><?php echo number_format($totalCategories); ?><small>个</small></div>
                <div class="label">业务板块</div>
            </div>
            <div class="stat-cell">
                <div class="num">24<small>h</small></div>
                <div class="label">在线服务</div>
            </div>
            <div class="stat-cell">
                <div class="num">100<small>%</small></div>
                <div class="label">为民承诺</div>
            </div>
        </div>
    </div>

    <div class="two-col">
        <div>
            <div class="block">
                <div class="block-head">
                    <h2>检察要闻</h2>
                    <a href="<?php echo BASE_URL; ?>category.php?slug=yaowen" class="more">查看全部 &raquo;</a>
                </div>
                <div class="block-body">
                    <?php if ($yaowenList): ?>
                    <ul class="news-list">
                        <?php $i = 0; foreach ($yaowenList as $art): $i++; ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $art['id']; ?>" class="news-title <?php echo !empty($art['is_top']) ? 'is-top' : ''; ?>">
                                <?php if (!empty($art['is_top'])): ?><span class="top-flag">置顶</span><?php endif; ?>
                                <?php echo e($art['title']); ?>
                            </a>
                            <span class="date"><?php echo formatDate($art['publish_time']); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php else: ?>
                    <div class="empty-state">
                        <div class="ico">📰</div>
                        <p>暂无内容</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($shenchaList): ?>
            <div class="block">
                <div class="block-head">
                    <h2>审查起诉</h2>
                    <a href="<?php echo BASE_URL; ?>category.php?slug=shencha" class="more">查看全部 &raquo;</a>
                </div>
                <div class="block-body">
                    <div class="case-row">
                        <?php $i = 0; foreach ($shenchaList as $art): $i++; ?>
                        <div class="case-item">
                            <div class="case-no">CASE · <?php echo str_pad($art['id'], 4, '0', STR_PAD_LEFT); ?></div>
                            <h4><a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $art['id']; ?>" style="color:inherit;"><?php echo e($art['title']); ?></a></h4>
                            <p><?php echo e(truncateStr($art['summary'] ?: $art['title'], 80)); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($xunshiList): ?>
            <div class="block">
                <div class="block-head">
                    <h2>公益诉讼</h2>
                    <a href="<?php echo BASE_URL; ?>category.php?slug=xunshi" class="more">查看全部 &raquo;</a>
                </div>
                <div class="block-body">
                    <ul class="news-list">
                        <?php foreach ($xunshiList as $art): ?>
                        <li>
                            <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $art['id']; ?>" class="news-title"><?php echo e($art['title']); ?></a>
                            <span class="date"><?php echo formatDate($art['publish_time']); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div>
            <div class="block">
                <div class="block-head">
                    <h2>关注排行</h2>
                </div>
                <div class="block-body">
                    <?php if ($hotArticles): ?>
                    <ol class="rank-list">
                        <?php $r = 0; foreach ($hotArticles as $art): $r++; ?>
                        <li>
                            <span class="rank-no"><?php echo $r; ?></span>
                            <a href="<?php echo BASE_URL; ?>article.php?id=<?php echo $art['id']; ?>"><?php echo e(truncateStr($art['title'], 26)); ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                    <?php else: ?>
                    <div class="empty-state"><div class="ico">📊</div><p>暂无数据</p></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="block">
                <div class="block-head">
                    <h2>12309 检察服务</h2>
                </div>
                <div class="block-body" style="text-align:center;">
                    <div style="background:linear-gradient(135deg, var(--pk-blue) 0%, var(--pk-blue-mid) 100%); color:#fff; padding:24px 16px; border-radius:var(--pk-radius);">
                        <div style="font-size:11px; opacity:0.8; letter-spacing:2px; margin-bottom:6px;">NATIONAL PROCURATORIAL SERVICE</div>
                        <div style="font-size:36px; font-weight:700; letter-spacing:4px; font-family:var(--pk-font-sans);">12309</div>
                        <div style="font-size:12px; margin-top:8px; color:var(--pk-gold-light);">检察服务热线</div>
                    </div>
                    <div style="margin-top:14px; font-size:13px; color:var(--pk-gray-600); line-height:1.8; text-align:left;">
                        <p>· 受理群众举报、控告、申诉</p>
                        <p>· 提供法律咨询、案件查询</p>
                        <p>· 接受律师阅卷预约</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>