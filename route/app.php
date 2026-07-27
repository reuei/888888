<?php
/**
 * 路由配置
 */
use think\facade\Route;

// 首页
Route::get('/', 'index/index');

// 用户中心路由组
Route::group('user', function () {
    // 登录注册
    Route::get('login', 'user/login');
    Route::post('login', 'user/dologin');
    Route::get('register', 'user/register');
    Route::post('register', 'user/doregister');
    Route::get('logout', 'user/logout');
    
    // 用户中心
    Route::get('dashboard', 'user/dashboard');
    Route::get('workplace', 'user/workplace');
    
    // 余额管理
    Route::get('balance', 'user/balance');
    Route::get('recharge', 'user/recharge');
    Route::post('recharge', 'user/dorecharge');
    
    // 产品中心
    Route::get('products', 'user/products');
    Route::get('buy', 'user/buy');
    Route::get('my-products', 'user/myProducts');
    
    // 插件中心
    Route::get('plugins', 'user/plugins');
    Route::get('buy-plugin', 'user/buyPlugin');
    Route::get('my-plugins', 'user/myPlugins');
    
    // 日志查询
    Route::get('balance-log', 'user/balanceLog');
    Route::get('login-log', 'user/loginLog');
    Route::get('operation-log', 'user/operationLog');
    
    // 账户设置
    Route::get('settings', 'user/settings');
    Route::post('settings', 'user/updateSettings');
    Route::get('feedback', 'user/feedback');
    Route::post('feedback', 'user/submitFeedback');
});

// 后台管理路由组
Route::group('admin', function () {
    Route::get('login', 'admin/login');
    Route::post('login', 'admin/dologin');
    Route::get('logout', 'admin/logout');
    
    Route::get('dashboard', 'admin/dashboard');
    Route::get('users', 'admin/users');
    Route::get('products', 'admin/products');
    Route::get('licenses', 'admin/licenses');
    Route::get('orders', 'admin/orders');
    Route::get('settings', 'admin/settings');
});

// 授权查询
Route::get('license-query', 'index/licenseQuery');
Route::post('license-query', 'index/doLicenseQuery');

// 文档中心
Route::get('documents', 'index/documents');