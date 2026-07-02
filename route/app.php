<?php
// +----------------------------------------------------------------------
// | 路由配置文件
// | 与原 main_legacy 项目 URL 结构保持兼容：/模块/控制器/操作?参数
// | 未匹配到以下显式规则时，ThinkPHP 默认路由将按 app/controller 目录解析
// +----------------------------------------------------------------------

use think\facade\Route;

// 安装向导（/install 映射到 /workspace/install/index.php）
Route::any('install/[:step]', function () {
    include APP_PATH . 'install/index.php';
    exit;
});

// 前台首页
Route::get('/', 'Index/index');
Route::get('index', 'Index/index');

// 登录/注销
Route::get('login', 'Login/index');
Route::post('login/doLogin', 'Login/doLogin');
Route::get('login/captcha', 'Login/captcha');
Route::get('login/logout', 'Login/logout');

// 开放 API 入口
Route::get('api/goods', 'Api/goods');
Route::get('api/goodsDetail', 'Api/goodsDetail');
Route::post('api/createOrder', 'Api/createOrder');
Route::get('api/orderQuery', 'Api/orderQuery');
Route::get('api/cards', 'Api/cards');

// 定时任务
Route::get('cron/backup', 'Cron/backup');

// OAuth
Route::get('oauth/:type', 'Oauth/index');
Route::get('oauth/callback', 'Oauth/callback');

// 客服聊天
Route::get('chat', 'Chat/index');

// 总站后台 /admin/控制器/操作（默认 index）
Route::rule('admin/:controller/:action', 'admin.:controller/:action');
Route::rule('admin/:controller', 'admin.:controller/index');

// 商户后台 /merchant/控制器/操作
Route::rule('merchant/:controller/:action', 'merchant.:controller/:action');
Route::rule('merchant/:controller', 'merchant.:controller/index');

// 分站后台 /subsite/控制器/操作
Route::rule('subsite/:controller/:action', 'subsite.:controller/:action');
Route::rule('subsite/:controller', 'subsite.:controller/index');

// 插件回调（保持 /plugin/xxx 形式）
Route::any('plugin/:action', 'Plugin/index');
