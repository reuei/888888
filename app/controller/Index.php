<?php
namespace app\controller;

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
        // 获取产品列表
        $stmt = $this->db->prepare("SELECT * FROM qf_products WHERE status = 1 ORDER BY sort DESC LIMIT 6");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 获取统计数据
        $stats = [
            'products' => $this->db->query("SELECT COUNT(*) FROM qf_products WHERE status = 1")->fetchColumn(),
            'users' => $this->db->query("SELECT COUNT(*) FROM qf_users")->fetchColumn(),
            'licenses' => $this->db->query("SELECT COUNT(*) FROM qf_licenses")->fetchColumn(),
        ];
        
        return $this->render('index/index', [
            'products' => $products,
            'stats' => $stats
        ]);
    }
    
    /**
     * 授权查询
     */
    public function licenseQuery()
    {
        return $this->render('index/license-query');
    }
    
    /**
     * 文档中心
     */
    public function documents()
    {
        return $this->render('index/documents');
    }
}