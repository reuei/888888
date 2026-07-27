<?php
namespace app\model;

/**
 * 授权模型
 */
class Licenses extends BaseModel
{
    protected $table = 'licenses';
    
    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
    
    /**
     * 关联产品
     */
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
    
    /**
     * 关联订单
     */
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }
    
    /**
     * 生成授权密钥
     */
    public static function generateKey()
    {
        return strtoupper(bin2hex(random_bytes(16)));
    }
    
    /**
     * 检查是否过期
     */
    public function isExpired()
    {
        if (!$this->expires_at) {
            return false; // 永久授权
        }
        return strtotime($this->expires_at) < time();
    }
}