<?php
declare(strict_types=1);

namespace app\service;

use think\facade\Db;

/**
 * 数据服务层
 *
 * 统一封装基于 MySQL + JSON 的数据读写，保持前端 REST API 兼容。
 * 所有业务数据以 `id + data(json)` 形式存储，表前缀默认为 `cdn_`。
 */
class DataService
{
    protected string $dataDir;
    protected string $configFile;
    protected string $lockFile;
    protected int $defaultPerPage = 20;

    /** @var array<string, string> resource 到表名（不含前缀）的映射 */
    protected array $resourceMap = [
        'articles' => 'articles',
        'coupons' => 'coupons',
        'couponRecords' => 'coupon_records',
        'skus' => 'skus',
        'packages' => 'packages',
        'merchants' => 'merchants',
        'users' => 'users',
        'orders' => 'orders',
        'categories' => 'categories',
        'adSlots' => 'ad_slots',
        'complaints' => 'complaints',
        'gateways' => 'gateways',
        'nodes' => 'nodes',
        'products' => 'products',
        'inviteCodes' => 'invite_codes',
        'userGroups' => 'user_groups',
        'userLevels' => 'user_levels',
        'realnameRecords' => 'realname_records',
        'roles' => 'roles',
        'backupRecords' => 'backup_records',
        'sites' => 'sites',
        'myPackages' => 'my_packages',
        'bOrders' => 'b_orders',
        'invoices' => 'invoices',
        'whitelistRecords' => 'whitelist_records',
        'financeRecords' => 'finance_records',
        'settlementRecords' => 'settlement_records',
        'commissionRecords' => 'commission_records',
        'operationLogs' => 'operation_logs',
        'apiDocs' => 'api_docs',
        'notifications' => 'notifications',
        'agents' => 'agents',
        'agentProducts' => 'agent_products',
        'pcTemplates' => 'pc_templates',
        'mobileTemplates' => 'mobile_templates',
        'cardTemplates' => 'card_templates',
        'luckyNumbers' => 'lucky_numbers',
        'dailyStats' => 'daily_stats',
        'merchantStats' => 'merchant_stats',
        'userGrowthStats' => 'user_growth_stats',
        'sourceLicenses' => 'source_licenses',
        'nodePurchases' => 'node_purchases',
        'updateRecords' => 'update_records',
        'salesAnnouncements' => 'sales_announcements',
        'salesOrders' => 'sales_orders',
    ];

    /** @var array<string, string> 主键前缀 */
    protected array $resourcePrefixes = [
        'articles' => 'A',
        'coupons' => 'CO',
        'couponRecords' => 'CR',
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
        'dailyStats' => 'DS',
        'merchantStats' => 'MS',
        'userGrowthStats' => 'UG',
        'sourceLicenses' => 'SL',
        'nodePurchases' => 'NP',
        'updateRecords' => 'UR',
        'salesAnnouncements' => 'SA',
        'salesOrders' => 'SO',
    ];

    public function __construct()
    {
        $this->dataDir = root_path() . 'data';
        $this->configFile = $this->dataDir . '/config.php';
        $this->lockFile = $this->dataDir . '/install.lock';

        if (!is_dir($this->dataDir)) {
            @mkdir($this->dataDir, 0755, true);
        }
    }

    public function getDataDir(): string
    {
        return $this->dataDir;
    }

    public function getConfigFile(): string
    {
        return $this->configFile;
    }

    public function getLockFile(): string
    {
        return $this->lockFile;
    }

    /**
     * 是否已完成安装
     */
    public function isInstalled(): bool
    {
        return file_exists($this->lockFile) && file_exists($this->configFile);
    }

    /**
     * 加载站点配置
     */
    public function loadConfig(): array
    {
        if (!file_exists($this->configFile)) {
            return [];
        }
        $config = require $this->configFile;
        return is_array($config) ? $config : [];
    }

    /**
     * 保存站点配置
     */
    public function saveConfig(array $config): bool
    {
        $content = "<?php\nreturn " . var_export($config, true) . ";\n";
        return file_put_contents($this->configFile, $content) !== false;
    }

    /**
     * 设置安装锁
     */
    public function setLock(): bool
    {
        return file_put_contents($this->lockFile, date('Y-m-d H:i:s')) !== false;
    }

    /**
     * 移除安装锁（重新安装时使用）
     */
    public function removeLock(): bool
    {
        if (file_exists($this->lockFile)) {
            return @unlink($this->lockFile);
        }
        return true;
    }

    public function resourcePrefixes(): array
    {
        return $this->resourcePrefixes;
    }

    /**
     * 将 resource 名称转换为表名（不含前缀）
     */
    public function getTableName(string $resource): string
    {
        return $this->resourceMap[$resource] ?? $resource;
    }

