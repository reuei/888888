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
 * 检查当前管理员是否属于指定角色组
 */
function check_admin_role($allowedRoles = ['super', 'admin', 'operator'])
{
    $admin = session('admin_user');
    if (empty($admin) || !in_array($admin['role'] ?? '', $allowedRoles, true)) {
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
