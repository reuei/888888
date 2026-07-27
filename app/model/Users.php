<?php
namespace app\model;

/**
 * 用户模型
 */
class Users extends BaseModel
{
    protected $table = 'users';
    
    /**
     * 用户授权关联
     */
    public function licenses()
    {
        return $this->hasMany(Licenses::class, 'user_id');
    }
    
    /**
     * 用户订单关联
     */
    public function orders()
    {
        return $this->hasMany(Orders::class, 'user_id');
    }
}