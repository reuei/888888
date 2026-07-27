<?php
namespace app\model;

use think\Model;

/**
 * 模型基类
 */
abstract class BaseModel extends Model
{
    // 自动时间戳
    protected $autoWriteTimestamp = true;
    
    // 时间字段名称
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';
    
    // 默认软删除
    // use \think\model\concern\SoftDelete;
    // protected $deleteTime = 'deleted_at';
    // protected $defaultSoftDelete = 0;
}