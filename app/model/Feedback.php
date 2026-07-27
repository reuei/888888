<?php
namespace app\model;

/**
 * 意见反馈模型
 */
class Feedback extends BaseModel
{
    protected $table = 'feedback';
    
    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
}