<?php
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function redirect($url) { header('Location: ' . $url); exit; }

function isLoggedIn() { return isset($_SESSION['user_id']); }
function currentUser() {
    if (!isLoggedIn()) return null;
    return DB::fetchOne("SELECT * FROM users WHERE id=?", [$_SESSION['user_id']]);
}
function isAdmin() { $u = currentUser(); return $u && in_array($u['role'], ['admin', 'super_admin']); }
function isSuperAdmin() { $u = currentUser(); return $u && $u['role'] === 'super_admin'; }

function getSetting($key, $default = '') {
    try { $r = DB::fetchOne("SELECT value FROM settings WHERE key=?", [$key]); return $r ? $r['value'] : $default; }
    catch (Exception $e) { return $default; }
}
function siteName() { return getSetting('site_name', SITE_NAME); }

function getCategories() {
    try { return DB::fetchAll("SELECT * FROM categories WHERE parent_id=0 AND show_in_menu=1 ORDER BY sort_order ASC"); }
    catch (Exception $e) { return []; }
}
function getChildCategories($parentId) {
    try { return DB::fetchAll("SELECT * FROM categories WHERE parent_id=? AND show_in_menu=1 ORDER BY sort_order ASC", [$parentId]); }
    catch (Exception $e) { return []; }
}
function getCategoryBySlug($slug) {
    try { return DB::fetchOne("SELECT * FROM categories WHERE slug=?", [$slug]); }
    catch (Exception $e) { return null; }
}

function formatDate($d, $fmt = 'Y-m-d') {
    if (!$d) return '';
    return date($fmt, strtotime($d));
}
function truncateStr($s, $len = 60) {
    $s = strip_tags($s);
    return mb_strlen($s) > $len ? mb_substr($s, 0, $len) . '...' : $s;
}
function paginate($total, $page, $perPage, $url) {
    $totalPages = ceil($total / $perPage);
    if ($totalPages <= 1) return '';
    $html = '<div class="pagination">';
    $url .= (strpos($url, '?') === false ? '?' : '&');
    if ($page > 1) $html .= '<a href="' . $url . 'page=' . ($page - 1) . '">&laquo;</a>';
    for ($i = 1; $i <= $totalPages; $i++) {
        $html .= $i == $page ? '<span class="current">' . $i . '</span>' : '<a href="' . $url . 'page=' . $i . '">' . $i . '</a>';
    }
    if ($page < $totalPages) $html .= '<a href="' . $url . 'page=' . ($page + 1) . '">&raquo;</a>';
    $html .= '</div>';
    return $html;
}

function uploadFile($field) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => '上传失败'];
    }
    $file = $_FILES[$field];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    if (!in_array($ext, $allowed)) return ['success' => false, 'error' => '不支持的文件类型'];
    if ($file['size'] > 5 * 1024 * 1024) return ['success' => false, 'error' => '文件不能超过5MB'];
    $dir = UPLOAD_DIR;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $name = date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = $dir . '/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) return ['success' => false, 'error' => '保存失败'];
    return ['success' => true, 'path' => $name];
}

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}
function checkCsrf() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return true;
    $token = $_POST['csrf_token'] ?? '';
    return $token && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}