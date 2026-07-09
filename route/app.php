<?php
use think\facade\Route;

Route::get('/', 'Index/index');
Route::get('/goods/:id', 'Index/goods');
Route::get('/category/:id', 'Index/category');
Route::get('/search', 'Index/search');
Route::post('/cart/add', 'Index/cartAdd');
Route::post('/order/create', 'Index/orderCreate');
Route::get('/order/:order_no', 'Index/order');
Route::get('/captcha', 'Index/captcha');
Route::get('/slider/captcha', 'Index/sliderCaptcha');
Route::post('/slider/verify', 'Index/sliderVerify');

Route::get('/login', 'User/login');
Route::post('/login', 'User/doLogin');
Route::get('/register', 'User/register');
Route::post('/register', 'User/doRegister');
Route::get('/logout', 'User/logout');

Route::group(function () {
    Route::get('/user/center', 'User/center');
    Route::get('/user/orders', 'User/orders');
    Route::get('/user/recharge', 'User/recharge');
    Route::post('/user/recharge', 'User/doRecharge');
    Route::get('/user/profile', 'User/profile');
    Route::post('/user/profile', 'User/saveProfile');
    Route::get('/user/messages', 'User/messages');
})->middleware(function ($request, \Closure $next) {
    if (!\think\facade\Session::has('user_id')) {
        return redirect('/login');
    }
    return $next($request);
});

Route::get('/admin/login', 'Admin/login');
Route::post('/admin/login', 'Admin/doLogin');
Route::get('/admin/logout', 'Admin/logout');
Route::get('/admin/slider', 'Admin/sliderCaptcha');
Route::post('/admin/slider/verify', 'Admin/sliderVerify');

Route::group(function () {
    Route::get('/admin', 'Admin/dashboard');
    Route::get('/admin/dashboard', 'Admin/dashboard');
    Route::get('/admin/screen', 'Admin/screen');
    
    Route::get('/admin/shop/users', 'Admin/shopUsers');
    Route::get('/admin/shop/realname', 'Admin/shopRealname');
    Route::get('/admin/shop/qualification', 'Admin/shopQualification');
    Route::get('/admin/shop/certification', 'Admin/shopCertification');
    Route::get('/admin/shop/risk', 'Admin/shopRisk');
    Route::get('/admin/shop/service', 'Admin/shopService');
    
    Route::get('/admin/message/publish', 'Admin/messagePublish');
    Route::post('/admin/message/publish', 'Admin/doMessagePublish');
    Route::get('/admin/message/list', 'Admin/messageList');
    
    Route::get('/admin/notice/publish', 'Admin/noticePublish');
    Route::post('/admin/notice/publish', 'Admin/doNoticePublish');
    Route::get('/admin/notice/list', 'Admin/noticeList');
    
    Route::get('/admin/system/site', 'Admin/systemSite');
    Route::post('/admin/system/site', 'Admin/saveSystemSite');
    Route::get('/admin/system/update', 'Admin/systemUpdate');
    Route::post('/admin/system/checkUpdate', 'Admin/checkUpdate');
    Route::post('/admin/system/upgrade', 'Admin/doUpgrade');
    Route::get('/admin/system/withdraw', 'Admin/systemWithdraw');
    Route::get('/admin/system/channel', 'Admin/systemChannel');
    
    Route::get('/admin/data/log', 'Admin/dataLog');
    Route::get('/admin/data/server', 'Admin/dataServer');
    Route::get('/admin/data/database', 'Admin/dataDatabase');
    Route::get('/admin/data/login', 'Admin/dataLogin');
})->middleware(function ($request, \Closure $next) {
    if (!\think\facade\Session::has('admin_id')) {
        return redirect('/admin/login');
    }
    return $next($request);
});

Route::miss(function () {
    return view('404');
});
