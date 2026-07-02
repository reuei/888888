<?php
/**
 * 授权产品控制器
 */

namespace app\controller;

use app\BaseController;
use app\Db;

class Product extends BaseController
{
    public function index()
    {
        $page = max(1, (int) input('page', 1));
        $pageSize = 12;
        $keyword = trim(input('keyword', ''));

        $where = 'status = 1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (name LIKE ? OR description LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM qef_product WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query("SELECT * FROM qef_product WHERE {$where} ORDER BY sort DESC, id DESC LIMIT {$offset}, {$pageSize}", $params);

        $this->assign('title', '授权产品');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('product/index');
    }

    public function detail()
    {
        $id = (int) input('id', 0);
        $product = Db::fetch("SELECT * FROM qef_product WHERE id = ? AND status = 1", [$id]);
        if (!$product) {
            throw new \Exception('产品不存在或已下架');
        }

        $this->assign('title', $product['name']);
        $this->assign('product', $product);
        $this->fetch('product/detail');
    }
}
