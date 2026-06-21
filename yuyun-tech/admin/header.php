<?php
/**
 * 后台公共头部
 */
require_once 'config.php';
check_auth();
handle_save();
$active_page = $active_page ?? 'dashboard';
$site_data = load_site_data();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>后台管理 - 语云科技</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.admin-content .admin-card h2 { font-size:18px; }
.data-table th, .data-table td { padding:12px; }
.admin-main { min-height:100vh; background:#f5f7fa; }
.form-group .form-input, .form-group .form-textarea, .form-group .form-select {
    background:#fff;
}
.item-card {
    background:#f8fafc;
    border:1px solid #e9ecef;
    border-radius:8px;
    padding:16px;
    margin-bottom:12px;
}
.item-card-actions {
    margin-top:12px;
    text-align:right;
}
.admin-sidebar { background: #1a1a2e; }
.admin-sidebar nav a { color: rgba(255,255,255,0.75); border-left: 3px solid transparent; padding: 14px 24px; display:flex; align-items:center; gap:10px; text-decoration:none; font-size:14px; }
.admin-sidebar nav a:hover { background:rgba(255,255,255,0.05); color:#fff; }
.admin-sidebar nav a.active { background: rgba(26,115,232,0.15); color:#fff; border-left-color:#ff6b35; }
.admin-sidebar-header { color:#fff; font-size:18px; font-weight:700; padding: 24px; border-bottom:1px solid rgba(255,255,255,0.08); }
.admin-sidebar-header .logo-icon { width:36px; height:36px; font-size:16px; display:inline-flex; align-items:center; justify-content:center; background: linear-gradient(135deg,#1a73e8,#ff6b35); border-radius:8px; color:#fff; margin-right:10px; }

@media (max-width: 768px) {
    .admin-sidebar { transform: translateX(-100%); position: fixed; z-index: 999; width: 250px; transition: transform 0.3s; }
    .admin-sidebar.active { transform: translateX(0); }
    .admin-main { margin-left: 0; }
    .admin-hamburger {
        display:block;
    }
}
</style>
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="admin-sidebar-header">
            <span class="logo-icon">Y</span>语云科技
        </div>
        <nav>
            <a href="index.php" class="<?php echo $active_page==='dashboard'?'active':''; ?>"><i class="fas fa-tachometer-alt"></i> 仪表盘</a>
            <a href="site.php" class="<?php echo $active_page==='site'?'active':''; ?>"><i class="fas fa-cog"></i> 网站设置</a>
            <a href="slides.php" class="<?php echo $active_page==='slides'?'active':''; ?>"><i class="fas fa-images"></i> 轮播图管理</a>
            <a href="products.php" class="<?php echo $active_page==='products'?'active':''; ?>"><i class="fas fa-cube"></i> 产品管理</a>
            <a href="partners.php" class="<?php echo $active_page==='partners'?'active':''; ?>"><i class="fas fa-handshake"></i> 合作伙伴</a>
            <a href="certificates.php" class="<?php echo $active_page==='certificates'?'active':''; ?>"><i class="fas fa-certificate"></i> 资质证书</a>
            <a href="employees.php" class="<?php echo $active_page==='employees'?'active':''; ?>"><i class="fas fa-user-tie"></i> 员工管理</a>
            <a href="testimonials.php" class="<?php echo $active_page==='testimonials'?'active':''; ?>"><i class="fas fa-comment-dots"></i> 用户评价</a>
            <a href="news.php" class="<?php echo $active_page==='news'?'active':''; ?>"><i class="fas fa-newspaper"></i> 新闻动态</a>
            <a href="locations.php" class="<?php echo $active_page==='locations'?'active':''; ?>"><i class="fas fa-map-marker-alt"></i> 地区分布</a>
            <a href="contact.php" class="<?php echo $active_page==='contact'?'active':''; ?>"><i class="fas fa-address-card"></i> 联系方式</a>
            <a href="icp.php" class="<?php echo $active_page=='icp'?'active':''; ?>"><i class="fas fa-shield-alt"></i> 备案信息</a>
            <a href="friendlinks.php" class="<?php echo $active_page==='friendlinks'?'active':''; ?>"><i class="fas fa-link"></i> 友情链接</a>
            <a href="account.php" class="<?php echo $active_page==='account'?'active':''; ?>"><i class="fas fa-user-circle"></i> 账号管理</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <div style="display:flex;align-items:center;gap:16px;">
                <button class="admin-hamburger" onclick="document.getElementById('adminSidebar').classList.toggle('active')" style="background:transparent;border:none;cursor:pointer;padding:8px;border-radius:6px;color:#4a4a4a;font-size:18px;">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 style="font-size:18px;font-weight:700;color:#1a1a2e;margin:0;">
                    <?php
                    $page_titles = [
                        'dashboard' => '仪表盘',
                        'site' => '网站设置',
                        'slides' => '轮播图管理',
                        'products' => '产品管理',
                        'partners' => '合作伙伴',
                        'certificates' => '资质证书',
                        'employees' => '员工管理',
                        'testimonials' => '用户评价',
                        'news' => '新闻动态',
                        'locations' => '地区分布',
                        'contact' => '联系方式',
                        'icp' => '备案信息',
                        'friendlinks' => '友情链接',
                        'account' => '账号管理',
                    ];
                    echo $page_titles[$active_page] ?? '后台管理';
                    ?>
                </h1>
            </div>
            <div class="admin-user">
                <a href="../index.php" target="_blank" class="btn btn-ghost btn-sm"><i class="fas fa-eye"></i> 查看网站</a>
                <a href="?logout=1" class="btn btn-secondary btn-sm" onclick="return confirm('确定要退出登录吗？')"><i class="fas fa-sign-out-alt"></i> 退出</a>
            </div>
        </header>

<?php
// 登出处理
if (isset($_GET['logout'])) {
    do_logout();
}
?>
