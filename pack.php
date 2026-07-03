<?php
/**
 * 打包脚本：将主站与授权站分别打包为加密 ZIP
 * - PHP 业务逻辑文件（控制器/common.php/functions.php）使用 gzdeflate+base64+eval 加密
 * - 配置、视图、安装脚本、静态资源、vendor 保持明文，确保可部署可定制
 *
 * 注意：本文件注释与字符串中避免直接出现 PHP 开始/结束标记字面量，
 * 以免词法分析器误判（注释中的结束标记会退出 PHP 模式）。
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
set_time_limit(0);

// PHP 开始/结束标记常量，避免在注释中直接书写字面量
define('PHP_OPEN', '<' . '?php');
define('PHP_CLOSE', '?' . '>');

/* ---------- 加密单个 PHP 文件内容 ---------- */
function encryptPhpSource(string $source): string
{
    // 去除起始开始标记与可能的结束标记
    $code = preg_replace('#^\s*<\?php\s*#i', '', $source);
    $code = preg_replace('#\s*\?>\s*$#', '', $code);
    $payload = base64_encode(gzdeflate($code, 9));
    // 生成自解码加载器（不在注释中出现标记字面量）
    $loader = PHP_OPEN . " eval(gzinflate(base64_decode('" . $payload . "'))); " . PHP_CLOSE . "\n";
    return $loader;
}

/* ---------- 判断是否为纯 PHP 文件且无顶层 return（可安全加密） ---------- */
function isSafeToEncrypt(string $source): bool
{
    // 必须以 PHP 开始标记开头
    if (!preg_match('#^\s*<\?php#i', $source)) return false;
    // 含 HTML 混排（视图/安装页/带 DOCTYPE 或 style/script 标签）不加密
    if (strpos($source, '<!DOCTYPE') !== false) return false;
    if (stripos($source, '<html') !== false) return false;
    if (stripos($source, '<style') !== false) return false;
    if (stripos($source, '<script') !== false) return false;
    // 含 PHP 关闭再开启的混排标记（视图风格）不加密
    if (preg_match('#\?>\s*<#', $source)) return false;
    // 使用 tokenizer 检测顶层 return（config/route/middleware/event 等不加密）
    $tokens = @token_get_all($source);
    if (is_array($tokens)) {
        $depth = 0;
        foreach ($tokens as $tok) {
            if (is_array($tok)) {
                if (defined('T_RETURN') && $tok[0] === T_RETURN && $depth === 0) {
                    return false;
                }
            } else {
                if ($tok === '{') $depth++;
                elseif ($tok === '}') $depth--;
            }
        }
    }
    return true;
}

/* ---------- 加密并写入 ZIP ---------- */
function addEncryptedFile(ZipArchive $zip, string $absFile, string $zipName): void
{
    $src = file_get_contents($absFile);
    if ($src === false) return;
    if (isSafeToEncrypt($src)) {
        $zip->addFromString($zipName, encryptPhpSource($src));
    } else {
        $zip->addFile($absFile, $zipName);
    }
}

/* ---------- 判断相对路径是否被排除 ---------- */
function isExcluded(string $rel, array $excludePaths): bool
{
    foreach ($excludePaths as $ex) {
        if (strpos($rel, $ex) === 0) return true;
    }
    return false;
}

