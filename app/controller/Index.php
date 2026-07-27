<?php
namespace app\controller;

use app\model\Products;
use app\model\Settings;

/**
 * 首页控制器
 */
class Index extends BaseController
{
    /**
     * 首页
     */
    public function index()
    {
        // 获取系统设置
        $settings = Settings::getConfig();
        
        // 获取在售产品
        $products = Products::where('status', 1)
            ->order('sort', 'desc')
            ->limit(6)
            ->select();
        
        // 统计数据
        $stats = [
            'products' => Products::where('status', 1)->count(),
            'users'    => \app\model\Users::count(),
            'licenses' => \app\model\Licenses::count(),
        ];
        
        return $this->fetch('', [
            'settings' => $settings,
            'products' => $products,
            'stats'    => $stats
        ]);
    }

    /**
     * 授权查询
     */
    public function licenseQuery()
    {
        return $this->fetch();
    }

    /**
     * 执行授权查询
     */
    public function doLicenseQuery()
    {
        $licenseKey = $this->request->post('license_key', '');
        
        if (empty($licenseKey)) {
            return $this->error('请输入授权密钥');
        }
        
        $license = \app\model\Licenses::where('license_key', $licenseKey)->find();
        
        if (!$license) {
            return $this->error('授权密钥不存在');
        }
        
        $product = Products::find($license->product_id);
        $user = \app\model\Users::find($license->user_id);
        
        return $this->success([
            'license' => $license,
            'product' => $product,
            'user'    => [
                'username' => $user->username
            ]
        ]);
    }

    /**
     * 文档中心
     */
    public function documents()
    {
        return $this->fetch();
    }
}