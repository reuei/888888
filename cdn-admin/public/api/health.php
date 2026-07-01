<?php
/**
 * CDN 防护加速平台 - API 健康检查
 *
 * 前端通过访问该文件判断当前是否运行在 PHP 主机环境。
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

echo json_encode(['ok' => true, 'runtime' => 'php']);
