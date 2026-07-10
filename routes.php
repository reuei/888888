<?php
/**
 * 路由注册
 */

use Framework\Response;

if (!file_exists(ROOT_PATH . '/config/database.php') || filesize(ROOT_PATH . '/config/database.php') < 200) {
    if (strpos($GLOBALS['__route_check_path'] ?? '', '/install') === false) {
        Response::redirect('/install');
    }
}

$router->get('/', [Home\Controller\IndexController::class, 'index']);
$router->get('/category/{id}', [Home\Controller\IndexController::class, 'category']);
$router->get('/goods/{id}', [Home\Controller\IndexController::class, 'goods']);
$router->get('/search', [Home\Controller\IndexController::class, 'search']);
$router->get('/notice', [Home\Controller\IndexController::class, 'notice']);
$router->get('/notice/{id}', [Home\Controller\IndexController::class, 'noticeDetail']);

$router->get('/login', [User\Controller\AuthController::class, 'login']);
$router->post('/login', [User\Controller\AuthController::class, 'doLogin']);
$router->get('/register', [User\Controller\AuthController::class, 'register']);
$router->post('/register', [User\Controller\AuthController::class, 'doRegister']);
$router->get('/logout', [User\Controller\AuthController::class, 'logout']);
$router->get('/slider', [User\Controller\AuthController::class, 'slider']);
$router->post('/slider/verify', [User\Controller\AuthController::class, 'sliderVerify']);

$router->get('/user', [User\Controller\CenterController::class, 'index']);
$router->get('/user/profile', [User\Controller\CenterController::class, 'profile']);
$router->post('/user/profile', [User\Controller\CenterController::class, 'saveProfile']);
$router->get('/user/orders', [User\Controller\CenterController::class, 'orders']);
$router->get('/user/recharge', [User\Controller\CenterController::class, 'recharge']);
$router->post('/user/recharge', [User\Controller\CenterController::class, 'doRecharge']);
$router->get('/user/messages', [User\Controller\CenterController::class, 'messages']);
$router->get('/user/messages/read/{id}', [User\Controller\CenterController::class, 'readMessage']);
$router->post('/user/password', [User\Controller\CenterController::class, 'changePassword']);
$router->post('/user/avatar', [User\Controller\CenterController::class, 'uploadAvatar']);

$router->get('/admin/login', [Admin\Controller\AuthController::class, 'login']);
$router->post('/admin/login', [Admin\Controller\AuthController::class, 'doLogin']);
$router->get('/admin/logout', [Admin\Controller\AuthController::class, 'logout']);

$router->get('/admin', [Admin\Controller\DashboardController::class, 'index']);
$router->get('/admin/dashboard', [Admin\Controller\DashboardController::class, 'index']);
$router->get('/admin/screen', [Admin\Controller\DashboardController::class, 'screen']);

$router->get('/admin/shop/users', [Admin\Controller\ShopController::class, 'users']);
$router->get('/admin/shop/realname', [Admin\Controller\ShopController::class, 'realname']);
$router->get('/admin/shop/qualification', [Admin\Controller\ShopController::class, 'qualification']);
$router->get('/admin/shop/certification', [Admin\Controller\ShopController::class, 'certification']);
$router->get('/admin/shop/risk', [Admin\Controller\ShopController::class, 'risk']);
$router->get('/admin/shop/service', [Admin\Controller\ShopController::class, 'service']);
$router->post('/admin/shop/action', [Admin\Controller\ShopController::class, 'action']);

$router->get('/admin/message/publish', [Admin\Controller\MessageController::class, 'publish']);
$router->post('/admin/message/publish', [Admin\Controller\MessageController::class, 'doPublish']);
$router->get('/admin/message/list', [Admin\Controller\MessageController::class, 'list']);
$router->post('/admin/message/delete', [Admin\Controller\MessageController::class, 'delete']);

$router->get('/admin/notice/publish', [Admin\Controller\NoticeController::class, 'publish']);
$router->post('/admin/notice/publish', [Admin\Controller\NoticeController::class, 'doPublish']);
$router->get('/admin/notice/list', [Admin\Controller\NoticeController::class, 'list']);
$router->post('/admin/notice/delete', [Admin\Controller\NoticeController::class, 'delete']);

$router->get('/admin/system/site', [Admin\Controller\SystemController::class, 'site']);
$router->post('/admin/system/site', [Admin\Controller\SystemController::class, 'saveSite']);
$router->get('/admin/system/update', [Admin\Controller\SystemController::class, 'update']);
$router->post('/admin/system/update/check', [Admin\Controller\SystemController::class, 'checkUpdate']);
$router->post('/admin/system/update/apply', [Admin\Controller\SystemController::class, 'applyUpdate']);
$router->get('/admin/system/withdraw', [Admin\Controller\SystemController::class, 'withdraw']);
$router->post('/admin/system/withdraw/action', [Admin\Controller\SystemController::class, 'withdrawAction']);
$router->get('/admin/system/channel', [Admin\Controller\SystemController::class, 'channel']);
$router->post('/admin/system/channel/toggle', [Admin\Controller\SystemController::class, 'toggleChannel']);

$router->get('/admin/data/log', [Admin\Controller\DataController::class, 'log']);
$router->get('/admin/data/server', [Admin\Controller\DataController::class, 'server']);
$router->get('/admin/data/database', [Admin\Controller\DataController::class, 'database']);
$router->get('/admin/data/login', [Admin\Controller\DataController::class, 'login']);

$router->get('/license', [License\Controller\IndexController::class, 'index']);
$router->post('/license/api/check', [License\Controller\ApiController::class, 'check']);
$router->post('/license/api/verify', [License\Controller\ApiController::class, 'verify']);
$router->post('/license/api/activate', [License\Controller\ApiController::class, 'activate']);
$router->post('/license/api/heartbeat', [License\Controller\ApiController::class, 'heartbeat']);
$router->get('/license/api/list', [License\Controller\ApiController::class, 'listLicenses']);
$router->get('/license/admin', [License\Controller\AdminController::class, 'login']);
$router->post('/license/admin', [License\Controller\AdminController::class, 'doLogin']);
$router->get('/license/admin/dashboard', [License\Controller\AdminController::class, 'dashboard']);
$router->get('/license/admin/licenses', [License\Controller\AdminController::class, 'licenses']);
$router->get('/license/admin/domains', [License\Controller\AdminController::class, 'domains']);
$router->get('/license/admin/logs', [License\Controller\AdminController::class, 'logs']);
$router->get('/license/admin/logout', [License\Controller\AdminController::class, 'logout']);

$router->get('/install', [Install\Controller\IndexController::class, 'index']);
$router->post('/install/step1', [Install\Controller\IndexController::class, 'step1']);
$router->post('/install/step2', [Install\Controller\IndexController::class, 'step2']);
$router->post('/install/step3', [Install\Controller\IndexController::class, 'step3']);
$router->post('/install/test', [Install\Controller\IndexController::class, 'testDb']);
