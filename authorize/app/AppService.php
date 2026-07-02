<?php
declare (strict_types = 1);

namespace app;

use think\Service;
use think\facade\Config;

/**
 * 应用服务类
 */
class AppService extends Service
{
    public function register()
    {
        // 服务注册
    }

    public function boot()
    {
        // 初始化授权站遗留 PDO 数据库封装
        $dbConfig = Config::get('database.connections.' . Config::get('database.default', 'mysql'), []);
        if (!empty($dbConfig) && !empty($dbConfig['database'])) {
            Db::init($dbConfig);
        }
    }
}
