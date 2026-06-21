<?php
/**
 * 公共函数库
 */

if (!defined('YUYUN_ROOT')) {
    define('YUYUN_ROOT', dirname(__DIR__));
}

require_once YUYUN_ROOT . '/includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 安全过滤
function yy_e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function yy_trim($str) {
    return trim($str ?? '');
}

function yy_truncate($str, $length = 100, $suffix = '...') {
    $str = $str ?? '';
    if (function_exists('mb_strlen')) {
        if (mb_strlen($str, 'UTF-8') <= $length) return $str;
        return mb_substr($str, 0, $length, 'UTF-8') . $suffix;
    }
    if (strlen($str) <= $length) return $str;
    return substr($str, 0, $length) . $suffix;
}

// 站点配置缓存
function getSetting($key, $default = '') {
    static $settings = null;
    if ($settings === null) {
        $db = YuyunDB::getInstance();
        if ($db->getType() === 'json') {
            $rows = $db->jsonAll('settings', 'id', 'ASC');
            foreach ($rows as $row) {
                $settings[$row['s_key']] = $row['s_value'];
            }
        } else {
            $rows = $db->query("SELECT s_key, s_value FROM settings");
            foreach ($rows as $row) {
                $settings[$row['s_key']] = $row['s_value'];
            }
        }
    }
    return isset($settings[$key]) ? $settings[$key] : $default;
}

