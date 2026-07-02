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

/**
 * 获取当前 URL（含查询参数）
 */
function current_url()
{
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    return $scheme . '://' . $host . $uri;
}

/**
 * 生成带指定语言参数切换的当前 URL
 */
function switch_lang_url($lang)
{
    $url = current_url();
    $parsed = parse_url($url);
    $query = [];
    if (!empty($parsed['query'])) {
        parse_str($parsed['query'], $query);
    }
    $query['lang'] = $lang;
    $parsed['query'] = http_build_query($query);
    return ($parsed['scheme'] ?? 'http') . '://' . $parsed['host'] . ($parsed['path'] ?? '/') . '?' . $parsed['query'];
}

function input($key = null, $default = null)
{
    // 优先从 ThinkPHP 请求对象读取（支持路由参数、get/post 等）
    if (function_exists('request')) {
        try {
            $request = request();
            if (is_object($request) && method_exists($request, 'param')) {
                if ($key === null) {
                    return $request->param();
                }
                $value = $request->param($key);
                if ($value !== null) {
                    return $value;
                }
            }
        } catch (Throwable $e) {
            // 未初始化时回退到 $_REQUEST
        }
    }

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
 * 检查当前管理员是否属于指定角色组
 */
function check_admin_role($allowedRoles = ['super', 'admin', 'operator'])
{
    $admin = session('admin_user');
    if (empty($admin)) {
        redirect(url('login') . '?type=admin');
    }
    if (!in_array($admin['role'] ?? '', $allowedRoles, true)) {
        throw new Exception('无权访问该页面');
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

/**
 * 获取商户费率分组
 */
function get_merchant_rate_group($merchantId)
{
    static $cache = [];
    $merchantId = (int) $merchantId;
    if (isset($cache[$merchantId])) {
        return $cache[$merchantId];
    }

    $default = [
        'rate' => '0.0000',
        'max_fee' => '0.00',
    ];

    try {
        $merchant = Db::fetch("SELECT rate_group_id FROM jz_merchant WHERE id = ?", [$merchantId]);
        if ($merchant && (int) $merchant['rate_group_id'] > 0) {
            $group = Db::fetch("SELECT rate, max_fee FROM jz_rate_group WHERE id = ?", [$merchant['rate_group_id']]);
            if ($group) {
                $cache[$merchantId] = [
                    'rate' => $group['rate'],
                    'max_fee' => $group['max_fee'],
                ];
                return $cache[$merchantId];
            }
        }
    } catch (Exception $e) {
        // 忽略
    }

    $cache[$merchantId] = $default;
    return $default;
}

/**
 * 计算订单手续费
 */
function calculate_order_fee($payAmount, $rateGroup)
{
    $rate = (float) ($rateGroup['rate'] ?? 0);
    $maxFee = (float) ($rateGroup['max_fee'] ?? 0);
    if ($rate <= 0) {
        return 0.00;
    }
    $fee = round($payAmount * $rate, 2);
    if ($maxFee > 0 && $fee > $maxFee) {
        $fee = $maxFee;
    }
    return $fee;
}

/**
 * 上传文件到 public/uploads
 * @param string $field 表单文件字段名
 * @param string $subDir 子目录，如 merchant/auth
 * @param array $allowedExt 允许扩展名白名单
 * @return array ['code'=>0, 'path'=>'相对路径', 'url'=>'访问URL'] / ['code'=>1, 'msg'=>'错误信息']
 */
function upload_file($field, $subDir = '', $allowedExt = ['jpg', 'jpeg', 'png', 'gif'])
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return ['code' => 1, 'msg' => '文件上传失败'];
    }

    $file = $_FILES[$field];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        return ['code' => 1, 'msg' => '不支持的文件格式'];
    }

    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['code' => 1, 'msg' => '文件大小不能超过5MB'];
    }

    // MIME 校验
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $extMimeMap = [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'gif' => ['image/gif'],
    ];
    if (isset($extMimeMap[$ext]) && !in_array($mime, $extMimeMap[$ext], true)) {
        return ['code' => 1, 'msg' => '文件类型与扩展名不符'];
    }

    // 图片内容校验
    if (isset($extMimeMap[$ext]) && !getimagesize($file['tmp_name'])) {
        return ['code' => 1, 'msg' => '图片文件内容异常'];
    }

    // 子目录防路径遍历
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

    $uploadRoot = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
    $targetDir = $uploadRoot . ($safeSubDir ? $safeSubDir . DIRECTORY_SEPARATOR : '');
    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0755, true);
    }

    $filename = date('Ymd') . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $targetPath = $targetDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['code' => 1, 'msg' => '文件保存失败'];
    }

    $relative = 'uploads/' . ($safeSubDir ? str_replace('\\', '/', $safeSubDir) . '/' : '') . $filename;
    return ['code' => 0, 'path' => $relative, 'url' => base_url($relative)];
}

/**
 * 发放积分与成长值
 * @param int $userId 用户ID
 * @param string $type 规则类型 register/login/order/review/invite/system
 * @param int $relatedId 关联ID
 * @param string $remark 备注
 * @return array ['code'=>0, 'msg'=>''] / ['code'=>1, 'msg'=>'']
 */
function award_points($userId, $type, $relatedId = 0, $remark = '')
{
    $userId = (int) $userId;
    if ($userId <= 0) {
        return ['code' => 1, 'msg' => '用户不存在'];
    }

    $rule = Db::fetch("SELECT * FROM jz_points_rule WHERE type = ? AND status = 1 ORDER BY sort ASC, id ASC LIMIT 1", [$type]);
    if (!$rule) {
        return ['code' => 1, 'msg' => '未启用对应积分规则'];
    }

    $points = (int) $rule['points'];
    $growthValue = (int) $rule['growth_value'];
    if ($points === 0 && $growthValue === 0) {
        return ['code' => 1, 'msg' => '无积分或成长值奖励'];
    }

    // 周期限制检查
    $limitCount = (int) $rule['limit_count'];
    if ($limitCount > 0) {
        $startTime = null;
        switch ($rule['limit_type']) {
            case 'day':
                $startTime = date('Y-m-d 00:00:00');
                break;
            case 'week':
                $startTime = date('Y-m-d 00:00:00', strtotime('monday this week'));
                break;
            case 'month':
                $startTime = date('Y-m-01 00:00:00');
                break;
            case 'once':
                $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_points_log WHERE user_id = ? AND type = ?", [$userId, $type]);
                if (($count['total'] ?? 0) >= 1) {
                    return ['code' => 1, 'msg' => '该奖励已领取'];
                }
                break;
        }
        if ($startTime) {
            $count = Db::fetch(
                "SELECT COUNT(*) AS total FROM jz_points_log WHERE user_id = ? AND type = ? AND create_time >= ?",
                [$userId, $type, $startTime]
            );
            if (($count['total'] ?? 0) >= $limitCount) {
                return ['code' => 1, 'msg' => '本周期内奖励次数已达上限'];
            }
        }
    }

    $user = Db::fetch("SELECT points, growth_value FROM jz_user WHERE id = ? FOR UPDATE", [$userId]);
    if (!$user) {
        return ['code' => 1, 'msg' => '用户不存在'];
    }

    $beforePoints = (int) $user['points'];
    $afterPoints = max(0, $beforePoints + $points);
    $newGrowth = (int) $user['growth_value'] + $growthValue;

    Db::execute(
        "UPDATE jz_user SET points = ?, growth_value = ? WHERE id = ?",
        [$afterPoints, $newGrowth, $userId]
    );

    if ($points != 0) {
        Db::insert('jz_points_log', [
            'user_id' => $userId,
            'type' => $type,
            'points' => $points,
            'before_points' => $beforePoints,
            'after_points' => $afterPoints,
            'remark' => $remark ?: $rule['name'],
            'related_id' => $relatedId,
            'create_time' => date('Y-m-d H:i:s'),
        ]);
    }

    return ['code' => 0, 'msg' => '积分发放成功', 'points' => $points, 'growth_value' => $growthValue];
}

