<?php
namespace Install\Controller;

use Framework\Controller;
use Framework\Response;

class IndexController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = dirname(__DIR__, 2) . '/';
    }

    public function index($request, $params = [])
    {
        $step = (int) $request->get('step', 1);
        $data = [
            'step' => $step,
            'env' => $this->checkEnv(),
            'success' => '',
            'error' => '',
        ];
        $this->view('Install.install.step' . $step, $data);
    }

    public function step1($request, $params = [])
    {
        if (!$request->isPost()) {
            return Response::redirect('/install?step=1');
        }
        return Response::json(['code' => 0, 'msg' => 'ok']);
    }

    public function step2($request, $params = [])
    {
        if (!$request->isPost()) {
            return Response::redirect('/install?step=2');
        }
        $dbHost = $request->post('db_host', '127.0.0.1');
        $dbPort = (int) $request->post('db_port', 3306);
        $dbName = $request->post('db_name', '');
        $dbUser = $request->post('db_user', '');
        $dbPass = $request->post('db_pass', '');

        try {
            $dsn = "mysql:host={$dbHost};port={$dbPort};charset=utf8mb4";
            $pdo = new \PDO($dsn, $dbUser, $dbPass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$dbName}`");

            $sqlFile = dirname(__DIR__, 2) . '/install/install.sql';
            $sql = file_get_contents($sqlFile);
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($statements as $s) {
                if ($s) {
                    $pdo->exec($s);
                }
            }

            $config = "<?php\nreturn [\n    'type' => 'mysql',\n    'hostname' => '" . addslashes($dbHost) . "',\n    'hostport' => {$dbPort},\n    'database' => '" . addslashes($dbName) . "',\n    'username' => '" . addslashes($dbUser) . "',\n    'password' => '" . addslashes($dbPass) . "',\n    'charset' => 'utf8mb4',\n    'prefix' => 'xw_',\n];\n";
            file_put_contents(dirname(__DIR__, 2) . '/config/database.php', $config);

            $adminUser = $request->post('admin_user', 'admin');
            $adminPass = $request->post('admin_pass', 'admin888');
            $hash = password_hash($adminPass, PASSWORD_DEFAULT);
            $pdo->exec("USE `{$dbName}`");
            $stmt = $pdo->prepare("INSERT INTO xw_admin (username, password, role, status, create_time) VALUES (?, ?, 'super', 1, ?)");
            $stmt->execute([$adminUser, $hash, date('Y-m-d H:i:s')]);

            @mkdir(dirname(__DIR__, 2) . '/runtime/cache', 0755, true);
            @mkdir(dirname(__DIR__, 2) . '/runtime/log', 0755, true);

            $lockFile = dirname(__DIR__, 2) . '/install/installed.lock';
            @file_put_contents($lockFile, date('Y-m-d H:i:s'));

            return Response::json(['code' => 0, 'msg' => '安装成功', 'data' => ['url' => '/admin/login']]);
        } catch (\Exception $e) {
            return Response::json(['code' => 1, 'msg' => '安装失败：' . $e->getMessage()]);
        }
    }

    public function step3($request, $params = [])
    {
        return Response::json(['code' => 0, 'msg' => 'ok']);
    }

    public function testDb($request, $params = [])
    {
        $host = $request->post('host', '127.0.0.1');
        $port = (int) $request->post('port', 3306);
        $user = $request->post('user', '');
        $pass = $request->post('pass', '');
        try {
            $pdo = new \PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass);
            return Response::json(['code' => 0, 'msg' => '连接成功']);
        } catch (\Exception $e) {
            return Response::json(['code' => 1, 'msg' => '连接失败：' . $e->getMessage()]);
        }
    }

    protected function checkEnv()
    {
        $items = [];
        $items['PHP >= 8.2'] = version_compare(PHP_VERSION, '8.2.0', '>=');
        $items['PDO 扩展'] = extension_loaded('pdo');
        $items['PDO_MySQL 扩展'] = extension_loaded('pdo_mysql');
        $items['mbstring 扩展'] = extension_loaded('mbstring');
        $items['JSON 扩展'] = extension_loaded('json');
        $items['openssl 扩展'] = extension_loaded('openssl');
        $items['session 扩展'] = extension_loaded('session');
        $items['curl 扩展'] = extension_loaded('curl');
        $items['config 可写'] = is_writable(dirname(__DIR__, 2) . '/config');
        $items['runtime 可写'] = is_writable(dirname(__DIR__, 2) . '/runtime') || @mkdir(dirname(__DIR__, 2) . '/runtime', 0755, true);
        $items['public 可写'] = is_writable(dirname(__DIR__, 2) . '/public');
        return $items;
    }
}
