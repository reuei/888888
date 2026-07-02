<?php
/**
 * 开放 API 接口
 * 调用方式：/api/goods?app_id=xxx&timestamp=xxx&nonce=xxx&sign=xxx
 * 签名：md5(app_id + timestamp + nonce + app_secret)
 */
class Api extends Controller
{
    private $key = null;

    public function __construct()
    {
        parent::__construct();
        $this->disableLayout();
    }

    /**
     * 接口入口统一认证
     */
    private function auth($action)
    {
        $result = api_auth();
        if ($result['code'] !== 0) {
            api_log(input('app_id', ''), $action, $_GET + $_POST, $result['msg'], 0);
            json_error($result['msg']);
        }

        $this->key = $result['data'];
        if (!api_check_permission($this->key, $action)) {
            api_log($this->key['app_id'], $action, $_GET + $_POST, '无接口权限', 0);
            json_error('无接口权限');
        }
    }

    /**
     * 成功响应并记录日志
     */
    private function success($action, $msg, $data = [])
    {
        if ($this->key) {
            api_log($this->key['app_id'], $action, $_GET + $_POST, $msg, 1);
        }
        json_success($msg, $data);
    }

    /**
     * 商品列表
     */
    public function goods()
    {
        $this->auth('goods');

        $page = max(1, (int) input('page', 1));
        $pageSize = min(100, max(1, (int) input('page_size', 20)));
        $keyword = input('keyword', '');
        $categoryId = (int) input('category_id', 0);

        $filters = [
            'keyword' => $keyword,
            'category_id' => $categoryId,
            'has_stock' => true,
        ];

        $merchantId = (int) input('merchant_id', 0);
        if ($merchantId > 0) {
            $filters['merchant_id'] = $merchantId;
        }

        $built = build_goods_search_where($filters);

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_goods g WHERE {$built['where']}", $built['params']);
        $total = (int) ($count['total'] ?? 0);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT g.id, g.name, g.cover, g.price, g.original_price, g.stock, g.sold, g.status, c.name AS category_name, m.shop_name
             FROM jz_goods g
             LEFT JOIN jz_category c ON g.category_id = c.id
             LEFT JOIN jz_merchant m ON g.merchant_id = m.id
             WHERE {$built['where']}
             ORDER BY g.sold DESC, g.id DESC
             LIMIT {$offset}, {$pageSize}",
            $built['params']
        );

        foreach ($list as &$item) {
            $item['effective'] = goods_effective_price($item);
        }

        $this->success('goods', '获取成功', [
            'list' => $list,
            'page' => $page,
            'page_size' => $pageSize,
            'total' => $total,
        ]);
    }

    /**
     * 商品详情
     */
    public function goodsDetail()
    {
        $this->auth('goodsDetail');

        $id = (int) input('id', 0);
        if ($id <= 0) {
            json_error('商品ID错误');
        }

        $goods = Db::fetch(
            "SELECT g.*, c.name AS category_name, m.shop_name
             FROM jz_goods g
             LEFT JOIN jz_category c ON g.category_id = c.id
             LEFT JOIN jz_merchant m ON g.merchant_id = m.id
             WHERE g.id = ? AND g.status = 1",
            [$id]
        );

        if (!$goods) {
            json_error('商品不存在或已下架');
        }

        $goods['effective'] = goods_effective_price($goods);
        $this->success('goodsDetail', '获取成功', $goods);
    }

    /**
     * 创建订单
     */
    public function createOrder()
    {
        $this->auth('createOrder');

        $goodsId = (int) input('goods_id', 0);
        $quantity = max(1, (int) input('quantity', 1));
        $contact = trim(input('contact', ''));

        if ($goodsId <= 0) {
            json_error('商品ID错误');
        }
        if (!$contact) {
            json_error('联系方式不能为空');
        }

        $goods = Db::fetch("SELECT * FROM jz_goods WHERE id = ? AND status = 1", [$goodsId]);
        if (!$goods) {
            json_error('商品不存在或已下架');
        }

        $effective = goods_effective_price($goods);
        $unitPrice = $effective['price'];
        $totalAmount = round($unitPrice * $quantity, 2);

        // 库存校验
        $isSeckill = $effective['activity'] === 'seckill';
        if ($isSeckill) {
            $available = (int) $goods['seckill_stock'] - (int) $goods['seckill_sold'];
            if ($available < $quantity) {
                json_error('秒杀库存不足');
            }
        } elseif ($goods['type'] == 1 && $goods['stock'] < $quantity) {
            json_error('库存不足');
        }

        // 生成订单号
        $orderNo = date('YmdHis') . rand(1000, 9999);
        $orderId = Db::insert('jz_order', [
            'order_no' => $orderNo,
            'goods_id' => $goodsId,
            'goods_name' => $goods['name'],
            'merchant_id' => $goods['merchant_id'],
            'subsite_id' => $goods['subsite_id'],
            'user_id' => 0,
            'quantity' => $quantity,
            'price' => $unitPrice,
            'total_amount' => $totalAmount,
            'pay_amount' => $totalAmount,
            'client_ip' => get_client_ip(),
            'contact' => $contact,
            'status' => 0,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        // 扣库存
        if ($isSeckill) {
            Db::execute("UPDATE jz_goods SET seckill_sold = seckill_sold + ? WHERE id = ? AND seckill_stock - seckill_sold >= ?", [$quantity, $goodsId, $quantity]);
        } elseif ($goods['type'] == 1) {
            Db::execute("UPDATE jz_goods SET stock = stock - ? WHERE id = ? AND stock >= ?", [$quantity, $goodsId, $quantity]);
        }

        plugin_trigger('order_created', ['order_id' => $orderId, 'order_no' => $orderNo, 'goods_id' => $goodsId, 'amount' => $totalAmount]);

        $this->success('createOrder', '创建成功', ['order_id' => $orderId, 'order_no' => $orderNo, 'pay_amount' => $totalAmount]);
    }

    /**
     * 订单查询
     */
    public function orderQuery()
    {
        $this->auth('orderQuery');

        $orderNo = trim(input('order_no', ''));
        if (!$orderNo) {
            json_error('订单号不能为空');
        }

        $order = Db::fetch(
            "SELECT o.*, g.name AS goods_name FROM jz_order o LEFT JOIN jz_goods g ON o.goods_id = g.id WHERE o.order_no = ?",
            [$orderNo]
        );
        if (!$order) {
            json_error('订单不存在');
        }

        $cards = [];
        if ($order['status'] == 2) {
            $cards = Db::query("SELECT card_no, card_password FROM jz_card WHERE order_id = ?", [$order['id']]);
        }

        $this->success('orderQuery', '获取成功', [
            'order' => $order,
            'cards' => $cards,
        ]);
    }

    /**
     * 获取卡密（订单已支付且未发货时自动发货）
     */
    public function cards()
    {
        $this->auth('cards');

        $orderNo = trim(input('order_no', ''));
        if (!$orderNo) {
            json_error('订单号不能为空');
        }

        $order = Db::fetch("SELECT * FROM jz_order WHERE order_no = ? AND status = 2", [$orderNo]);
        if (!$order) {
            json_error('订单不存在或未支付');
        }

        $cards = Db::query("SELECT card_no, card_password FROM jz_card WHERE order_id = ?", [$order['id']]);
        $this->success('cards', '获取成功', ['cards' => $cards]);
    }
}