/**
 * 生成唯一兑换单号
 */
function generate_points_order_no()
{
    return 'PT' . date('YmdHis') . random_int(100000, 999999);
}

/**
 * 获取商品当前生效价格与活动信息
 * @param array $goods 商品数组
 * @return array ['price'=>现价, 'original_price'=>原价, 'activity'=>'none/seckill/discount', 'label'=>'标签']
 */
function goods_effective_price(array $goods)
{
    $now = time();
    $price = (float) $goods['price'];
    $originalPrice = (float) ($goods['original_price'] ?: $goods['price']);

    // 秒杀优先
    if (!empty($goods['is_seckill']) && !empty($goods['seckill_price'])) {
        $start = !empty($goods['seckill_start']) ? strtotime($goods['seckill_start']) : 0;
        $end = !empty($goods['seckill_end']) ? strtotime($goods['seckill_end']) : PHP_INT_MAX;
        if ($start <= $now && $now <= $end && (int) $goods['seckill_stock'] > (int) $goods['seckill_sold']) {
            return [
                'price' => (float) $goods['seckill_price'],
                'original_price' => $price,
                'activity' => 'seckill',
                'label' => '秒杀',
            ];
        }
    }

    // 限时折扣
    if (!empty($goods['is_discount']) && !empty($goods['discount_price'])) {
        $start = !empty($goods['discount_start']) ? strtotime($goods['discount_start']) : 0;
        $end = !empty($goods['discount_end']) ? strtotime($goods['discount_end']) : PHP_INT_MAX;
        if ($start <= $now && $now <= $end) {
            return [
                'price' => (float) $goods['discount_price'],
                'original_price' => $price,
                'activity' => 'discount',
                'label' => '限时折扣',
            ];
        }
    }

    return [
        'price' => $price,
        'original_price' => $originalPrice,
        'activity' => 'none',
        'label' => '',
    ];
}

/**
 * 检查商品是否处于秒杀活动中
 */
function goods_in_seckill(array $goods)
{
    $info = goods_effective_price($goods);
    return $info['activity'] === 'seckill';
}

/**
 * 获取备份文件存储目录
 */
function backup_storage_path()
{
    $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR;
    if (!is_dir($path)) {
        @mkdir($path, 0750, true);
    }
    return $path;
}

/**
 * 生成备份文件名
 */
function backup_generate_filename()
{
    return date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.sql';
}

/**
 * 获取当前数据库所有表名
 */
function backup_get_tables()
{
    $rows = Db::query("SHOW TABLES");
    $tables = [];
    foreach ($rows as $row) {
        $tables[] = array_values($row)[0];
    }
    return $tables;
}

/**
 * 对字符串进行 SQL 转义（用于生成 INSERT 语句）
 */
function backup_sql_escape($value)
{
    if ($value === null) {
        return 'NULL';
    }
    return "'" . str_replace(["\\", "'", "\n", "\r"], ["\\\\", "\\'", "\\n", "\\r"], $value) . "'";
}

/**
 * 创建数据库备份
 * @param string $name 备份名称
 * @param int $type 1手动 2自动
 * @param string $remark 备注
 * @return array ['code'=>0, 'msg'=>'', 'id'=>备份ID, 'path'=>'']
 */
function backup_database($name, $type = 1, $remark = '')
{
    $dbConfig = Config::get('database');
    if (empty($dbConfig)) {
        return ['code' => 1, 'msg' => '数据库配置不存在'];
    }

    $backupPath = backup_storage_path();
    if (!is_dir($backupPath) || !is_writable($backupPath)) {
        return ['code' => 1, 'msg' => '备份目录不可写：' . $backupPath];
    }

    $filename = backup_generate_filename();
    $filepath = $backupPath . $filename;

    // 优先尝试 mysqldump
    $usedMysqldump = false;
    $mysqldump = function_exists('shell_exec') ? @shell_exec('which mysqldump 2>/dev/null') : '';
    $mysqldump = $mysqldump ? trim($mysqldump) : '';

    if ($mysqldump && function_exists('shell_exec') && function_exists('escapeshellarg')) {
        $cmd = sprintf(
            '%s --host=%s --port=%d --user=%s --password=%s --single-transaction --skip-lock-tables --set-charset --default-character-set=%s %s > %s',
            $mysqldump,
            escapeshellarg($dbConfig['hostname']),
            (int) ($dbConfig['hostport'] ?? 3306),
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['password']),
            escapeshellarg($dbConfig['charset'] ?? 'utf8mb4'),
            escapeshellarg($dbConfig['database']),
            escapeshellarg($filepath)
        );
        @shell_exec($cmd);
        if (is_file($filepath) && filesize($filepath) > 0) {
            $usedMysqldump = true;
        }
    }

    if (empty($usedMysqldump)) {
        // PHP 兜底备份
        $result = backup_database_php($filepath);
        if ($result['code'] !== 0) {
            return $result;
        }
    }

    if (!is_file($filepath) || filesize($filepath) <= 0) {
        return ['code' => 1, 'msg' => '备份文件生成失败或为空'];
    }

    $fileSize = filesize($filepath);
    $fileMd5 = md5_file($filepath);
    $admin = session('admin_user') ?? [];

    $backupId = Db::insert('jz_backup', [
        'name' => $name ?: date('Y-m-d H:i:s 备份'),
        'filename' => $filename,
        'file_size' => $fileSize,
        'file_md5' => $fileMd5,
        'type' => in_array($type, [1, 2], true) ? $type : 1,
        'status' => 0,
        'operator_id' => $admin['id'] ?? 0,
        'operator_name' => $admin['username'] ?? '',
        'remark' => $remark,
        'create_time' => date('Y-m-d H:i:s'),
    ]);

    return [
        'code' => 0,
        'msg' => '备份成功',
        'id' => $backupId,
        'path' => $filepath,
        'size' => $fileSize,
        'md5' => $fileMd5,
    ];
}

