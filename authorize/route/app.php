<?php
// +----------------------------------------------------------------------
// | 路由规则（兼容原 authorize_legacy URL 路径）
// +----------------------------------------------------------------------

use think\facade\Route;

// 安装向导
Route::any('install/[:step]', function () {
    include app()->getAppPath() . '..' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'index.php';
    exit;
});

// 前台首页
Route::get('/', 'Index/index');
Route::get('index', 'Index/index');
Route::get('index/:action', 'Index/:action');

// 登录/注册
Route::get('login', 'Login/index');
Route::rule('login/:action', 'Login/:action');

// 用户中心
Route::get('user', 'User/index');
Route::rule('user/:action', 'User/:action');

// 授权产品
Route::get('product', 'Product/index');
Route::get('product/:action', 'Product/:action');

// 插件市场
Route::get('plugin', 'Plugin/index');
Route::get('plugin/:action', 'Plugin/:action');

// 订单
Route::rule('order/:action', 'Order/:action');

// 后台登录
Route::get('admin/admin', 'admin.Admin/index');
Route::rule('admin/admin/:action', 'admin.Admin/:action');

// 后台仪表盘
Route::get('admin/dashboard', 'admin.Dashboard/index');
Route::get('admin/dashboard/:action', 'admin.Dashboard/:action');

// 后台授权码管理
Route::get('admin/license', 'admin.License/index');
Route::rule('admin/license/:action', 'admin.License/:action');

// 后台订单管理
Route::get('admin/order', 'admin.Order/index');
Route::get('admin/order/:action', 'admin.Order/:action');

// 后台插件管理
Route::get('admin/plugin', 'admin.Plugin/index');
Route::rule('admin/plugin/:action', 'admin.Plugin/:action');

// 后台产品管理
Route::get('admin/product', 'admin.Product/index');
Route::rule('admin/product/:action', 'admin.Product/:action');

// 后台充值管理
Route::get('admin/recharge', 'admin.Recharge/index');
Route::rule('admin/recharge/:action', 'admin.Recharge/:action');

// 后台系统设置
Route::get('admin/setting', 'admin.Setting/index');
Route::rule('admin/setting/:action', 'admin.Setting/:action');

// 后台用户管理
Route::get('admin/user', 'admin.User/index');
Route::rule('admin/user/:action', 'admin.User/:action');

// 后台版本管理
Route::get('admin/version', 'admin.Version/index');
Route::rule('admin/version/:action', 'admin.Version/:action');

// API
Route::get('api/download', 'api.Download/index');
Route::post('api/license/:action', 'api.License/:action');
