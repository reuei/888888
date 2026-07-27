<?php
namespace app\model;

/**
 * 产品模型
 */
class Products extends BaseModel
{
    protected $table = 'products';
    
    /**
     * 产品授权关联
     */
    public function licenses()
    {
        return $this->hasMany(Licenses::class, 'product_id');
    }
    
    /**
     * 产品订单关联
     */
    public function orders()
    {
        return $this->hasMany(Orders::class, 'product_id');
    }
}