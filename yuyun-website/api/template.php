<?php
/**
 * 语云科技 - 模板管理API
 * 支持获取模板列表和切换模板
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/core/Functions.php';

// 设置响应头
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];

/**
 * 获取可用模板列表
 */
function getTemplates() {
    $templatesDir = TEMPLATES_PATH;
    $currentTemplate = get_config('template') ?: 'default';

    $templates = [];

    if (is_dir($templatesDir)) {
        $dirs = scandir($templatesDir);

        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') continue;

            $templatePath = $templatesDir . '/' . $dir;
            if (!is_dir($templatePath)) continue;

            // 读取模板配置文件
            $configFile = $templatePath . '/template.json';
            $info = [
                'name' => $dir,
                'display_name' => ucfirst($dir),
                'description' => '',
                'author' => '',
                'version' => '1.0.0',
                'screenshot' => '',
                'is_active' => ($dir === $currentTemplate)
            ];

            if (file_exists($configFile)) {
                $config = json_decode(file_get_contents($configFile), true);
                if ($config) {
                    $info = array_merge($info, $config);
                    $info['is_active'] = ($dir === $currentTemplate);
                }
            }

            // 自动检测截图
            if (empty($info['screenshot'])) {
                $screenshotFiles = ['screenshot.png', 'preview.png', 'thumb.jpg', 'thumb.png'];
                foreach ($screenshotFiles as $sf) {
                    if (file_exists($templatePath . '/' . $sf)) {
                        $info['screenshot'] = '/templates/' . $dir . '/' . $sf;
                        break;
                    }
                }
            }

            $templates[] = $info;
        }
    }

    // 如果没有任何模板，返回默认模板信息
    if (empty($templates)) {
        $templates[] = [
            'name' => 'default',
            'display_name' => '默认模板',
            'description' => '语云科技默认企业官网模板',
            'author' => '语云科技',
            'version' => '1.0.0',
            'screenshot' => '',
            'is_active' => true
        ];
    }

    success($templates);
}

/**
 * 切换模板
 */
function switchTemplate() {
    require_admin();

    $templateName = trim($_POST['template_name'] ?? '');

    if (empty($templateName)) {
        error('请指定模板名称');
    }

    // 安全检查：只允许字母数字和下划线
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $templateName)) {
        error('模板名称包含非法字符');
    }

    // 检查模板是否存在
    $templatePath = TEMPLATES_PATH . $templateName;
    if (!is_dir($templatePath)) {
        error('指定的模板不存在');
    }

    // 更新配置
    set_config('template', $templateName);

    log_message("管理员切换模板为: {$templateName}");

    success([
        'template' => $templateName,
        'message' => '模板切换成功'
    ], '模板切换成功，页面即将刷新...');
}

/**
 * 上传自定义模板
 */
function uploadTemplate() {
    require_admin();

    if (!isset($_FILES['template_file'])) {
        error('请选择要上传的模板文件');
    }

    $file = $_FILES['template_file'];

    // 只允许zip文件
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'zip') {
        error('只支持ZIP格式的模板包');
    }

    // 大小限制20MB
    if ($file['size'] > 20971520) {
        error('模板包大小不能超过20MB');
    }

    $tempFile = $file['tmp_name'];
    $templateName = pathinfo($file['name'], PATHINFO_FILENAME);

    // 安全化名称
    $templateName = preg_replace('/[^a-zA-Z0-9_-]/', '', $templateName);
    if (empty($templateName)) {
        $templateName = 'custom_' . time();
    }

    $targetDir = TEMPLATES_PATH . $templateName;

    // 如果同名目录已存在，先删除
    if (is_dir($targetDir)) {
        deleteDirectory($targetDir);
    }

    mkdir($targetDir, 0755, true);

    // 解压ZIP
    $zip = new ZipArchive();
    if ($zip->open($tempFile) === true) {
        $zip->extractTo($targetDir);
        $zip->close();

        log_message("管理员上传了自定义模板: {$templateName}");

        success([
            'template_name' => $templateName,
            'path' => $targetDir
        ], '模板上传并解压成功');
    } else {
        // 解压失败，清理目录
        deleteDirectory($targetDir);
        error('ZIP文件解压失败，请确保文件格式正确');
    }
}

/**
 * 递归删除目录
 */
function deleteDirectory($dir) {
    if (!is_dir($dir)) return true;

    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $path = $dir . '/' . $item;
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }

    return rmdir($dir);
}

// 路由分发
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($method) {
    case 'GET':
        getTemplates();
        break;

    case 'POST':
        switch ($action) {
            case 'switch':
                switchTemplate();
                break;
            case 'upload':
                uploadTemplate();
                break;
            default:
                error('未知的操作类型');
        }
        break;

    default:
        error('不支持的请求方法');
}
