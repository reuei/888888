<?php
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header('Location: ' . BASE_URL . $url);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUser() {
    if (!isLoggedIn()) return null;
    return DB::fetchOne("SELECT * FROM users WHERE id=?", [$_SESSION['user_id']]);
}

function isAdmin() {
    $user = currentUser();
    return $user && in_array($user['role'], ['admin', 'super_admin']);
}

function isSuperAdmin() {
    $user = currentUser();
    return $user && $user['role'] === 'super_admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        die('无权访问');
    }
}

function getSetting($key, $default = '') {
    $row = DB::fetchOne("SELECT value FROM settings WHERE `key`=?", [$key]);
    return $row ? $row['value'] : $default;
}

function setSetting($key, $value) {
    $exists = DB::fetchOne("SELECT id FROM settings WHERE `key`=?", [$key]);
    if ($exists) {
        DB::update('settings', ['value' => $value], '`key`=?', [$key]);
    } else {
        DB::insert('settings', ['key' => $key, 'value' => $value]);
    }
}

function getCategories() {
    return DB::fetchAll("SELECT * FROM categories WHERE parent_id=0 ORDER BY sort_order ASC, id ASC");
}

function getChildCategories($parentId) {
    return DB::fetchAll("SELECT * FROM categories WHERE parent_id=? ORDER BY sort_order ASC, id ASC", [$parentId]);
}

function getCategoryBySlug($slug) {
    return DB::fetchOne("SELECT * FROM categories WHERE slug=?", [$slug]);
}

function getCategory($id) {
    return DB::fetchOne("SELECT * FROM categories WHERE id=?", [$id]);
}

function getBreadcrumb($catId) {
    $path = [];
    while ($catId) {
        $cat = getCategory($catId);
        if (!$cat) break;
        array_unshift($path, $cat);
        $catId = $cat['parent_id'];
    }
    return $path;
}

function truncateStr($str, $len = 50) {
    if (mb_strlen($str, 'UTF-8') <= $len) return $str;
    return mb_substr($str, 0, $len, 'UTF-8') . '...';
}

function formatDate($date, $format = 'Y-m-d') {
    if (is_numeric($date)) {
        return date($format, $date);
    }
    return date($format, strtotime($date));
}

function uploadFile($field, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'], $maxSize = 5242880) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] != UPLOAD_ERR_OK) {
        return ['success' => false, 'msg' => '文件上传失败'];
    }
    $file = $_FILES[$field];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        return ['success' => false, 'msg' => '不支持的文件类型'];
    }
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'msg' => '文件过大'];
    }
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    $filename = date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $subdir = date('Ym');
    $targetDir = UPLOAD_DIR . '/' . $subdir;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $filepath = $targetDir . '/' . $filename;
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => $subdir . '/' . $filename];
    }
    return ['success' => false, 'msg' => '文件保存失败'];
}

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function paginate($total, $page, $perPage, $url) {
    $totalPages = ceil($total / $perPage);
    if ($totalPages <= 1) return '';
    $html = '<div class="pagination">';
    if ($page > 1) {
        $html .= '<a href="' . $url . '&page=' . ($page - 1) . '">上一页</a>';
    }
    for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++) {
        if ($i == $page) {
            $html .= '<span class="current">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $url . '&page=' . $i . '">' . $i . '</a>';
        }
    }
    if ($page < $totalPages) {
        $html .= '<a href="' . $url . '&page=' . ($page + 1) . '">下一页</a>';
    }
    $html .= '</div>';
    return $html;
}
