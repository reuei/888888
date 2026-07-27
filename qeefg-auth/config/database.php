<?php
return [
    'connections' => [
        'mysql' => [
            'hostname' => '127.0.0.1',
            'database' => 'qeefg_auth',
            'username' => 'root',
            'password' => '',
            'hostport' => '3306',
            'charset' => 'utf8mb4',
            'prefix' => 'qf_'
        ]
    ],
    'site' => [
        'name' => 'QEEFG授权站',
        'title' => 'QEEFG授权站 - 专业软件授权管理平台',
        'description' => 'QEEFG授权站是一个专业的软件授权管理平台，提供软件授权、许可证管理、产品管理等服务。',
        'keywords' => '授权,许可证,软件授权,授权管理',
        'url' => 'https://auth.qeefg.com',
        'email' => 'support@qeefg.com',
        'qq' => '123456789',
        'theme_color' => '#667eea'
    ],
    'auth' => [
        'session_key' => 'qeefg_auth_session',
        'token_expire' => 86400
    ]
];
