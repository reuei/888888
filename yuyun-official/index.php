<?php
/**
 * 前台入口
 */

define('YUYUN_ROOT', __DIR__);

require_once YUYUN_ROOT . '/config.php';

if (!defined('INSTALLED') || !INSTALLED) {
    header('Location: install.php');
    exit;
}

require_once YUYUN_ROOT . '/includes/functions.php';
require_once YUYUN_ROOT . '/includes/auth.php';

$page = isset($_GET['page']) ? trim($_GET['page']) : 'home';
$allowedPages = ['home', 'about', 'company', 'products', 'contact', 'partners', 'international'];
if (!in_array($page, $allowedPages)) {
    $page = 'home';
}

$pageFile = YUYUN_ROOT . '/pages/' . $page . '.php';
if (!file_exists($pageFile)) {
    $page = 'home';
    $pageFile = YUYUN_ROOT . '/pages/home.php';
}

$pageTitles = [
    'home' => '',
    'about' => '关于我们',
    'company' => '公司简介',
    'products' => '产品介绍',
    'contact' => '联系我们',
    'partners' => '合作伙伴',
    'international' => '国际版官网',
];

$currentPage = $page;
$siteTitle = getSetting('site_title', '语云科技');
$title = $pageTitles[$page] ? $pageTitles[$page] . ' - ' . $siteTitle : $siteTitle;
$keywords = getSetting('site_keywords');
$description = getSetting('site_description');
$favicon = getSetting('site_favicon');
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo yy_e($title); ?></title>
    <?php if ($keywords): ?><meta name="keywords" content="<?php echo yy_e($keywords); ?>"><?php endif; ?>
    <?php if ($description): ?><meta name="description" content="<?php echo yy_e($description); ?>"><?php endif; ?>
    <?php if ($favicon && file_exists(YUYUN_ROOT . '/' . $favicon)): ?><link rel="icon" href="<?php echo yy_e($favicon); ?>"><?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo templateUrl('css/style.css'); ?>">
</head>
<body>
    <?php
    $headerPath = templatePath('partials/header.php');
    if (!file_exists($headerPath)) {
        $headerPath = YUYUN_ROOT . '/templates/default/partials/header.php';
    }
    $footerPath = templatePath('partials/footer.php');
    if (!file_exists($footerPath)) {
        $footerPath = YUYUN_ROOT . '/templates/default/partials/footer.php';
    }
    $jsUrl = templateUrl('js/main.js');
    if (!file_exists(YUYUN_ROOT . '/templates/' . getSetting('current_template', 'default') . '/js/main.js')) {
        $jsUrl = './templates/default/js/main.js';
    }
    include $headerPath;
    ?>
    <main class="main">
        <?php include $pageFile; ?>
    </main>
    <?php include $footerPath; ?>
    <script src="<?php echo yy_e($jsUrl); ?>"></script>
</body>
</html>
