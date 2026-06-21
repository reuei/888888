<?php
/**
 * 前台留言提交接口
 */

define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/config.php';

if (!defined('INSTALLED') || !INSTALLED) {
    json(['success' => false, 'message' => '网站未安装']);
}

require_once YUYUN_ROOT . '/includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

function json($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json(['success' => false, 'message' => '非法请求']);
}

$csrf = yy_trim($_POST['csrf_token'] ?? '');
if (!verifyCsrf($csrf)) {
    json(['success' => false, 'message' => '安全验证失败，请刷新页面重试']);
}

$name = yy_trim($_POST['name'] ?? '');
$phone = yy_trim($_POST['phone'] ?? '');
$email = yy_trim($_POST['email'] ?? '');
$content = yy_trim($_POST['content'] ?? '');

if (empty($name) || empty($phone) || empty($content)) {
    json(['success' => false, 'message' => '请填写姓名、电话和留言内容']);
}

$data = [
    'name' => $name,
    'phone' => $phone,
    'email' => $email,
    'content' => $content,
    'status' => 0,
];

try {
    dbInsert('messages', $data);
    json(['success' => true, 'message' => '提交成功，我们将尽快与您联系']);
} catch (Exception $e) {
    json(['success' => false, 'message' => '提交失败：' . $e->getMessage()]);
}
