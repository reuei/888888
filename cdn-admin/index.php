<?php
/**
 * CDN 防护加速平台 - 虚拟主机根目录入口
 *
 * 适用于无法将文档根目录设置为 public/ 的虚拟主机。
 * 所有请求会转发到 public/index.php，由 ThinkPHP 接管。
 */

$publicIndex = __DIR__ . '/public/index.php';

if (!file_exists($publicIndex)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    exit("public/index.php 不存在，请检查项目文件是否完整。\n");
}

// 让 ThinkPHP 使用 public/index.php 作为实际入口文件，但保留原始 SCRIPT_NAME/PHP_SELF，
// 以便在子目录部署时能够正确解析路由（如 /cdn-admin/install）。
$_SERVER['SCRIPT_FILENAME'] = $publicIndex;
if (!isset($_SERVER['SCRIPT_NAME']) || $_SERVER['SCRIPT_NAME'] === '') {
    $_SERVER['SCRIPT_NAME'] = ($_SERVER['PHP_SELF'] ?? '') !== '' ? $_SERVER['PHP_SELF'] : '/index.php';
}

require $publicIndex;
