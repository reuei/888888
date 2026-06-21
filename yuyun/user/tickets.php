<?php
require __DIR__ . '/../includes/config.php';
require_login();
$pageTitle = '我的工单';
$db = getDb();
$user = current_user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $now = date('Y-m-d H:i:s');
    if (isset($_POST['close'])) {
        $db->prepare('UPDATE tickets SET status=1, updated_at=:now WHERE id=:id AND user_id=:uid')->execute([':id'=>$_POST['ticket_id'],':uid'=>$user['id'],':now'=>$now]);
        flash('success', '工单已关闭');
        redirect(YUYUN_URL . '/user/tickets.php?id=' . intval($_POST['ticket_id']));
    } elseif (isset($_POST['reply'])) {
        $content = trim($_POST['content'] ?? '');
        if ($content) {
            $db->prepare('INSERT INTO ticket_replies (ticket_id, user_id, content, created_at) VALUES (:tid,:uid,:c,:now)')->execute([':tid'=>$_POST['ticket_id'],':uid'=>$user['id'],':c'=>$content,':now'=>$now]);
            $db->prepare('UPDATE tickets SET updated_at=:now WHERE id=:id')->execute([':id'=>$_POST['ticket_id'],':now'=>$now]);
        }
        redirect(YUYUN_URL . '/user/tickets.php?id=' . intval($_POST['ticket_id']));
    } else {
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? '');
        $content = trim($_POST['content'] ?? '');
        if ($title && $content) {
            $stmt = $db->prepare('INSERT INTO tickets (user_id, title, category, status, created_at, updated_at) VALUES (:uid,:t,:c,0,:now,:now)');
            $stmt->execute([':uid'=>$user['id'],':t'=>$title,':c'=>$category,':now'=>$now]);
            $tid = $db->lastInsertId();
            $db->prepare('INSERT INTO ticket_replies (ticket_id, user_id, content, created_at) VALUES (:tid,:uid,:c,:now)')->execute([':tid'=>$tid,':uid'=>$user['id'],':c'=>$content,':now'=>$now]);
            flash('success', '工单已提交');
            redirect(YUYUN_URL . '/user/tickets.php?id=' . $tid);
        } else {
            flash('error', '请填写标题和内容');
            redirect(YUYUN_URL . '/user/tickets.php');
        }
    }
}
$detailId = intval($_GET['id'] ?? 0);
require __DIR__ . '/../includes/header.php';
?>
<section class="section bg-white">
    <div class="container">
        <div style="display:grid;grid-template-columns:240px 1fr;gap:24px">
            <div style="background:var(--dark-2);border-radius:12px;padding:14px 0;height:fit-content">
                <a href="<?php echo YUYUN_URL ?>/user/index.php"><i class="iconfont icon-gauge"></i> <?php echo __('welcome') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/notifications.php"><i class="iconfont icon-bell"></i> <?php echo __('notifications') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/tickets.php" class="active"><i class="iconfont icon-ticket"></i> <?php echo __('my_tickets') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/feedback.php"><i class="iconfont icon-edit"></i> <?php echo __('feedback') ?></a>
                <a href="<?php echo YUYUN_URL ?>/user/profile.php"><i class="iconfont icon-user"></i> <?php echo __('profile') ?></a>
            </div>
            <div>
                <h2 style="margin-bottom:20px">我的工单</h2>
                <?php echo render_flash() ?>
                <?php if ($detailId): ?>
                    <?php
                    $t = $db->prepare('SELECT * FROM tickets WHERE id=:id AND user_id=:uid LIMIT 1');
                    $t->execute([':id'=>$detailId,':uid'=>$user['id']]);
                    $ticket = $t->fetch(PDO::FETCH_ASSOC);
                    if (!$ticket) { echo '<div class="alert alert-error">工单不存在</div>'; }
                    else {
                        $replies = $db->prepare('SELECT r.*, u.nickname, u.email FROM ticket_replies r LEFT JOIN users u ON r.user_id=u.id WHERE r.ticket_id=:tid ORDER BY r.created_at');
                        $replies->execute([':tid'=>$ticket['id']]);
                    ?>
                    <div class="admin-card">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                            <h3 style="margin:0"><?php echo e($ticket['title']) ?></h3>
                            <span class="btn btn-sm <?php echo $ticket['status']==0?'btn-primary':'btn-dark' ?>"><?php echo $ticket['status']==0?'处理中':'已关闭' ?></span>
                        </div>
                        <div style="margin-bottom:20px">
                            <?php foreach ($replies->fetchAll(PDO::FETCH_ASSOC) as $r): ?>
                                <div style="margin-bottom:16px;padding:14px;border-radius:8px;background:<?php echo $r['is_staff']?'#fff7e6':'#f5f7fa' ?>;border-left:3px solid <?php echo $r['is_staff']?'var(--brand)':'#ccc' ?>">
                                    <div style="font-weight:600;margin-bottom:6px;color:var(--dark)"><?php echo e($r['nickname'] ?: $r['email']) ?> <?php echo $r['is_staff']?'<span style="color:var(--brand)">[官方]</span>':'' ?></div>
                                    <div style="color:var(--text-2);white-space:pre-wrap"><?php echo nl2br(e($r['content'])) ?></div>
                                    <div style="font-size:12px;color:#999;margin-top:6px"><?php echo e($r['created_at']) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($ticket['status'] == 0): ?>
                            <form method="post" class="mt-4">
                                <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
                                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id'] ?>">
                                <div class="form-group"><textarea name="content" class="form-control" placeholder="回复内容" required></textarea></div>
                                <button type="submit" name="reply" class="btn btn-primary">回复</button>
                                <button type="submit" name="close" class="btn btn-outline" style="margin-left:10px" onclick="return confirm('确定关闭该工单？')">关闭工单</button>
                            </form>
                        <?php endif; ?>
                    </div>
                    <?php } ?>
                    <p><a href="<?php echo YUYUN_URL ?>/user/tickets.php" class="more">&larr; 返回工单列表</a></p>
                <?php else: ?>
                    <div class="admin-card">
                        <h3 style="margin-bottom:16px">提交新工单</h3>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo csrf_token() ?>">
                            <div class="form-row">
                                <div class="form-group"><label>标题</label><input type="text" name="title" class="form-control" required></div>
                                <div class="form-group"><label>分类</label>
                                    <select name="category" class="form-control">
                                        <option>技术咨询</option>
                                        <option>售后问题</option>
                                        <option>账单疑问</option>
                                        <option>投诉建议</option>
                                        <option>其他</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group"><label>问题描述</label><textarea name="content" class="form-control" required></textarea></div>
                            <button type="submit" class="btn btn-primary">提交工单</button>
                        </form>
                    </div>
                    <div class="admin-card">
                        <h3 style="margin-bottom:16px">工单记录</h3>
                        <table class="admin-table">
                            <thead><tr><th>标题</th><th>分类</th><th>状态</th><th>更新时间</th><th>操作</th></tr></thead>
                            <tbody>
                                <?php
                                $list = $db->prepare('SELECT * FROM tickets WHERE user_id=:uid ORDER BY updated_at DESC');
                                $list->execute([':uid'=>$user['id']]);
                                foreach ($list->fetchAll(PDO::FETCH_ASSOC) as $tk): ?>
                                <tr>
                                    <td><?php echo e($tk['title']) ?></td>
                                    <td><?php echo e($tk['category']) ?></td>
                                    <td><?php echo $tk['status']==0?'<span class="status-dot status-open"></span>处理中':'<span class="status-dot status-closed"></span>已关闭' ?></td>
                                    <td><?php echo e($tk['updated_at']) ?></td>
                                    <td><a href="?id=<?php echo $tk['id'] ?>" class="text-brand">查看</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