    /**
     * 校验 resource 是否受支持
     */
    public function validateResource(string $resource): bool
    {
        return isset($this->resourceMap[$resource]);
    }

    /**
     * 测试当前 ThinkPHP 数据库连接是否可用
     */
    public function testConnection(): array
    {
        try {
            $result = Db::query('SELECT 1 AS ok');
            return ['ok' => true, 'error' => '', 'detail' => $result[0] ?? []];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * 使用原始 PDO 测试数据库连接（安装前使用）
     */
    public function testPdoConnection(string $host, int $port, string $user, string $pass, ?string $database = null): array
    {
        $dsn = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $host, $port);
        if ($database !== null && $database !== '') {
            $dsn .= sprintf(';dbname=%s', $database);
        }

        try {
            $pdo = new \PDO($dsn, $user, $pass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_TIMEOUT => 5,
            ]);
            $version = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
            return ['ok' => true, 'error' => '', 'version' => $version];
        } catch (\PDOException $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * 当前数据库中是否存在某张表
     */
    public function tableExists(string $resource): bool
    {
        $table = $this->getTableName($resource);
        $prefix = config('database.connections.mysql.prefix', '');
        $fullTable = $prefix . $table;
        $database = config('database.connections.mysql.database', '');

        try {
            $sql = "SELECT 1 FROM information_schema.tables WHERE table_schema = ? AND table_name = ? LIMIT 1";
            $rows = Db::query($sql, [$database, $fullTable]);
            return !empty($rows);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * 执行建表 SQL
     */
    public function initTables(): bool
    {
        $sqlFile = root_path() . 'install/database.sql';
        if (!file_exists($sqlFile)) {
            throw new \RuntimeException('install/database.sql 文件不存在');
        }

        $sql = file_get_contents($sqlFile);
        if ($sql === false || $sql === '') {
            throw new \RuntimeException('无法读取 install/database.sql');
        }

        // 移除注释并拆分语句
        $statements = $this->splitSqlStatements($sql);

        foreach ($statements as $statement) {
            try {
                Db::execute($statement);
            } catch (\Throwable $e) {
                throw new \RuntimeException('SQL 执行失败：' . $e->getMessage());
            }
        }

        return true;
    }

    /**
     * 查询列表（默认按创建时间倒序）
     */
    public function list(string $resource): array
    {
        $this->assertResource($resource);
        $table = $this->getTableName($resource);
        $rows = Db::name($table)
            ->order('created_at', 'desc')
            ->column('data');

        return $this->decodeRows($rows);
    }

    /**
     * 根据 ID 查询单条
     */
    public function find(string $resource, string $id): ?array
    {
        $this->assertResource($resource);
        $table = $this->getTableName($resource);
        $row = Db::name($table)->where('id', $id)->value('data');

        return $row !== null ? $this->decodeRow($row) : null;
    }

    /**
     * 创建记录
     */
    public function create(string $resource, array $body): array
    {
        $this->assertResource($resource);
        $table = $this->getTableName($resource);
        $prefix = $this->resourcePrefixes[$resource] ?? 'ID';

        // 防止客户端传入 id 导致不一致
        unset($body['id']);

        $newId = $this->generateId($table, $prefix);
        $newItem = array_merge($body, ['id' => $newId]);

        Db::name($table)->insert([
            'id' => $newId,
            'data' => json_encode($newItem, JSON_UNESCAPED_UNICODE),
        ]);

        return $newItem;
    }

    /**
     * 更新记录
     */
    public function update(string $resource, string $id, array $body): ?array
    {
        $this->assertResource($resource);
        $table = $this->getTableName($resource);
        $existing = $this->find($resource, $id);

        if ($existing === null) {
            return null;
        }

        // 禁止通过 PUT 修改 id
        unset($body['id']);
        $updated = array_merge($existing, $body);

        Db::name($table)->where('id', $id)->update([
            'data' => json_encode($updated, JSON_UNESCAPED_UNICODE),
        ]);

        return $updated;
    }

    /**
     * 删除记录
     */
    public function delete(string $resource, string $id): bool
    {
        $this->assertResource($resource);
        $table = $this->getTableName($resource);
        return Db::name($table)->where('id', $id)->delete() > 0;
    }

    /**
     * 统计记录数
     */
    public function count(string $resource): int
    {
        $this->assertResource($resource);
        $table = $this->getTableName($resource);
        return (int) Db::name($table)->count();
    }

    /**
     * 简单搜索：按关键字模糊匹配 JSON 数据
     */
    public function search(string $resource, string $keyword): array
    {
        $this->assertResource($resource);
        $table = $this->getTableName($resource);

        $rows = Db::name($table)
            ->whereLike('data', '%' . addcslashes($keyword, '%_\\') . '%')
            ->order('created_at', 'desc')
            ->column('data');

        return $this->decodeRows($rows);
    }

    /**
     * 分页查询
     *
     * @return array{list: array, total: int, page: int, limit: int, pages: int}
     */
    public function paginate(string $resource, int $page = 1, int $limit = 0, string $keyword = ''): array
    {
        $this->assertResource($resource);
        $table = $this->getTableName($resource);

        if ($limit <= 0) {
            $limit = $this->defaultPerPage;
        }
        $page = max(1, $page);
        $limit = max(1, min(200, $limit));

        $query = Db::name($table);
        if ($keyword !== '') {
            $query->whereLike('data', '%' . addcslashes($keyword, '%_\\') . '%');
        }

        $total = (int) $query->count();
        $pages = (int) ceil($total / $limit);

        $query = Db::name($table);
        if ($keyword !== '') {
            $query->whereLike('data', '%' . addcslashes($keyword, '%_\\') . '%');
        }

        $rows = $query
            ->order('created_at', 'desc')
            ->limit(($page - 1) * $limit, $limit)
            ->column('data');

        return [
            'list' => $this->decodeRows($rows),
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'pages' => $pages,
        ];
    }

    /**
     * 在事务中执行回调
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public function transaction(callable $callback)
    {
        return Db::transaction($callback);
    }

    /**
     * 导入演示数据
     */
    public function importDemo(): bool
    {
        $demoFile = root_path() . 'install/data-demo.php';
        if (!file_exists($demoFile)) {
            return false;
        }

        $demoData = require $demoFile;
        if (!is_array($demoData)) {
            return false;
        }

        return $this->transaction(function () use ($demoData) {
            foreach ($demoData as $resource => $items) {
                if (!isset($this->resourceMap[$resource])) {
                    continue;
                }
                $table = $this->getTableName($resource);

                foreach ($items as $item) {
                    if (!isset($item['id'])) {
                        continue;
                    }
                    // 避免重复安装时主键冲突
                    Db::name($table)->where('id', $item['id'])->delete();
                    Db::name($table)->insert([
                        'id' => $item['id'],
                        'data' => json_encode($item, JSON_UNESCAPED_UNICODE),
                    ]);
                }
            }
            return true;
        });
    }

    /**
     * 生成新 ID
     */
    protected function generateId(string $table, string $prefix): string
    {
        $prefixLen = strlen($prefix);
        $result = Db::name($table)
            ->whereLike('id', $prefix . '%')
            ->field("MAX(CAST(SUBSTRING(id, " . ($prefixLen + 1) . ") AS UNSIGNED)) AS max_num")
            ->find();

        $maxNum = isset($result['max_num']) && $result['max_num'] !== null ? (int) $result['max_num'] : 0;
        return $prefix . str_pad((string) ($maxNum + 1), 3, '0', STR_PAD_LEFT);
    }

    /**
     * 环境检测
     */
    public function getEnvChecks(): array
    {
        $minPhpVersion = '8.2.0';
        $configDir = root_path() . 'config';
        $runtimeDir = root_path() . 'runtime';

        $disabledFunctions = ini_get('disable_functions');
        $disabledList = $disabledFunctions !== '' ? explode(',', $disabledFunctions) : [];
        $criticalFunctions = ['file_put_contents', 'file_get_contents', 'mkdir', 'unlink', 'json_encode', 'json_decode', 'pdo'];
        $blockedFunctions = [];
        foreach ($criticalFunctions as $func) {
            if (in_array($func, $disabledList, true)) {
                $blockedFunctions[] = $func;
            }
        }

        return [
            'php_version' => [
                'name' => 'PHP 版本',
                'required' => '>= ' . $minPhpVersion,
                'current' => PHP_VERSION,
                'ok' => version_compare(PHP_VERSION, $minPhpVersion, '>='),
            ],
            'pdo_mysql' => [
                'name' => 'PDO_MySQL 扩展',
                'required' => '已启用',
                'current' => extension_loaded('pdo_mysql') ? '已启用' : '未启用',
                'ok' => extension_loaded('pdo_mysql'),
            ],
            'json_ext' => [
                'name' => 'JSON 扩展',
                'required' => '已启用',
                'current' => extension_loaded('json') ? '已启用' : '未启用',
                'ok' => extension_loaded('json'),
            ],
            'openssl_ext' => [
                'name' => 'OpenSSL 扩展',
                'required' => '已启用（密码哈希推荐）',
                'current' => extension_loaded('openssl') ? '已启用' : '未启用',
                'ok' => extension_loaded('openssl'),
            ],
            'disabled_functions' => [
                'name' => '关键函数是否被禁用',
                'required' => '无关键函数被禁用',
                'current' => empty($blockedFunctions) ? '正常' : '被禁用：' . implode(', ', $blockedFunctions),
                'ok' => empty($blockedFunctions),
            ],
            'open_basedir' => [
                'name' => 'open_basedir 限制',
                'required' => '包含项目目录',
                'current' => $this->checkOpenBasedir(),
                'ok' => $this->checkOpenBasedir() === '正常',
            ],
            'writable_config' => [
                'name' => '配置目录可写',
                'required' => 'config/ 可写',
                'current' => $this->isDirReallyWritable($configDir),
                'ok' => $this->isDirReallyWritable($configDir) === '可写',
            ],
            'writable_data' => [
                'name' => '数据目录可写',
                'required' => 'data/ 可写',
                'current' => $this->isDirReallyWritable($this->dataDir),
                'ok' => $this->isDirReallyWritable($this->dataDir) === '可写',
            ],
            'writable_runtime' => [
                    'name' => '运行缓存目录可写',
                    'required' => 'runtime/ 可写',
                    'current' => $this->isDirReallyWritable($runtimeDir),
                    'ok' => $this->isDirReallyWritable($runtimeDir) === '可写',
                ],
                'memory_limit' => [
                    'name' => 'PHP 内存限制',
                    'required' => '≥ 64M',
                    'current' => ini_get('memory_limit'),
                    'ok' => $this->memoryToBytes(ini_get('memory_limit')) >= 64 * 1024 * 1024,
                ],
                'max_execution_time' => [
                    'name' => 'PHP 最大执行时间',
                    'required' => '≥ 30 秒',
                    'current' => ini_get('max_execution_time') . ' 秒',
                    'ok' => (int) ini_get('max_execution_time') === 0 || (int) ini_get('max_execution_time') >= 30,
                ],
            ];
    }

    /**
     * 将 PHP 内存限制字符串转换为字节数
     */
    protected function memoryToBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '') {
            return 0;
        }
        $last = strtolower($value[strlen($value) - 1]);
        $num = (int) $value;
        switch ($last) {
            case 'g':
                $num *= 1024;
            case 'm':
                $num *= 1024;
            case 'k':
                $num *= 1024;
        }
        return $num;
    }

    /**
     * 实际写入测试，避免 is_writable 在部分虚拟主机上误判
     */
    protected function isDirReallyWritable(string $dir): string
    {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (!is_dir($dir)) {
            return '目录不存在';
        }
        $testFile = rtrim($dir, '/') . '/.write_test_' . uniqid();
        $written = @file_put_contents($testFile, 'test');
        if ($written === false) {
            return '不可写';
        }
        @unlink($testFile);
        return '可写';
    }

    /**
     * 检查 open_basedir 是否包含项目目录
     */
    protected function checkOpenBasedir(): string
    {
        $openBasedir = ini_get('open_basedir');
        if ($openBasedir === '' || $openBasedir === false) {
            return '正常';
        }
        $projectRoot = root_path();
        $paths = explode(PATH_SEPARATOR, $openBasedir);
        foreach ($paths as $path) {
            $path = rtrim($path, '/\\');
            if ($path !== '' && str_starts_with($projectRoot, $path . DIRECTORY_SEPARATOR)) {
                return '正常';
            }
            if ($path !== '' && rtrim($projectRoot, '/\\') === $path) {
                return '正常';
            }
        }
        return '受限：' . $openBasedir;
    }

    public function allChecksOk(array $checks): bool
    {
        foreach ($checks as $c) {
            if (!$c['ok']) {
                return false;
            }
        }
        return true;
    }

    /**
     * 断言 resource 有效
     */
    protected function assertResource(string $resource): void
    {
        if (!$this->validateResource($resource)) {
            throw new \InvalidArgumentException('不支持的资源类型：' . $resource);
        }
    }

    /**
     * 解码单行 JSON 数据
     */
    protected function decodeRow(mixed $data): ?array
    {
        if (!is_string($data)) {
            return null;
        }
        $decoded = json_decode($data, true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * 解码多行 JSON 数据
     */
    protected function decodeRows(array $rows): array
    {
        $result = [];
        foreach ($rows as $data) {
            $decoded = $this->decodeRow($data);
            if ($decoded !== null) {
                $result[] = $decoded;
            }
        }
        return $result;
    }

    /**
     * 拆分 SQL 文件为单条语句
     */
    protected function splitSqlStatements(string $sql): array
    {
        // 移除 /* ... */ 多行注释与 -- 行注释
        $sql = preg_replace('/\/\*[\s\S]*?\*\//', '', $sql) ?: $sql;
        $lines = explode("\n", $sql);
        $filtered = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '--')) {
                continue;
            }
            $filtered[] = $line;
        }

        $cleaned = implode("\n", $filtered);
        $parts = array_map('trim', explode(';', $cleaned));
        $statements = [];
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            $statements[] = $part;
        }
        return $statements;
    }
}
