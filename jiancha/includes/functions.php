<?php
function e($s) { return htmlspecialchars((string) ($s ?? ''), ENT_QUOTES, 'UTF-8'); }
function redirect($u) { header('Location: ' . $u); exit; }
function isLoggedIn() { return !empty($_SESSION['user_id']); }
function currentUser() { return isLoggedIn() ? DB::fetchOne("SELECT * FROM users WHERE id=?", [$_SESSION['user_id']]) : null; }
function isAdmin() {
    $u = currentUser();
    return $u && in_array($u['role'] ?? '', ['admin', 'super_admin']);
}
function isSuperAdmin() {
    $u = currentUser();
    return ($u['role'] ?? '') === 'super_admin';
}
function siteName() { return getSetting('site_name', '人民检察'); }
function getSetting($k, $d = '') {
    try { $r = DB::fetchOne("SELECT value FROM settings WHERE key=?", [$k]); return $r ? $r['value'] : $d; }
    catch (Exception $e) { return $d; }
}
function getCategories($m = true) {
    try {
        $sql = "SELECT * FROM categories WHERE 1=1";
        if ($m) $sql .= " AND show_in_menu=1";
        $sql .= " ORDER BY sort_order ASC";
        return DB::fetchAll($sql);
    } catch (Exception $e) { return []; }
}
function getCategoryBySlug($s) {
    try { return DB::fetchOne("SELECT * FROM categories WHERE slug=?", [$s]); }
    catch (Exception $e) { return null; }
}
function formatDate($d, $f = 'Y-m-d') { return $d ? date($f, strtotime($d)) : ''; }
function truncateStr($s, $n = 100) { $s = strip_tags((string) $s); return mb_strlen($s) > $n ? mb_substr($s, 0, $n) . '...' : $s; }
function paginate($total, $page, $perPage, $url) {
    if ($total <= $perPage) return '';
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    $sep = strpos($url, '?') !== false ? '&' : '?';
    $h = '<div class="pagination">';
    if ($page > 1) $h .= '<a href="' . $url . $sep . 'page=' . ($page - 1) . '" class="prev">上一页</a>';
    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);
    if ($start > 1) $h .= '<a href="' . $url . $sep . 'page=1">1</a>';
    if ($start > 2) $h .= '<span class="dots">...</span>';
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $page) $h .= '<span class="current">' . $i . '</span>';
        else $h .= '<a href="' . $url . $sep . 'page=' . $i . '">' . $i . '</a>';
    }
    if ($end < $totalPages - 1) $h .= '<span class="dots">...</span>';
    if ($end < $totalPages) $h .= '<a href="' . $url . $sep . 'page=' . $totalPages . '">' . $totalPages . '</a>';
    if ($page < $totalPages) $h .= '<a href="' . $url . $sep . 'page=' . ($page + 1) . '" class="next">下一页</a>';
    $h .= '</div>';
    return $h;
}
function uploadFile($f) {
    if (empty($_FILES[$f]) || $_FILES[$f]['error'] !== UPLOAD_ERR_OK) return ['success' => false, 'error' => '未选择文件'];
    $ext = strtolower(pathinfo($_FILES[$f]['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (!in_array($ext, $allowed)) return ['success' => false, 'error' => '仅允许图片文件'];
    $fn = uniqid() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $p = UPLOAD_DIR . $fn;
    if (!move_uploaded_file($_FILES[$f]['tmp_name'], $p)) return ['success' => false, 'error' => '上传失败'];
    return ['success' => true, 'path' => $fn];
}
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrfField() { return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">'; }
function checkCsrf() {
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}
