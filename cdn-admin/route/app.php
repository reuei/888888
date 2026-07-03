<?php
use think\facade\Route;

// 健康检查
Route::get('api/health', 'health/index');

// 登录
Route::post('api/login', 'auth/login');

// 当前用户信息
Route::get('api/me', 'auth/profile');

// REST API：/api/:resource 支持 GET/POST/PUT/DELETE
Route::rule('api/:resource', 'api/index', 'GET|POST|PUT|DELETE');

// 安装向导
Route::get('install', 'install/index');
Route::post('install', 'install/index');

// CDN 站点入口
Route::get('cdn', 'index/cdn');

// React SPA 前端路由兜底：所有非文件/非 API 请求返回对应站点 HTML
Route::get('/:path', 'index/spa')->pattern(['path' => '[\w\-\/\.]+']);
Route::get('/', 'index/index');
