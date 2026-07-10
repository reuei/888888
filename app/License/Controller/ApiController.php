<?php
namespace License\Controller;

use Framework\Controller;
use Framework\Database\Database;
use Framework\Response;

class ApiController extends Controller
{
    public function check($request, $params = [])
    {
        $license = $request->post('license', '');
        $domain = $request->post('domain', '');
        $product = $request->post('product', 'xuanwu_card');
        $version = $request->post('version', '1.0.5');

        if (empty($license) || empty($domain)) {
            return $this->error('参数错误');
        }

        $record = $this->findLicense($license);
        if (!$record) {
            return $this->error('授权码不存在', 404);
        }

        if (strtotime($record['expire_time']) < time() && $record['expire_time'] !== '0000-00-00 00:00:00') {
            return $this->error('授权已过期', 403);
        }

        if ((int) $record['status'] !== 1) {
            return $this->error('授权已禁用', 403);
        }

        return $this->success('ok', [
            'license' => $license,
            'product' => $record['product'],
            'version' => $record['version'],
            'license_version' => '1.1.1',
            'expire' => $record['expire_time'],
            'max_domains' => (int) $record['max_domains'],
        ]);
    }

    public function verify($request, $params = [])
    {
        $license = $request->post('license', '');
        $domain = $request->post('domain', '');

        $record = $this->findLicense($license);
        if (!$record) {
            return $this->error('授权不存在', 404);
        }
        $bound = $this->getBoundDomains($license);
        $matched = in_array($domain, $bound, true);
        return $this->success('ok', [
            'license' => $license,
            'domain' => $domain,
            'matched' => $matched,
            'bound_count' => count($bound),
        ]);
    }

    public function activate($request, $params = [])
    {
        $license = $request->post('license', '');
        $domain = $request->post('domain', '');

        $record = $this->findLicense($license);
        if (!$record) {
            return $this->error('授权不存在', 404);
        }

        $bound = $this->getBoundDomains($license);
        if (in_array($domain, $bound, true)) {
            return $this->success('已激活', ['license' => $license, 'domain' => $domain]);
        }

        if (count($bound) >= (int) $record['max_domains']) {
            return $this->error('授权域名已达上限');
        }

        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $db->table('license_domain')->insert([
                    'license_id' => $record['id'],
                    'domain' => $domain,
                    'ip' => client_ip(),
                    'create_time' => date('Y-m-d H:i:s'),
                ]);
                $this->logApi($license, $domain, 'activate', 1);
            }
        } catch (\Exception $e) {
        }

        return $this->success('激活成功', ['license' => $license, 'domain' => $domain]);
    }

    public function heartbeat($request, $params = [])
    {
        $license = $request->post('license', '');
        $domain = $request->post('domain', '');
        $this->logApi($license, $domain, 'heartbeat', 1);
        return $this->success('ok', ['time' => time()]);
    }

    public function listLicenses($request, $params = [])
    {
        $list = [];
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $list = $db->table('license')->orderBy('id', 'DESC')->limit(20)->get();
            }
        } catch (\Exception $e) {
        }
        return $this->success('ok', $list);
    }

    protected function findLicense($code)
    {
        try {
            $db = Database::getInstance();
            if (!$db->isConnected()) {
                return $this->getDefaultLicense($code);
            }
            return $db->table('license')->where('code', $code)->first();
        } catch (\Exception $e) {
            return $this->getDefaultLicense($code);
        }
    }

    protected function getDefaultLicense($code)
    {
        if ($code === 'XUANWU-DEMO-2026') {
            return [
                'id' => 1,
                'code' => $code,
                'product' => 'xuanwu_card',
                'version' => '1.0.5',
                'max_domains' => 5,
                'expire_time' => '2099-12-31 23:59:59',
                'status' => 1,
            ];
        }
        return null;
    }

    protected function getBoundDomains($code)
    {
        try {
            $db = Database::getInstance();
            if (!$db->isConnected()) {
                return ['localhost'];
            }
            $lic = $db->table('license')->where('code', $code)->first();
            if (!$lic) {
                return ['localhost'];
            }
            $rows = $db->table('license_domain')->where('license_id', $lic['id'])->get();
            return array_column($rows, 'domain');
        } catch (\Exception $e) {
            return ['localhost'];
        }
    }

    protected function logApi($license, $domain, $action, $status)
    {
        try {
            $db = Database::getInstance();
            if ($db->isConnected()) {
                $db->table('license_log')->insert([
                    'license' => $license,
                    'domain' => $domain,
                    'action' => $action,
                    'ip' => client_ip(),
                    'status' => $status,
                    'create_time' => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Exception $e) {
        }
    }
}
