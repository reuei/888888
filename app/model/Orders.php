<?php
namespace app\model;

/**
 * 订单模型
 */
class Orders extends BaseModel
{
    protected $table = 'orders';
    
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
     * 生成订单号
     */
    public static function generateOrderNo()
    {
        return date('YmdHis') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}