<?php
/**
 * 后台授权产品管理
 */

namespace app\controller\admin;

use app\BaseController;
use app\Db;
use think\App;

class Product extends BaseController
{
    public function __construct(App $app)
    {
        parent::__construct($app);
        require_admin_login();
        $this->setLayout('layout/admin');
    }

    public function index()
    {
        $page = max(1, (int) input('page', 1));
        $pageSize = 15;
        $keyword = trim(input('keyword', ''));

        $where = '1=1';
        $params = [];
        if ($keyword) {
            $where .= ' AND name LIKE ?';
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
        $this->fetch('admin/product/index');
    }

    public function add()
    {
        $this->assign('title', '新增产品');
        $this->assign('product', []);
        $this->fetch('admin/product/edit');
    }

    public function edit()
    {
        $id = (int) input('id', 0);
        $product = Db::fetch("SELECT * FROM qef_product WHERE id = ?", [$id]);
        if (!$product) {
            throw new \Exception('产品不存在');
        }
        $this->assign('title', '编辑产品');
        $this->assign('product', $product);
        $this->fetch('admin/product/edit');
    }

    public function save()
    {
        $id = (int) input('id', 0);
        $name = trim(input('name', ''));
        $description = trim(input('description', ''));
        $price = (float) input('price', 0);
        $licenseType = input('license_type', 'code');
        $validDays = (int) input('valid_days', 0);
        $sort = (int) input('sort', 0);
        $status = (int) input('status', 1);

        if (!$name) {
            json_error('请输入产品名称');
        }
        if (!in_array($licenseType, ['code', 'domain'], true)) {
            json_error('授权类型错误');
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'license_type' => $licenseType,
            'valid_days' => $validDays,
            'sort' => $sort,
            'status' => $status,
        ];

        if ($id) {
            Db::update('qef_product', $data, 'id = ?', [$id]);
            admin_log('编辑产品', ['id' => $id]);
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            Db::insert('qef_product', $data);
            admin_log('新增产品', ['name' => $name]);
        }

        json_success('保存成功', ['redirect' => url('admin/product')]);
    }

    public function toggleStatus()
    {
        $id = (int) input('id', 0);
        $product = Db::fetch("SELECT status FROM qef_product WHERE id = ?", [$id]);
        if (!$product) {
            json_error('产品不存在');
        }
        $status = $product['status'] == 1 ? 0 : 1;
        Db::update('qef_product', ['status' => $status], 'id = ?', [$id]);
        admin_log('切换产品状态', ['id' => $id, 'status' => $status]);
        json_success('操作成功');
    }

    public function delete()
    {
        $id = (int) input('id', 0);
        Db::delete('qef_product', 'id = ?', [$id]);
        admin_log('删除产品', ['id' => $id]);
        json_success('删除成功');
    }
}
