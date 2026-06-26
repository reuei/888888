<?php
$userSidebar = current_user();
$unreadSidebar = unread_notification_count($userSidebar['id']);
$currentFile = basename($_SERVER['PHP_SELF']);
$items = [
    ['url'=>'/user/index.php','icon'=>'gauge','text'=>__('welcome')],
    ['url'=>'/user/notifications.php','icon'=>'bell','text'=>__('notifications'),'badge'=>$unreadSidebar],
    ['url'=>'/user/tickets.php','icon'=>'ticket','text'=>__('my_tickets')],
    ['url'=>'/user/feedback.php','icon'=>'edit','text'=>__('feedback')],
    ['url'=>'/user/profile.php','icon'=>'user','text'=>__('profile')],
];
?>
<aside class="user-sidebar">
    <?php foreach ($items as $it): ?>
    <a href="<?php echo YUYUN_URL . $it['url'] ?>" class="<?php echo $currentFile === basename($it['url']) ? 'active' : '' ?>">
        <i class="iconfont icon-<?php echo $it['icon'] ?>"></i>
        <span><?php echo $it['text'] ?></span>
        <?php if (!empty($it['badge']) && $it['badge'] > 0): ?>
        <span class="sidebar-badge"><?php echo (int)$it['badge'] ?></span>
        <?php endif; ?>
    </a>
    <?php endforeach; ?>
</aside>
