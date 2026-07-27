<?php
/**
 * 数据库配置
 */
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'type'            => 'mysql',
            'hostname'        => env('database.hostname', '127.0.0.1'),
            'database'        => env('database.database', 'qeefg_auth'),
            'username'        => env('database.username', 'root'),
            'password'        => env('database.password', ''),
            'hostport'        => env('database.hostport', '3306'),
            'charset'         => env('database.charset', 'utf8mb4'),
            'prefix'          => env('database.prefix', 'qf_'),
            'debug'           => env('app.debug', true),
            'fields_cache'    => false,
            'schema_cache'    => false,
        ],
    ],
];