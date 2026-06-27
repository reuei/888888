<?php
/**
 * C端前台控制器
 */
class Index extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/main');
    }

    /**
     * 读取模板配置
     */
    private function getTemplateConfig()
    {
        $default = [
            'home_seo_title' => '',
            'home_category_limit' => '12',
            'home_show_categories' => '1',
            'home_show_articles' => '1',
            'home_article_limit' => '5',
            'home_goods_order' => 'sold',
            'home_goods_limit' => '24',
            'home_show_stats' => '1',
            'home_stats_text' => '平台交易 安全快捷',
            'goods_seo_title' => '全部商品',
            'goods_page_size' => '24',
            'goods_default_sort' => 'sold',
            'goods_show_stock' => '1',
            'goods_show_sold' => '1',
            'goods_show_merchant' => '1',
            'goods_show_recommend' => '1',
            'goods_recommend_limit' => '6',
            'goods_empty_tip' => '暂无相关商品',
        ];

        $rows = Db::query("SELECT cfg_key, cfg_value FROM jz_config WHERE cfg_group = 'template'");
        $config = [];
        foreach ($rows as $row) {
            $shortKey = substr($row['cfg_key'], 10);
            $config[$shortKey] = $row['cfg_value'];
        }

        return array_merge($default, $config);
    }

    /**
     * 首页
     */
    public function index()
    {
        $tpl = $this->getTemplateConfig();

        // 导航分类
        $categories = [];
        if (($tpl['home_show_categories'] ?? '1') === '1') {
            $limit = (int) ($tpl['home_category_limit'] ?? 12);
            $categories = Db::query("SELECT * FROM jz_category WHERE status = 1 AND is_nav = 1 ORDER BY sort DESC, id ASC LIMIT {$limit}");
        }

        // 推荐商品（上架 + 有库存）
        $order = $this->resolveGoodsOrder($tpl['home_goods_order'] ?? 'sold');
        $limit = (int) ($tpl['home_goods_limit'] ?? 24);
        $goods = Db::query(
            "SELECT g.*, m.shop_name, c.name as category_name
             FROM jz_goods g
             LEFT JOIN jz_merchant m ON g.merchant_id = m.id
             LEFT JOIN jz_category c ON g.category_id = c.id
             WHERE g.status = 1 AND g.stock > 0
             ORDER BY {$order}
             LIMIT {$limit}"
        );

        // 公告
        $articles = [];
        if (($tpl['home_show_articles'] ?? '1') === '1') {
            $articleLimit = (int) ($tpl['home_article_limit'] ?? 5);
            $articles = Db::query("SELECT id, title, create_time FROM jz_article WHERE status = 1 ORDER BY id DESC LIMIT {$articleLimit}");
        }

        // 首页广告
        $homeBanner = Db::query("SELECT * FROM jz_ad WHERE position = 'home_banner' AND status = 1 ORDER BY sort DESC, id DESC LIMIT 5");
        $homeTop = Db::query("SELECT * FROM jz_ad WHERE position = 'home_top' AND status = 1 ORDER BY sort DESC, id DESC LIMIT 3");

        $title = $tpl['home_seo_title'] ?: site_config('site_name', '鲸商城 Pro');

        $this->assign('title', $title);
        $this->assign('tpl', $tpl);
        $this->assign('categories', $categories);
        $this->assign('goods', $goods);
        $this->assign('articles', $articles);
        $this->assign('homeBanner', $homeBanner);
        $this->assign('homeTop', $homeTop);
        $this->fetch('index/index');
    }

    /**
     * 解析商品排序
     */
    private function resolveGoodsOrder($order)
    {
        $map = [
            'sold' => 'g.sold DESC, g.id DESC',
            'id' => 'g.id DESC',
            'price_asc' => 'g.price ASC, g.id DESC',
            'price_desc' => 'g.price DESC, g.id DESC',
        ];
        return $map[$order] ?? $map['sold'];
    }

    /**
     * 分类商品列表 / 购卡页
     */
    public function category()
    {
        $tpl = $this->getTemplateConfig();
        $categoryId = (int) input('id', 0);
        $keyword = input('keyword', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = (int) ($tpl['goods_page_size'] ?? 24);
        $sort = input('sort', $tpl['goods_default_sort'] ?? 'sold');

        $where = 'g.status = 1 AND g.stock > 0';
        $params = [];

        if ($categoryId) {
            // 支持父分类下所有子分类
            $subIds = Db::query("SELECT id FROM jz_category WHERE parent_id = ? AND status = 1", [$categoryId]);
            $ids = array_merge([$categoryId], array_column($subIds, 'id'));
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $where .= " AND g.category_id IN ({$placeholders})";
            $params = array_merge($params, $ids);
        }

        if ($keyword) {
            $where .= ' AND g.name LIKE ?';
            $params[] = '%' . $keyword . '%';
        }

        $count = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_goods g WHERE {$where}",
            $params
        );
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $order = $this->resolveGoodsOrder($sort);
        $list = Db::query(
            "SELECT g.*, m.shop_name, c.name as category_name
             FROM jz_goods g
             LEFT JOIN jz_merchant m ON g.merchant_id = m.id
             LEFT JOIN jz_category c ON g.category_id = c.id
             WHERE {$where}
             ORDER BY {$order}
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $category = null;
        if ($categoryId) {
            $category = Db::fetch("SELECT * FROM jz_category WHERE id = ? AND status = 1", [$categoryId]);
        }

        // 分类页广告
        $categoryTop = Db::query("SELECT * FROM jz_ad WHERE position = 'category_top' AND status = 1 ORDER BY sort DESC, id DESC LIMIT 3");

        $title = $category ? h($category['name']) : h($tpl['goods_seo_title'] ?? '全部商品');

        $this->assign('title', $title);
        $this->assign('tpl', $tpl);
        $this->assign('category', $category);
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('sort', $sort);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->assign('categoryTop', $categoryTop);
        $this->fetch('index/category');
    }

    /**
     * 商品详情
     */
    public function goods()
    {
        $id = (int) input('id', 0);
        $goods = Db::fetch(
            "SELECT g.*, m.shop_name, m.id as merchant_id, c.name as category_name
             FROM jz_goods g
             LEFT JOIN jz_merchant m ON g.merchant_id = m.id
             LEFT JOIN jz_category c ON g.category_id = c.id
             WHERE g.id = ? AND g.status = 1",
            [$id]
        );

        if (!$goods) {
            throw new Exception('商品不存在或已下架');
        }

        // 同类推荐
        $recommend = Db::query(
            "SELECT id, name, cover, price, stock, sold FROM jz_goods
             WHERE category_id = ? AND status = 1 AND id != ?
             ORDER BY sold DESC LIMIT 6",
            [$goods['category_id'], $id]
        );

        // 商品详情页广告
        $goodsBottom = Db::query("SELECT * FROM jz_ad WHERE position = 'goods_bottom' AND status = 1 ORDER BY sort DESC, id DESC LIMIT 3");

        $this->assign('title', h($goods['name']));
        $this->assign('goods', $goods);
        $this->assign('recommend', $recommend);
        $this->assign('goodsBottom', $goodsBottom);
        $this->fetch('index/goods');
    }

    /**
     * 创建订单（Ajax）
     */
    public function buy()
    {
        $goodsId = (int) input('goods_id', 0);
        $quantity = max(1, (int) input('quantity', 1));
        $contact = trim(input('contact', ''));
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? '';

        if (!$goodsId) {
            json_error('请选择商品');
        }

        $goods = Db::fetch(
            "SELECT g.*, m.shop_name FROM jz_goods g
             LEFT JOIN jz_merchant m ON g.merchant_id = m.id
             WHERE g.id = ? AND g.status = 1",
            [$goodsId]
        );
        if (!$goods) {
            json_error('商品不存在或已下架');
        }

        // 风控配置
        $risk = $this->getRiskConfig();

        // 联系方式校验
        if (($risk['contact_required'] ?? '1') === '1' && !$contact) {
            json_error('请填写联系方式，用于查询订单');
        }

        // 黑名单关键词
        $blacklist = trim($risk['blacklist_words'] ?? '');
        if ($blacklist) {
            $words = array_filter(array_map('trim', explode(',', $blacklist)));
            $checkText = $contact . ' ' . $goods['name'];
            foreach ($words as $word) {
                if ($word && mb_stripos($checkText, $word) !== false) {
                    json_error('订单存在风险，请联系客服');
                }
            }
        }

        // 金额校验
        $totalAmount = round($goods['price'] * $quantity, 2);
        $minAmount = (float) ($risk['min_amount'] ?? 0.01);
        $maxAmount = (float) ($risk['max_amount'] ?? 50000);
        if ($totalAmount < $minAmount) {
            json_error('订单金额低于最小限额 ¥' . $minAmount);
        }
        if ($totalAmount > $maxAmount) {
            json_error('订单金额超过最大限额 ¥' . $maxAmount);
        }

        // 同IP限购
        if (($risk['ip_limit'] ?? '0') === '1') {
            $ipLimitCount = (int) ($risk['ip_limit_count'] ?? 0);
            if ($ipLimitCount > 0) {
                $todayCount = Db::fetch(
                    "SELECT COUNT(*) AS total FROM jz_order WHERE client_ip = ? AND create_time > ?",
                    [$clientIp, date('Y-m-d 00:00:00')]
                );
                if ((int) ($todayCount['total'] ?? 0) >= $ipLimitCount) {
                    json_error('今日下单次数已达上限');
                }
            }
        }

        // 卡密类商品校验库存
        if ($goods['type'] == 1 && $goods['stock'] < $quantity) {
            json_error('库存不足，当前剩余 ' . $goods['stock']);
        }

        // 金额随机化（用于规避风控）
        $payAmount = $totalAmount;
        if (($risk['amount_jitter'] ?? '0') === '1') {
            $range = (float) ($risk['jitter_range'] ?? 0.01);
            $jitter = $totalAmount * $range * (mt_rand(-100, 100) / 100);
            $payAmount = round($totalAmount + $jitter, 2);
            $payAmount = max(0.01, $payAmount);
        }

        $orderNo = 'JZ' . date('YmdHis') . mt_rand(1000, 9999);

        $orderId = Db::insert('jz_order', [
            'order_no' => $orderNo,
            'user_id' => 0,
            'merchant_id' => $goods['merchant_id'],
            'subsite_id' => $goods['subsite_id'],
            'goods_id' => $goods['id'],
            'goods_name' => $goods['name'],
            'quantity' => $quantity,
            'price' => $goods['price'],
            'total_amount' => $totalAmount,
            'pay_amount' => $payAmount,
            'status' => 0,
            'client_ip' => $clientIp,
            'contact' => $contact,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        // 扣减库存（下单即冻结库存，支付后发货）
        if ($goods['type'] == 1) {
            Db::execute("UPDATE jz_goods SET stock = stock - ? WHERE id = ? AND stock >= ?", [$quantity, $goodsId, $quantity]);
        }

        json_success('订单创建成功', [
            'order_id' => $orderId,
            'order_no' => $orderNo,
            'redirect' => url('index/pay', ['order_no' => $orderNo]),
        ]);
    }

    /**
     * 读取风控配置
     */
    private function getRiskConfig()
    {
        $default = [
            'amount_jitter' => '0',
            'jitter_range' => '0.01',
            'min_amount' => '0.01',
            'max_amount' => '50000.00',
            'ip_limit' => '0',
            'ip_limit_count' => '10',
            'blacklist_words' => '',
            'contact_required' => '1',
        ];

        $rows = Db::query("SELECT cfg_key, cfg_value FROM jz_config WHERE cfg_group = 'risk'");
        $config = [];
        foreach ($rows as $row) {
            $shortKey = substr($row['cfg_key'], 5);
            $config[$shortKey] = $row['cfg_value'];
        }

        return array_merge($default, $config);
    }

    /**
     * 支付页
     */
    public function pay()
    {
        $orderNo = input('order_no', '');
        $order = Db::fetch(
            "SELECT o.*, m.shop_name FROM jz_order o
             LEFT JOIN jz_merchant m ON o.merchant_id = m.id
             WHERE o.order_no = ?",
            [$orderNo]
        );

        if (!$order) {
            throw new Exception('订单不存在');
        }
        if ($order['status'] != 0) {
            redirect(url('index/order', ['no' => $orderNo]));
        }

        // 可用支付渠道
        $channels = Db::query("SELECT * FROM jz_payment_channel WHERE status = 1 ORDER BY sort ASC");
        if (empty($channels)) {
            // 默认给一个模拟渠道
            $channels = [
                ['code' => 'alipay', 'name' => '支付宝'],
                ['code' => 'wxpay', 'name' => '微信支付'],
            ];
        }

        $this->assign('title', '订单支付');
        $this->assign('order', $order);
        $this->assign('channels', $channels);
        $this->fetch('index/pay');
    }

    /**
     * 执行支付（Ajax / 模拟）
     */
    public function doPay()
    {
        $orderNo = input('order_no', '');
        $channel = input('channel', 'alipay');

        $order = Db::fetch("SELECT * FROM jz_order WHERE order_no = ? AND status = 0", [$orderNo]);
        if (!$order) {
            json_error('订单不存在或已支付');
        }

        $payTime = date('Y-m-d H:i:s');

        Db::execute(
            "UPDATE jz_order SET status = 1, pay_channel = ?, pay_time = ?, update_time = ? WHERE id = ?",
            [$channel, $payTime, $payTime, $order['id']]
        );

        // 卡密类自动发货
        if ($order['quantity'] > 0) {
            $cards = Db::query(
                "SELECT * FROM jz_card WHERE goods_id = ? AND status = 0 ORDER BY id ASC LIMIT ?",
                [$order['goods_id'], (int) $order['quantity']]
            );

            $cardIds = array_column($cards, 'id');
            $cardContent = implode("\n", array_column($cards, 'content'));

            if (!empty($cardIds)) {
                $placeholders = implode(',', array_fill(0, count($cardIds), '?'));
                Db::execute(
                    "UPDATE jz_card SET status = 1, order_id = ?, sale_time = ? WHERE id IN ({$placeholders})",
                    array_merge([$order['order_no'], $payTime], $cardIds)
                );
            }

            // 增加销量
            Db::execute(
                "UPDATE jz_goods SET sold = sold + ? WHERE id = ?",
                [$order['quantity'], $order['goods_id']]
            );

            // 保存发货内容到订单（便于查询）
            Db::execute(
                "UPDATE jz_order SET deliver_content = ?, status = 2, update_time = ? WHERE id = ?",
                [$cardContent, $payTime, $order['id']]
            );
        }

        json_success('支付成功', ['redirect' => url('index/order', ['no' => $orderNo])]);
    }

    /**
     * 公告详情
     */
    public function article()
    {
        $id = (int) input('id', 0);
        $article = Db::fetch("SELECT * FROM jz_article WHERE id = ? AND status = 1", [$id]);
        if (!$article) {
            throw new Exception('公告不存在');
        }

        $this->assign('title', h($article['title']));
        $this->assign('article', $article);
        $this->fetch('index/article');
    }

    /**
     * 订单查询 / 详情
     */
    public function order()
    {
        $orderNo = input('no', '');
        $order = null;
        $queryMode = false;

        if ($orderNo) {
            $order = Db::fetch(
                "SELECT o.*, m.shop_name FROM jz_order o
                 LEFT JOIN jz_merchant m ON o.merchant_id = m.id
                 WHERE o.order_no = ?",
                [$orderNo]
            );
        } else {
            $queryMode = true;
        }

        $this->assign('title', $order ? '订单详情' : '查询订单');
        $this->assign('order', $order);
        $this->assign('queryMode', $queryMode);
        $this->assign('orderNo', $orderNo);
        $this->fetch('index/order');
    }

    /**
     * 订单查询提交
     */
    public function queryOrder()
    {
        $orderNo = input('order_no', '');
        $contact = input('contact', '');

        if (!$orderNo && !$contact) {
            json_error('请输入订单号或联系方式');
        }

        $where = '1=1';
        $params = [];
        if ($orderNo) {
            $where .= ' AND order_no = ?';
            $params[] = $orderNo;
        }
        if ($contact) {
            $where .= ' AND contact = ?';
            $params[] = $contact;
        }

        $order = Db::fetch("SELECT order_no FROM jz_order WHERE {$where} ORDER BY id DESC LIMIT 1", $params);
        if (!$order) {
            json_error('未找到订单');
        }

        json_success('查询成功', ['redirect' => url('index/order', ['no' => $order['order_no']])]);
    }
}
