<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$id = intval($_GET['id'] ?? 0);
$page = DB::fetchOne("SELECT * FROM pages WHERE id=? AND status=1", [$id]);

if (!$page) {
    header('HTTP/1.1 404 Not Found');
    $pageTitle = '页面不存在';
    include __DIR__ . '/includes/header.php';
    echo '<div class="container" style="padding:50px 0; text-align:center;"><h2>页面不存在</h2></div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $page['title'];
include __DIR__ . '/includes/header.php';
?>

    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <span class="sep">/</span>
            <span><?php echo e($page['title']); ?></span>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="section" style="max-width:900px; margin:0 auto;">
                <div class="section-body" style="padding:30px 40px;">
                    <h1 style="text-align:center; font-size:26px; margin-bottom:20px;"><?php echo e($page['title']); ?></h1>
                    <div class="article-content">
                        <?php echo $page['content']; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
