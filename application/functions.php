<?php
/**
 * 全局辅助函数
 */

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

function base_url($path = '')
{
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = $scheme . '://' . $host;
    return $base . '/' . ltrim($path, '/');
}

function input($key = null, $default = null)
{
    if ($key === null) {
        return $_REQUEST;
    }
    return $_REQUEST[$key] ?? $default;
}

function session($key = null, $value = null)
{
    if ($key === null) {
        return $_SESSION;
    }
    if ($value === null) {
        return $_SESSION[$key] ?? null;
    }
    $_SESSION[$key] = $value;
}

function json_success($msg = '操作成功', $data = [], $code = 0)
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => $code, 'msg' => $msg, 'data' => $data]);
    exit;
}

function json_error($msg = '操作失败', $code = 1, $data = [])
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => $code, 'msg' => $msg, 'data' => $data]);
    exit;
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function h($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function password_hash_custom($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function password_verify_custom($password, $hash)
{
    return password_verify($password, $hash);
}

function generate_token($length = 32)
{
    return bin2hex(random_bytes($length / 2));
}

/**
 * 记录管理员操作日志
 */
function admin_log($action, $content = '')
{
    try {
        $admin = session('admin_user') ?? [];
        Db::insert('jz_admin_log', [
            'admin_id' => $admin['id'] ?? 0,
            'admin_name' => $admin['username'] ?? '',
            'action' => $action,
            'content' => is_array($content) || is_object($content) ? json_encode($content, JSON_UNESCAPED_UNICODE) : (string) $content,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'create_time' => date('Y-m-d H:i:s'),
        ]);
    } catch (Exception $e) {
        // 忽略日志写入失败，避免影响主流程
    }
}

/**
 * 读取站点配置
 */
function site_config($key = null, $default = null)
{
    static $config = null;
    if ($config === null) {
        $config = [];
        try {
            $rows = Db::query("SELECT cfg_key, cfg_value FROM jz_config WHERE cfg_group = 'base'");
            foreach ($rows as $row) {
                $shortKey = substr($row['cfg_key'], 5);
                $config[$shortKey] = $row['cfg_value'];
            }
        } catch (Exception $e) {
            // 安装前或未初始化时忽略
        }
    }

    if ($key === null) {
        return $config;
    }

    $defaults = [
        'site_name' => '鲸商城 Pro',
        'copyright' => '鲸商城 Pro v1.0.0',
    ];

    if (!isset($config[$key]) && isset($defaults[$key])) {
        return $defaults[$key];
    }

    return $config[$key] ?? $default;
}

/**
 * 识别当前访问的分站
 * 优先级：URL 参数 subsite > 二级域名前缀 > session 缓存
 * 返回分站数组，未识别返回 null
 */
function current_subsite()
{
    static $subsite = false;
    if ($subsite !== false) {
        return $subsite;
    }

    // 显式返回总站
    if (input('clear_subsite', '') === '1') {
        clear_current_subsite();
        $subsite = null;
        return $subsite;
    }

    // 调试/指定分站
    $subsiteParam = input('subsite', '');
    if ($subsiteParam) {
        $subsite = Db::fetch("SELECT * FROM jz_subsite WHERE domain_prefix = ? AND status = 1", [$subsiteParam]);
        if ($subsite) {
            session('current_subsite', $subsite);
            return $subsite;
        }
    }

    // 通过二级域名识别，例如 sub1.example.com
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $mainHost = $_SERVER['SERVER_NAME'] ?? $host;
    if ($host && $mainHost && $host !== $mainHost && strpos($host, $mainHost) !== false) {
        $prefix = trim(str_replace('.' . $mainHost, '', $host), '.');
        if ($prefix && $prefix !== 'www') {
            $subsite = Db::fetch("SELECT * FROM jz_subsite WHERE domain_prefix = ? AND status = 1", [$prefix]);
            if ($subsite) {
                session('current_subsite', $subsite);
                return $subsite;
            }
        }
    }

    // 尝试从 session 恢复
    $sessionSubsite = session('current_subsite');
    if ($sessionSubsite && is_array($sessionSubsite)) {
        $subsite = $sessionSubsite;
        return $subsite;
    }

    $subsite = null;
    return $subsite;
}

/**
 * 清空当前分站缓存（用于总站页面）
 */
function clear_current_subsite()
{
    unset($_SESSION['current_subsite']);
}

/**
 * 获取客户端 IP
 */
function get_client_ip()
{
    $keys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip = trim(strtok($_SERVER[$key], ','));
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
            return $ip;
        }
    }
    return '0.0.0.0';
}

/**
 * 读取安全配置
 */