/**
 * 使用 PHP 生成数据库备份文件
 */
function backup_database_php($filepath)
{
    try {
        $fp = @fopen($filepath, 'w');
        if (!$fp) {
            return ['code' => 1, 'msg' => '无法创建备份文件'];
        }

        $dbConfig = Config::get('database');
        $database = $dbConfig['database'];

        fwrite($fp, "-- 鲸商城 Pro 数据库备份\n");
        fwrite($fp, "-- 生成时间：" . date('Y-m-d H:i:s') . "\n");
        fwrite($fp, "-- 数据库：{$database}\n");
        fwrite($fp, "SET NAMES utf8mb4;\n");
        fwrite($fp, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

        $tables = backup_get_tables();
        foreach ($tables as $table) {
            // DROP TABLE
            fwrite($fp, "DROP TABLE IF EXISTS `{$table}`;\n");

            // CREATE TABLE
            $create = Db::fetch("SHOW CREATE TABLE `{$table}`");
            $createSql = $create['Create Table'] ?? '';
            if ($createSql) {
                fwrite($fp, $createSql . ";\n\n");
            }

            // INSERT DATA
            $columns = Db::query("SHOW COLUMNS FROM `{$table}`");
            $columnNames = array_map(function ($c) {
                return '`' . $c['Field'] . '`';
            }, $columns);
            $columnStr = implode(', ', $columnNames);

            $offset = 0;
            $batchSize = 100;
            do {
                $rows = Db::query("SELECT * FROM `{$table}` LIMIT {$offset}, {$batchSize}");
                if (empty($rows)) {
                    break;
                }

                $valuesList = [];
                foreach ($rows as $row) {
                    $values = [];
                    foreach ($row as $val) {
                        $values[] = backup_sql_escape($val);
                    }
                    $valuesList[] = '(' . implode(', ', $values) . ')';
                }

                if (!empty($valuesList)) {
                    fwrite($fp, "INSERT INTO `{$table}` ({$columnStr}) VALUES \n");
                    fwrite($fp, implode(",\n", $valuesList) . ";\n\n");
                }

                $offset += $batchSize;
            } while (count($rows) === $batchSize);
        }

        fwrite($fp, "SET FOREIGN_KEY_CHECKS = 1;\n");
        fclose($fp);

        return ['code' => 0, 'msg' => '备份成功'];
    } catch (Exception $e) {
        if (isset($fp) && $fp) {
            @fclose($fp);
        }
        @unlink($filepath);
        return ['code' => 1, 'msg' => '备份失败：' . $e->getMessage()];
    }
}

/**
 * 将 SQL 文件拆分为可执行语句数组
 */
function backup_split_sql($sql)
{
    $sql = trim($sql);
    if ($sql === '') {
        return [];
    }

    // 移除单行注释
    $sql = preg_replace('/--[^\n]*\n/', "\n", $sql);
    // 移除多行注释
    $sql = preg_replace('/\/\*[\s\S]*?\*\//', '', $sql);

    $statements = [];
    $current = '';
    $len = strlen($sql);
    $inString = false;
    $stringChar = '';

    for ($i = 0; $i < $len; $i++) {
        $char = $sql[$i];
        $current .= $char;

        if ($inString) {
            if ($char === '\\' && $i + 1 < $len) {
                $current .= $sql[++$i];
            } elseif ($char === $stringChar) {
                $inString = false;
                $stringChar = '';
            }
        } else {
            if ($char === "'" || $char === '"' || $char === '`') {
                $inString = true;
                $stringChar = $char;
            } elseif ($char === ';') {
                $statements[] = trim($current);
                $current = '';
            }
        }
    }

    if (trim($current) !== '') {
        $statements[] = trim($current);
    }

    return array_values(array_filter($statements, function ($s) {
        return trim($s) !== '';
    }));
}

/**
 * 从备份文件恢复数据库
 * @param int $backupId 备份记录ID
 * @return array ['code'=>0, 'msg'=>'']
 */
function backup_restore($backupId)
{
    $backupId = (int) $backupId;
    if ($backupId <= 0) {
        return ['code' => 1, 'msg' => '参数错误'];
    }

    $record = Db::fetch("SELECT * FROM jz_backup WHERE id = ? AND status = 0", [$backupId]);
    if (!$record) {
        return ['code' => 1, 'msg' => '备份记录不存在或已被恢复/删除'];
    }

    $backupPath = backup_storage_path();
    $filepath = $backupPath . $record['filename'];
    if (!is_file($filepath)) {
        return ['code' => 1, 'msg' => '备份文件不存在：' . $record['filename']];
    }

    $md5 = md5_file($filepath);
    if ($md5 !== $record['file_md5']) {
        return ['code' => 1, 'msg' => '备份文件校验失败，可能被篡改'];
    }

    $sql = @file_get_contents($filepath);
    if ($sql === false) {
        return ['code' => 1, 'msg' => '无法读取备份文件'];
    }

    try {
        $pdo = Db::getPdo();
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $pdo->exec("SET NAMES utf8mb4");

        $statements = backup_split_sql($sql);
        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            if ($stmt === '' || stripos($stmt, 'SET FOREIGN_KEY_CHECKS') === 0 || stripos($stmt, 'SET NAMES') === 0) {
                continue;
            }
            $pdo->exec($stmt);
        }

        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

        Db::update('jz_backup', ['status' => 1], 'id = ?', [$backupId]);

        $admin = session('admin_user') ?? [];
        admin_log('backup_restore', [
            'id' => $backupId,
            'filename' => $record['filename'],
            'admin_id' => $admin['id'] ?? 0,
        ]);

        return ['code' => 0, 'msg' => '数据库恢复成功'];
    } catch (Exception $e) {
        return ['code' => 1, 'msg' => '恢复失败：' . $e->getMessage()];
    }
}

/**
 * 删除备份文件及记录
 * @param int $backupId 备份记录ID
 * @return array ['code'=>0, 'msg'=>'']
 */
function backup_delete($backupId)
{
    $backupId = (int) $backupId;
    if ($backupId <= 0) {
        return ['code' => 1, 'msg' => '参数错误'];
    }

    $record = Db::fetch("SELECT * FROM jz_backup WHERE id = ?", [$backupId]);
    if (!$record) {
        return ['code' => 1, 'msg' => '备份记录不存在'];
    }

    $backupPath = backup_storage_path();
    $filepath = $backupPath . $record['filename'];
    if (is_file($filepath)) {
        @unlink($filepath);
    }

    Db::update('jz_backup', ['status' => 2], 'id = ?', [$backupId]);

    $admin = session('admin_user') ?? [];
    admin_log('backup_delete', [
        'id' => $backupId,
        'filename' => $record['filename'],
        'admin_id' => $admin['id'] ?? 0,
    ]);

    return ['code' => 0, 'msg' => '删除成功'];
}

/**
 * 格式化文件大小
 */
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

/**
 * 获取自动备份接口密钥
 */
