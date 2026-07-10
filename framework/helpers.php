<?php
/**
 * 框架全局辅助函数
 */

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        $app = \Framework\App::getInstance();
        $config = $app->getConfig();
        if ($key === null) {
            return $config;
        }
        return $config[$key] ?? $default;
    }
}

if (!function_exists('url')) {
    function url($path = '/')
    {
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset($path)
    {
        return '/static/' . ltrim($path, '/');
    }
}

if (!function_exists('h')) {
    function h($str)
    {
        return htmlspecialchars((string) $str, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('db')) {
    function db()
    {
        return \Framework\App::db();
    }
}

if (!function_exists('cache')) {
    function cache()
    {
        return \Framework\App::cache();
    }
}

if (!function_exists('session')) {
    function session()
    {
        return \Framework\Session::class;
    }
}

if (!function_exists('random_str')) {
    function random_str($length = 16)
    {
        return bin2hex(random_bytes((int) ceil($length / 2)));
    }
}

if (!function_exists('order_no')) {
    function order_no()
    {
        return date('YmdHis') . mt_rand(100000, 999999);
    }
}

if (!function_exists('format_money')) {
    function format_money($amount)
    {
        return number_format((float) $amount, 2, '.', '');
    }
}

if (!function_exists('format_time')) {
    function format_time($timestamp, $format = 'Y-m-d H:i:s')
    {
        if (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        }
        if (!$timestamp) {
            return '-';
        }
        return date($format, (int) $timestamp);
    }
}

if (!function_exists('client_ip')) {
    function client_ip()
    {
        foreach (['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $k) {
            if (!empty($_SERVER[$k])) {
                $ip = trim(explode(',', $_SERVER[$k])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }
}

if (!function_exists('json_out')) {
    function json_out($data, $code = 0, $msg = 'success')
    {
        \Framework\Response::json($data, $code, $msg);
    }
}

if (!function_exists('verify_license')) {
    function verify_license($license = null)
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $cacheKey = 'license_' . md5($host);
        $cached = cache()->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }
        $apiUrl = config('license.api_url', 'https://license.xuanwu.com');
        $license = $license ?? config('license.key', '');
        $result = ['valid' => true, 'expire' => '永久', 'version' => '1.0.5'];
        try {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $apiUrl . '/api/verify',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query([
                    'license' => $license,
                    'domain' => $host,
                    'version' => '1.0.5',
                    'product' => 'xuanwu_card',
                ]),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (is_array($data) && isset($data['code']) && $data['code'] === 0) {
                    $result = array_merge($result, $data['data'] ?? []);
                }
            }
        } catch (\Exception $e) {
            // 网络异常时默认通过
        }
        cache()->set($cacheKey, $result, 3600);
        return $result;
    }
}
