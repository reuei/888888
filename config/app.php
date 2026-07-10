<?php
return [
    'name' => '玄武发卡',
    'version' => '1.0.5',
    'license_version' => '1.1.1',
    'debug' => false,
    'timezone' => 'Asia/Shanghai',
    'default_controller' => 'Home',
    'default_action' => 'index',
    'license' => [
        'api_url' => 'https://license.xuanwu.com',
        'key' => '',
    ],
    'cache' => [
        'type' => 'file',
    ],
    'session' => [
        'name' => 'XUANWU_SID',
        'expire' => 7200,
    ],
];