function backup_get_cron_key()
{
    try {
        $row = Db::fetch("SELECT cfg_value FROM jz_config WHERE cfg_key = 'base_backup_cron_key'");
        if ($row && !empty($row['cfg_value'])) {
            return $row['cfg_value'];
        }

        $key = generate_token(32);
        Db::insert('jz_config', [
            'cfg_key' => 'base_backup_cron_key',
            'cfg_value' => $key,
            'cfg_group' => 'base',
            'description' => '自动备份接口密钥',
        ]);
        return $key;
    } catch (Exception $e) {
        return '';
    }
}

/**
 * 获取消息模板
 * @param string $type sms|email
 * @param string $code 模板编码
 * @return array|null
 */
function message_template($type, $code)
{
    return Db::fetch(
        "SELECT * FROM jz_message_template WHERE type = ? AND code = ? AND status = 1",
        [$type, $code]
    );
}

/**
 * 解析模板变量 {var_name}
 * @param string $content 模板内容
 * @param array $vars 变量数组
 * @return string
 */
function message_parse($content, array $vars)
{
    $vars['site_name'] = $vars['site_name'] ?? site_config('site_name', '鲸商城 Pro');
    $vars['site_url'] = $vars['site_url'] ?? base_url();

    return preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function ($matches) use ($vars) {
        return $vars[$matches[1]] ?? '';
    }, $content);
}

/**
 * 读取邮件配置
 */
function email_config()
{
    return [
        'host' => site_config('email_host', ''),
        'port' => (int) site_config('email_port', '465'),
        'secure' => site_config('email_secure', 'ssl'),
        'user' => site_config('email_user', ''),
        'pass' => site_config('email_pass', ''),
        'from' => site_config('email_from', site_config('email_user', '')),
    ];
}

/**
 * 使用 SMTP 发送邮件
 * @param string $to 收件人邮箱
 * @param string $subject 主题
 * @param string $body 内容（支持 HTML）
 * @return array ['code'=>0, 'msg'=>''] / ['code'=>1, 'msg'=>'']
 */
function smtp_send_mail($to, $subject, $body)
{
    $config = email_config();
    if (empty($config['host']) || empty($config['user']) || empty($config['pass'])) {
        return ['code' => 1, 'msg' => '邮件服务器未配置'];
    }

    $host = $config['host'];
    $port = $config['port'] ?: 465;
    $secure = strtolower($config['secure']);
    $username = $config['user'];
    $password = $config['pass'];
    $from = $config['from'] ?: $username;

    $remote = ($secure === 'ssl' ? 'ssl://' : '') . $host . ':' . $port;
    $fp = @stream_socket_client($remote, $errno, $errstr, 10);
    if (!$fp) {
        return ['code' => 1, 'msg' => '无法连接邮件服务器：' . $errstr];
    }

    $read = function () use ($fp) {
        $data = '';
        while ($line = @fgets($fp, 515)) {
            $data .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return $data;
    };

    $cmd = function ($command) use ($fp, $read) {
        @fwrite($fp, $command . "\r\n");
        return $read();
    };

    $read();

    $hello = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    $cmd('EHLO ' . $hello);

    if ($secure === 'tls') {
        $cmd('STARTTLS');
        @stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        $cmd('EHLO ' . $hello);
    }

    $cmd('AUTH LOGIN');
    $cmd(base64_encode($username));
    $cmd(base64_encode($password));
    $cmd('MAIL FROM:<' . $from . '>');
    $cmd('RCPT TO:<' . $to . '>');
    $cmd('DATA');

    $boundary = md5(uniqid('boundary', true));
    $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

    $headers = "From: =?UTF-8?B?" . base64_encode($config['from'] ?: $config['user']) . "?= <{$from}>\r\n";
    $headers .= "To: <{$to}>\r\n";
    $headers .= "Subject: {$subject}\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8; boundary={$boundary}\r\n";
    $headers .= "Content-Transfer-Encoding: base64\r\n";

    $message = $headers . "\r\n" . chunk_split(base64_encode($body)) . "\r\n.\r\n";
    $result = $cmd($message);

    $cmd('QUIT');
    @fclose($fp);

    if (strpos($result, '250') === 0) {
        return ['code' => 0, 'msg' => '邮件发送成功'];
    }
    return ['code' => 1, 'msg' => '邮件发送失败：' . $result];
}

/**
 * 发送邮件（基于模板）
 * @param string $to 收件人
 * @param string $templateCode 模板编码
 * @param array $vars 模板变量
 * @param string $subject 可选自定义主题
 * @param string $body 可选自定义内容
 * @return array
 */
function send_email($to, $templateCode, array $vars = [], $subject = '', $body = '')
{
    $template = message_template('email', $templateCode);
    if (!$template && !$subject && !$body) {
        return ['code' => 1, 'msg' => '邮件模板不存在'];
    }

    if ($template) {
        $subject = $subject ?: message_parse($template['title'], $vars);
        $body = $body ?: message_parse($template['content'], $vars);
    }

    if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return ['code' => 1, 'msg' => '收件人邮箱格式错误'];
    }

    $result = smtp_send_mail($to, $subject, $body);

    try {
        Db::insert('jz_email_log', [
            'recipient' => $to,
            'template_code' => $templateCode,
            'subject' => $subject,
            'content' => $body,
            'result' => $result['msg'],
            'status' => $result['code'] === 0 ? 1 : 0,
            'create_time' => date('Y-m-d H:i:s'),
        ]);
    } catch (Exception $e) {
        // 记录失败不影响主流程
    }

    return $result;
}

/**
 * 读取短信配置
 */
function sms_config()
{
    return [
        'gateway' => site_config('sms_gateway', ''),
        'app_id' => site_config('sms_app_id', ''),
        'app_key' => site_config('sms_app_key', ''),
        'sign' => site_config('sms_sign', ''),
        'api_url' => site_config('sms_api_url', ''),
        'api_method' => site_config('sms_api_method', 'POST'),
        'api_params' => site_config('sms_api_params', ''),
        'debug' => (int) site_config('sms_debug', '0'),
    ];
}

/**
 * 发送短信（基于模板）
 * @param string $mobile 手机号
 * @param string $templateCode 模板编码
 * @param array $vars 模板变量
 * @return array
 */
