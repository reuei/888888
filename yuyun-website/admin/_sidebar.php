<?php
/**
 * 后台管理 - 公共侧边栏组件
 * 被各管理页面include引用
 * 需要在调用前已定义 YUYUN_ROOT 并执行 session_start() 和 require_admin()
 */

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('collapsed')">
        <i class="fas fa-angle-double-left"></i>
    </div>

    <div class="sidebar-header">
        <div class="sidebar-logo">语</div>
        <div class="sidebar-title">
            <h2>语云科技</h2>
            <span>管理中心 v1.0</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">主导航</div>
            <a href="dashboard.php" class="nav-item <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>仪表盘</span>
            </a>
            <a href="settings.php" class="nav-item <?php echo $currentPage === 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>站点设置</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">内容管理</div>
            <a href="content.php" class="nav-item <?php echo $currentPage === 'content.php' ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i>
                <span>内容管理</span>
            </a>
            <a href="products.php" class="nav-item <?php echo $currentPage === 'products.php' ? 'active' : ''; ?>">
                <i class="fas fa-box"></i>
                <span>产品管理</span>
            </a>
            <a href="templates.php" class="nav-item <?php echo $currentPage === 'templates.php' ? 'active' : ''; ?>">
                <i class="fas fa-palette"></i>
                <span>模板管理</span>
            </a>
            <a href="staff.php" class="nav-item <?php echo $currentPage === 'staff.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>员工管理</span>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">运营管理</div>
            <a href="users.php" class="nav-item <?php echo $currentPage === 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-friends"></i>
                <span>用户管理</span>
            </a>
            <a href="tickets.php" class="nav-item <?php echo $currentPage === 'tickets.php' ? 'active' : ''; ?>">
                <i class="fas fa-ticket-alt"></i>
                <span>工单管理</span>
                <?php
                    // 显示待处理工单数量
                    $tickets = get_content('tickets') ?: [];
                    $openCount = count(array_filter($tickets, fn($t) => in_array($t['status'] ?? '', ['open', 'processing'])));
                    if ($openCount > 0):
                ?>
                    <span class="badge"><?php echo $openCount; ?></span>
                <?php endif; ?>
            </a>
        </div>

        <div class="nav-section">
            <div class="nav-section-title">系统</div>
            <a href="system.php" class="nav-item <?php echo $currentPage === 'system.php' ? 'active' : ''; ?>">
                <i class="fas fa-server"></i>
                <span>系统管理</span>
            </a>
            <a href="../api/auth.php?action=logout" class="nav-item" onclick="return confirm('确定要退出登录吗？');">
                <i class="fas fa-sign-out-alt"></i>
                <span>退出登录</span>
            </a>
        </div>
    </nav>
</aside>

<!-- 移动端遮罩 -->
<div class="sidebar-overlay" onclick="
    document.getElementById('sidebar').classList.remove('mobile-show');
    document.querySelector('.sidebar-overlay')?.classList.remove('show');
"></div>
