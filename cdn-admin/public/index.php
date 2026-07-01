<?php
/**
 * CDN 防护加速平台 - PHP 入口
 *
 * 该文件用于在 Apache + PHP 虚拟主机上托管 React SPA。
 * 构建后 dist/ 目录会同时包含 index.html 与本文件，
 * .htaccess 会把所有前端路由重定向到本文件，再由 HashRouter 接管。
 */

$htmlFile = __DIR__ . '/index.html';

if (!file_exists($htmlFile)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    exit("index.html 不存在。请先执行 npm run build 生成构建产物。\n");
}

$html = file_get_contents($htmlFile);

if ($html === false) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    exit("无法读取 index.html。\n");
}

// 注入运行环境标识，前端可据此判断是否在 PHP 主机运行
$envScript = '<script>window.__CDN_ADMIN_RUNTIME__="php";</script>';
$html = str_replace('<head>', '<head>' . $envScript, $html);

echo $html;
