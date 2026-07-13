<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$msg = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $name = trim($_POST['name'] ?? '');

    if (empty($content)) {
        $msg = '请填写留言内容';
        $msgType = 'error';
    } else {
        DB::insert('messages', [
            'type' => 'message',
            'title' => $title,
            'content' => $content,
            'name' => $name,
            'contact' => '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'status' => 0,
        ]);
        $msg = '留言已提交，感谢您的参与！';
        $msgType = 'success';
    }
}

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 10;
$offset = ($page - 1) * $perPage;
$total = DB::fetchOne("SELECT COUNT(*) as cnt FROM messages WHERE type='message' AND status=1")['cnt'];
$messages = DB::fetchAll("SELECT * FROM messages WHERE type='message' AND status=1 ORDER BY create_time DESC LIMIT $offset, $perPage");

$pageTitle = '留言板';
include __DIR__ . '/includes/header.php';
?>

    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo BASE_URL; ?>index.php">首页</a>
            <span class="sep">/</span>
            <span>留言板</span>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="content-wrap">
                <div class="main-col">
                    <div class="section">
                        <div class="section-header">
                            <h3>留言板</h3>
                        </div>
                        <div class="section-body">
                            <?php if ($msg): ?>
                            <div style="padding:10px 15px; border-radius:4px; margin-bottom:20px; <?php echo $msgType == 'success' ? 'background:#f6ffed; color:#52c41a; border:1px solid #b7eb8f;' : 'background:#fff1f0; color:#f5222d; border:1px solid #ffa39e;'; ?>">
                                <?php echo e($msg); ?>
                            </div>
                            <?php endif; ?>

                            <form method="post" data-toast-form style="margin-bottom:30px;">
                                <div class="form-item">
                                    <label>昵称（选填）</label>
                                    <input type="text" name="name" value="<?php echo e($_POST['name'] ?? ''); ?>" placeholder="请输入您的昵称">
                                </div>
                                <div class="form-item">
                                    <label>留言标题（选填）</label>
                                    <input type="text" name="title" value="<?php echo e($_POST['title'] ?? ''); ?>" placeholder="留言标题" data-validate="title">
                                    <div class="field-tip"></div>
                                </div>
                                <div class="form-item">
                                    <label>留言内容 *</label>
                                    <textarea name="content" rows="4" required placeholder="请输入留言内容" data-validate="content"><?php echo e($_POST['content'] ?? ''); ?></textarea>
                                    <div class="field-tip"></div>
                                </div>
                                <button type="submit" class="btn">提交留言</button>
                            </form>

                            <h4 style="margin-bottom:15px; padding-bottom:10px; border-bottom:1px solid #f0f0f0;">最新留言</h4>
                            <?php if ($messages): ?>
                                <?php foreach ($messages as $m): ?>
                                <div style="padding:15px 0; border-bottom:1px solid #f5f5f5;">
                                    <div style="margin-bottom:8px;">
                                        <strong><?php echo e($m['name'] ?: '匿名'); ?></strong>
                                        <span style="color:#999; font-size:12px; margin-left:10px;"><?php echo formatDate($m['create_time'], 'Y-m-d H:i'); ?></span>
                                    </div>
                                    <?php if ($m['title']): ?>
                                    <p style="font-weight:500; margin-bottom:5px;"><?php echo e($m['title']); ?></p>
                                    <?php endif; ?>
                                    <p style="color:#666; line-height:1.8;"><?php echo nl2br(e($m['content'])); ?></p>
                                </div>
                                <?php endforeach; ?>
                                <?php echo paginate($total, $page, $perPage, BASE_URL . 'message.php'); ?>
                            <?php else: ?>
                            <p style="text-align:center; color:#999; padding:30px 0;">暂无留言</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="side-col">
                    <div class="side-block">
                        <div class="side-block-title">留言须知</div>
                        <div class="side-block-body" style="font-size:13px; color:#666; line-height:1.8;">
                            <p>1. 请文明留言，遵守相关法律法规；</p>
                            <p style="margin-top:5px;">2. 留言经审核后显示；</p>
                            <p style="margin-top:5px;">3. 请勿发布违法违规内容。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include __DIR__ . '/includes/footer.php'; ?>
