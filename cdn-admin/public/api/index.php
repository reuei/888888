<?php
/**
 * CDN 防护加速平台 - 通用 REST API 入口
 *
 * 该接口替代浏览器 localStorage，把所有 mock 数据持久化到服务器 JSON 文件，
 * 使项目能在无 Node.js 的 PHP 虚拟主机上完整运行。
 *
 * 约定：
 *   GET    /api/index.php?resource=articles          读取列表
 *   POST   /api/index.php?resource=articles          新增
 *   PUT    /api/index.php?resource=articles&id=A001  更新
 *   DELETE /api/index.php?resource=articles&id=A001  删除
 *
 * 首次请求时若 data/data.json 不存在，会自动复制 data/default.json 作为初始结构。
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// 未安装时拒绝 API 请求
$configFile = dirname(__DIR__) . '/api/config.php';
$lockFile = dirname(__DIR__) . '/api/install.lock';
if (!file_exists($configFile) || !file_exists($lockFile)) {
    http_response_code(403);
    echo json_encode(['error' => 'System not installed. Please run install.php first.']);
    exit;
}

$dataDir = __DIR__ . '/data';
$dataFile = $dataDir . '/data.json';
$defaultFile = $dataDir . '/default.json';

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

function loadData(string $file, string $defaultFile): array {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if ($content !== false) {
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
    }
    if (file_exists($defaultFile)) {
        $content = file_get_contents($defaultFile);
        if ($content !== false) {
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
    }
    return [];
}

function saveData(string $file, array $data): void {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$resourcePrefixes = [
    'articles' => 'A',
    'coupons' => 'CO',
    'skus' => 'S',
    'packages' => 'PKG',
    'merchants' => 'M',
    'users' => 'U',
    'orders' => 'O',
    'categories' => 'C',
    'adSlots' => 'AD',
    'complaints' => 'CP',
    'gateways' => 'GW',
    'nodes' => 'N',
    'products' => 'P',
    'inviteCodes' => 'I',
    'userGroups' => 'G',
    'userLevels' => 'L',
    'realnameRecords' => 'R',
    'roles' => 'R',
    'backupRecords' => 'B',
    'sites' => 'ST',
    'myPackages' => 'MP',
    'bOrders' => 'BO',
    'invoices' => 'INV',
    'whitelistRecords' => 'W',
    'financeRecords' => 'F',
    'settlementRecords' => 'SET',
    'commissionRecords' => 'CM',
    'operationLogs' => 'L',
    'apiDocs' => 'A',
    'notifications' => 'NT',
    'agents' => 'AG',
    'agentProducts' => 'AP',
    'pcTemplates' => 'TPC',
    'mobileTemplates' => 'TPM',
    'cardTemplates' => 'TC',
    'luckyNumbers' => 'LN',
];

$method = $_SERVER['REQUEST_METHOD'];
$resource = $_GET['resource'] ?? '';
$id = $_GET['id'] ?? null;

if (!$resource) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing resource parameter']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true) ?: [];

$data = loadData($dataFile, $defaultFile);

switch ($method) {
    case 'GET':
        echo json_encode($data[$resource] ?? []);
        break;

    case 'POST':
        $prefix = $resourcePrefixes[$resource] ?? 'ID';
        $items = $data[$resource] ?? [];
        $newId = $prefix . str_pad((string) (count($items) + 1), 3, '0', STR_PAD_LEFT);
        $newItem = array_merge($body, ['id' => $newId]);
        array_unshift($items, $newItem);
        $data[$resource] = $items;
        saveData($dataFile, $data);
        echo json_encode($newItem);
        break;

    case 'PUT':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id parameter']);
            exit;
        }
        $items = $data[$resource] ?? [];
        $updated = null;
        foreach ($items as &$item) {
            if (isset($item['id']) && $item['id'] === $id) {
                $item = array_merge($item, $body);
                $updated = $item;
                break;
            }
        }
        if ($updated === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Resource not found']);
            exit;
        }
        $data[$resource] = $items;
        saveData($dataFile, $data);
        echo json_encode($updated);
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing id parameter']);
            exit;
        }
        $items = $data[$resource] ?? [];
        $before = count($items);
        $items = array_values(array_filter($items, fn ($item) => !isset($item['id']) || $item['id'] !== $id));
        $data[$resource] = $items;
        saveData($dataFile, $data);
        echo json_encode(['success' => count($items) < $before]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
