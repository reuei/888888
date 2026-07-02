<?php

use app\Db;
use think\facade\Request;
use think\facade\Session;
use think\facade\Config;

if (!function_exists('url')) {
    function url($path = '', $params = [])
    {
        $base = '/';
        if ($path) {
            $base .= ltrim($path, '/');
        }
        if (!empty($params)) {
            $base .= '?' . http_build_query($params);
        }
        return $base;
    }
}

if (!function_exists('base_url')) {
    function base_url($path = '')
    {
        $scheme = Request::scheme();
        $host = Request::host();
        return $scheme . '://' . $host . '/' . ltrim($path, '/');
    }
}

if (!function_exists('current_url')) {
    function current_url()
    {
        return Request::url(true);
    }
}

if (!function_exists('input')) {
    function input($key = null, $default = null)
    {
        if ($key === null) {
            return Request::param();
        }
        return Request::param($key, $default);
    }
}

if (!function_exists('session')) {
    function session($key = null, $value = null)
    {
        if ($key === null) {
            return Session::all();
        }
        if ($value === null) {
            return Session::get($key);
        }
        Session::set($key, $value);
    }
}

if (!function_exists('json_success')) {
    function json_success($msg = '操作成功', $data = [], $code = 0)
    {
        throw new \think\exception\HttpResponseException(json(['code' => $code, 'msg' => $msg, 'data' => $data]));
    }
}

if (!function_exists('json_error')) {
    function json_error($msg = '操作失败', $code = 1, $data = [])
    {
        throw new \think\exception\HttpResponseException(json(['code' => $code, 'msg' => $msg, 'data' => $data]));
    }
}

if (!function_exists('redirect')) {
    function redirect($url)
    {
        throw new \think\exception\HttpResponseException(\think\Response::create($url, 'redirect', 302));
    }
}

