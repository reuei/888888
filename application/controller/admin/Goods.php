<?php
/**
 * 总站后台 - 商品管理示例
 */
class Admin_Goods extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/admin');
        if (!session('admin_user')) {
            redirect(url('login') . '?type=admin');
        }
    }

    public function index()
    {
        $keyword = input('keyword', '');
        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND name LIKE ?';
            $params[] = '%' . $keyword . '%';
        }

        $goods = Db::query("SELECT g.*, m.shop_name FROM jz_goods g LEFT JOIN jz_merchant m ON g.merchant_id = m.id WHERE {$where} ORDER BY g.id DESC LIMIT 20", $params);
        $this->assign('title', '全平台商品列表');
        $this->assign('goods', $goods);
        $this->assign('keyword', $keyword);
        $this->fetch('admin/goods/index');
    }
}
