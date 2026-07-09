<?php
return [
    'app_namespace'    => 'app',
    'with_route'       => true,
    'default_app'      => 'index',
    'default_timezone' => 'Asia/Shanghai',
    'app_name'         => '玄武发卡',
    'app_version'      => '1.0.4',
    'app_map'          => [],
    'domain_bind'      => [],
    'deny_app_list'    => [],
    'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',
    'error_message'    => '页面错误！请稍后再试～',
    'show_error_msg'   => true,
];
