<?php
namespace app\model;

/**
 * 操作日志模型
 */
class OperationLogs extends BaseModel
{
    protected $table = 'operation_logs';
    
    // 无需时间戳
    protected $autoWriteTimestamp = false;
}