function send_sms($mobile, $templateCode, array $vars = [])
{
    $template = message_template('sms', $templateCode);
    if (!$template) {
        return ['code' => 1, 'msg' => '短信模板不存在'];
    }

    $config = sms_config();
    if (empty($config['gateway']) && empty($config['api_url'])) {
        return ['code' => 1, 'msg' => '短信网关未配置'];
    }

    $content = message_parse($template['content'], $vars);
    if ($config['sign']) {
        $content = '【' . $config['sign'] . '】' . $content;
    }

    if (!preg_match('/^1[3-9]\d{9}$/', $mobile)) {
        return ['code' => 1, 'msg' => '手机号格式错误'];
    }

    // 调试模式：仅记录不发短信
    if ($config['debug']) {
        Db::insert('jz_sms_log', [
            'mobile' => $mobile,
            'template_code' => $templateCode,
            'content' => $content,
            'gateway' => $config['gateway'] ?: 'custom',
            'result' => '调试模式未发送',
            'status' => 0,
            'create_time' => date('Y-m-d H:i:s'),
        ]);
        return ['code' => 0, 'msg' => '调试模式：短信已记录但未发送', 'content' => $content];
    }

    // 通用 HTTP 短信网关
    if (!empty($config['api_url'])) {
        $params = [];
        if ($config['api_params']) {
            parse_str($config['api_params'], $params);
        }
        $params['mobile'] = $mobile;
        $params['content'] = $content;
        $params['sign'] = $config['sign'];
        $params['app_id'] = $config['app_id'];
        $params['app_key'] = $config['app_key'];

        $result = http_request($config['api_url'], $params, strtoupper($config['api_method']) === 'GET' ? 'GET' : 'POST');
        $success = $result['code'] === 0;
        Db::insert('jz_sms_log', [
            'mobile' => $mobile,
            'template_code' => $templateCode,
            'content' => $content,
            'gateway' => 'custom',
            'result' => mb_substr($result['data'] ?? $result['msg'], 0, 500),
            'status' => $success ? 1 : 0,
            'create_time' => date('Y-m-d H:i:s'),
        ]);
        return $success ? ['code' => 0, 'msg' => '短信发送成功'] : ['code' => 1, 'msg' => '短信发送失败：' . $result['msg']];
    }

    return ['code' => 1, 'msg' => '不支持的短信网关：' . $config['gateway']];
}

/**
 * HTTP 请求辅助函数
 * @param string $url 请求地址
 * @param array $data 请求数据
 * @param string $method GET|POST
 * @param int $timeout 超时秒数
 * @return array ['code'=>0, 'data'=>'', 'msg'=>'']
 */
function http_request($url, $data = [], $method = 'GET', $timeout = 30)
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return ['code' => 1, 'msg' => 'URL 格式错误'];
    }

    if ($method === 'GET' && !empty($data)) {
        $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($data);
    }

    $options = [
        'http' => [
            'method' => $method,
            'timeout' => $timeout,
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'ignore_errors' => true,
        ],
    ];

    if ($method === 'POST' && !empty($data)) {
        $options['http']['content'] = http_build_query($data);
    }

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        return ['code' => 1, 'msg' => '请求失败'];
    }
    return ['code' => 0, 'data' => $response];
}

/**
 * 统一消息发送入口
 * @param string $type sms|email
 * @param string $code 模板编码
 * @param string $recipient 收件人邮箱或手机号
 * @param array $vars 模板变量
 * @return array
 */
function message_send($type, $code, $recipient, array $vars = [])
{
    if ($type === 'email') {
        return send_email($recipient, $code, $vars);
    }
    if ($type === 'sms') {
        return send_sms($recipient, $code, $vars);
    }
    return ['code' => 1, 'msg' => '不支持的消息类型'];
}

/* ============================================================
 * 第三方登录 OAuth
 * ============================================================ */

/**
 * 读取第三方登录配置
 */
function oauth_config($type)
{
    $prefix = 'oauth_' . $type . '_';
    return [
        'appid' => site_config($prefix . 'appid', ''),
        'secret' => site_config($prefix . 'secret', ''),
        'enabled' => (int) site_config($prefix . 'enabled', '0'),
        'authorize_url' => site_config($prefix . 'authorize_url', ''),
        'token_url' => site_config($prefix . 'token_url', ''),
        'userinfo_url' => site_config($prefix . 'userinfo_url', ''),
        'scope' => site_config($prefix . 'scope', ''),
    ];
}

/**
 * OAuth 回调地址
 */
function oauth_callback_url($type)
{
    return base_url('oauth/callback?type=' . $type);
}

/**
 * 获取 OAuth 提供者内置配置（QQ/微信/GitHub）
 */
function oauth_provider($type)
{
    $providers = [
        'qq' => [
            'authorize_url' => 'https://graph.qq.com/oauth2.0/authorize',
            'token_url' => 'https://graph.qq.com/oauth2.0/token',
            'userinfo_url' => 'https://graph.qq.com/user/get_user_info',
            'scope' => 'get_user_info',
        ],
        'weixin' => [
            'authorize_url' => 'https://open.weixin.qq.com/connect/qrconnect',
            'token_url' => 'https://api.weixin.qq.com/sns/oauth2/access_token',
            'userinfo_url' => 'https://api.weixin.qq.com/sns/userinfo',
            'scope' => 'snsapi_login',
        ],
        'github' => [
            'authorize_url' => 'https://github.com/login/oauth/authorize',
            'token_url' => 'https://github.com/login/oauth/access_token',
            'userinfo_url' => 'https://api.github.com/user',
            'scope' => 'user:email',
        ],
    ];
    return $providers[$type] ?? [];
}

/**
 * 生成 OAuth 授权跳转 URL
 */
function oauth_authorize_url($type)
{
    $config = oauth_config($type);
    $provider = oauth_provider($type);
    if (empty($config['appid']) || empty($provider['authorize_url'])) {
        return '';
    }

    $state = generate_token(16);
    session('oauth_state_' . $type, $state);

    $params = [
        'client_id' => $config['appid'],
        'redirect_uri' => oauth_callback_url($type),
        'response_type' => 'code',
        'state' => $state,
        'scope' => $config['scope'] ?: $provider['scope'],
    ];

    // 微信使用 #wechat_redirect
    $suffix = $type === 'weixin' ? '#wechat_redirect' : '';
    return $provider['authorize_url'] . '?' . http_build_query($params) . $suffix;
}

/**
 * 验证 OAuth state 参数
 */
function oauth_verify_state($type, $state)
{
    $key = 'oauth_state_' . $type;
    $expected = session($key);
    unset($_SESSION[$key]);
    return $expected && $expected === $state;
}

/**
 * 使用 code 换取 access_token
 */
function oauth_get_token($type, $code)
{
    $config = oauth_config($type);
    $provider = oauth_provider($type);
    if (empty($config['secret']) || empty($provider['token_url'])) {
        return ['code' => 1, 'msg' => 'OAuth 配置不完整'];
    }

    $params = [
        'grant_type' => 'authorization_code',
        'client_id' => $config['appid'],
        'client_secret' => $config['secret'],
        'code' => $code,
        'redirect_uri' => oauth_callback_url($type),
    ];

    $result = http_request($provider['token_url'], $params, 'POST', 30);
    if ($result['code'] !== 0) {
        return ['code' => 1, 'msg' => 'Token 请求失败：' . $result['msg']];
    }

    parse_str($result['data'], $data);
    if (!empty($data['access_token'])) {
        return ['code' => 0, 'data' => $data];
    }

    // GitHub 返回 JSON
    $json = json_decode($result['data'], true);
    if (!empty($json['access_token'])) {
        return ['code' => 0, 'data' => $json];
    }

    return ['code' => 1, 'msg' => '获取 Token 失败：' . $result['data']];
}

