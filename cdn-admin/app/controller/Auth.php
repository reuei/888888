<?php
declare(strict_types=1);

namespace app\controller;

use app\service\DataService;
use think\Request;
use think\Response;

/**
 * 登录认证控制器
 *
 * S 端：读取 data/config.php 中管理员账号。
 * B 端：优先从 MySQL `cdn_merchants` 表验证，同时保留演示账号 merchant/123456 兼容。
 */
class Auth
{
    protected DataService $dataService;

    /** @var string 演示 B 端账号 */
    protected string $demoMerchantAccount = 'merchant';

    /** @var string 演示 B 端密码 */
    protected string $demoMerchantPassword = '123456';

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * 登录
     */
    public function login(Request $request): Response
    {
        $input = $this->getInputData($request);
        $account = trim((string) ($input['account'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $role = $input['role'] ?? '';

        if ($account === '' || $password === '' || !in_array($role, ['s', 'b'], true)) {
            return $this->error('缺少账号、密码或角色', 400);
        }

        if (!$this->dataService->isInstalled()) {
            return $this->error('系统尚未安装', 403);
        }

        try {
            if ($role === 's') {
                return $this->loginAsAdmin($account, $password);
            }

            return $this->loginAsMerchant($account, $password);
        } catch (\Throwable $e) {
            return $this->error('登录失败：' . $e->getMessage(), 500);
        }
    }

    /**
     * 当前登录用户信息（S 端返回管理员，B 端返回演示信息）
     */
    public function profile(Request $request): Response
    {
        if (!$this->dataService->isInstalled()) {
            return $this->error('系统尚未安装', 403);
        }

        $config = $this->dataService->loadConfig();

        return json([
            'admin' => [
                'username' => $config['admin']['username'] ?? 'admin',
                'installedAt' => $config['installedAt'] ?? null,
            ],
            'demoMerchant' => [
                'account' => $this->demoMerchantAccount,
                'notice' => 'B 端演示账号，生产环境请删除或修改',
            ],
        ]);
    }

    /**
     * S 端管理员登录
     */
    protected function loginAsAdmin(string $account, string $password): Response
    {
        $config = $this->dataService->loadConfig();
        $admin = $config['admin'] ?? [];

        if (
            isset($admin['username'], $admin['password']) &&
            $admin['username'] === $account &&
            password_verify($password, $admin['password'])
        ) {
            return json([
                'success' => true,
                'role' => 's',
                'account' => $account,
            ]);
        }

        return $this->error('账号或密码错误', 401);
    }

    /**
     * B 端商户登录
     */
    protected function loginAsMerchant(string $account, string $password): Response
    {
        // 保留演示账号兼容
        if ($account === $this->demoMerchantAccount && $password === $this->demoMerchantPassword) {
            return json([
                'success' => true,
                'role' => 'b',
                'account' => $account,
                'notice' => '演示账号登录',
            ]);
        }

        // 从 MySQL 商户表验证
        $merchants = $this->dataService->list('merchants');
        foreach ($merchants as $merchant) {
            $merchantAccount = $merchant['account'] ?? $merchant['shopName'] ?? '';
            if ($merchantAccount === '' || $merchantAccount !== $account) {
                continue;
            }

            $hashed = $merchant['password_hash'] ?? '';
            $plain = $merchant['password'] ?? '';

            if (
                ($hashed !== '' && password_verify($password, $hashed)) ||
                ($plain !== '' && $plain === $password)
            ) {
                return json([
                    'success' => true,
                    'role' => 'b',
                    'account' => $account,
                    'merchant' => $merchant,
                ]);
            }
        }

        return $this->error('账号或密码错误', 401);
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