function get_security_config()
{
    static $config = null;
    if ($config !== null) {
        return $config;
    }

    $defaults = [
        'security_admin_2fa' => '0',
        'security_merchant_2fa' => '0',
        'security_login_fail_limit' => '5',
        'security_login_lock_minutes' => '30',
        'security_captcha_login' => '0',
        'security_captcha_join' => '0',
    ];

    $config = [];
    try {
        $rows = Db::query("SELECT cfg_key, cfg_value FROM jz_config WHERE cfg_group = 'security'");
        foreach ($rows as $row) {
            $config[$row['cfg_key']] = $row['cfg_value'];
        }
    } catch (Exception $e) {
        // 安装前忽略
    }

    $config = array_merge($defaults, $config);
    return $config;
}

/**
 * 检查 IP 是否在黑名单
 */
function is_ip_blacklisted($ip = null)
{
    $ip = $ip ?: get_client_ip();
    if ($ip === '0.0.0.0') {
        return false;
    }

    try {
        $list = Db::query(
            "SELECT ip FROM jz_ip_blacklist WHERE status = 1 AND (expire_time IS NULL OR expire_time > ?)",
            [date('Y-m-d H:i:s')]
        );
        foreach ($list as $item) {
            $pattern = '/^' . str_replace(['.', '%', '*'], ['\\.', '.*', '.*'], $item['ip']) . '$/';
            if (preg_match($pattern, $ip)) {
                return true;
            }
        }
    } catch (Exception $e) {
        return false;
    }

    return false;
}

/**
 * 记录登录尝试
 */
function record_login_attempt($username, $type, $status, $remark = '')
{
    try {
        Db::insert('jz_login_log', [
            'username' => $username,
            'type' => $type,
            'ip' => get_client_ip(),
            'status' => $status ? 1 : 0,
            'remark' => $remark,
            'create_time' => date('Y-m-d H:i:s'),
        ]);
    } catch (Exception $e) {
        // 忽略日志写入失败
    }
}

/**
 * 检查账号/IP 是否处于登录锁定状态
 */
function is_login_locked($username, $ip = null, $type = 'admin')
{
    $config = get_security_config();
    $limit = (int) ($config['security_login_fail_limit'] ?? 5);
    $minutes = (int) ($config['security_login_lock_minutes'] ?? 30);

    if ($limit <= 0 || $minutes <= 0) {
        return false;
    }

    $ip = $ip ?: get_client_ip();
    $lockTime = date('Y-m-d H:i:s', strtotime("-{$minutes} minute"));

    try {
        // 按账号统计
        $accountFail = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_login_log
             WHERE username = ? AND type = ? AND status = 0 AND create_time > ?",
            [$username, $type, $lockTime]
        );
        if ((int) ($accountFail['total'] ?? 0) >= $limit) {
            return true;
        }

        // 按 IP 统计
        $ipFail = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_login_log
             WHERE ip = ? AND type = ? AND status = 0 AND create_time > ?",
            [$ip, $type, $lockTime]
        );
        if ((int) ($ipFail['total'] ?? 0) >= $limit * 2) {
            return true;
        }
    } catch (Exception $e) {
        return false;
    }

    return false;
}

/**
 * 生成并输出图形验证码
 */
function captcha_output($key = 'captcha')
{
    $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));
    session('captcha_' . $key, $code);

    $width = 120;
    $height = 40;
    $image = imagecreatetruecolor($width, $height);
    $bgColor = imagecolorallocate($image, 243, 244, 246);
    imagefill($image, 0, 0, $bgColor);

    // 干扰线
    for ($i = 0; $i < 4; $i++) {
        $lineColor = imagecolorallocate($image, mt_rand(150, 220), mt_rand(150, 220), mt_rand(150, 220));
        imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $lineColor);
    }

    // 干扰点
    for ($i = 0; $i < 30; $i++) {
        $pointColor = imagecolorallocate($image, mt_rand(150, 220), mt_rand(150, 220), mt_rand(150, 220));
        imagesetpixel($image, mt_rand(0, $width), mt_rand(0, $height), $pointColor);
    }

    // 文字
    $textColor = imagecolorallocate($image, 37, 99, 235);
    $fontSize = 5;
    $charWidth = imagefontwidth($fontSize);
    $charHeight = imagefontheight($fontSize);
    $x = ($width - ($charWidth * 4 + 30)) / 2;
    for ($i = 0; $i < 4; $i++) {
        $y = mt_rand(20, $height - $charHeight + 4);
        imagestring($image, $fontSize, (int) $x, $y, $code[$i], $textColor);
        $x += $charWidth + 8;
    }

    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
    exit;
}

/**
 * 校验图形验证码
 */
function captcha_verify($input, $key = 'captcha')
{
    $sessionKey = 'captcha_' . $key;
    $code = session($sessionKey);
    if (!$code) {
        return false;
    }
    // 验证一次后清除，防止重放
    unset($_SESSION[$sessionKey]);
    return strtoupper($input) === $code;
}

/**
 * 是否需要显示验证码
 */
function captcha_required($key)
{
    $config = get_security_config();
    return ($config['security_captcha_' . $key] ?? '0') === '1';
}
