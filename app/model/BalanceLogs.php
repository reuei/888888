<?php
namespace app\model;

/**
 * 余额日志模型
 */
class BalanceLogs extends BaseModel
{
    protected $table = 'balance_logs';
    
    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}