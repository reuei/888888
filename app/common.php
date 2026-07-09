<?php
use think\facade\Db;
use think\facade\Cache;
use think\facade\Session;

if (!function_exists('h')) {
    function h($str)
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('site_config')) {
    function site_config($key = null, $default = null)
    {
        static $config = null;
        if ($config === null) {
            try {
                $config = Cache::get('site_config');
                if (!$config) {
                    $list = Db::name('config')->column('cfg_value', 'cfg_key');
                    $config = $list ?: [];
                    Cache::set('site_config', $config, 3600);
                }
            } catch (\Exception $e) {
                $config = [];
            }
        }
        if ($key === null) return $config;
        return $config[$key] ?? $default;
    }
}

if (!function_exists('format_money')) {
    function format_money($amount)
    {
        return number_format($amount, 2, '.', '');
    }
}

if (!function_exists('format_date')) {
    function format_date($time, $format = 'Y-m-d H:i:s')
    {
        if (is_numeric($time)) {
            return date($format, $time);
        }
        return date($format, strtotime($time));
    }
}

if (!function_exists('generate_order_no')) {
    function generate_order_no()
    {
        return date('YmdHis') . rand(1000, 9999) . mt_rand(100, 999);
    }
}

if (!function_exists('update_check_remote')) {
    function update_check_remote()
    {
        $currentVersion = config('app.app_version');
        $apiUrl = config('license.api_url', 'https://qeefg.duziemd.cn');
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 10, 'verify' => false]);
            $response = $client->post($apiUrl . '/api/license/check_update', [
                'form_params' => [
                    'version' => $currentVersion,
                    'domain' => request()->host(),
                    'license_code' => config('license.license_code', ''),
                ]
            ]);
            $result = json_decode($response->getBody(), true);
            if ($result && isset($result['code']) && $result['code'] == 0) {
                return $result['data'];
            }
        } catch (\Exception $e) {
            // 静默失败
        }
        return ['has_update' => false, 'latest_version' => $currentVersion, 'update_url' => ''];
    }
}

if (!function_exists('update_apply_upgrade')) {
    function update_apply_upgrade($packageUrl)
    {
        try {
            $backupDir = root_path() . 'backup_' . date('YmdHis');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            $updateDir = root_path() . 'update_tmp';
            if (is_dir($updateDir)) {
                del_dir($updateDir);
            }
            mkdir($updateDir, 0755, true);
            
            $zipFile = $updateDir . '/update.zip';
            file_put_contents($zipFile, file_get_contents($packageUrl));
            
            $zip = new \ZipArchive();
            if ($zip->open($zipFile) === true) {
                $zip->extractTo($updateDir);
                $zip->close();
                
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($updateDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::SELF_FIRST
                );
                
                foreach ($files as $file) {
                    $relativePath = $file->getFilename();
                    if ($file->isDir()) continue;
                    $destPath = root_path() . $relativePath;
                    if (file_exists($destPath)) {
                        copy($destPath, $backupDir . '/' . $relativePath);
                    }
                    @copy($file->getPathname(), $destPath);
                }
                
                del_dir($updateDir);
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }
}

if (!function_exists('del_dir')) {
    function del_dir($dir)
    {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? del_dir($path) : unlink($path);
        }
        rmdir($dir);
    }
}

if (!function_exists('slider_captcha_verify')) {
    function slider_captcha_verify($token, $x)
    {
        $key = 'slider_' . $token;
        $stored = Cache::get($key);
        if (!$stored) return false;
        $diff = abs($x - $stored['x']);
        Cache::delete($key);
        return $diff <= 5;
    }
}

if (!function_exists('slider_captcha_generate')) {
    function slider_captcha_generate()
    {
        $token = md5(uniqid() . rand(1000, 9999));
        $x = rand(50, 250);
        $y = rand(20, 120);
        Cache::set('slider_' . $token, ['x' => $x, 'y' => $y], 300);
        return ['token' => $token, 'x' => $x, 'y' => $y];
    }
}