if (!function_exists('h')) {
    function h($str)
    {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('password_hash_custom')) {
    function password_hash_custom($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}

if (!function_exists('password_verify_custom')) {
    function password_verify_custom($password, $hash)
    {
        return password_verify($password, $hash);
    }
}

if (!function_exists('generate_token')) {
    function generate_token($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }
}

if (!function_exists('admin_log')) {
    function admin_log($action, $content = '')
    {
        try {
            $admin = session('admin_user') ?? [];
            Db::insert('qef_admin_log', [
                'admin_id' => $admin['id'] ?? 0,
                'admin_name' => $admin['username'] ?? '',
                'action' => $action,
                'content' => is_array($content) || is_object($content) ? json_encode($content, JSON_UNESCAPED_UNICODE) : (string) $content,
                'ip' => Request::ip(),
                'create_time' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
        }
    }
}

if (!function_exists('get_admin_user')) {
    function get_admin_user()
    {
        return session('admin_user');
    }
}

if (!function_exists('require_admin_login')) {
    function require_admin_login()
    {
        if (empty(session('admin_user'))) {
            throw new \think\exception\HttpException(302, '请先登录', null, ['Location' => url('admin/admin')]);
        }
    }
}

if (!function_exists('require_user_login')) {
    function require_user_login()
    {
        if (empty(session('user'))) {
            throw new \think\exception\HttpException(302, '请先登录', null, ['Location' => url('login')]);
        }
    }
}

if (!function_exists('get_user')) {
    function get_user()
    {
        return session('user');
    }
}

if (!function_exists('site_config')) {
    function site_config($key = null, $default = null)
    {
        static $config = null;
        if ($config === null) {
            $config = [];
            try {
                $rows = Db::query("SELECT cfg_key, cfg_value FROM qef_config WHERE cfg_group = 'base'");
                foreach ($rows as $row) {
                    $config[$row['cfg_key']] = $row['cfg_value'];
                }
            } catch (\Throwable $e) {
            }
        }

        if ($key === null) {
            return $config;
        }

        $defaults = [
            'site_name' => 'QEEFG 寄售系统售卖网站',
            'currency_unit' => '¥',
            'copyright' => 'QEEFG v1.0.0',
        ];

        if (!isset($config[$key]) && isset($defaults[$key])) {
            return $defaults[$key];
        }

        return $config[$key] ?? $default;
    }
}

if (!function_exists('pagination')) {
    function pagination($total, $page, $pageSize, $urlTemplate)
    {
        $total = max(0, (int) $total);
        $page = max(1, (int) $page);
        $pageSize = max(1, (int) $pageSize);
        $totalPages = max(1, (int) ceil($total / $pageSize));
        $page = min($page, $totalPages);

        $html = '<div class="pagination">';
        if ($page > 1) {
            $html .= '<a href="' . str_replace('{page}', $page - 1, $urlTemplate) . '" class="btn btn-sm btn-outline">上一页</a>';
        }
        $html .= '<span class="page-info">第 ' . $page . ' / ' . $totalPages . ' 页，共 ' . $total . ' 条</span>';
        if ($page < $totalPages) {
            $html .= '<a href="' . str_replace('{page}', $page + 1, $urlTemplate) . '" class="btn btn-sm btn-outline">下一页</a>';
        }
        $html .= '</div>';
        return $html;
    }
}

if (!function_exists('get_client_ip')) {
    function get_client_ip()
    {
        return Request::ip();
    }
}

if (!function_exists('upload_file')) {
    function upload_file($field, $subDir = '', $allowedExt = ['jpg', 'jpeg', 'png', 'gif'])
    {
        $file = Request::file($field);
        if (!$file) {
            return ['code' => 1, 'msg' => '文件上传失败'];
        }

        $ext = strtolower($file->extension());
        if (!in_array($ext, $allowedExt, true)) {
            return ['code' => 1, 'msg' => '不支持的文件格式'];
        }

        $maxSize = 20 * 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            return ['code' => 1, 'msg' => '文件大小不能超过20MB'];
        }

        $safeSubDir = '';
        if ($subDir) {
            $parts = explode('/', trim(str_replace('\\', '/', $subDir), '/'));
            $safeParts = [];
            foreach ($parts as $part) {
                $part = trim($part);
                if ($part === '' || $part === '.' || $part === '..') {
                    continue;
                }
                $safeParts[] = $part;
            }
            $safeSubDir = implode(DIRECTORY_SEPARATOR, $safeParts);
        }

        $uploadRoot = public_path() . 'uploads' . DIRECTORY_SEPARATOR;
        $targetDir = $uploadRoot . ($safeSubDir ? $safeSubDir . DIRECTORY_SEPARATOR : '');
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $filename = date('Ymd') . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $targetPath = $targetDir . $filename;

        if (!$file->move($targetDir, $filename)) {
            return ['code' => 1, 'msg' => '文件保存失败'];
        }

        $relative = 'uploads/' . ($safeSubDir ? str_replace('\\', '/', $safeSubDir) . '/' : '') . $filename;
        return ['code' => 0, 'path' => $relative, 'url' => base_url($relative)];
    }
}

if (!function_exists('upload_zip')) {
    function upload_zip($field, $subDir = '')
    {
        return upload_file($field, $subDir, ['zip']);
    }
}

if (!function_exists('generate_order_no')) {
    function generate_order_no()
    {
        return 'QEF' . date('YmdHis') . random_int(1000, 9999);
    }
}

if (!function_exists('generate_auth_code')) {
    function generate_auth_code()
    {
        return strtoupper(substr(bin2hex(random_bytes(16)), 0, 24));
    }
}

if (!function_exists('api_key')) {
    function api_key()
    {
        return site_config('api_key', '');
    }
}

if (!function_exists('api_sign')) {
    function api_sign(array $params, $key = null)
    {
        unset($params['sign']);
        ksort($params);
        $str = http_build_query($params);
        return hash_hmac('sha256', $str, $key ?: api_key());
    }
}

if (!function_exists('verify_api_sign')) {
    function verify_api_sign(array $params, $key = null)
    {
        $sign = $params['sign'] ?? '';
        $expected = api_sign($params, $key);
        return hash_equals(strtolower($expected), strtolower($sign));
    }
}

if (!function_exists('format_price')) {
    function format_price($price)
    {
        return site_config('currency_unit', '¥') . number_format((float) $price, 2);
    }
}

if (!function_exists('status_text')) {
    function status_text($status, $map)
    {
        return $map[$status] ?? '未知';
    }
}

if (!function_exists('format_size')) {
    function format_size($size)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        $size = max(0, (int) $size);
        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }
        return round($size, 2) . ' ' . $units[$unitIndex];
    }
}

if (!function_exists('download_token')) {
    function download_token($file, $expires = 3600)
    {
        $expiresAt = time() + $expires;
        $data = $file . '|' . $expiresAt;
        $sign = hash_hmac('sha256', $data, api_key());
        return base64_encode($data . '|' . $sign);
    }
}

if (!function_exists('verify_download_token')) {
    function verify_download_token($token)
    {
        $raw = base64_decode($token, true);
        if (!$raw || substr_count($raw, '|') !== 2) {
            return false;
        }
        [$file, $expiresAt, $sign] = explode('|', $raw);
        $expected = hash_hmac('sha256', $file . '|' . $expiresAt, api_key());
        if (!hash_equals($expected, $sign)) {
            return false;
        }
        if ((int) $expiresAt < time()) {
            return false;
        }
        return $file;
    }
}
