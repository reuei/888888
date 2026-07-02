<?php
declare(strict_types=1);

namespace app\service;

class DataService
{
    protected string $dataDir;
    protected string $dataFile;
    protected string $defaultFile;
    protected string $configFile;
    protected string $lockFile;

    public function __construct()
    {
        $this->dataDir = root_path() . 'data';
        $this->dataFile = $this->dataDir . '/data.json';
        $this->defaultFile = $this->dataDir . '/default.json';
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

    public function isInstalled(): bool
    {
        return file_exists($this->lockFile) && file_exists($this->configFile);
    }

    public function loadConfig(): array
    {
        if (!file_exists($this->configFile)) {
            return [];
        }
        $config = require $this->configFile;
        return is_array($config) ? $config : [];
    }

    public function saveConfig(array $config): bool
    {
        $content = "<?php\nreturn " . var_export($config, true) . ";\n";
        return file_put_contents($this->configFile, $content) !== false;
    }

    public function setLock(): bool
    {
        return file_put_contents($this->lockFile, date('Y-m-d H:i:s')) !== false;
    }

    public function loadDefaultData(): array
    {
        if (!file_exists($this->defaultFile)) {
            return [];
        }
        $content = file_get_contents($this->defaultFile);
        if ($content === false) {
            return [];
        }
        $decoded = json_decode($content, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function loadData(): array
    {
        if (file_exists($this->dataFile)) {
            $content = file_get_contents($this->dataFile);
            if ($content !== false) {
                $decoded = json_decode($content, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }
        return $this->loadDefaultData();
    }

    public function saveData(array $data): bool
    {
        return file_put_contents(
            $this->dataFile,
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        ) !== false;
    }

    public function initData(bool $withDemo = false): bool
    {
        $data = $this->loadDefaultData();

        if ($withDemo) {
            $demoFile = root_path() . 'install/data-demo.php';
            if (file_exists($demoFile)) {
                $demoData = require $demoFile;
                if (is_array($demoData)) {
                    $data = array_merge($data, $demoData);
                }
            }
        }

        return $this->saveData($data);
    }

    public function getEnvChecks(): array
    {
        $minPhpVersion = '8.0.0';
        $apiDir = dirname($this->configFile);

        if (!is_dir($apiDir)) {
            @mkdir($apiDir, 0755, true);
        }

        return [
            'php_version' => [
                'name' => 'PHP 版本',
                'required' => '>= ' . $minPhpVersion,
                'current' => PHP_VERSION,
                'ok' => version_compare(PHP_VERSION, $minPhpVersion, '>='),
            ],
            'json_ext' => [
                'name' => 'JSON 扩展',
                'required' => '已启用',
                'current' => extension_loaded('json') ? '已启用' : '未启用',
                'ok' => extension_loaded('json'),
            ],
            'writable_data' => [
                'name' => '数据目录可写',
                'required' => 'data/ 可写',
                'current' => is_dir($this->dataDir) && is_writable($this->dataDir) ? '可写' : '不可写',
                'ok' => is_dir($this->dataDir) && is_writable($this->dataDir),
            ],
        ];
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

    public function resourcePrefixes(): array
    {
        return [
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
    }
}
