<?php
if (!defined('YUYUN_ROOT')) {
    define('YUYUN_ROOT', dirname(__DIR__));
}

$availableLanguages = ['zh' => '中文', 'en' => 'English'];

function detectLang(): string {
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['zh','en'], true)) {
        setcookie('lang', $_GET['lang'], time()+86400*365, '/');
        return $_GET['lang'];
    }
    if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], ['zh','en'], true)) {
        return $_COOKIE['lang'];
    }
    if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['lang']) && in_array($_SESSION['lang'], ['zh','en'], true)) {
        return $_SESSION['lang'];
    }
    return 'zh';
}

$_SESSION['lang'] = detectLang();
$currentLang = $_SESSION['lang'];

$translations = [
    'zh' => [
        'home' => '首页',
        'about' => '关于我们',
        'company' => '公司简介',
        'products' => '产品介绍',
        'partners' => '合作伙伴',
        'contact' => '联系我们',
        'intl' => '国际版官网',
        'user_center' => '用户中心',
        'admin' => '后台',
        'login' => '登录',
        'logout' => '退出',
        'register' => '注册',
        'submit' => '提交',
        'save' => '保存',
        'cancel' => '取消',
        'edit' => '编辑',
        'delete' => '删除',
        'search' => '搜索',
        'phone' => '电话',
        'email' => '邮箱',
        'password' => '密码',
        'confirm_password' => '确认密码',
        'nickname' => '昵称',
        'welcome' => '欢迎',
        'back_to_top' => '返回顶部',
        'online_support' => '在线客服',
        'learn_more' => '立即了解',
        'view_products' => '查看产品',
        'detail' => '了解详情',
        'copyright' => '版权所有',
        'beian' => '备案号',
        'police' => '公安网备案号',
        'sales_phone' => '销售电话',
        'login_welcome' => '欢迎回来',
        'login_subtitle' => '登录语云科技用户中心',
        'register_account' => '创建账号',
        'register_subtitle' => '注册后即可提交工单与建议',
        'verify_login' => '邮箱验证码登录',
        'verify_subtitle' => '无需密码，验证码 5 分钟内有效',
        'send_code' => '发送验证码',
        'verify_code' => '验证码',
        'my_tickets' => '我的工单',
        'feedback' => '建议/举报',
        'profile' => '个人资料',
        'notifications' => '消息通知',
        'no_notifications' => '暂无消息',
        'mark_read' => '标记已读',
        'system_notice' => '系统公告',
        'theme_light' => '浅色模式',
        'theme_dark' => '深色模式',
        'language' => '语言',
        'banner_default' => '欢迎来到语云科技官网！',
    ],
    'en' => [
        'home' => 'Home',
        'about' => 'About',
        'company' => 'Company',
        'products' => 'Products',
        'partners' => 'Partners',
        'contact' => 'Contact',
        'intl' => 'International',
        'user_center' => 'User Center',
        'admin' => 'Admin',
        'login' => 'Login',
        'logout' => 'Logout',
        'register' => 'Register',
        'submit' => 'Submit',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'search' => 'Search',
        'phone' => 'Phone',
        'email' => 'Email',
        'password' => 'Password',
        'confirm_password' => 'Confirm Password',
        'nickname' => 'Nickname',
        'welcome' => 'Welcome',
        'back_to_top' => 'Back to Top',
        'online_support' => 'Support',
        'learn_more' => 'Learn More',
        'view_products' => 'Products',
        'detail' => 'Details',
        'copyright' => 'All Rights Reserved',
        'beian' => 'ICP',
        'police' => 'Police Filing',
        'sales_phone' => 'Sales',
        'login_welcome' => 'Welcome Back',
        'login_subtitle' => 'Login to YuYun user center',
        'register_account' => 'Create Account',
        'register_subtitle' => 'Register to submit tickets & feedback',
        'verify_login' => 'Email Code Login',
        'verify_subtitle' => 'No password needed, code valid for 5 minutes',
        'send_code' => 'Send Code',
        'verify_code' => 'Verification Code',
        'my_tickets' => 'My Tickets',
        'feedback' => 'Feedback',
        'profile' => 'Profile',
        'notifications' => 'Notifications',
        'no_notifications' => 'No notifications',
        'mark_read' => 'Mark Read',
        'system_notice' => 'System Notice',
        'theme_light' => 'Light Mode',
        'theme_dark' => 'Dark Mode',
        'language' => 'Language',
        'banner_default' => 'Welcome to YuYun Technology!',
    ],
];

function __(string $key): string {
    global $translations, $currentLang;
    return $translations[$currentLang][$key] ?? ($translations['zh'][$key] ?? $key);
}

function langUrl(string $lang): string {
    $url = $_SERVER['REQUEST_URI'] ?? '/';
    $url = preg_replace('/([?&])lang=[^&]+&?/', '$1', $url);
    $url = rtrim($url, '?&');
    $sep = strpos($url, '?') === false ? '?' : '&';
    return $url . $sep . 'lang=' . $lang;
}
