<?php
/**
 * 主站在线更新 / 授权验证 API
 * 对应主站 functions.php 中的 update_check_remote / update_apply_upgrade 调用
 */
class Api_License extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->layoutEnabled = false;
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
     * 校验签名
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

    /**
     * 授权校验 + 版本检查
     * POST /api/license/check
     */
    public function check()
    {
        $this->checkSign();

        $authCode = trim(input('auth_code', ''));
        $authDomain = trim(input('auth_domain', ''));
        $currentVersion = trim(input('current_version', ''));

        if (!$authCode) {
            $this->error('请输入授权码');
        }

        $license = Db::fetch(
            "SELECT l.*, p.name as product_name FROM qef_license l LEFT JOIN qef_product p ON l.product_id = p.id WHERE l.auth_code = ?",
            [$authCode]
        );

        $licenseValid = false;
        $licenseMsg = '';

        if (!$license) {
            $licenseMsg = '授权码不存在';
        } elseif ((int) $license['status'] !== 1) {
            $licenseMsg = '授权已被禁用';
        } elseif ($license['expire_time'] && $license['expire_time'] < date('Y-m-d H:i:s')) {
            $licenseMsg = '授权已过期';
        } else {
            if ($license['license_type'] === 'domain') {
                if (!$authDomain) {
                    $this->error('域名授权需传入 auth_domain 参数');
                }
                if (strtolower($license['auth_domain']) !== strtolower($authDomain)) {
                    $licenseMsg = '域名未授权';
                } else {
                    $licenseValid = true;
                }
            } else {
                $licenseValid = true;
            }
        }

        // 查询最新版本
        $version = Db::fetch("SELECT * FROM qef_version WHERE is_latest = 1 ORDER BY id DESC LIMIT 1");
        if (!$version) {
            $version = Db::fetch("SELECT * FROM qef_version ORDER BY id DESC LIMIT 1");
        }

        $hasUpdate = false;
        $latestVersion = $currentVersion;
        if ($version) {
            $latestVersion = $version['version'];
            $hasUpdate = version_compare($latestVersion, $currentVersion, '>');
        }

        $this->success('ok', [
            'license_valid' => $licenseValid,
            'license_msg' => $licenseMsg,
            'license_type' => $license['license_type'] ?? '',
            'auth_domain' => $license['auth_domain'] ?? '',
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion,
            'has_update' => $hasUpdate,
            'release_date' => $version['release_date'] ?? '',
            'update_desc' => $version['update_desc'] ?? '',
            'force_update' => (bool) ($version['force_update'] ?? 0),
        ]);
    }

    /**
     * 获取更新包下载地址
     * POST /api/license/download
     */
    public function download()
    {
        $this->checkSign();

        $authCode = trim(input('auth_code', ''));
        $authDomain = trim(input('auth_domain', ''));
        $version = trim(input('version', ''));

        if (!$authCode) {
            $this->error('请输入授权码');
        }
        if (!$version) {
            $this->error('请输入版本号');
        }

        $license = Db::fetch("SELECT * FROM qef_license WHERE auth_code = ?", [$authCode]);
        if (!$license) {
            $this->error('授权码不存在');
        }
        if ((int) $license['status'] !== 1) {
            $this->error('授权已被禁用');
        }
        if ($license['expire_time'] && $license['expire_time'] < date('Y-m-d H:i:s')) {
            $this->error('授权已过期');
        }
        if ($license['license_type'] === 'domain') {
            if (!$authDomain) {
                $this->error('域名授权需传入 auth_domain 参数');
            }
            if (strtolower($license['auth_domain']) !== strtolower($authDomain)) {
                $this->error('域名未授权');
            }
        }

        $versionRow = Db::fetch("SELECT * FROM qef_version WHERE version = ?", [$version]);
        if (!$versionRow) {
            $this->error('版本不存在');
        }

        $this->success('ok', [
            'download_url' => base_url('api/download?type=version&id=' . (int) $versionRow['id']),
            'file_md5' => $versionRow['file_md5'] ?? '',
            'file_size' => (int) ($versionRow['file_size'] ?? 0),
            'version' => $versionRow['version'],
        ]);
    }
}
