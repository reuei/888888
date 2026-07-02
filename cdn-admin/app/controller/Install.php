<?php
declare(strict_types=1);

namespace app\controller;

use app\service\DataService;
use think\facade\Request;
use think\Response;

class Install
{
    protected DataService $dataService;

    public function __construct(DataService $dataService)
    {
        $this->dataService = $dataService;
    }

    public function index(): Response
    {
        $step = max(1, min(4, (int) Request::get('step', 1)));
        $error = '';

        if (Request::isPost() && $step === 2) {
            $adminUser = trim(Request::post('admin_user', ''));
            $adminPass = Request::post('admin_pass', '');
            $adminPass2 = Request::post('admin_pass2', '');
            $demo = Request::post('import_demo') !== null;

            if ($adminUser === '' || $adminPass === '') {
                $error = '管理员账号和密码不能为空。';
            } elseif ($adminPass !== $adminPass2) {
                $error = '两次输入的密码不一致。';
            } elseif (strlen($adminPass) < 6) {
                $error = '密码长度至少 6 位。';
            } else {
                $config = [
                    'installed' => true,
                    'installedAt' => date('Y-m-d H:i:s'),
                    'admin' => [
                        'username' => $adminUser,
                        'password' => password_hash($adminPass, PASSWORD_DEFAULT),
                    ],
                    'demoData' => $demo,
                ];

                $dataOk = $this->dataService->initData($demo);
                $configOk = $this->dataService->saveConfig($config);
                $lockOk = $this->dataService->setLock();

                if ($dataOk && $configOk && $lockOk) {
                    return redirect('/install?step=3');
                }
                $error = '写入配置文件失败，请检查 data/ 目录权限。';
            }
        }

        $checks = $this->dataService->getEnvChecks();
        $html = $this->render($step, $checks, $error);

        return Response::create($html, 'html', 200)->header([
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }

    protected function render(int $step, array $checks, string $error): string
    {
        $isInstalled = $this->dataService->isInstalled();
        ob_start();
        ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CDN 防护加速平台 - 安装向导</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-indigo-600 px-8 py-6">
            <h1 class="text-2xl font-bold text-white">CDN 防护加速平台安装向导</h1>
            <p class="text-indigo-100 mt-2">基于 ThinkPHP 的虚拟主机一键安装</p>
        </div>

        <div class="px-8 py-6">
            <?php if ($isInstalled && $step !== 3): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800">
                    <p class="font-medium">系统已安装</p>
                    <p class="text-sm mt-1">如需重新安装，请先删除 <code class="bg-yellow-100 px-1 rounded">data/install.lock</code> 与 <code class="bg-yellow-100 px-1 rounded">data/config.php</code>。</p>
                    <div class="mt-4">
                        <a href="/" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">进入平台</a>
                    </div>
                </div>
            <?php elseif ($step === 1): ?>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">步骤 1：环境检测</h2>
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <?php foreach ($checks as $c): ?>
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                            <span class="text-gray-700"><?php echo htmlspecialchars($c['name']); ?></span>
                            <div class="text-right text-sm">
                                <div>当前：<?php echo htmlspecialchars($c['current']); ?></div>
                                <div class="text-gray-400">要求：<?php echo htmlspecialchars($c['required']); ?></div>
                            </div>
                            <div class="ml-4">
                                <?php if ($c['ok']): ?>
                                    <span class="text-green-600 font-medium">通过</span>
                                <?php else: ?>
                                    <span class="text-red-600 font-medium">失败</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!$this->dataService->allChecksOk($checks)): ?>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 mb-4">
                        <p>环境检测未通过，请根据上方提示调整主机环境后刷新本页。</p>
                        <p class="text-sm mt-1">常见解决方式：将 <code class="bg-red-100 px-1 rounded">data/</code> 目录权限设置为 755 或 777。</p>
                    </div>
                    <button disabled class="w-full bg-gray-300 text-white py-3 rounded-lg cursor-not-allowed">下一步</button>
                <?php else: ?>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-700 mb-6">
                        <p>环境检测通过，可以继续安装。</p>
                    </div>
                    <a href="/install?step=2" class="block w-full text-center bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition">下一步：初始化配置</a>
                <?php endif; ?>

            <?php elseif ($step === 2): ?>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">步骤 2：初始化配置</h2>
                <?php if ($error): ?>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 mb-4"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST" action="/install?step=2" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">管理员账号</label>
                        <input type="text" name="admin_user" value="admin" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">管理员密码</label>
                        <input type="password" name="admin_pass" required minlength="6"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">确认密码</label>
                        <input type="password" name="admin_pass2" required minlength="6"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" id="import_demo" name="import_demo" checked
                               class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <label for="import_demo" class="ml-2 text-sm text-gray-700">导入演示数据（推荐首次安装勾选）</label>
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition">开始安装</button>
                </form>

            <?php elseif ($step === 3): ?>
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">安装完成</h2>
                    <p class="text-gray-600 mb-6">系统已成功安装，数据文件已生成。</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800 text-left mb-6">
                        <p class="font-medium mb-1">安全提示</p>
                        <p class="text-sm">安装完成后建议删除或重命名 <code class="bg-yellow-100 px-1 rounded">app/controller/Install.php</code> 与 <code class="bg-yellow-100 px-1 rounded">install/</code> 目录，防止被重复执行。</p>
                    </div>
                    <a href="/" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition">进入平台</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
        <?php
        return ob_get_clean();
    }
}