/**
 * 获取 OAuth 用户信息（简化版，适配 QQ/微信/GitHub 通用字段）
 */
function oauth_get_userinfo($type, $token, $openid = '')
{
    $provider = oauth_provider($type);
    if (empty($provider['userinfo_url'])) {
        return ['code' => 1, 'msg' => '未配置用户信息接口'];
    }

    $params = ['access_token' => $token];
    if ($type === 'qq') {
        $params['oauth_consumer_key'] = oauth_config('qq')['appid'];
        $params['openid'] = $openid;
    }
    if ($type === 'weixin') {
        $params['openid'] = $openid;
    }

    $result = http_request($provider['userinfo_url'], $params, 'GET', 30);
    if ($result['code'] !== 0) {
        return ['code' => 1, 'msg' => '用户信息请求失败：' . $result['msg']];
    }

    $data = json_decode($result['data'], true);
    if (!$data) {
        // QQ 返回 callback( ... )
        if (preg_match('/callback\((.*)\);/s', $result['data'], $m)) {
            $data = json_decode($m[1], true);
        }
    }
    if (empty($data)) {
        return ['code' => 1, 'msg' => '解析用户信息失败'];
    }

    // 统一字段
    $user = [
        'openid' => $openid,
        'unionid' => '',
        'nickname' => '',
        'avatar' => '',
    ];

    if ($type === 'github') {
        $user['openid'] = (string) ($data['id'] ?? '');
        $user['nickname'] = $data['login'] ?? $data['name'] ?? '';
        $user['avatar'] = $data['avatar_url'] ?? '';
    } elseif ($type === 'weixin') {
        $user['openid'] = $data['openid'] ?? $openid;
        $user['unionid'] = $data['unionid'] ?? '';
        $user['nickname'] = $data['nickname'] ?? '';
        $user['avatar'] = $data['headimgurl'] ?? '';
    } elseif ($type === 'qq') {
        $user['openid'] = $openid;
        $user['nickname'] = $data['nickname'] ?? '';
        $user['avatar'] = $data['figureurl_qq_2'] ?? $data['figureurl_qq_1'] ?? '';
    }

    return ['code' => 0, 'data' => $user];
}

/**
 * OAuth 登录或注册并绑定
 * @return array ['code'=>0, 'msg'=>'', 'user_id'=>]
 */
function oauth_login_or_register($type, array $oauthUser)
{
    $openid = $oauthUser['openid'] ?? '';
    if (!$openid) {
        return ['code' => 1, 'msg' => '第三方账号信息缺失'];
    }

    $bind = Db::fetch(
        "SELECT * FROM jz_oauth_bind WHERE type = ? AND openid = ?",
        [$type, $openid]
    );

    if ($bind) {
        $user = Db::fetch("SELECT * FROM jz_user WHERE id = ? AND status = 1", [$bind['user_id']]);
        if (!$user) {
            return ['code' => 1, 'msg' => '绑定用户不存在或已被禁用'];
        }
        session('user_contact', $user['nickname']);
        return ['code' => 0, 'msg' => '登录成功', 'user_id' => $user['id']];
    }

    // 创建新用户并绑定
    $nickname = $oauthUser['nickname'] ?: ($type . '_' . substr($openid, -8));
    $mobile = '';

    // 避免昵称重复
    $exist = Db::fetch("SELECT id FROM jz_user WHERE nickname = ? LIMIT 1", [$nickname]);
    if ($exist) {
        $nickname .= '_' . generate_token(4);
    }

    $userId = Db::insert('jz_user', [
        'nickname' => $nickname,
        'mobile' => $mobile,
        'password' => password_hash_custom(generate_token(16)),
        'create_time' => date('Y-m-d H:i:s'),
    ]);

    Db::insert('jz_oauth_bind', [
        'user_id' => $userId,
        'type' => $type,
        'openid' => $openid,
        'unionid' => $oauthUser['unionid'] ?? '',
        'nickname' => $oauthUser['nickname'] ?? '',
        'avatar' => $oauthUser['avatar'] ?? '',
        'create_time' => date('Y-m-d H:i:s'),
    ]);

    award_points($userId, 'register');
    session('user_contact', $nickname);

    return ['code' => 0, 'msg' => '登录成功', 'user_id' => $userId];
}

/* ============================================================
 * 开放 API
 * ============================================================ */

/**
 * API 认证并返回密钥信息
 */
function api_auth()
{
    $appId = input('app_id', '');
    $sign = input('sign', '');
    $timestamp = input('timestamp', '');
    $nonce = input('nonce', '');

    if (!$appId || !$sign || !$timestamp || !$nonce) {
        return ['code' => 1, 'msg' => '缺少认证参数'];
    }

    // 时间戳校验（允许 ±5 分钟）
    if (abs(time() - (int) $timestamp) > 300) {
        return ['code' => 1, 'msg' => '请求时间戳已过期'];
    }

    $key = Db::fetch("SELECT * FROM jz_api_key WHERE app_id = ? AND status = 1", [$appId]);
    if (!$key) {
        return ['code' => 1, 'msg' => 'API 密钥不存在或已禁用'];
    }

    // IP 白名单校验
    if (!empty($key['ips'])) {
        $ips = array_filter(array_map('trim', explode(',', $key['ips'])));
        if (!empty($ips) && !in_array(get_client_ip(), $ips, true)) {
            return ['code' => 1, 'msg' => '当前 IP 不在允许列表'];
        }
    }

    // 签名字符串：app_id + timestamp + nonce + app_secret 的 MD5
    $expected = md5($appId . $timestamp . $nonce . $key['app_secret']);
    if (!hash_equals($expected, strtolower($sign))) {
        return ['code' => 1, 'msg' => '签名错误'];
    }

    // 更新调用统计
    Db::execute(
        "UPDATE jz_api_key SET request_count = request_count + 1, last_request_time = ? WHERE id = ?",
        [date('Y-m-d H:i:s'), $key['id']]
    );

    return ['code' => 0, 'data' => $key];
}

/**
 * 校验 API 权限
 */
function api_check_permission($key, $action)
{
    if (empty($key['permissions'])) {
        return true;
    }
    $permissions = array_map('trim', explode(',', $key['permissions']));
    return in_array('*', $permissions, true) || in_array($action, $permissions, true);
}

/**
 * 记录 API 日志
 */
function api_log($appId, $action, $params, $result, $status = 1)
{
    try {
        Db::insert('jz_api_log', [
            'app_id' => $appId,
            'action' => $action,
            'params' => is_array($params) ? json_encode($params, JSON_UNESCAPED_UNICODE) : $params,
            'result' => mb_substr(is_string($result) ? $result : json_encode($result, JSON_UNESCAPED_UNICODE), 0, 500),
            'ip' => get_client_ip(),
            'status' => $status ? 1 : 0,
            'create_time' => date('Y-m-d H:i:s'),
        ]);
    } catch (Exception $e) {
    }
}

