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
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .card {
            width: 100%;
            max-width: 640px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #4f46e5;
            color: #fff;
            padding: 24px 32px;
        }
        .header h1 { margin: 0 0 8px; font-size: 24px; }
        .header p { margin: 0; opacity: 0.9; }
        .body { padding: 32px; }
        h2 { margin: 0 0 16px; font-size: 18px; color: #1f2937; }
        .check-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .check-row:last-child { border-bottom: none; }
        .check-info { flex: 1; }
        .check-name { color: #374151; font-size: 14px; }
        .check-meta { color: #9ca3af; font-size: 12px; margin-top: 2px; }
        .check-status { font-size: 14px; font-weight: 500; }
        .status-ok { color: #16a34a; }
        .status-fail { color: #dc2626; }
        .alert {
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .alert-red { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .alert-green { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        .alert-yellow { background: #fefce8; border: 1px solid #fde68a; color: #854d0e; }
        .btn {
            display: inline-block;
            width: 100%;
            padding: 12px;
            background: #4f46e5;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn:hover { background: #4338ca; }
        .btn:disabled {
            background: #d1d5db;
            cursor: not-allowed;
        }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            font-size: 14px;
            color: #374151;
            margin-bottom: 6px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }
        .form-control:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .checkbox-row {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .checkbox-row input { margin-right: 8px; }
        .checkbox-row label { font-size: 14px; color: #374151; }
        code {
            background: rgba(0,0,0,0.05);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: Consolas, Monaco, monospace;
        }
        .success-icon {
            width: 64px;
            height: 64px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .text-center { text-align: center; }
        .text-sm { font-size: 13px; }
        .mt-4 { margin-top: 16px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>CDN 防护加速平台安装向导</h1>
            <p>基于 ThinkPHP 的虚拟主机一键安装</p>
        </div>

        <div class="body">
            <?php if ($isInstalled && $step !== 3): ?>
                <div class="alert alert-yellow">
                    <p style="font-weight:600;margin:0 0 4px;">系统已安装</p>
                    <p class="text-sm" style="margin:0;">如需重新安装，请先删除 <code>data/install.lock</code> 与 <code>data/config.php</code>。</p>
                    <div class="mt-4">
                        <a href="/" class="btn" style="width:auto;display:inline-block;padding:10px 20px;">进入平台</a>
                    </div>
                </div>
            <?php elseif ($step === 1): ?>
                <h2>步骤 1：环境检测</h2>
                <div class="alert" style="background:#f9fafb;border:1px solid #e5e7eb;padding:0 16px;">
                    <?php foreach ($checks as $c): ?>
                        <div class="check-row">
                            <div class="check-info">
                                <div class="check-name"><?php echo htmlspecialchars($c['name']); ?></div>
                                <div class="check-meta">当前：<?php echo htmlspecialchars($c['current']); ?> / 要求：<?php echo htmlspecialchars($c['required']); ?></div>
                            </div>
                            <div class="check-status <?php echo $c['ok'] ? 'status-ok' : 'status-fail'; ?>">
                                <?php echo $c['ok'] ? '通过' : '失败'; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!$this->dataService->allChecksOk($checks)): ?>
                    <div class="alert alert-red">
                        <p style="margin:0 0 4px;">环境检测未通过，请根据上方提示调整主机环境后刷新本页。</p>
                        <p class="text-sm" style="margin:0;">常见解决方式：将 <code>data/</code> 目录权限设置为 755 或 777。</p>
                    </div>
                    <button disabled class="btn">下一步</button>
                <?php else: ?>
                    <div class="alert alert-green">
                        <p style="margin:0;">环境检测通过，可以继续安装。</p>
                    </div>
                    <a href="/install?step=2" class="btn">下一步：初始化配置</a>
                <?php endif; ?>

            <?php elseif ($step === 2): ?>
                <h2>步骤 2：初始化配置</h2>
                <?php if ($error): ?>
                    <div class="alert alert-red"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST" action="/install?step=2">
                    <div class="form-group">
                        <label>管理员账号</label>
                        <input type="text" name="admin_user" value="admin" required class="form-control">
                    </div>
                    <div class="form-group">
                        <label>管理员密码</label>
                        <input type="password" name="admin_pass" required minlength="6" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>确认密码</label>
                        <input type="password" name="admin_pass2" required minlength="6" class="form-control">
                    </div>
                    <div class="checkbox-row">
                        <input type="checkbox" id="import_demo" name="import_demo" checked>
                        <label for="import_demo">导入演示数据（推荐首次安装勾选）</label>
                    </div>
                    <button type="submit" class="btn">开始安装</button>
                </form>

            <?php elseif ($step === 3): ?>
                <div class="text-center" style="padding:24px 0;">
                    <div class="success-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2>安装完成</h2>
                    <p style="color:#6b7280;margin:0 0 24px;">系统已成功安装，数据文件已生成。</p>
                    <div class="alert alert-yellow text-left">
                        <p style="font-weight:600;margin:0 0 4px;">安全提示</p>
                        <p class="text-sm" style="margin:0;">安装完成后建议删除或重命名 <code>app/controller/Install.php</code> 与 <code>install/</code> 目录，防止被重复执行。</p>
                    </div>
                    <a href="/" class="btn" style="width:auto;display:inline-block;padding:10px 24px;">进入平台</a>
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
