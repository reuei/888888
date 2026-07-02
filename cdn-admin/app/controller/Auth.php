<?php
declare(strict_types=1);

namespace app\controller;

use app\service\DataService;
use think\Request;
use think\Response;

class Auth
{
    protected DataService $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function login(Request $request): Response
    {
        $input = $this->getInputData($request);
        $account = trim($input['account'] ?? '');
        $password = $input['password'] ?? '';
        $role = $input['role'] ?? '';

        if (!$account || !$password || !in_array($role, ['s', 'b'], true)) {
            return json(['error' => 'Missing account, password or role'], 400);
        }

        if (!$this->dataService->isInstalled()) {
            return json(['error' => 'System not installed'], 403);
        }

        $config = $this->dataService->loadConfig();

        if ($role === 's') {
            $admin = $config['admin'] ?? [];
            if (
                isset($admin['username'], $admin['password']) &&
                $admin['username'] === $account &&
                password_verify($password, $admin['password'])
            ) {
                return json(['success' => true, 'role' => 's']);
            }
        } elseif ($role === 'b') {
            if ($account === 'merchant' && $password === '123456') {
                return json(['success' => true, 'role' => 'b']);
            }
        }

        return json(['error' => '账号或密码错误'], 401);
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
}
