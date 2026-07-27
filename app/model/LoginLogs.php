<?php
namespace app\model;

/**
 * 登录日志模型
 */
class LoginLogs extends BaseModel
{
    protected $table = 'login_logs';
    
    // 无需时间戳
    protected $autoWriteTimestamp = false;
}