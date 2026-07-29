<?php
/**
 * 应用配置
 */
return [
    'app_name'        => 'QEEFG授权站',
    'app_debug'       => env('APP_DEBUG', true),
    'app_trace'       => env('APP_TRACE', false),
    'default_timezone' => 'Asia/Shanghai',
    'default_lang'    => 'zh-cn',
    'default_module'  => 'index',
    'default_controller' => 'Index',
    'default_action'  => 'index',
    'url_route_on'    => true,
    'url_route_must'  => false,
    'url_reverse_on'  => true,
    'url_html_suffix' => 'html',
    'url_common_param' => true,
];