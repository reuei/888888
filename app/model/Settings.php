<?php
namespace app\model;

/**
 * 系统设置模型
 */
class Settings extends BaseModel
{
    protected $table = 'settings';
    
    /**
     * 获取系统配置
     */
    public static function getConfig()
    {
        $settings = self::all();
        $config = [];
        
        foreach ($settings as $item) {
            $config[$item->key] = $item->value;
        }
        
        return $config;
    }
}