<?php
/**
 * 语云科技 - 文件上传API
 * 使用 Uploader 类处理图片上传
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/core/Functions.php';
require_once YUYUN_ROOT . '/core/Uploader.php';

// 设置响应头
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 仅接受POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error('仅支持POST上传', 405);
}

// 检查是否有文件上传
if (!isset($_FILES['file']) && !isset($_FILES['image'])) {
    error('没有选择要上传的文件');
}

$fileField = isset($_FILES['file']) ? 'file' : 'image';
$subDir = $_POST['subdir'] ?? 'images';

// 根据子目录设置允许的类型
$uploader = new Uploader($subDir);

// 可选参数配置
$maxSize = intval($_POST['max_size'] ?? 0);
if ($maxSize > 0) {
    $uploader->setMaxSize($maxSize * 1024 * 1024); // MB转字节
}

// 处理上传
$result = $uploader->upload($_FILES[$fileField]);

if ($result['success']) {
    log_message("文件上传成功: {$result['filename']} ({$result['path']})");
    success([
        'success' => true,
        'path' => $result['path'],
        'url' => $result['url'],
        'filename' => $result['filename'],
        'original_name' => $result['original_name'],
        'size' => $result['size']
    ], '上传成功');
} else {
    error($result['message']);
}