function setSetting($key, $value) {
    $db = YuyunDB::getInstance();
    if ($db->getType() === 'json') {
        $rows = $db->jsonAll('settings', 'id', 'ASC');
        $found = false;
        foreach ($rows as &$row) {
            if ($row['s_key'] === $key) {
                $row['s_value'] = $value;
                $row['updated_at'] = date('Y-m-d H:i:s');
                $found = true;
                break;
            }
        }
        if (!$found) {
            $rows[] = [
                'id' => 0,
                's_key' => $key,
                's_value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }
        file_put_contents(YUYUN_ROOT . '/data/json/settings.json', json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    } else {
        $existing = $db->queryOne("SELECT id FROM settings WHERE s_key = ?", [$key]);
        if ($existing) {
            $db->execute("UPDATE settings SET s_value = ?, updated_at = CURRENT_TIMESTAMP WHERE s_key = ?", [$value, $key]);
        } else {
            $db->execute("INSERT INTO settings (s_key, s_value) VALUES (?, ?)", [$key, $value]);
        }
    }
}

// 获取多个配置项
function getSettings($keys) {
    $result = [];
    foreach ($keys as $k) {
        $result[$k] = getSetting($k);
    }
    return $result;
}

// 通用 CRUD 辅助
function dbAll($table, $orderBy = 'sort_order', $direction = 'ASC') {
    $db = YuyunDB::getInstance();
    if ($db->getType() === 'json') {
        return $db->jsonAll($table, $orderBy, $direction);
    }
    return $db->query("SELECT * FROM $table ORDER BY $orderBy $direction");
}

function dbActive($table, $orderBy = 'sort_order', $direction = 'ASC') {
    $db = YuyunDB::getInstance();
    if ($db->getType() === 'json') {
        return $db->jsonWhere($table, ['is_active' => 1], $orderBy, $direction);
    }
    return $db->query("SELECT * FROM $table WHERE is_active = 1 ORDER BY $orderBy $direction");
}

function dbFind($table, $id) {
    $db = YuyunDB::getInstance();
    if ($db->getType() === 'json') {
        return $db->jsonFind($table, $id);
    }
    return $db->queryOne("SELECT * FROM $table WHERE id = ?", [$id]);
}

function dbInsert($table, $data) {
    $db = YuyunDB::getInstance();
    if ($db->getType() === 'json') {
        return $db->jsonInsert($table, $data);
    }
    $keys = array_keys($data);
    $placeholders = array_fill(0, count($keys), '?');
    $sql = "INSERT INTO $table (" . implode(',', $keys) . ") VALUES (" . implode(',', $placeholders) . ")";
    $db->execute($sql, array_values($data));
    return $db->lastInsertId();
}

function dbUpdate($table, $id, $data) {
    $db = YuyunDB::getInstance();
    if ($db->getType() === 'json') {
        return $db->jsonUpdate($table, $id, $data);
    }
    $sets = [];
    foreach ($data as $k => $v) {
        $sets[] = "$k = ?";
    }
    $sql = "UPDATE $table SET " . implode(',', $sets) . " WHERE id = ?";
    $values = array_values($data);
    $values[] = $id;
    return $db->execute($sql, $values);
}

function dbDelete($table, $id) {
    $db = YuyunDB::getInstance();
    if ($db->getType() === 'json') {
        return $db->jsonDelete($table, $id);
    }
    return $db->execute("DELETE FROM $table WHERE id = ?", [$id]);
}

// 文件上传
function yyUpload($file, $subdir) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['error' => '未选择文件'];
    }
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    $exts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
    $type = $file['type'];
    $name = $file['name'];
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    if (!in_array($type, $allowed) || !in_array($ext, $exts)) {
        return ['error' => '仅允许上传 jpg/png/gif/webp/svg 图片'];
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['error' => '文件大小不能超过 5MB'];
    }

    $dir = YUYUN_ROOT . '/uploads/' . $subdir;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $newName = date('YmdHis') . '_' . uniqid() . '.' . $ext;
    $target = $dir . '/' . $newName;
    if (move_uploaded_file($file['tmp_name'], $target)) {
        return ['path' => 'uploads/' . $subdir . '/' . $newName];
    }
    return ['error' => '上传失败，请检查目录权限'];
}

// CSRF Token
function csrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// 提示消息
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// 当前模板路径
function templatePath($file = '') {
    $theme = getSetting('current_template', 'default');
    $base = YUYUN_ROOT . '/templates/' . $theme;
    return $file ? $base . '/' . $file : $base;
}

function templateUrl($file = '') {
    $theme = getSetting('current_template', 'default');
    return './templates/' . $theme . ($file ? '/' . $file : '');
}

// 页面标题
function pageTitle($pageName = '') {
    $siteTitle = getSetting('site_title', '语云科技');
    if ($pageName) {
        return $pageName . ' - ' . $siteTitle;
    }
    return $siteTitle;
}

// 操作日志（JSON 模式下写入 logs.json，数据库模式下写入 logs 表）
function addLog($action, $detail = '') {
    if (!INSTALLED) return false;
    $db = YuyunDB::getInstance();
    $data = [
        'action' => $action,
        'detail' => $detail,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        'created_at' => date('Y-m-d H:i:s'),
    ];
    try {
        if ($db->getType() === 'json') {
            return $db->jsonInsert('logs', $data);
        }
        return dbInsert('logs', $data);
    } catch (Exception $e) {
        return false;
    }
}

function getLogs($limit = 50) {
    $db = YuyunDB::getInstance();
    if ($db->getType() === 'json') {
        $rows = $db->jsonAll('logs', 'id', 'DESC');
        return array_slice($rows, 0, $limit);
    }
    return $db->query("SELECT * FROM logs ORDER BY id DESC LIMIT ?", [$limit]);
}

// 地图 embed URL
function mapEmbedUrl($type, $key, $lat, $lng) {
    switch ($type) {
        case 'baidu':
            return "https://map.baidu.com/search/" . urlencode(getSetting('company_address', '北京市'));
        case 'gaode':
            return "https://ditu.amap.com/search?query=" . urlencode(getSetting('company_address', '北京市'));
        case 'tencent':
            return "https://map.qq.com/?type=poi&keyword=" . urlencode(getSetting('company_address', '北京市'));
        default:
            return "https://map.baidu.com/search/" . urlencode(getSetting('company_address', '北京市'));
    }
}
