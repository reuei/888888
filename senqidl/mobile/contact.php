<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$currentPage = 'contact';
$settings = DB::getSetting();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $messageText = trim($_POST['message'] ?? '');

    $errors = [];
    if (empty($name)) $errors[] = '请填写您的姓名';
    if (empty($phone)) $errors[] = '请填写您的电话';
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = '请输入有效的邮箱地址';
    if (empty($messageText)) $errors[] = '请填写留言内容';

    if (empty($errors)) {
        $contactData = [
            'id' => time(),
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'message' => $messageText,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $contactList = [];
        $contactFile = DATA_PATH . '/contact.json';
        if (file_exists($contactFile)) {
            $existing = json_decode(file_get_contents($contactFile), true);
            if (is_array($existing)) {
                $contactList = $existing;
            }
        }
        $contactList[] = $contactData;
        file_put_contents($contactFile, json_encode($contactList, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $message = '感谢您的留言！我们的顾问将尽快与您联系。';
        $messageType = 'success';
    } else {
        $message = implode('；', $errors);
        $messageType = 'error';
    }
}

$faqData = [
    ['q' => '你们提供哪些服务？', 'a' => '我们提供品牌网站建设、SEO优化、外贸营销、品牌设计、小程序开发、数字营销等全链路品牌数字化解决方案。'],
    ['q' => '项目周期一般是多久？', 'a' => '根据项目规模和复杂度不同，周期也会有所差异。一般来说，企业官网项目为2-4周，SEO优化项目为3-6个月，外贸营销项目为6-12个月。'],
    ['q' => '你们的收费标准是怎样的？', 'a' => '我们根据客户的具体需求提供定制化报价。不同服务类型、项目规模、需求复杂度都会影响报价。欢迎通过电话或在线咨询获取详细报价。'],
    ['q' => '是否提供售后服务？', 'a' => '是的，所有项目均提供长期售后服务，包括技术支持、功能维护、数据监控等。我们承诺7x24小时响应客户需求。']
];
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#1a5fff">
    <title>联系我们 - <?php echo h(SITE_NAME); ?></title>
    <link rel="stylesheet" href="/mobile/assets/css/style.css">
</head>
<body class="page page-contact">

<header class="site-header">
    <div class="header-inner">
        <a href="/mobile/index.php" class="logo">
            <span class="logo-text"><?php echo h(SITE_NAME); ?></span>
        </a>
        <button class="hamburger" id="hamburger" aria-label="菜单">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu">
    <nav>
        <ul>
            <li><a href="/mobile/index.php">首页</a></li>
            <li><a href="/mobile/about.php">关于我们</a></li>
            <li><a href="/mobile/service.php">服务项目</a></li>
            <li><a href="/mobile/case.php">客户案例</a></li>
            <li><a href="/mobile/news.php">新闻资讯</a></li>
            <li><a href="/mobile/contact.php">联系我们</a></li>
        </ul>
    </nav>
</div>
<div class="mobile-menu-backdrop" id="mobileMenuBackdrop"></div>

<div class="page-header">
    <h1>联系我们</h1>
    <p>我们期待与您的合作</p>
    <div class="breadcrumb-nav">
        <a href="/mobile/index.php">首页</a>
        <span class="sep">/</span>
        <span class="current">联系我们</span>
    </div>
</div>

<section class="section">
    <div class="contact-info-cards">
        <div class="contact-card">
            <div class="contact-card-icon">📞</div>
            <div class="contact-card-info">
                <h4>电话咨询</h4>
                <p><?php echo h($settings['contact_phone'] ?? ''); ?></p>
            </div>
        </div>
        <div class="contact-card">
            <div class="contact-card-icon">✉️</div>
            <div class="contact-card-info">
                <h4>邮箱联系</h4>
                <p><?php echo h($settings['contact_email'] ?? ''); ?></p>
            </div>
        </div>
        <div class="contact-card">
            <div class="contact-card-icon">📍</div>
            <div class="contact-card-info">
                <h4>公司地址</h4>
                <p><?php echo h($settings['contact_address'] ?? ''); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="contact-form">
        <h3>在线留言</h3>
        <p style="font-size: 0.85rem; color: var(--text-light); margin: 0 0 16px;">请填写以下表单，我们会尽快与您联系。</p>

        <?php if ($message): ?>
        <div class="form-alert <?php echo $messageType; ?>">
            <?php echo h($message); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/mobile/contact.php" class="validate-form">
            <div class="form-group">
                <label class="form-label" for="name">姓名 <span class="required">*</span></label>
                <input type="text" id="name" name="name" class="form-control" required data-required placeholder="请输入您的姓名" value="<?php echo h($_POST['name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="phone">电话 <span class="required">*</span></label>
                <input type="tel" id="phone" name="phone" class="form-control" required data-required placeholder="请输入您的联系电话" value="<?php echo h($_POST['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="email">邮箱</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="请输入您的邮箱地址" value="<?php echo h($_POST['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="message">留言 <span class="required">*</span></label>
                <textarea id="message" name="message" class="form-control" rows="5" required data-required placeholder="请详细描述您的需求..."><?php echo h($_POST['message'] ?? ''); ?></textarea>
            </div>
            <button type="submit" name="contact_submit" class="btn btn-primary btn-block">提交咨询</button>
        </form>
    </div>
</section>

<div class="map-placeholder">
    <div>
        <div class="icon">🗺️</div>
        <h4>公司位置</h4>
        <p><?php echo h($settings['contact_address'] ?? '中国·深圳'); ?></p>
    </div>
</div>

<section class="section">
    <div class="section-header">
        <h2 class="section-title">常见问题</h2>
        <div class="section-divider"></div>
    </div>
    <div class="faq-list">
        <?php foreach ($faqData as $faq): ?>
        <div class="faq-item">
            <button class="faq-question">
                <span><?php echo h($faq['q']); ?></span>
                <span class="faq-icon">+</span>
            </button>
            <div class="faq-answer">
                <p><?php echo h($faq['a']); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<nav class="tab-bar">
    <a href="/mobile/index.php" class="tab-item">
        <img src="/mobile/assets/images/home.svg" alt="首页">
        <span>首页</span>
    </a>
    <a href="/mobile/service.php" class="tab-item">
        <img src="/mobile/assets/images/service.svg" alt="服务">
        <span>服务</span>
    </a>
    <a href="/mobile/case.php" class="tab-item">
        <img src="/mobile/assets/images/case.svg" alt="案例">
        <span>案例</span>
    </a>
    <a href="/mobile/news.php" class="tab-item">
        <img src="/mobile/assets/images/news.svg" alt="新闻">
        <span>新闻</span>
    </a>
    <a href="/mobile/contact.php" class="tab-item active">
        <img src="/mobile/assets/images/contact_h.svg" alt="联系">
        <span>联系</span>
    </a>
</nav>
<script src="/mobile/assets/js/main.js"></script>
</body>
</html>