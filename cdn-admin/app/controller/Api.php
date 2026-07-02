<?php
declare(strict_types=1);

namespace app\controller;

use app\service\DataService;
use think\Request;
use think\Response;

/**
 * RESTful API 控制器
 *
 * 统一入口：/api/:resource
 * 支持 GET/POST/PUT/DELETE，并可按 id、search、page、limit 查询。
 */
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
            return $this->error('System not installed. Please run install first.', 403);
        }

        if (!$resource || !$this->dataService->validateResource($resource)) {
            return $this->error('Invalid or unsupported resource', 400);
        }

        $method = $request->method();
        $id = trim((string) $request->param('id', ''));
        $body = $this->getInputData($request);

        try {
            switch ($method) {
                case 'GET':
                    return $this->handleGet($request, $resource, $id);

                case 'POST':
                    $newItem = $this->dataService->create($resource, $body);
                    return json($newItem, 201);

                case 'PUT':
                    if ($id === '') {
                        return $this->error('Missing id parameter', 400);
                    }
                    $updated = $this->dataService->update($resource, $id, $body);
                    if ($updated === null) {
                        return $this->error('Resource not found', 404);
                    }
                    return json($updated);

                case 'DELETE':
                    if ($id === '') {
                        return $this->error('Missing id parameter', 400);
                    }
                    $success = $this->dataService->delete($resource, $id);
                    return json(['success' => $success]);

                default:
                    return $this->error('Method not allowed', 405);
            }
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 400);
        } catch (\Throwable $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * 处理 GET 请求：支持按 id 查询、搜索、分页、全部列表
     */
    protected function handleGet(Request $request, string $resource, string $id): Response
    {
        if ($id !== '') {
            $item = $this->dataService->find($resource, $id);
            if ($item === null) {
                return $this->error('Resource not found', 404);
            }
            return json($item);
        }

        $search = trim((string) $request->param('search', ''));
        $hasPaginateFlag = $request->param('paginate') !== null;
        $hasPageParam = $request->param('page') !== null;

        if ($hasPaginateFlag || $hasPageParam) {
            $page = (int) $request->param('page', 1);
            $limit = (int) $request->param('limit', 0);
            return json($this->dataService->paginate($resource, $page, $limit, $search));
        }

        if ($search !== '') {
            return json($this->dataService->search($resource, $search));
        }

        return json($this->dataService->list($resource));
    }

    /**
     * 统一错误响应
     */
    protected function error(string $message, int $code): Response
    {
        return json(['error' => $message], $code);
    }

    /**
     * 解析请求体
     */
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
}