/* ============================================================
 * 插件 / 机器人对接
 * ============================================================ */

/**
 * 触发插件事件
 * @param string $eventType 事件类型 order_created|order_paid|order_delivered
 * @param array $payload 事件数据
 */
function plugin_trigger($eventType, array $payload)
{
    $plugins = Db::query(
        "SELECT * FROM jz_plugin WHERE status = 1 AND FIND_IN_SET(?, event_types)",
        [$eventType]
    );

    foreach ($plugins as $plugin) {
        $config = json_decode($plugin['config'] ?? '{}', true);
        if ($plugin['type'] === 'webhook' && !empty($config['url'])) {
            plugin_webhook_send($plugin, $eventType, $payload);
        }
    }
}

/**
 * 发送 Webhook
 */
function plugin_webhook_send(array $plugin, $eventType, array $payload)
{
    $config = json_decode($plugin['config'] ?? '{}', true);
    $url = $config['url'] ?? '';
    $secret = $config['secret'] ?? '';
    if (!$url) {
        return;
    }

    $data = [
        'event' => $eventType,
        'time' => time(),
        'payload' => $payload,
    ];

    $headers = ["Content-Type: application/json"];
    if ($secret) {
        $sign = hash_hmac('sha256', json_encode($data, JSON_UNESCAPED_UNICODE), $secret);
        $headers[] = "X-Plugin-Signature: {$sign}";
    }

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'timeout' => 30,
            'ignore_errors' => true,
        ],
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);
    $success = $response !== false;

    try {
        Db::insert('jz_plugin_log', [
            'plugin_id' => $plugin['id'],
            'event_type' => $eventType,
            'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'response' => mb_substr((string) $response, 0, 2000),
            'status' => $success ? 1 : 0,
            'create_time' => date('Y-m-d H:i:s'),
        ]);
    } catch (Exception $e) {
    }
}

/* ============================================================
 * 多语言
 * ============================================================ */

/**
 * 获取当前语言
 */
function current_lang()
{
    $supported = ['zh-cn', 'en'];
    $default = 'zh-cn';

    $lang = input('lang', '');
    if ($lang && in_array(strtolower($lang), $supported, true)) {
        session('lang', strtolower($lang));
        return strtolower($lang);
    }

    $sessionLang = session('lang');
    if ($sessionLang && in_array(strtolower($sessionLang), $supported, true)) {
        return strtolower($sessionLang);
    }

    return $default;
}

/**
 * 加载语言包
 */
function load_lang($lang)
{
    static $cache = [];
    if (isset($cache[$lang])) {
        return $cache[$lang];
    }

    $file = APP_PATH . 'lang' . DIRECTORY_SEPARATOR . $lang . '.php';
    if (!is_file($file)) {
        $cache[$lang] = [];
        return [];
    }

    $cache[$lang] = require $file;
    return $cache[$lang];
}

/**
 * 翻译
 * @param string $key 语言键，支持点号分隔
 * @param array $replace 替换变量
 * @param string|null $lang 指定语言
 */
function lang($key, array $replace = [], $lang = null)
{
    $lang = $lang ?: current_lang();
    $data = load_lang($lang);

    $keys = explode('.', $key);
    $value = $data;
    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            // 回退到中文
            if ($lang !== 'zh-cn') {
                return lang($key, $replace, 'zh-cn');
            }
            return $key;
        }
        $value = $value[$k];
    }

    if (is_string($value)) {
        foreach ($replace as $search => $replaceValue) {
            $value = str_replace(':' . $search, $replaceValue, $value);
        }
    }

    return $value;
}

/* ============================================================
 * 全站搜索与筛选
 * ============================================================ */

/**
 * 商品搜索 SQL 构造器
 * @param array $filters 过滤条件
 * @return array ['where'=>'', 'params'=>[]]
 */
function build_goods_search_where(array $filters)
{
    $where = 'g.status = 1';
    $params = [];

    if (!empty($filters['keyword'])) {
        $where .= ' AND (g.name LIKE ? OR g.content LIKE ?)';
        $params[] = '%' . $filters['keyword'] . '%';
        $params[] = '%' . $filters['keyword'] . '%';
    }

    if (!empty($filters['category_id'])) {
        $subIds = Db::query("SELECT id FROM jz_category WHERE parent_id = ? AND status = 1", [$filters['category_id']]);
        $ids = array_merge([(int) $filters['category_id']], array_column($subIds, 'id'));
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $where .= " AND g.category_id IN ({$placeholders})";
        $params = array_merge($params, $ids);
    }

    if (!empty($filters['merchant_id'])) {
        $where .= ' AND g.merchant_id = ?';
        $params[] = (int) $filters['merchant_id'];
    }

    if (!empty($filters['subsite_id'])) {
        $where .= ' AND g.subsite_id = ?';
        $params[] = (int) $filters['subsite_id'];
    }

    if (!empty($filters['min_price'])) {
        $where .= ' AND g.price >= ?';
        $params[] = (float) $filters['min_price'];
    }
    if (!empty($filters['max_price'])) {
        $where .= ' AND g.price <= ?';
        $params[] = (float) $filters['max_price'];
    }

    if (isset($filters['has_stock']) && $filters['has_stock']) {
        $where .= ' AND (g.stock > 0 OR (g.is_seckill = 1 AND g.seckill_stock > g.seckill_sold))';
    }

    return ['where' => $where, 'params' => $params];
}

/**
 * 搜索建议
 * @param string $keyword
 * @param int $limit
 * @return array
 */
function search_suggest($keyword, $limit = 10)
{
    $keyword = trim($keyword);
    if ($keyword === '') {
        return [];
    }

    $goods = Db::query(
        "SELECT id, name, price FROM jz_goods WHERE status = 1 AND name LIKE ? ORDER BY sold DESC LIMIT ?",
        ['%' . $keyword . '%', (int) $limit]
    );

    $categories = Db::query(
        "SELECT id, name FROM jz_category WHERE status = 1 AND name LIKE ? LIMIT ?",
        ['%' . $keyword . '%', (int) $limit]
    );

    return ['goods' => $goods, 'categories' => $categories];
}

/* ============================================================
 * 在线更新与授权验证
 * ============================================================ */

/**
 * 读取本地授权配置
 */
function update_get_license_config()
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

/**
 * 生成授权站请求签名
 */
function update_sign(array $params, $apiKey)
{
    ksort($params);
    $str = http_build_query($params);
    return hash_hmac('sha256', $str, $apiKey ?? '');
}

/**
 * 向授权站发起 POST 请求并解析 JSON
 */
function update_http_post($apiUrl, $path, array $params, $apiKey)
{
    $params['timestamp'] = time();
    $params['nonce'] = bin2hex(random_bytes(8));
    $params['sign'] = update_sign($params, $apiKey);

    $url = rtrim($apiUrl, '/') . $path;
    $result = http_request($url, $params, 'POST', 60);
    if ($result['code'] !== 0) {
        throw new Exception('授权站请求失败：' . $result['msg']);
    }

    $data = json_decode($result['data'], true);
    if (!is_array($data)) {
        throw new Exception('授权站返回格式错误');
    }
    if (($data['code'] ?? 1) !== 0) {
        throw new Exception($data['msg'] ?? '授权站返回错误');
    }
    return $data['data'] ?? [];
}

