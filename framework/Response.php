<?php
namespace Framework;

class Response
{
    public static function json($data, $code = 0, $msg = 'success', $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['code' => $code, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function success($msg = 'success', $data = null)
    {
        self::json($data, 0, $msg);
    }

    public static function error($msg = 'error', $code = 1, $data = null, $status = 200)
    {
        self::json($data, $code, $msg, $status);
    }

    public static function html($content, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');
        echo $content;
        exit;
    }

    public static function redirect($url, $status = 302)
    {
        http_response_code($status);
        header('Location: ' . $url);
        exit;
    }

    public static function notFound()
    {
        http_response_code(404);
        self::html('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>404</title>'
            . '<style>body{font-family:sans-serif;background:#0a0a0a;color:#e5e5e5;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}'
            . '.box{text-align:center;padding:40px}.code{font-size:96px;font-weight:bold;color:#10b981;margin:0}.msg{color:#a3a3a3;margin-top:16px;font-size:18px}</style></head>'
            . '<body><div class="box"><h1 class="code">404</h1><p class="msg">页面不存在</p>'
            . '<a href="/" style="color:#10b981;text-decoration:none;margin-top:24px;display:inline-block">返回首页</a></div></body></html>');
    }
}
