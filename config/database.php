<?php
/**
 * 数据库配置文件
 */
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'type'            => 'mysql',
            'hostname'        => env('DB_HOST', '127.0.0.1'),
            'database'        => env('DB_NAME', 'qeefg_auth'),
            'username'        => env('DB_USER', 'root'),
            'password'        => env('DB_PASS', ''),
            'hostport'        => env('DB_PORT', '3306'),
            'charset'         => 'utf8mb4',
            'prefix'          => 'qf_',
        ],
    ],
];

