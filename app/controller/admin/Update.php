<?php
/**
 * Migrated from main_legacy/controller/app/controller/admin/Update.php
 */
namespace app\controller\admin;

/**
 * 在线更新与授权管理
 */
class Update extends Controller
{
    public function __construct()
    {
        parent::__construct();
        check_admin_role(['super', 'admin']);
    }

    /**
     * 更新与授权状态页
     */
    public function index()
    {
        $currentVersion = Config::get('app.app_version', '1.0.0');
        $license = $this->getLicenseConfig();

        // 检查授权与更新信息
        $remoteInfo = null;
        $error = '';
        try {
            $remoteInfo = update_check_remote($license);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        $this->assign('title', '在线更新');
        $this->assign('currentVersion', $currentVersion);
        $this->assign('license', $license);
        $this->assign('remoteInfo', $remoteInfo);
        $this->assign('error', $error);
        $this->fetch('admin/update/index');
    }

    /**
     * 保存授权配置
     */
    public function saveLicense()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_error('请求方式错误');
        }

        $authCode = trim(input('auth_code', ''));
        $authDomain = trim(input('auth_domain', ''));
        $apiUrl = trim(input('api_url', ''));
        $apiKey = trim(input('api_key', ''));

        if (!$authCode) {
            json_error('授权码不能为空');
        }
        if (!$apiUrl) {
            json_error('授权站地址不能为空');
        }

        $config = [
            'auth_code' => $authCode,
            'auth_domain' => $authDomain ?: ($_SERVER['HTTP_HOST'] ?? ''),
            'api_url' => rtrim($apiUrl, '/'),
            'api_key' => $apiKey,
            'update_time' => date('Y-m-d H:i:s'),
        ];

        $file = APP_PATH . 'config' . DIRECTORY_SEPARATOR . 'license.php';
        $content = "<?php\nreturn " . var_export($config, true) . ";\n";
        if (file_put_contents($file, $content) === false) {
            json_error('授权配置文件写入失败');
        }

        // 立即验证一次
        try {
            $result = update_check_remote($config);
            if (isset($result['license_valid']) && !$result['license_valid']) {
                json_error('授权验证未通过：' . ($result['license_msg'] ?? '未知原因'));
            }
            admin_log('更新授权配置', ['api_url' => $apiUrl, 'auth_domain' => $config['auth_domain']]);
            json_success('保存并验证成功', $result);
        } catch (\Exception $e) {
            json_error('配置已保存，但授权站验证失败：' . $e->getMessage());
        }
    }

    /**
     * 执行更新检查
     */
    public function check()
    {
        try {
            $info = update_check_remote($this->getLicenseConfig());
            json_success('检查成功', $info);
        } catch (\Exception $e) {
            json_error($e->getMessage());
        }
    }

    /**
     * 下载并应用更新包
     */
    public function upgrade()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            json_error('请求方式错误');
        }

        $version = input('version', '');
        if (!$version) {
            json_error('版本号不能为空');
        }

        try {
            $result = update_apply_upgrade($version, $this->getLicenseConfig());
            admin_log('系统在线更新', ['version' => $version, 'result' => $result]);
            json_success('更新成功', $result);
        } catch (\Exception $e) {
            json_error('更新失败：' . $e->getMessage());
        }
    }

    /**
     * 读取本地授权配置
     */
    private function getLicenseConfig()
    {
        $file = APP_PATH . 'config' . DIRECTORY_SEPARATOR . 'license.php';
        if (is_file($file)) {
            return array_merge([
                'auth_code' => '',
                'auth_domain' => '',
                'api_url' => '',
                'api_key' => '',
            ], require $file);
        }
        return [
            'auth_code' => '',
            'auth_domain' => '',
            'api_url' => '',
            'api_key' => '',
        ];
    }
}