/**
 * 检查授权状态与最新版本
 * @param array|null $license 授权配置
 * @return array
 * @throws Exception
 */
function update_check_remote($license = null)
{
    $license = $license ?: update_get_license_config();
    if (empty($license['auth_code']) || empty($license['api_url'])) {
        throw new Exception('授权配置不完整，请先填写授权站地址与授权码');
    }

    $currentVersion = Config::get('app.app_version', '1.0.0');
    $params = [
        'auth_code' => $license['auth_code'],
        'auth_domain' => $license['auth_domain'] ?: ($_SERVER['HTTP_HOST'] ?? ''),
        'current_version' => $currentVersion,
        'php_version' => PHP_VERSION,
    ];

    $data = update_http_post($license['api_url'], '/api/license/check', $params, $license['api_key']);

    $latestVersion = $data['latest_version'] ?? $currentVersion;
    $hasUpdate = version_compare($latestVersion, $currentVersion, '>');

    return [
        'license_valid' => (bool) ($data['license_valid'] ?? false),
        'license_msg' => $data['license_msg'] ?? '',
        'license_type' => $data['license_type'] ?? '',
        'auth_domain' => $data['auth_domain'] ?? ($license['auth_domain'] ?: ''),
        'current_version' => $currentVersion,
        'latest_version' => $latestVersion,
        'has_update' => $hasUpdate,
        'release_date' => $data['release_date'] ?? '',
        'update_desc' => $data['update_desc'] ?? '',
        'force_update' => (bool) ($data['force_update'] ?? false),
    ];
}

/**
 * 下载并应用更新包
 * @param string $version 目标版本号
 * @param array|null $license 授权配置
 * @return array
 * @throws Exception
 */
function update_apply_upgrade($version, $license = null)
{
    $license = $license ?: update_get_license_config();
    if (empty($license['auth_code']) || empty($license['api_url'])) {
        throw new Exception('授权配置不完整');
    }

    $rootPath = dirname(APP_PATH) . DIRECTORY_SEPARATOR;
    $workDir = $rootPath . 'runtime' . DIRECTORY_SEPARATOR . 'update' . DIRECTORY_SEPARATOR;
    if (!is_dir($workDir)) {
        @mkdir($workDir, 0755, true);
    }

    // 获取下载地址
    $params = [
        'auth_code' => $license['auth_code'],
        'auth_domain' => $license['auth_domain'] ?: ($_SERVER['HTTP_HOST'] ?? ''),
        'version' => $version,
    ];
    $data = update_http_post($license['api_url'], '/api/license/download', $params, $license['api_key']);
    $downloadUrl = $data['download_url'] ?? '';
    if (!$downloadUrl) {
        throw new Exception('授权站未返回更新包下载地址');
    }

    // 下载更新包
    $zipFile = $workDir . 'upgrade_' . preg_replace('/[^0-9a-zA-Z._-]/', '_', $version) . '.zip';
    $zipContent = @file_get_contents($downloadUrl, false, stream_context_create([
        'http' => ['timeout' => 120, 'ignore_errors' => true],
    ]));
    if ($zipContent === false || strlen($zipContent) < 100) {
        throw new Exception('更新包下载失败');
    }
    if (@file_put_contents($zipFile, $zipContent) === false) {
        throw new Exception('更新包保存失败');
    }

    // 校验文件 MD5
    if (!empty($data['file_md5']) && md5_file($zipFile) !== strtolower($data['file_md5'])) {
        @unlink($zipFile);
        throw new Exception('更新包校验失败');
    }

    // 解压
    $extractDir = $workDir . 'upgrade_' . preg_replace('/[^0-9a-zA-Z._-]/', '_', $version) . DIRECTORY_SEPARATOR;
    if (is_dir($extractDir)) {
        update_rmdir($extractDir);
    }
    @mkdir($extractDir, 0755, true);

    $zip = new ZipArchive();
    if ($zip->open($zipFile) !== true) {
        @unlink($zipFile);
        throw new Exception('更新包解压失败');
    }
    $zip->extractTo($extractDir);
    $zip->close();
    @unlink($zipFile);

    // 读取升级清单
    $manifestFile = $extractDir . 'update.json';
    $manifest = [];
    if (is_file($manifestFile)) {
        $manifest = json_decode(file_get_contents($manifestFile), true) ?: [];
    }

    // 执行 SQL 升级脚本
    $sqlFile = $extractDir . 'update.sql';
    if (is_file($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        $pdo = Db::getPdo();
        $pdo->exec("SET NAMES utf8mb4");
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $statement) {
            if ($statement) {
                $pdo->exec($statement);
            }
        }
    }

    // 复制文件（仅允许覆盖 application 与 public，保护配置与授权文件）
    $protectedFiles = [
        'application/config/database.php',
        'application/config/license.php',
        'install/auth.php',
        'install/installed.lock',
    ];
    $allowedDirs = ['application', 'public'];
    $copied = [];
    foreach ($allowedDirs as $dirName) {
        $sourceDir = $extractDir . $dirName;
        if (!is_dir($sourceDir)) {
            continue;
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            $relative = $dirName . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            $relativeUnix = str_replace('\\', '/', $relative);
            if (in_array($relativeUnix, $protectedFiles, true)) {
                continue;
            }
            $target = $rootPath . $relative;
            if ($item->isDir()) {
                if (!is_dir($target)) {
                    @mkdir($target, 0755, true);
                }
                continue;
            }
            if (@copy($item->getPathname(), $target)) {
                $copied[] = $relativeUnix;
            }
        }
    }

    // 执行升级后脚本
    $afterFile = $extractDir . 'update.php';
    if (is_file($afterFile)) {
        require $afterFile;
    }

    // 更新版本号到 app.php
    $appConfigFile = APP_PATH . 'config' . DIRECTORY_SEPARATOR . 'app.php';
    $appConfig = [];
    if (is_file($appConfigFile)) {
        $appConfig = require $appConfigFile;
    }
    if (is_array($appConfig)) {
        $appConfig['app']['app_version'] = $version;
        @file_put_contents($appConfigFile, "<?php\nreturn " . var_export($appConfig, true) . ";\n");
    }

    // 清理临时目录
    update_rmdir($extractDir);

    return [
        'version' => $version,
        'copied_files' => $copied,
        'manifest' => $manifest,
    ];
}

/**
 * 递归删除目录
 */
function update_rmdir($dir)
{
    if (!is_dir($dir)) {
        return;
    }
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        if ($item->isDir()) {
            @rmdir($item->getPathname());
        } else {
            @unlink($item->getPathname());
        }
    }
    @rmdir($dir);
}
