<?php
/**
 * 主站消费用公开 API
 * 所有接口需校验签名 sign（HMAC-SHA256）
 */
class Api extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->layoutEnabled = false;
    }

    /**
     * 统一签名校验
     */
    private function checkSign()
    {
        $params = $_POST;
        if (empty($params['sign'])) {
            $this->error('缺少签名');
        }
        if (empty(api_key())) {
            $this->error('API 密钥未配置');
        }
        if (!verify_api_sign($params)) {
            $this->error('签名错误');
        }
    }

    private function success($msg = 'ok', $data = [])
    {
        json_success($msg, $data);
    }

    private function error($msg = 'error', $code = 1)
    {
        json_error($msg, $code);
    }

    /**
     * 授权校验
     * 参数：auth_code | license_type(code/domain) | domain(可选)
     */
    public function check()
    {
        $this->checkSign();
        $authCode = trim(input('auth_code', ''));
        $licenseType = input('license_type', 'code');
        $domain = trim(input('domain', ''));

        if (!$authCode) {
            $this->error('请输入授权码');
        }

        $license = Db::fetch(
            "SELECT l.*, p.name as product_name FROM qef_license l LEFT JOIN qef_product p ON l.product_id = p.id WHERE l.auth_code = ?",
            [$authCode]
        );

        if (!$license) {
            $this->success('授权无效', ['valid' => false, 'reason' => '授权码不存在']);
        }

        if ($license['status'] != 1) {
            $this->success('授权无效', ['valid' => false, 'reason' => '授权已被禁用']);
        }

        if ($license['license_type'] !== $licenseType) {
            $this->success('授权无效', ['valid' => false, 'reason' => '授权类型不匹配']);
        }

        if ($license['expire_time'] && $license['expire_time'] < date('Y-m-d H:i:s')) {
            $this->success('授权无效', ['valid' => false, 'reason' => '授权已过期']);
        }

        if ($license['license_type'] === 'domain') {
            if (!$domain) {
                $this->error('域名授权需传入 domain 参数');
            }
            if (strtolower($license['auth_domain']) !== strtolower($domain)) {
                $this->success('授权无效', ['valid' => false, 'reason' => '域名未授权']);
            }
        }

        $this->success('授权有效', [
            'valid' => true,
            'auth_code' => $license['auth_code'],
            'product_name' => $license['product_name'],
            'license_type' => $license['license_type'],
            'expire_time' => $license['expire_time'],
            'domain' => $license['auth_domain'],
        ]);
    }

    /**
     * 查询最新版本信息
     */
    public function latestVersion()
    {
        $this->checkSign();
        $version = Db::fetch("SELECT * FROM qef_version WHERE is_latest = 1 ORDER BY id DESC LIMIT 1");
        if (!$version) {
            $version = Db::fetch("SELECT * FROM qef_version ORDER BY id DESC LIMIT 1");
        }
        if (!$version) {
            $this->success('暂无版本', ['has_new' => false]);
        }

        $this->success('ok', [
            'has_new' => true,
            'version' => $version['version'],
            'file_md5' => $version['file_md5'],
            'file_size' => (int) $version['file_size'],
            'release_date' => $version['release_date'],
            'update_desc' => $version['update_desc'],
            'force_update' => (int) $version['force_update'],
            'download_url' => base_url('api/download?type=version&id=' . $version['id']),
        ]);
    }

    /**
     * 插件下载地址（返回一次性 token）
     */
    public function pluginDownload()
    {
        $this->checkSign();
        $pluginId = (int) input('plugin_id', 0);
        $userId = (int) input('user_id', 0);

        $plugin = Db::fetch("SELECT * FROM qef_plugin WHERE id = ? AND status = 1", [$pluginId]);
        if (!$plugin) {
            $this->error('插件不存在或已下架');
        }

        $owned = Db::fetch("SELECT id FROM qef_user_plugin WHERE user_id = ? AND plugin_id = ?", [$userId, $pluginId]);
        if (!$owned && (float) $plugin['price'] > 0) {
            $this->error('未购买该插件');
        }

        $token = download_token($plugin['file_path']);
        $this->success('ok', [
            'download_url' => base_url('api/download?type=plugin&token=' . urlencode($token)),
            'file_md5' => $plugin['file_md5'],
            'version' => $plugin['version'],
        ]);
    }

    /**
     * 通用下载入口（验证 token 后输出文件）
     */
    public function download()
    {
        $type = input('type', '');
        $token = input('token', '');
        $id = (int) input('id', 0);

        $filePath = '';
        if ($type === 'plugin') {
            $filePath = verify_download_token($token);
            if (!$filePath) {
                $this->error('下载链接无效或已过期');
            }
        } elseif ($type === 'version') {
            $version = Db::fetch("SELECT file_path FROM qef_version WHERE id = ?", [$id]);
            if (!$version) {
                $this->error('版本不存在');
            }
            $filePath = $version['file_path'];
        } else {
            $this->error('下载类型错误');
        }

        $fullPath = ROOT_PATH . 'public' . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $filePath);
        if (!is_file($fullPath)) {
            $this->error('文件不存在');
        }

        $filename = basename($fullPath);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;
    }

    /**
     * 批量校验授权（逗号分隔 auth_code）
     */
    public function verifyBatch()
    {
        $this->checkSign();
        $codes = input('auth_codes', '');
        $codes = array_filter(array_map('trim', explode(',', $codes)));
        if (empty($codes)) {
            $this->error('请输入授权码');
        }

        $placeholders = implode(',', array_fill(0, count($codes), '?'));
        $rows = Db::query("SELECT auth_code, status, license_type, auth_domain, expire_time FROM qef_license WHERE auth_code IN ({$placeholders})", $codes);

        $map = [];
        foreach ($rows as $row) {
            $valid = $row['status'] == 1 && (!$row['expire_time'] || $row['expire_time'] >= date('Y-m-d H:i:s'));
            $map[$row['auth_code']] = [
                'valid' => $valid,
                'license_type' => $row['license_type'],
                'domain' => $row['auth_domain'],
                'expire_time' => $row['expire_time'],
            ];
        }

        $result = [];
        foreach ($codes as $code) {
            $result[$code] = $map[$code] ?? ['valid' => false, 'reason' => '授权码不存在'];
        }

        $this->success('ok', ['result' => $result]);
    }
}