/* ---------- 递归打包目录 ---------- */
function packSite(string $root, string $zipFile, array $encryptDirs, array $copyDirs, array $extraFiles, array $excludePaths): void
{
    @unlink($zipFile);
    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        die("无法创建 {$zipFile}\n");
    }

    $stats = ['encrypted' => 0, 'plain' => 0];

    // 1) 加密目录中的 PHP 文件
    foreach ($encryptDirs as $dir) {
        $absDir = $root . DIRECTORY_SEPARATOR . $dir;
        if (!is_dir($absDir)) continue;
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($absDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($it as $f) {
            if (!$f->isFile()) continue;
            $rel = str_replace($root . DIRECTORY_SEPARATOR, '', $f->getPathname());
            $rel = str_replace('\\', '/', $rel);
            if (isExcluded($rel, $excludePaths)) continue;
            if (strtolower($f->getExtension()) === 'php') {
                $src = file_get_contents($f->getPathname());
                if (isSafeToEncrypt($src)) {
                    $zip->addFromString($rel, encryptPhpSource($src));
                    $stats['encrypted']++;
                } else {
                    $zip->addFile($f->getPathname(), $rel);
                    $stats['plain']++;
                }
            } else {
                $zip->addFile($f->getPathname(), $rel);
                $stats['plain']++;
            }
        }
    }

    // 2) 明文复制目录（config/view/public/install/route/lang/vendor 等）
    foreach ($copyDirs as $dir) {
        $absDir = $root . DIRECTORY_SEPARATOR . $dir;
        if (!is_dir($absDir)) continue;
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($absDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($it as $f) {
            if (!$f->isFile()) continue;
            $rel = str_replace($root . DIRECTORY_SEPARATOR, '', $f->getPathname());
            $rel = str_replace('\\', '/', $rel);
            if (isExcluded($rel, $excludePaths)) continue;
            // 排除 vendor 中的 .git 目录
            if (strpos($rel, 'vendor/') === 0 && strpos($rel, '/.git/') !== false) continue;
            $zip->addFile($f->getPathname(), $rel);
            $stats['plain']++;
        }
    }

    // 3) 顶层文件
    foreach ($extraFiles as $file) {
        $abs = $root . DIRECTORY_SEPARATOR . $file;
        if (is_file($abs)) {
            $zip->addFile($abs, $file);
            $stats['plain']++;
        }
    }

    $zip->close();
    $size = filesize($zipFile);
    echo sprintf("OK  %-32s  %.2f MB  (加密 %d / 明文 %d)\n", basename($zipFile), $size / 1048576, $stats['encrypted'], $stats['plain']);
}

/* ============================================================
 * 主站打包
 * ============================================================ */
$mainRoot = '/workspace';
$mainZip  = '/workspace/main_site_encrypted.zip';

$mainEncryptDirs = ['app', 'main_legacy'];
$mainCopyDirs    = ['config', 'install', 'lang', 'public', 'route', 'view', 'vendor'];
$mainExtraFiles  = ['composer.json', 'composer.lock', 'think', 'nginx.conf', '.htaccess', '.example.env', '.gitignore', 'LICENSE.txt'];
// 排除 main_legacy 中除 functions.php 外的所有文件
$mainExcludes = [
    'main_legacy/controller/',
    'main_legacy/view/',
    'main_legacy/lang/',
    'main_legacy/Controller.php',
    'main_legacy/Db.php',
    'main_legacy/Model.php',
    'main_legacy/Route.php',
    'main_legacy/bootstrap.php',
    'main_legacy/config.php',
    'runtime/',
];

echo "开始打包主站...\n";
packSite($mainRoot, $mainZip, $mainEncryptDirs, $mainCopyDirs, $mainExtraFiles, $mainExcludes);

/* ============================================================
 * 授权站打包
 * ============================================================ */
$authRoot = '/workspace/authorize';
$authZip  = '/workspace/authorize_site_encrypted.zip';

$authEncryptDirs = ['app'];
$authCopyDirs    = ['config', 'install', 'public', 'route', 'view', 'vendor'];
$authExtraFiles  = ['composer.json', 'composer.lock', 'think', 'nginx.conf', '.example.env', '.gitignore', 'LICENSE.txt'];
$authExcludes    = ['runtime/'];

echo "开始打包授权站...\n";
packSite($authRoot, $authZip, $authEncryptDirs, $authCopyDirs, $authExtraFiles, $authExcludes);

echo "\n全部打包完成。\n";
echo "主站：  /workspace/main_site_encrypted.zip\n";
echo "授权站：/workspace/authorize_site_encrypted.zip\n";
