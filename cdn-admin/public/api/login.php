<?php
/**
 * CDN 防护加速平台 - 登录接口
 *
 * S 端验证安装时设置的管理员账号。
 * B 端验证演示商户账号 merchant / 123456。
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$account = trim($input['account'] ?? '');
$password = $input['password'] ?? '';
$role = $input['role'] ?? '';

if (!$account || !$password || !in_array($role, ['s', 'b'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing account, password or role']);
    exit;
}

$configFile = dirname(__DIR__) . '/api/config.php';
if (!file_exists($configFile)) {
    http_response_code(403);
    echo json_encode(['error' => 'System not installed']);
    exit;
}

$config = require $configFile;

if ($role === 's') {
    $admin = $config['admin'] ?? [];
    if (
        isset($admin['username'], $admin['password']) &&
        $admin['username'] === $account &&
        password_verify($password, $admin['password'])
    ) {
        echo json_encode(['success' => true, 'role' => 's']);
        exit;
    }
} elseif ($role === 'b') {
    // B 端演示账号：merchant / 123456
    if ($account === 'merchant' && $password === '123456') {
        echo json_encode(['success' => true, 'role' => 'b']);
        exit;
    }
}

http_response_code(401);
echo json_encode(['error' => '账号或密码错误']);
