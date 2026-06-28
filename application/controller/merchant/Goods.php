<?php
/**
 * 商户后台 - 商品管理
 */
class Merchant_Goods extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/merchant');
        if (!session('merchant_user')) {
            redirect(url('login') . '?type=merchant');
        }
    }

    /**
     * 商品列表
     */
    public function index()
    {
        $merchant = session('merchant_user');
        $keyword = input('keyword', '');
        $status = input('status', '');
        $categoryId = input('category_id', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = 'g.merchant_id = ?';
        $params = [$merchant['id']];

        if ($keyword) {
            $where .= ' AND g.name LIKE ?';
            $params[] = '%' . $keyword . '%';
        }
        if ($status !== '') {
            $where .= ' AND g.status = ?';
            $params[] = (int) $status;
        }
        if ($categoryId !== '') {
            $where .= ' AND g.category_id = ?';
            $params[] = (int) $categoryId;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_goods g WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT g.*, c.name AS category_name
             FROM jz_goods g
             LEFT JOIN jz_category c ON g.category_id = c.id
             WHERE {$where}
             ORDER BY g.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $categories = Db::query("SELECT id, name FROM jz_category WHERE status = 1 ORDER BY sort ASC, id ASC");

        $this->assign('title', '商品列表');
        $this->assign('list', $list);
        $this->assign('categories', $categories);
        $this->assign('keyword', $keyword);
        $this->assign('status', $status);
        $this->assign('categoryId', $categoryId);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('merchant/goods/index');
    }

    /**
     * 新增 / 编辑商品页面
     */
    public function create()
    {
        $merchant = session('merchant_user');
        $id = (int) input('id', 0);

        $goods = null;
        if ($id) {
            $goods = Db::fetch("SELECT * FROM jz_goods WHERE id = ? AND merchant_id = ?", [$id, $merchant['id']]);
            if (!$goods) {
                throw new Exception('商品不存在');
            }
        }

        $categories = Db::query("SELECT id, name FROM jz_category WHERE status = 1 ORDER BY sort ASC, id ASC");

        $this->assign('title', $goods ? '编辑商品' : '新增商品');
        $this->assign('goods', $goods);
        $this->assign('categories', $categories);
        $this->fetch('merchant/goods/create');
    }

    /**
     * 保存商品
     */
    public function save()
    {
        $merchant = session('merchant_user');
        $id = (int) input('id', 0);
        $name = trim(input('name', ''));
        $categoryId = (int) input('category_id', 0);
        $price = (float) input('price', 0);
        $originalPrice = (float) input('original_price', 0);
        $stock = (int) input('stock', 0);
        $lowStock = (int) input('low_stock', 10);
        $type = (int) input('type', 1);
        $content = input('content', '');
        $cover = input('cover', '');
        $status = (int) input('status', 1);

        if (!$name) {
            json_error('请输入商品名称');
        }
        if (!$categoryId) {
            json_error('请选择商品分类');
        }
        if ($price <= 0) {
            json_error('售价必须大于0');
        }
        if (!in_array($type, [1, 2, 3], true)) {
            json_error('商品类型错误');
        }

        $data = [
            'name' => $name,
            'category_id' => $categoryId,
            'price' => $price,
            'original_price' => $originalPrice,
            'stock' => max(0, $stock),
            'low_stock' => max(1, $lowStock),
            'type' => $type,
            'content' => $content,
            'cover' => $cover,
            'status' => $status,
        ];

        if ($id) {
            $goods = Db::fetch("SELECT * FROM jz_goods WHERE id = ? AND merchant_id = ?", [$id, $merchant['id']]);
            if (!$goods) {
                json_error('商品不存在');
            }
            // 卡密类商品不允许直接修改库存，需通过卡密管理
            if ($goods['type'] == 1) {
                unset($data['stock']);
            }
            Db::update('jz_goods', $data, 'id = ?', [$id]);
            json_success('商品更新成功', ['redirect' => url('merchant/goods')]);
        } else {
            $data['merchant_id'] = $merchant['id'];
            $data['subsite_id'] = $merchant['subsite_id'] ?? 0;
            $data['sold'] = 0;
            $data['create_time'] = date('Y-m-d H:i:s');
            Db::insert('jz_goods', $data);
            json_success('商品创建成功', ['redirect' => url('merchant/goods')]);
        }
    }

    /**
     * 切换商品上下架
     */
    public function toggleStatus()
    {
        $merchant = session('merchant_user');
        $id = (int) input('id', 0);
        $status = (int) input('status', 0);

        if (!$id || !in_array($status, [0, 1], true)) {
            json_error('参数错误');
        }

        $goods = Db::fetch("SELECT id FROM jz_goods WHERE id = ? AND merchant_id = ?", [$id, $merchant['id']]);
        if (!$goods) {
            json_error('商品不存在');
        }

        Db::execute(
            "UPDATE jz_goods SET status = ?, update_time = ? WHERE id = ?",
            [$status, date('Y-m-d H:i:s'), $id]
        );

        json_success($status == 1 ? '已上架' : '已下架');
    }

    /**
     * 删除商品
     */
    public function delete()
    {
        $merchant = session('merchant_user');
        $id = (int) input('id', 0);

        if (!$id) {
            json_error('参数错误');
        }

        $goods = Db::fetch("SELECT id FROM jz_goods WHERE id = ? AND merchant_id = ?", [$id, $merchant['id']]);
        if (!$goods) {
            json_error('商品不存在');
        }

        // 检查是否存在订单
        $orderCount = Db::fetch("SELECT COUNT(*) AS total FROM jz_order WHERE goods_id = ?", [$id]);
        if (($orderCount['total'] ?? 0) > 0) {
            json_error('该商品存在订单记录，无法删除');
        }

        Db::execute("DELETE FROM jz_card WHERE goods_id = ? AND merchant_id = ?", [$id, $merchant['id']]);
        Db::execute("DELETE FROM jz_goods WHERE id = ?", [$id]);

        json_success('商品已删除');
    }

    /**
     * 批量导入卡密页
     */
    public function import()
    {
        $merchant = session('merchant_user');
        $goodsList = Db::query("SELECT id, name FROM jz_goods WHERE merchant_id = ? AND type = 1 ORDER BY id DESC", [$merchant['id']]);

        $this->assign('title', '批量导入卡密');
        $this->assign('goodsList', $goodsList);
        $this->fetch('merchant/goods/import');
    }

    /**
     * 执行导入
     */
    public function doImport()
    {
        $merchant = session('merchant_user');
        $goodsId = (int) input('goods_id');
        $content = input('content', '');
        $separator = input('separator', 'newline');
        $dedup = input('dedup', '1');

        if (!$goodsId) {
            json_error('请选择商品');
        }
        if (!$content) {
            json_error('请输入或粘贴卡密内容');
        }

        $goods = Db::fetch("SELECT * FROM jz_goods WHERE id = ? AND merchant_id = ?", [$goodsId, $merchant['id']]);
        if (!$goods) {
            json_error('商品不存在或无权限');
        }

        $delim = ["newline" => "\n", "comma" => ",", "tab" => "\t"];
        $split = $delim[$separator] ?? "\n";
        if ($separator === 'custom') {
            $split = input('custom_sep', "\n");
        }

        $lines = explode($split, $content);
        $success = 0;
        $fail = 0;
        $errors = [];

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (mb_strlen($line) < 4) {
                $fail++;
                $errors[] = ['line' => $index + 1, 'content' => $line, 'reason' => '卡密长度不足'];
                continue;
            }

            if ($dedup) {
                $exists = Db::fetch("SELECT id FROM jz_card WHERE goods_id = ? AND content = ?", [$goodsId, $line]);
                if ($exists) {
                    $fail++;
                    $errors[] = ['line' => $index + 1, 'content' => $line, 'reason' => '卡密已存在'];
                    continue;
                }
            }

            Db::insert('jz_card', [
                'goods_id' => $goodsId,
                'merchant_id' => $merchant['id'],
                'content' => $line,
                'status' => 0,
                'create_time' => date('Y-m-d H:i:s'),
            ]);
            $success++;
        }

        if ($success > 0) {
            Db::execute("UPDATE jz_goods SET stock = stock + ? WHERE id = ?", [$success, $goodsId]);
        }

        json_success('导入完成', [
            'success' => $success,
            'fail' => $fail,
            'errors' => $errors,
        ]);
    }

    /**
     * 卡密管理（跳转）
     */
    public function card()
    {
        redirect(url('merchant/card'));
    }
}
