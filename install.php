<?php
/**
 * QEEFG授权站安装脚本
 * 使用方法: php install.php
 */

echo "========================================\n";
echo "    QEEFG授权站系统安装脚本\n";
echo "========================================\n\n";

// 检查PHP版本
echo "[1] 检查PHP版本...\n";
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    die("错误: PHP版本需要 >= 8.1，当前版本: " . PHP_VERSION . "\n");
}
echo "✓ PHP版本: " . PHP_VERSION . "\n\n";

// 检查扩展
echo "[2] 检查必需扩展...\n";
$requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'openssl'];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        die("错误: 缺少扩展 {$ext}\n");
    }
    echo "✓ {$ext}\n";
}
echo "\n";

// 检查.env文件
echo "[3] 检查配置文件...\n";
if (!file_exists(__DIR__ . '/.env')) {
    if (file_exists(__DIR__ . '/.env.example')) {
        copy(__DIR__ . '/.env.example', __DIR__ . '/.env');
        echo "✓ 已创建 .env 文件，请配置数据库信息后重新运行安装脚本\n";
        exit;
    } else {
        die("错误: 未找到 .env.example 文件\n");
    }
}
echo "✓ 配置文件存在\n\n";

// 检查目录权限
echo "[4] 检查目录权限...\n";
$dirs = [
    __DIR__ . '/runtime',
    __DIR__ . '/storage',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    if (!is_writable($dir)) {
        chmod($dir, 0755);
    }
    echo "✓ {$dir}\n";
}
echo "\n";

// 读取配置
echo "[5] 读取数据库配置...\n";
$envContent = file_get_contents(__DIR__ . '/.env');
$config = [];

preg_match('/DB_HOST\s*=\s*(.+)/', $envContent, $matches);
$config['hostname'] = trim($matches[1] ?? '127.0.0.1');

preg_match('/DB_NAME\s*=\s*(.+)/', $envContent, $matches);
$config['database'] = trim($matches[1] ?? 'qeefg_auth');

preg_match('/DB_USER\s*=\s*(.+)/', $envContent, $matches);
$config['username'] = trim($matches[1] ?? 'root');

preg_match('/DB_PASS\s*=\s*(.+)/', $envContent, $matches);
$config['password'] = trim($matches[1] ?? '');

preg_match('/DB_PORT\s*=\s*(.+)/', $envContent, $matches);
$config['hostport'] = trim($matches[1] ?? '3306');

echo "数据库名: {$config['database']}\n";
echo "数据库用户: {$config['username']}\n";
echo "数据库地址: {$config['hostname']}:{$config['hostport']}\n\n";

// 测试数据库连接
echo "[6] 测试数据库连接...\n";
try {
    $dsn = "mysql:host={$config['hostname']};port={$config['hostport']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ 数据库连接成功\n\n";
} catch (PDOException $e) {
    die("错误: 数据库连接失败 - " . $e->getMessage() . "\n");
}

// 创建数据库
echo "[7] 创建数据库...\n";
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $pdo->exec("USE `{$config['database']}`");
    echo "✓ 数据库已创建/选择\n\n";
} catch (PDOException $e) {
    die("错误: 创建数据库失败 - " . $e->getMessage() . "\n");
}

// 导入SQL
echo "[8] 导入数据表...\n";
$sqlFile = __DIR__ . '/install.sql';
if (!file_exists($sqlFile)) {
    die("错误: 未找到 install.sql 文件\n");
}

$sqlContent = file_get_contents($sqlFile);
$sqlStatements = array_filter(array_map('trim', explode(';', $sqlContent)));

try {
    $pdo->beginTransaction();
    foreach ($sqlStatements as $statement) {
        if (!empty($statement) && $statement !== ';') {
            $pdo->exec($statement);
        }
    }
    $pdo->commit();
    echo "✓ 数据表导入成功\n\n";
} catch (PDOException $e) {
    $pdo->rollBack();
    die("错误: 导入数据表失败 - " . $e->getMessage() . "\n");
}

// 安装完成
echo "========================================\n";
echo "    安装完成！\n";
echo "========================================\n\n";

echo "默认管理员账号:\n";
echo "用户名: admin\n";
echo "密码: password\n\n";

echo "请访问网站开始使用:\n";
echo "前台: http://your-domain.com/\n";
echo "后台: http://your-domain.com/admin/login\n\n";

echo "注意事项:\n";
echo "1. 请及时修改管理员密码\n";
echo "2. 生产环境请设置 APP_DEBUG = false\n";
echo "3. 建议使用HTTPS协议\n\n";