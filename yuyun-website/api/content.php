<?php
/**
 * 语云科技 - 内容管理API
 * 支持获取和更新各类内容数据（轮播图、合作伙伴、产品、员工、证书、链接、配置）
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/core/Functions.php';

// 设置响应头
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// 允许的内容类型
$allowed_types = ['banners', 'partners', 'products', 'staff', 'certificates', 'links', 'config'];

/**
 * 获取内容数据
 */
function handleGet() {
    global $allowed_types;
    $type = $_GET['type'] ?? '';

    if (empty($type)) {
        error('缺少内容类型参数');
    }

    if (!in_array($type, $allowed_types)) {
        error('无效的内容类型');
    }

    // 特殊处理config
    if ($type === 'config') {
        $data = get_config();
        // 移除敏感信息
        unset($data['admin_password'], $data['smtp_password']);
        success($data);
        return;
    }

    $id = intval($_GET['id'] ?? 0);

    if ($id > 0) {
        // 获取单条数据
        $items = get_content($type);
        $item = null;
        foreach ($items as $i) {
            if ((int)$i['id'] === $id) {
                $item = $i;
                break;
            }
        }
        if ($item) {
            success($item);
        } else {
            error('内容不存在');
        }
    } else {
        // 获取列表
        $data = get_content($type);
        success($data);
    }
}

/**
 * 创建或更新内容
 */
function handlePost() {
    global $allowed_types;

    // 权限验证：管理员或CSRF验证
    if (!is_admin()) {
        if (!verify_csrf($_POST['csrf_token'] ?? '')) {
            error('权限不足或CSRF验证失败', 403);
        }
    }

    $type = $_POST['type'] ?? $_GET['type'] ?? '';
    $action = $_POST['action'] ?? '';

    if (empty($type)) {
        error('缺少内容类型参数');
    }

    if (!in_array($type, $allowed_types)) {
        error('无效的内容类型');
    }

    switch ($action) {
        case 'create':
            createItem($type);
            break;

        case 'update':
            updateItem($type);
            break;

        case 'delete':
            deleteItem($type);
            break;

        case 'sort':
            sortItems($type);
            break;

        default:
            error('未知的操作类型');
    }
}

/**
 * 新增内容项
 */
function createItem($type) {
    $data = json_decode($_POST['data'] ?? '[]', true);

    if (empty($data) && !is_array($data)) {
        error('无效的数据格式');
    }

    $items = get_content($type);

    // 生成ID
    $max_id = 0;
    foreach ($items as $item) {
        if (($item['id'] ?? 0) > $max_id) {
            $max_id = $item['id'];
        }
    }

    $data['id'] = $max_id + 1;
    $data['created_at'] = date('Y-m-d H:i:s');
    $data['updated_at'] = date('Y-m-d H:i:s');

    $items[] = $data;

    if (save_content($type, $items)) {
        log_message("管理员创建了{$type}内容，ID: {$data['id']}");
        success($data, '创建成功');
    } else {
        error('保存失败');
    }
}

/**
 * 更新内容项
 */
function updateItem($type) {
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        error('缺少ID参数');
    }

    $data = json_decode($_POST['data'] ?? '{}', true);

    if (empty($data)) {
        error('无效的数据格式');
    }

    $items = get_content($type);
    $found = false;

    foreach ($items as &$item) {
        if ((int)$item['id'] === $id) {
            $data['id'] = $id;
            $data['created_at'] = $item['created_at'] ?? date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $item = array_merge($item, $data);
            $found = true;
            break;
        }
    }
    unset($item);

    if (!$found) {
        error('内容不存在');
    }

    if (save_content($type, $items)) {
        log_message("管理员更新了{$type}内容，ID: {$id}");
        success($data, '更新成功');
    } else {
        error('保存失败');
    }
}

/**
 * 删除内容项
 */
function deleteItem($type) {
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        error('缺少ID参数');
    }

    $items = get_content($type);
    $found = false;
    $newItems = [];

    foreach ($items as $item) {
        if ((int)$item['id'] !== $id) {
            $newItems[] = $item;
        } else {
            $found = true;
        }
    }

    if (!$found) {
        error('内容不存在');
    }

    if (save_content($type, $newItems)) {
        log_message("管理员删除了{$type}内容，ID: {$id}");
        success(null, '删除成功');
    } else {
        error('删除失败');
    }
}

/**
 * 排序内容项
 */
function sortItems($type) {
    $order = json_decode($_POST['order'] ?? '[]', true);

    if (!is_array($order)) {
        error('无效的排序数据');
    }

    $items = get_content($type);
    $orderedItems = [];

    foreach ($order as $id) {
        foreach ($items as $item) {
            if ((int)$item['id'] === (int)$id) {
                $orderedItems[] = $item;
                break;
            }
        }
    }

    if (save_content($type, $orderedItems)) {
        success(null, '排序已保存');
    } else {
        error('排序保存失败');
    }
}

// 路由分发
switch ($method) {
    case 'GET':
        handleGet();
        break;

    case 'POST':
        handlePost();
        break;

    default:
        error('不支持的请求方法');
}
