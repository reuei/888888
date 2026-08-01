<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/Auth.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if (!Auth::isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => '未登录']);
    exit;
}

switch ($action) {
    case 'toggle_status':
        $table = $_POST['table'] ?? $_GET['table'] ?? '';
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        $status = (int)($_POST['status'] ?? $_GET['status'] ?? 0);

        $allowedTables = ['slides', 'services', 'cases', 'news'];
        if (!in_array($table, $allowedTables)) {
            echo json_encode(['success' => false, 'message' => '无效的表名']);
            exit;
        }
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => '无效的ID']);
            exit;
        }

        $result = DB::update($table, $id, ['status' => $status]);
        if ($result) {
            echo json_encode(['success' => true, 'message' => '状态已更新']);
        } else {
            echo json_encode(['success' => false, 'message' => '更新失败']);
        }
        break;

    case 'delete':
        $table = $_POST['table'] ?? $_GET['table'] ?? '';
        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);

        $allowedTables = ['slides', 'services', 'cases', 'news'];
        if (!in_array($table, $allowedTables)) {
            echo json_encode(['success' => false, 'message' => '无效的表名']);
            exit;
        }
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => '无效的ID']);
            exit;
        }

        $result = DB::delete($table, $id);
        if ($result) {
            echo json_encode(['success' => true, 'message' => '已删除']);
        } else {
            echo json_encode(['success' => false, 'message' => '删除失败']);
        }
        break;

    case 'save_sort':
        $table = $_POST['table'] ?? $_GET['table'] ?? '';
        $items = $_POST['items'] ?? $_GET['items'] ?? '';

        $allowedTables = ['slides'];
        if (!in_array($table, $allowedTables)) {
            echo json_encode(['success' => false, 'message' => '无效的表名']);
            exit;
        }

        $items = json_decode($items, true);
        if (!is_array($items)) {
            echo json_encode(['success' => false, 'message' => '数据格式错误']);
            exit;
        }

        foreach ($items as $item) {
            $id = (int)($item['id'] ?? 0);
            $sort = (int)($item['sort'] ?? 0);
            if ($id > 0) {
                DB::update($table, $id, ['sort' => $sort]);
            }
        }
        echo json_encode(['success' => true, 'message' => '排序已保存']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => '无效的操作']);
        break;
}

exit;