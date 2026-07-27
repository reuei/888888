<?php
namespace app\controller;

class Index extends BaseController
{
    public function index()
    {
        $stmt = $this->db->query("SELECT * FROM qf_products WHERE status = 1 ORDER BY sort ASC LIMIT 6");
        $products = $stmt->fetchAll();
        
        return $this->render('index/index', [
            'products' => $products,
            'user' => $this->getUser()
        ]);
    }
    
    public function licenseQuery()
    {
        $licenseKey = $this->get('license_key');
        $result = null;
        
        if ($licenseKey) {
            $stmt = $this->db->prepare("SELECT l.*, p.name as product_name, u.username as user_name 
                FROM qf_licenses l 
                LEFT JOIN qf_products p ON l.product_id = p.id 
                LEFT JOIN qf_users u ON l.user_id = u.id 
                WHERE l.license_key = ?");
            $stmt->execute([$licenseKey]);
            $result = $stmt->fetch();
        }
        
        return $this->render('index/license-query', [
            'result' => $result,
            'licenseKey' => $licenseKey,
            'user' => $this->getUser()
        ]);
    }
    
    public function documents()
    {
        $documents = [
            ['title' => '快速入门指南', 'description' => '帮助您快速了解和使用QEEFG授权系统', 'category' => '入门'],
            ['title' => 'API接口文档', 'description' => '详细的API接口说明和调用示例', 'category' => '开发'],
            ['title' => '授权管理手册', 'description' => '授权密钥的创建、管理和使用方法', 'category' => '管理'],
            ['title' => '常见问题解答', 'description' => '使用过程中常见问题的解决方案', 'category' => '帮助'],
            ['title' => '产品更新日志', 'description' => '记录产品的所有更新和改进', 'category' => '更新'],
            ['title' => '服务协议', 'description' => 'QEEFG授权站服务使用协议', 'category' => '法律'],
        ];
        
        return $this->render('index/documents', [
            'documents' => $documents,
            'user' => $this->getUser()
        ]);
    }
}
