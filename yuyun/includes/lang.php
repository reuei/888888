<?php
function current_lang(): string {
    static $lang = null;
    if ($lang !== null) {
        return $lang;
    }
    $allowed = ['zh', 'en'];
    if (isset($_GET['lang']) && in_array($_GET['lang'], $allowed, true)) {
        $lang = $_GET['lang'];
        setcookie('yuyun_lang', $lang, time() + 86400 * 30, '/');
        $_SESSION['yuyun_lang'] = $lang;
        return $lang;
    }
    if (!empty($_SESSION['yuyun_lang']) && in_array($_SESSION['yuyun_lang'], $allowed, true)) {
        $lang = $_SESSION['yuyun_lang'];
        return $lang;
    }
    if (!empty($_COOKIE['yuyun_lang']) && in_array($_COOKIE['yuyun_lang'], $allowed, true)) {
        $lang = $_COOKIE['yuyun_lang'];
        return $lang;
    }
    $lang = setting('site_language', 'zh') ?: 'zh';
    return $lang;
}

function L(string $key, string $default = ''): string {
    static $dict = [];
    $lang = current_lang();
    if (!isset($dict[$lang])) {
        $file = YUYUN_ROOT . '/lang/' . $lang . '.php';
        $dict[$lang] = is_file($file) ? require $file : [];
    }
    return $dict[$lang][$key] ?? $default;
}
