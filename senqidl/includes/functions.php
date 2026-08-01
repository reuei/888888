<?php
// 辅助函数
function isMobile() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $mobileAgents = ['mobile', 'android', 'iphone', 'ipad', 'ipod', 'blackberry', 'windows phone', 'operamini', 'iemobile', 'wpdesktop', 'fennec'];
    $userAgent = strtolower($userAgent);
    foreach ($mobileAgents as $agent) {
        if (strpos($userAgent, $agent) !== false) {
            return true;
        }
    }
    // 也检查是否有移动设备标识
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
        return true;
    }
    return false;
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function assetUrl($path) {
    return SITE_URL . $path;
}

function siteUrl($path = '') {
    return SITE_URL . ($path ? '/' . ltrim($path, '/') : '');
}

function h($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function truncate($str, $len = 100, $suffix = '...') {
    $str = trim($str);
    if (mb_strlen($str) <= $len) return $str;
    return mb_substr($str, 0, $len) . $suffix;
}

function formatDate($date, $format = 'Y-m-d') {
    if (!$date) return '';
    return date($format, strtotime($date));
}

function currentPage() {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    return basename($path, '.php');
}

function currentDir() {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $parts = explode('/', trim($path, '/'));
    return $parts[0] ?? '';
}

function activeNav($page) {
    return currentPage() === $page ? 'active' : '';
}

function breadcrumb($items) {
    $html = '<div class="breadcrumb">';
    foreach ($items as $key => $item) {
        if ($key > 0) $html .= ' <span class="sep">/</span> ';
        if (isset($item['url'])) {
            $html .= '<a href="' . h($item['url']) . '">' . h($item['title']) . '</a>';
        } else {
            $html .= '<span>' . h($item['title']) . '</span>';
        }
    }
    $html .= '</div>';
    return $html;
}

function pagination($total, $current, $perPage, $urlPattern) {
    $totalPages = ceil($total / $perPage);
    if ($totalPages <= 1) return '';
    $html = '<div class="pagination">';
    for ($i = 1; $i <= $totalPages; $i++) {
        $class = $i == $current ? 'class="active"' : '';
        $html .= "<a href=\"" . str_replace('{page}', $i, $urlPattern) . "\" $class>$i</a>";
    }
    $html .= '</div>';
    return $html;
}
