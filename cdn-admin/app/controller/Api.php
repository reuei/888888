<?php
declare(strict_types=1);

namespace app\controller;

use app\service\DataService;
use think\Request;
use think\Response;

class Api
{
    protected DataService $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function index(Request $request, string $resource = ''): Response
    {
        if (!$this->dataService->isInstalled()) {
            return json(['error' => 'System not installed. Please run install first.'], 403);
        }

        $method = $request->method();
        $id = $request->param('id');
        $body = $this->getInputData($request);

        if (!$resource) {
            return json(['error' => 'Missing resource parameter'], 400);
        }

        $data = $this->dataService->loadData();

        switch ($method) {
            case 'GET':
                return json($data[$resource] ?? []);

            case 'POST':
                return $this->create($resource, $body, $data);

            case 'PUT':
                if (!$id) {
                    return json(['error' => 'Missing id parameter'], 400);
                }
                return $this->update($resource, $id, $body, $data);

            case 'DELETE':
                if (!$id) {
                    return json(['error' => 'Missing id parameter'], 400);
                }
                return $this->delete($resource, $id, $data);

            default:
                return json(['error' => 'Method not allowed'], 405);
        }
    }

    protected function getInputData(Request $request): array
    {
        $contentType = $request->header('Content-Type', '');
        $input = $request->getInput();

        if (stripos($contentType, 'application/json') !== false) {
            $decoded = json_decode($input, true);
            return is_array($decoded) ? $decoded : [];
        }

        return $request->post() ?: [];
    }

    protected function create(string $resource, array $body, array $data): Response
    {
        $prefixes = $this->dataService->resourcePrefixes();
        $prefix = $prefixes[$resource] ?? 'ID';
        $items = $data[$resource] ?? [];

        $max = 0;
        foreach ($items as $item) {
            if (isset($item['id'])) {
                $num = (int) preg_replace('/^' . preg_quote($prefix, '/') . '/', '', (string) $item['id']);
                $max = max($max, $num);
            }
        }
        $newId = $prefix . str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
        $newItem = array_merge($body, ['id' => $newId]);
        array_unshift($items, $newItem);
        $data[$resource] = $items;
        $this->dataService->saveData($data);
        return json($newItem);
    }

    protected function update(string $resource, string $id, array $body, array $data): Response
    {
        $items = $data[$resource] ?? [];
        $updated = null;
        foreach ($items as &$item) {
            if (isset($item['id']) && $item['id'] === $id) {
                $item = array_merge($item, $body);
                $updated = $item;
                break;
            }
        }
        unset($item);

        if ($updated === null) {
            return json(['error' => 'Resource not found'], 404);
        }

        $data[$resource] = $items;
        $this->dataService->saveData($data);
        return json($updated);
    }

    protected function delete(string $resource, string $id, array $data): Response
    {
        $items = $data[$resource] ?? [];
        $before = count($items);
        $items = array_values(array_filter($items, fn ($item) => !isset($item['id']) || $item['id'] !== $id));
        $data[$resource] = $items;
        $this->dataService->saveData($data);
        return json(['success' => count($items) < $before]);
    }
}
