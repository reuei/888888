<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/slide.php';

if (!file_exists(DB_PATH)) redirect('install.php');

$yaowen = DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY is_top DESC, publish_time DESC LIMIT 8");
$hot = DB::fetchAll("SELECT * FROM articles WHERE status=1 ORDER BY views DESC LIMIT 8");
$totalArt = DB::fetchOne("SELECT COUNT(*) as c FROM articles WHERE status=1")['c'] ?? 0;
$totalCat = DB::fetchOne("SELECT COUNT(*) as c FROM categories")['c'] ?? 0;
$pageTitle = '';
include __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <h1>忠诚 担当 公正 清廉</h1>
  <p>人民检察院是国家的法律监督机关 · 依法行使检察权</p>
  <div class="hero-actions">
    <a href="report.php" class="btn btn-gold">信访举报</a>
    <a href="anticorruption.php" class="btn btn-outline">检察反腐</a>
  </div>
</section>

<div class="container">
  <div class="hero-grid">
    <div><?php echo getSliderHtml(); ?></div>
    <div class="feat">
      <div class="feat-img">
        <svg viewBox="0 0 200 100"><circle cx="160" cy="30" r="30" fill="none" stroke="#d4a53c" stroke-width=".8"/><circle cx="160" cy="30" r="18" fill="none" stroke="#d4a53c" stroke-width=".5"/><path d="M160 12 L170 28 L186 26 L182 42 L196 50 L182 58 L186 74 L170 72 L160 88 L150 72 L134 74 L138 58 L124 50 L138 42 L134 26 L150 28 Z" fill="#d4a53c" opacity=".7"/></svg>
        <div style="position:relative;z-index:1;text-align:center;color:#fff">
          <div style="font-size:11px;color:#d4a53c;letter-spacing:3px;margin-bottom:8px">PEOPLE'S PROCURATORATE</div>
          <div style="font-size:22px;font-weight:800;letter-spacing:3px">法治中国</div>
        </div>
      </div>
      <div class="feat-body">
        <span class="tag">头条</span>
        <h3><?php echo $yaowen ? e($yaowen[0]['title']) : '深入开展检察监督 维护社会公平正义'; ?></h3>
        <p>检察机关坚持总体国家安全观，依法履行法律监督职责，以高质量检察履职服务保障经济社会高质量发展。</p>
        <div class="meta">来源：检察要闻 · <?php echo date('Y-m-d'); ?></div>
      </div>
    </div>
  </div>

  <div class="stats">
    <div class="stat"><div class="num"><?php echo number_format($totalArt); ?></div><div class="label">检务公开信息</div></div>
    <div class="stat"><div class="num"><?php echo number_format($totalCat); ?></div><div class="label">业务板块</div></div>
    <div class="stat"><div class="num">24h</div><div class="label">在线服务</div></div>
    <div class="stat"><div class="num">100%</div><div class="label">为民承诺</div></div>
  </div>

  <div class="grid grid-side">
    <div>
      <div class="card" style="margin-bottom:20px">
        <div class="card-head"><span>检察要闻</span><a href="category.php?slug=yaowen">更多 &raquo;</a></div>
        <div class="card-body">
          <?php if ($yaowen): ?>
          <ul class="news-list">
            <?php foreach ($yaowen as $a): ?>
            <li><a href="article.php?id=<?php echo $a['id']; ?>"><?php if ($a['is_top']): ?><span class="top">置顶</span><?php endif; ?><?php echo e($a['title']); ?></a><span class="date"><?php echo formatDate($a['publish_time']); ?></span></li>
            <?php endforeach; ?>
          </ul>
          <?php else: ?><div class="empty">暂无内容</div><?php endif; ?>
        </div>
      </div>

      <div class="service-grid">
        <a href="report.php" class="service-card">
          <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg></div>
          <h4>信访举报</h4>
          <p>12309检察服务热线</p>
        </a>
        <a href="category.php?slug=shencha" class="service-card">
          <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="16" y1="16" x2="21" y2="21"/></svg></div>
          <h4>审查起诉</h4>
          <p>依法履行检察职责</p>
        </a>
        <a href="category.php?slug=xunshi" class="service-card">
          <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg></div>
          <h4>公益诉讼</h4>
          <p>守护公共利益</p>
        </a>
        <a href="category.php?slug=fagui" class="service-card">
          <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7z"/><path d="M9 12l2 2 4-4"/></svg></div>
          <h4>法律法规</h4>
          <p>检察法律体系</p>
        </a>
        <a href="cases.php" class="service-card">
          <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg></div>
          <h4>典型案例</h4>
          <p>以案释法</p>
        </a>
        <a href="topic.php" class="service-card">
          <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
          <h4>专题专栏</h4>
          <p>检察专题报道</p>
        </a>
      </div>
    </div>

    <div>
      <div class="card" style="margin-bottom:20px">
        <div class="card-head">关注排行</div>
        <div class="card-body">
          <?php if ($hot): ?>
          <ol class="rank-list">
            <?php foreach ($hot as $a): ?>
            <li><a href="article.php?id=<?php echo $a['id']; ?>"><?php echo e(truncateStr($a['title'], 24)); ?></a></li>
            <?php endforeach; ?>
          </ol>
          <?php else: ?><div class="empty">暂无数据</div><?php endif; ?>
        </div>
      </div>

      <div class="service-hero">
        <div class="num">12309</div>
        <div class="label">检察服务热线</div>
        <div class="info">
          <p>· 受理群众举报、控告、申诉</p>
          <p>· 提供法律咨询、案件查询</p>
          <p>· 接受律师阅卷预约</p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>