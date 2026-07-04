<?php
declare(strict_types=1);

namespace app\controller;

use app\service\DataService;
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\Response;

/**
 * 安装向导控制器
 *
 * 支持环境检测、MySQL 配置、自动建库建表、导入演示数据、生成配置文件。
 */
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
            return $this->handleInstall($error);
        }

        $checks = $this->dataService->getEnvChecks();
        $html = $this->render($step, $checks, $error);

        return Response::create($html, 'html', 200)->header([
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }

    /**
     * 执行安装核心逻辑
     */
    protected function handleInstall(string &$error): Response
    {
        $dbHost = trim(Request::post('db_host', '127.0.0.1'));
        $dbPort = (int) Request::post('db_port', 3306);
        $dbName = trim(Request::post('db_name', ''));
        $dbUser = trim(Request::post('db_user', ''));
        $dbPass = Request::post('db_pass', '');
        $dbPrefix = trim(Request::post('db_prefix', 'cdn_'));
        $adminUser = trim(Request::post('admin_user', ''));
        $adminPass = Request::post('admin_pass', '');
        $adminPass2 = Request::post('admin_pass2', '');
        $demo = Request::post('import_demo') !== null;

        // 基础校验
        if ($dbHost === '' || $dbName === '' || $dbUser === '') {
            $error = '请填写完整的数据库信息。';
            return $this->renderStep2($error);
        }
        if ($dbPrefix === '') {
            $error = '表前缀不能为空，建议使用 cdn_。';
            return $this->renderStep2($error);
        }
        if ($adminUser === '' || $adminPass === '') {
            $error = '管理员账号和密码不能为空。';
            return $this->renderStep2($error);
        }
        if ($adminPass !== $adminPass2) {
            $error = '两次输入的密码不一致。';
            return $this->renderStep2($error);
        }
        if (strlen($adminPass) < 6) {
            $error = '密码长度至少 6 位。';
            return $this->renderStep2($error);
        }

        // 1. 测试数据库连接并自动建库
        $pdoTest = $this->dataService->testPdoConnection($dbHost, $dbPort, $dbUser, $dbPass);
        if (!$pdoTest['ok']) {
            $error = '数据库连接失败：' . $pdoTest['error'];
            return $this->renderStep2($error);
        }

        if (!$this->checkJsonSupport($pdoTest['version'] ?? '')) {
            $error = 'MySQL 版本过低：需要 MySQL 5.7+ 或 MariaDB 10.2+ 以支持 JSON 类型。当前版本：' . ($pdoTest['version'] ?? '未知');
            return $this->renderStep2($error);
        }

        $pdo = $this->createPdo($dbHost, $dbPort, $dbUser, $dbPass);
        try {
            $pdo->exec(sprintf(
                "CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
                str_replace('`', '``', $dbName)
            ));
        } catch (\PDOException $e) {
            $error = '创建数据库失败：' . $e->getMessage();
            return $this->renderStep2($error);
        }

        // 2. 验证目标库可访问
        $dbTest = $this->dataService->testPdoConnection($dbHost, $dbPort, $dbUser, $dbPass, $dbName);
        if (!$dbTest['ok']) {
            $error = '无法访问目标数据库：' . $dbTest['error'];
            return $this->renderStep2($error);
        }

        // 3. 生成并写入数据库配置
        $databaseConfig = $this->buildDatabaseConfig($dbHost, $dbPort, $dbName, $dbUser, $dbPass, $dbPrefix);
        $configFile = root_path() . 'config/database.php';
        $configContent = "<?php\nreturn " . var_export($databaseConfig, true) . ";\n";
        if (file_put_contents($configFile, $configContent) === false) {
            $error = '写入数据库配置文件失败，请检查 config/ 目录权限。';
            return $this->renderStep2($error);
        }

        // 4. 重新加载配置到 ThinkPHP 并重连数据库
        Config::set($databaseConfig, 'database');
        try {
            Db::connect('mysql', true);
        } catch (\Throwable $e) {
            $this->cleanupOnFailure();
            $error = '数据库配置生效失败：' . $e->getMessage();
            return $this->renderStep2($error);
        }

        // 5. 创建数据表
        try {
            $this->dataService->initTables();
        } catch (\Throwable $e) {
            $this->cleanupOnFailure();
            $error = '创建数据表失败：' . $e->getMessage();
            return $this->renderStep2($error);
        }

        // 6. 保存站点配置
        $siteConfig = [
            'installed' => true,
            'installedAt' => date('Y-m-d H:i:s'),
            'dbConfigured' => true,
            'admin' => [
                'username' => $adminUser,
                'password' => password_hash($adminPass, PASSWORD_DEFAULT),
            ],
            'demoData' => $demo,
            'dbPrefix' => $dbPrefix,
        ];

        if (!$this->dataService->saveConfig($siteConfig)) {
            $this->cleanupOnFailure();
            $error = '写入站点配置文件失败，请检查 data/ 目录权限。';
            return $this->renderStep2($error);
        }

        // 7. 导入演示数据
        if ($demo) {
            try {
                $this->dataService->importDemo();
            } catch (\Throwable $e) {
                $this->cleanupOnFailure();
                $error = '导入演示数据失败：' . $e->getMessage();
                return $this->renderStep2($error);
            }
        }

        // 8. 写入初始操作日志
        try {
            $this->dataService->create('operationLogs', [
                'operator' => $adminUser,
                'module' => '系统安装',
                'action' => '完成安装',
                'detail' => sprintf('数据库 %s，表前缀 %s，演示数据：%s', $dbName, $dbPrefix, $demo ? '是' : '否'),
                'ip' => Request::ip() ?? 'unknown',
                'createdAt' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // 操作日志失败不影响安装结果，仅记录
        }

        // 9. 设置安装锁
        if (!$this->dataService->setLock()) {
            $error = '创建安装锁文件失败，请检查 data/ 目录权限。';
            return $this->renderStep2($error);
        }

        return redirect('./install?step=3');
    }

    /**
     * 构造完整 ThinkPHP 数据库配置
     */
    protected function buildDatabaseConfig(
        string $host,
        int $port,
        string $database,
        string $user,
        string $pass,
        string $prefix
    ): array {
        return [
            'default'         => 'mysql',
            'time_query_rule' => [],
            'auto_timestamp'  => true,
            'datetime_format' => 'Y-m-d H:i:s',
            'datetime_field'  => '',
            'connections'     => [
                'mysql' => [
                    'type'            => 'mysql',
                    'hostname'        => $host,
                    'database'        => $database,
                    'username'        => $user,
                    'password'        => $pass,
                    'hostport'        => $port,
                    'params'          => [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    ],
                    'charset'         => 'utf8mb4',
                    'prefix'          => $prefix,
                    'deploy'          => 0,
                    'rw_separate'     => false,
                    'master_num'      => 1,
                    'slave_no'        => '',
                    'fields_strict'   => true,
                    'break_reconnect' => true,
                    'trigger_sql'     => true,
                    'fields_cache'    => false,
                ],
            ],
        ];
    }

    /**
     * 创建原始 PDO 连接
     */
    protected function createPdo(string $host, int $port, string $user, string $pass): \PDO
    {
        $dsn = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $host, $port);
        return new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_TIMEOUT => 5,
        ]);
    }

    /**
     * 检查数据库是否支持 JSON 类型
     */
    protected function checkJsonSupport(string $version): bool
    {
        $version = trim($version);
        if ($version === '') {
            return true;
        }

        if (stripos($version, 'MariaDB') !== false) {
            if (preg_match('/(\d+\.\d+)/', $version, $matches)) {
                return version_compare($matches[1], '10.2', '>=');
            }
            return true;
        }

        if (preg_match('/(\d+\.\d+)/', $version, $matches)) {
            return version_compare($matches[1], '5.7', '>=');
        }

        return false;
    }

    /**
     * 安装失败时清理已生成的配置文件，方便重新安装
     */
    protected function cleanupOnFailure(): void
    {
        $configFile = root_path() . 'config/database.php';
        if (file_exists($configFile)) {
            @unlink($configFile);
        }
        $this->dataService->removeLock();
    }

    protected function renderStep2(string $error = ''): Response
    {
        $checks = $this->dataService->getEnvChecks();
        $html = $this->render(2, $checks, $error);
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
            background: linear-gradient(135deg, #ecf2ff 0%, #dbe8ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .card {
            width: 100%;
            max-width: 680px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(90deg, #0052d9, #4656ff);
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
            background: linear-gradient(90deg, #0052d9, #4656ff);
            color: #fff;
            text-align: center;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn:hover { background: linear-gradient(90deg, #003bb3, #3244cc); }
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
            border-color: #0052d9;
            box-shadow: 0 0 0 3px rgba(0, 82, 217, 0.1);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        @media (max-width: 480px) {
            .form-row { grid-template-columns: 1fr; }
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
        .section-title {
            font-size: 15px;
            font-weight: 600;
            color: #0052d9;
            margin: 24px 0 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .section-title:first-child { margin-top: 0; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>CDN 防护加速平台安装向导</h1>
            <p>比鹿云盾更开放，比 SCDN 更经济的企业级 CDN 防护加速后台</p>
        </div>

        <div class="body">
            <?php if ($isInstalled && $step !== 3): ?>
                <div class="alert alert-yellow">
                    <p style="font-weight:600;margin:0 0 4px;">系统已安装</p>
                    <p class="text-sm" style="margin:0;">如需重新安装，请先删除 <code>data/install.lock</code>、<code>data/config.php</code> 与 <code>config/database.php</code>。</p>
                    <div class="mt-4">
                        <a href="./" class="btn" style="width:auto;display:inline-block;padding:10px 20px;">进入平台</a>
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
                        <p class="text-sm" style="margin:0 0 4px;">EasyPanel 用户：请确认 PHP 版本 ≥ 8.2，开启 PDO_MySQL，并将 <code>config/</code>、<code>data/</code>、<code>runtime/</code> 目录权限设置为 755 或 777。</p>
                        <p class="text-sm" style="margin:0;">若内存或执行时间不足，可在 EasyPanel「PHP 设置」中调整 <code>memory_limit ≥ 64M</code>、<code>max_execution_time ≥ 30</code>，或直接编辑项目根目录的 <code>.user.ini</code>。详细说明见 docs/easypanel-deploy.md。</p>
                    </div>
                    <button disabled class="btn">下一步</button>
                <?php else: ?>
                    <div class="alert alert-green">
                        <p style="margin:0;">环境检测通过，可以继续安装。</p>
                    </div>
                    <a href="./install?step=2" class="btn">下一步：数据库配置</a>
                <?php endif; ?>

            <?php elseif ($step === 2): ?>
                <h2>步骤 2：数据库与管理员配置</h2>
                <?php if ($error): ?>
                    <div class="alert alert-red"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="POST" action="./install?step=2">
                    <div class="section-title">MySQL 数据库信息</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>数据库主机</label>
                            <input type="text" name="db_host" value="127.0.0.1" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>端口</label>
                            <input type="number" name="db_port" value="3306" required class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>数据库名</label>
                        <input type="text" name="db_name" placeholder="cdn_admin" required class="form-control">
                        <div class="text-sm" style="color:#6b7280;margin-top:4px;">若数据库不存在，安装程序会自动创建</div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>数据库账号</label>
                            <input type="text" name="db_user" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>数据库密码</label>
                            <input type="password" name="db_pass" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>表前缀</label>
                        <input type="text" name="db_prefix" value="cdn_" required class="form-control">
                        <div class="text-sm" style="color:#6b7280;margin-top:4px;">建议使用 <code>cdn_</code>，安装后会自动创建 <code>cdn_articles</code> 等数据表</div>
                    </div>

                    <div class="section-title">管理员账号</div>
                    <div class="form-group">
                        <label>管理员账号</label>
                        <input type="text" name="admin_user" value="admin" required class="form-control">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>管理员密码</label>
                            <input type="password" name="admin_pass" required minlength="6" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>确认密码</label>
                            <input type="password" name="admin_pass2" required minlength="6" class="form-control">
                        </div>
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
                    <p style="color:#6b7280;margin:0 0 24px;">MySQL 数据库、数据表、管理员账号已创建完成。</p>
                    <div class="alert alert-yellow text-left">
                        <p style="font-weight:600;margin:0 0 4px;">安全提示</p>
                        <p class="text-sm" style="margin:0;">安装完成后建议删除或重命名 <code>app/controller/Install.php</code> 与 <code>install/</code> 目录，防止被重复执行。</p>
                    </div>
                    <a href="./" class="btn" style="width:auto;display:inline-block;padding:10px 24px;">进入平台</a>
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
