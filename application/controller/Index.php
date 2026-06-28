<?php
/**
 * C端前台控制器
 */
class Index extends Controller
{
    protected $subsite = null;

    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/main');
        $this->subsite = current_subsite();
        $this->assign('currentSubsite', $this->subsite);
    }

    /**
     * 获取当前分站 ID
     */
    protected function getSubsiteId()
    {
        return $this->subsite ? (int) $this->subsite['id'] : 0;
    }

    /**
     * 获取分站过滤条件
     */
    protected function getSubsiteWhere($alias = 'g')
    {
        $subsiteId = $this->getSubsiteId();
        if ($subsiteId > 0) {
            return " AND {$alias}.subsite_id = {$subsiteId}";
        }
        return '';
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
        $subsiteWhere = $this->getSubsiteWhere('g');
        $goods = Db::query(
            "SELECT g.*, m.shop_name, c.name as category_name
             FROM jz_goods g
             LEFT JOIN jz_merchant m ON g.merchant_id = m.id
             LEFT JOIN jz_category c ON g.category_id = c.id
             WHERE g.status = 1 AND g.stock > 0{$subsiteWhere}
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
        if ($this->subsite) {
            $title = $this->subsite['name'] . ' - ' . $title;
        }

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

        $subsiteId = $this->getSubsiteId();
        if ($subsiteId > 0) {
            $where .= ' AND g.subsite_id = ?';
            $params[] = $subsiteId;
        }

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
        $subsiteId = $this->getSubsiteId();
        $params = [$id];
        $where = 'g.id = ? AND g.status = 1';
        if ($subsiteId > 0) {
            $where .= ' AND g.subsite_id = ?';
            $params[] = $subsiteId;
        }

        $goods = Db::fetch(
            "SELECT g.*, m.shop_name, m.id as merchant_id, c.name as category_name
             FROM jz_goods g
             LEFT JOIN jz_merchant m ON g.merchant_id = m.id
             LEFT JOIN jz_category c ON g.category_id = c.id
             WHERE {$where}",
            $params
        );

        if (!$goods) {
            throw new Exception('商品不存在或已下架');
        }

        // 同类推荐
        $recParams = [$goods['category_id'], $id];
        $recWhere = 'category_id = ? AND status = 1 AND id != ?';
        if ($subsiteId > 0) {
            $recWhere .= ' AND subsite_id = ?';
            $recParams[] = $subsiteId;
        }
        $recommend = Db::query(
            "SELECT id, name, cover, price, stock, sold FROM jz_goods
             WHERE {$recWhere}
             ORDER BY sold DESC LIMIT 6",
            $recParams
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
     * 校验优惠券
     */
    private function validateCoupon($code, $goodsId, $categoryId, $totalAmount)
    {
        $now = date('Y-m-d H:i:s');

        // 优先查询用户已领取的优惠券
        $userCoupon = Db::fetch(
            "SELECT uc.*, c.scope, c.scope_id
             FROM jz_user_coupon uc
             LEFT JOIN jz_coupon c ON uc.coupon_id = c.id
             WHERE uc.coupon_code = ? AND uc.status = 0",
            [$code]
        );

        if ($userCoupon) {
            if ($userCoupon['expire_time'] && $userCoupon['expire_time'] < $now) {
                return ['valid' => false, 'msg' => '优惠券已过期'];
            }
            if ((float) $userCoupon['min_amount'] > $totalAmount) {
                return ['valid' => false, 'msg' => '订单金额未满 ¥' . $userCoupon['min_amount']];
            }
            if ($userCoupon['scope'] === 'category' && (int) $userCoupon['scope_id'] !== (int) $categoryId) {
                return ['valid' => false, 'msg' => '该优惠券不适用当前分类'];
            }
            if ($userCoupon['scope'] === 'goods' && (int) $userCoupon['scope_id'] !== (int) $goodsId) {
                return ['valid' => false, 'msg' => '该优惠券不适用当前商品'];
            }

            $coupon = $userCoupon;
            $coupon['user_coupon_id'] = $userCoupon['id'];
        } else {
            // 查询固定券码
            $coupon = Db::fetch("SELECT * FROM jz_coupon WHERE code = ? AND status = 1", [$code]);
            if (!$coupon) {
                return ['valid' => false, 'msg' => '优惠券不存在或已禁用'];
            }
            if ($coupon['start_time'] && $coupon['start_time'] > $now) {
                return ['valid' => false, 'msg' => '优惠券尚未开始'];
            }
            if ($coupon['end_time'] && $coupon['end_time'] < $now) {
                return ['valid' => false, 'msg' => '优惠券已过期'];
            }
            if ($coupon['total_count'] > 0 && $coupon['used_count'] >= $coupon['total_count']) {
                return ['valid' => false, 'msg' => '优惠券已发放完毕'];
            }
            if ((float) $coupon['min_amount'] > $totalAmount) {
                return ['valid' => false, 'msg' => '订单金额未满 ¥' . $coupon['min_amount']];
            }
            if ($coupon['scope'] === 'category' && (int) $coupon['scope_id'] !== (int) $categoryId) {
                return ['valid' => false, 'msg' => '该优惠券不适用当前分类'];
            }
            if ($coupon['scope'] === 'goods' && (int) $coupon['scope_id'] !== (int) $goodsId) {
                return ['valid' => false, 'msg' => '该优惠券不适用当前商品'];
            }
        }

        $amount = 0;
        if ((int) $coupon['type'] === 1) {
            // 满减
            $amount = min((float) $coupon['amount'], $totalAmount);
        } elseif ((int) $coupon['type'] === 2) {
            // 折扣
            $amount = round($totalAmount * (1 - (float) $coupon['amount']), 2);
            $amount = min($amount, $totalAmount - 0.01);
        } else {
            // 固定金额
            $amount = min((float) $coupon['amount'], $totalAmount);
        }

        return ['valid' => true, 'amount' => max(0, $amount)];
    }

    /**
     * 将已过期的用户优惠券标记为已过期
     */
    private function expireUserCoupons()
    {
        $now = date('Y-m-d H:i:s');
        Db::execute(
            "UPDATE jz_user_coupon SET status = 2 WHERE status = 0 AND expire_time IS NOT NULL AND expire_time < ?",
            [$now]
        );
    }

    /**
     * 优惠券领取中心
     */
    public function coupon()
    {
        $this->expireUserCoupons();

        $contact = trim(input('contact', ''));
        $now = date('Y-m-d H:i:s');

        // 可领取优惠券：启用、领取券（code为空）、未过期、未领完
        $where = "status = 1 AND code = '' AND (total_count = 0 OR receive_count < total_count)";
        $params = [];
        if ($now) {
            $where .= " AND (start_time IS NULL OR start_time <= ?) AND (end_time IS NULL OR end_time >= ?)";
            $params[] = $now;
            $params[] = $now;
        }

        $list = Db::query(
            "SELECT * FROM jz_coupon WHERE {$where} ORDER BY id DESC",
            $params
        );

        // 用户已领取的优惠券
        $myCoupons = [];
        if ($contact) {
            $user = Db::fetch("SELECT id FROM jz_user WHERE mobile = ? OR nickname = ? LIMIT 1", [$contact, $contact]);
            if ($user) {
                $myCoupons = Db::query(
                    "SELECT uc.*, c.name AS coupon_name, c.scope, c.scope_id
                     FROM jz_user_coupon uc
                     LEFT JOIN jz_coupon c ON uc.coupon_id = c.id
                     WHERE uc.user_id = ? AND uc.status = 0
                     ORDER BY uc.id DESC",
                    [$user['id']]
                );
            }
        }

        $this->assign('title', '领券中心');
        $this->assign('list', $list);
        $this->assign('myCoupons', $myCoupons);
        $this->assign('contact', $contact);
        $this->fetch('index/coupon');
    }

    /**
     * 领取优惠券（Ajax）
     */
    public function receiveCoupon()
    {
        $couponId = (int) input('coupon_id', 0);
        $contact = trim(input('contact', ''));

        if (!$couponId) {
            json_error('请选择优惠券');
        }
        if (!$contact) {
            json_error('请填写联系方式');
        }

        $coupon = Db::fetch("SELECT * FROM jz_coupon WHERE id = ? AND status = 1", [$couponId]);
        if (!$coupon) {
            json_error('优惠券不存在或已禁用');
        }
        if ($coupon['code'] !== '') {
            json_error('该券不支持领取');
        }

        $now = date('Y-m-d H:i:s');
        if ($coupon['start_time'] && $coupon['start_time'] > $now) {
            json_error('优惠券尚未开始');
        }
        if ($coupon['end_time'] && $coupon['end_time'] < $now) {
            json_error('优惠券已过期');
        }
        if ($coupon['total_count'] > 0 && (int) $coupon['receive_count'] >= (int) $coupon['total_count']) {
            json_error('优惠券已领取完毕');
        }

        // 查找或创建用户
        $user = Db::fetch("SELECT * FROM jz_user WHERE mobile = ? OR nickname = ? LIMIT 1", [$contact, $contact]);
        if (!$user) {
            $userId = Db::insert('jz_user', [
                'nickname' => $contact,
                'mobile' => $contact,
                'password' => password_hash_custom(substr(md5(uniqid()), 0, 8)),
                'create_time' => $now,
            ]);
        } else {
            $userId = $user['id'];
        }

        // 每人限领
        $limit = (int) $coupon['limit_per_user'];
        if ($limit > 0) {
            $received = Db::fetch(
                "SELECT COUNT(*) AS total FROM jz_user_coupon WHERE coupon_id = ? AND user_id = ?",
                [$couponId, $userId]
            );
            if ((int) ($received['total'] ?? 0) >= $limit) {
                json_error('您已达到领取上限');
            }
        }

        // 生成唯一券码
        $code = 'CP' . date('Ymd') . strtoupper(substr(uniqid(), -6)) . mt_rand(10, 99);
        while (Db::fetch("SELECT id FROM jz_user_coupon WHERE coupon_code = ?", [$code])) {
            $code = 'CP' . date('Ymd') . strtoupper(substr(uniqid(), -6)) . mt_rand(10, 99);
        }

        // 过期时间：优先使用优惠券结束时间
        $expireTime = $coupon['end_time'] ?: null;

        Db::insert('jz_user_coupon', [
            'user_id' => $userId,
            'coupon_id' => $couponId,
            'coupon_code' => $code,
            'amount' => $coupon['amount'],
            'min_amount' => $coupon['min_amount'],
            'type' => $coupon['type'],
            'status' => 0,
            'expire_time' => $expireTime,
            'create_time' => $now,
        ]);

        Db::execute("UPDATE jz_coupon SET receive_count = receive_count + 1 WHERE id = ?", [$couponId]);

        json_success('领取成功', ['coupon_code' => $code]);
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

        // 分站访问时校验商品归属
        $subsiteId = $this->getSubsiteId();
        if ($subsiteId > 0 && (int) $goods['subsite_id'] !== $subsiteId) {
            json_error('当前分站暂无该商品');
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

        // 优惠券校验与抵扣
        $couponCode = trim(input('coupon_code', ''));
        $couponAmount = 0;
        $userCouponId = 0;
        if ($couponCode) {
            $couponResult = $this->validateCoupon($couponCode, $goodsId, $goods['category_id'], $totalAmount);
            if ($couponResult['valid']) {
                $couponAmount = $couponResult['amount'];
                $userCouponId = $couponResult['user_coupon_id'] ?? 0;
            } else {
                json_error($couponResult['msg']);
            }
        }

        // 金额随机化（用于规避风控）
        $payAmount = max(0.01, round($totalAmount - $couponAmount, 2));
        if (($risk['amount_jitter'] ?? '0') === '1') {
            $range = (float) ($risk['jitter_range'] ?? 0.01);
            $jitter = $payAmount * $range * (mt_rand(-100, 100) / 100);
            $payAmount = round($payAmount + $jitter, 2);
            $payAmount = max(0.01, $payAmount);
        }

        // 根据联系方式查找或创建用户
        $userId = 0;
        if ($contact) {
            $user = Db::fetch("SELECT * FROM jz_user WHERE mobile = ? OR nickname = ? LIMIT 1", [$contact, $contact]);
            if (!$user) {
                $userId = Db::insert('jz_user', [
                    'nickname' => $contact,
                    'mobile' => $contact,
                    'password' => password_hash_custom(substr(md5(uniqid()), 0, 8)),
                    'create_time' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $userId = $user['id'];
            }
        }

        $orderNo = 'JZ' . date('YmdHis') . mt_rand(1000, 9999);

        $orderId = Db::insert('jz_order', [
            'order_no' => $orderNo,
            'user_id' => $userId,
            'merchant_id' => $goods['merchant_id'],
            'subsite_id' => $goods['subsite_id'],
            'goods_id' => $goods['id'],
            'goods_name' => $goods['name'],
            'quantity' => $quantity,
            'price' => $goods['price'],
            'total_amount' => $totalAmount,
            'pay_amount' => $payAmount,
            'coupon_code' => $couponCode,
            'coupon_amount' => $couponAmount,
            'status' => 0,
            'client_ip' => $clientIp,
            'contact' => $contact,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        // 更新优惠券使用统计
        if ($couponCode && $couponAmount > 0) {
            Db::execute("UPDATE jz_coupon SET used_count = used_count + 1 WHERE code = ?", [$couponCode]);
            if ($userCouponId > 0) {
                Db::execute(
                    "UPDATE jz_user_coupon SET status = 1, order_id = ?, use_time = ? WHERE id = ?",
                    [$orderId, date('Y-m-d H:i:s'), $userCouponId]
                );
            }
        }

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

        // 可用支付渠道：全局 + 当前分站
        $subsiteId = $this->getSubsiteId();
        if ($subsiteId > 0) {
            $channels = Db::query(
                "SELECT * FROM jz_payment_channel
                 WHERE status = 1 AND (scope = 'global' OR (scope = 'subsite' AND scope_id = ?))
                 ORDER BY sort ASC",
                [$subsiteId]
            );
        } else {
            $channels = Db::query("SELECT * FROM jz_payment_channel WHERE status = 1 AND scope = 'global' ORDER BY sort ASC");
        }
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
     * 执行支付（Ajax / 跳转真实网关）
     */
    public function doPay()
    {
        $orderNo = input('order_no', '');
        $channelCode = input('channel', 'alipay');

        $order = Db::fetch("SELECT * FROM jz_order WHERE order_no = ? AND status = 0", [$orderNo]);
        if (!$order) {
            json_error('订单不存在或已支付');
        }

        $subsiteId = $this->getSubsiteId();
        if ($subsiteId > 0) {
            $channel = Db::fetch(
                "SELECT * FROM jz_payment_channel
                 WHERE code = ? AND status = 1 AND (scope = 'global' OR (scope = 'subsite' AND scope_id = ?))",
                [$channelCode, $subsiteId]
            );
        } else {
            $channel = Db::fetch("SELECT * FROM jz_payment_channel WHERE code = ? AND status = 1 AND scope = 'global'", [$channelCode]);
        }
        $config = $channel ? json_decode($channel['config'] ?: '{}', true) : [];

        // 配置了真实支付网关时，返回跳转参数
        if (!empty($config['gateway_url'])) {
            $payParams = $this->buildPayParams($order, $channelCode, $config);
            json_success('请完成支付', [
                'type' => 'redirect',
                'gateway' => $config['gateway_url'],
                'params' => $payParams,
            ]);
        }

        // 否则本地模拟支付完成
        $this->completeOrder($order, $channelCode);

        json_success('支付成功', ['redirect' => url('index/order', ['no' => $orderNo])]);
    }

    /**
     * 构建真实支付请求参数（通用签名）
     */
    private function buildPayParams($order, $channelCode, $config)
    {
        $timestamp = time();
        $params = [
            'channel' => $channelCode,
            'order_no' => $order['order_no'],
            'amount' => (string) $order['pay_amount'],
            'goods_name' => $order['goods_name'],
            'notify_url' => base_url('index/notify?channel=' . $channelCode),
            'return_url' => base_url('index/order?no=' . $order['order_no']),
            'timestamp' => $timestamp,
        ];

        // 添加商户在配置中声明的扩展参数
        foreach (['app_id', 'mch_id', 'extra'] as $key) {
            if (!empty($config[$key])) {
                $params[$key] = $config[$key];
            }
        }

        // 签名
        ksort($params);
        $signKey = $config['sign_key'] ?? '';
        $params['sign'] = strtoupper(md5(http_build_query($params) . '&key=' . $signKey));

        return $params;
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
        $subsiteId = $this->getSubsiteId();

        if ($orderNo) {
            $where = 'o.order_no = ?';
            $params = [$orderNo];
            if ($subsiteId > 0) {
                $where .= ' AND o.subsite_id = ?';
                $params[] = $subsiteId;
            }
            $order = Db::fetch(
                "SELECT o.*, m.shop_name FROM jz_order o
                 LEFT JOIN jz_merchant m ON o.merchant_id = m.id
                 WHERE {$where}",
                $params
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
        $subsiteId = $this->getSubsiteId();

        if (!$orderNo && !$contact) {
            json_error('请输入订单号或联系方式');
        }

        $where = '1=1';
        $params = [];
        if ($subsiteId > 0) {
            $where .= ' AND subsite_id = ?';
            $params[] = $subsiteId;
        }
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

    /**
     * 个人中心首页
     */
    public function user()
    {
        $contact = $this->getCurrentContact();
        $user = null;
        $stats = [];

        if ($contact) {
            $user = Db::fetch("SELECT * FROM jz_user WHERE mobile = ? OR nickname = ? LIMIT 1", [$contact, $contact]);
            if ($user) {
                $subsiteId = $this->getSubsiteId();
                $where = 'user_id = ?';
                $params = [$user['id']];
                if ($subsiteId > 0) {
                    $where .= ' AND subsite_id = ?';
                    $params[] = $subsiteId;
                }
                $stats = Db::fetch(
                    "SELECT
                        COUNT(*) AS total_orders,
                        IFNULL(SUM(CASE WHEN status >= 1 THEN pay_amount ELSE 0 END), 0) AS total_pay,
                        COUNT(CASE WHEN status = 0 THEN 1 END) AS unpaid_orders,
                        COUNT(CASE WHEN status = 2 THEN 1 END) AS delivered_orders
                     FROM jz_order WHERE {$where}",
                    $params
                );
            }
        }

        $this->assign('title', '个人中心');
        $this->assign('contact', $contact);
        $this->assign('user', $user);
        $this->assign('stats', $stats);
        $this->fetch('index/user');
    }

    /**
     * 用户登录/查询（按联系方式）
     */
    public function userLogin()
    {
        $contact = trim(input('contact', ''));
        if (!$contact) {
            json_error('请填写联系方式');
        }

        $user = Db::fetch("SELECT id FROM jz_user WHERE mobile = ? OR nickname = ? LIMIT 1", [$contact, $contact]);
        if (!$user) {
            // 未下单过的用户也允许进入个人中心
            $userId = Db::insert('jz_user', [
                'nickname' => $contact,
                'mobile' => $contact,
                'password' => password_hash_custom(substr(md5(uniqid()), 0, 8)),
                'create_time' => date('Y-m-d H:i:s'),
            ]);
            award_points($userId, 'register');
        } else {
            award_points($user['id'], 'login');
        }

        session('user_contact', $contact);
        json_success('登录成功', ['redirect' => url('index/user')]);
    }

    /**
     * 用户退出
     */
    public function userLogout()
    {
        unset($_SESSION['user_contact']);
        redirect(url('index/user'));
    }

    /**
     * 用户订单列表
     */
    public function userOrders()
    {
        $contact = $this->getCurrentContact();
        if (!$contact) {
            redirect(url('index/user'));
        }

        $user = Db::fetch("SELECT * FROM jz_user WHERE mobile = ? OR nickname = ? LIMIT 1", [$contact, $contact]);
        if (!$user) {
            redirect(url('index/user'));
        }

        $status = input('status', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 10;

        $subsiteId = $this->getSubsiteId();
        $where = 'o.user_id = ?';
        $params = [$user['id']];
        if ($subsiteId > 0) {
            $where .= ' AND o.subsite_id = ?';
            $params[] = $subsiteId;
        }
        if ($status !== '') {
            $where .= ' AND o.status = ?';
            $params[] = (int) $status;
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_order o WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT o.*, m.shop_name FROM jz_order o
             LEFT JOIN jz_merchant m ON o.merchant_id = m.id
             WHERE {$where}
             ORDER BY o.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $this->assign('title', '我的订单');
        $this->assign('list', $list);
        $this->assign('status', $status);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('index/user_orders');
    }

    /**
     * 用户优惠券列表
     */
    public function userCoupons()
    {
        $contact = $this->getCurrentContact();
        if (!$contact) {
            redirect(url('index/user'));
        }

        $user = Db::fetch("SELECT * FROM jz_user WHERE mobile = ? OR nickname = ? LIMIT 1", [$contact, $contact]);
        if (!$user) {
            redirect(url('index/user'));
        }

        $this->expireUserCoupons();

        $status = input('status', '');
        $where = 'uc.user_id = ?';
        $params = [$user['id']];
        if ($status !== '') {
            $where .= ' AND uc.status = ?';
            $params[] = (int) $status;
        }

        $list = Db::query(
            "SELECT uc.*, c.name AS coupon_name, c.scope, c.scope_id
             FROM jz_user_coupon uc
             LEFT JOIN jz_coupon c ON uc.coupon_id = c.id
             WHERE {$where}
             ORDER BY uc.id DESC",
            $params
        );

        $this->assign('title', '我的优惠券');
        $this->assign('list', $list);
        $this->assign('status', $status);
        $this->fetch('index/user_coupons');
    }

    /**
     * 获取当前联系方式
     */
    private function getCurrentContact()
    {
        return trim(session('user_contact') ?? '');
    }

    /**
     * 支付异步回调（通用签名验证）
     * 外部通道将订单号、金额、时间戳、签名 POST 到 /index/notify?channel=xxx
     */
    public function notify()
    {
        $channelCode = input('channel', '');
        $orderNo = input('order_no', '');
        $amount = input('amount', '');
        $timestamp = input('timestamp', '');
        $sign = input('sign', '');

        if (!$channelCode || !$orderNo || !$amount || !$timestamp || !$sign) {
            echo 'FAIL: 参数缺失';
            exit;
        }

        $channel = Db::fetch("SELECT * FROM jz_payment_channel WHERE code = ? AND status = 1", [$channelCode]);
        if (!$channel) {
            echo 'FAIL: 通道不存在';
            exit;
        }

        $config = json_decode($channel['config'] ?: '{}', true);
        $signKey = $config['sign_key'] ?? '';
        if (!$signKey) {
            echo 'FAIL: 通道未配置密钥';
            exit;
        }

        // 5 分钟时差校验
        if (abs(time() - (int) $timestamp) > 300) {
            echo 'FAIL: 请求超时';
            exit;
        }

        // 验证签名
        $params = [
            'channel' => $channelCode,
            'order_no' => $orderNo,
            'amount' => $amount,
            'timestamp' => $timestamp,
        ];
        ksort($params);
        $expectedSign = strtoupper(md5(http_build_query($params) . '&key=' . $signKey));
        if ($sign !== $expectedSign) {
            echo 'FAIL: 签名错误';
            exit;
        }

        $order = Db::fetch("SELECT * FROM jz_order WHERE order_no = ? AND status = 0", [$orderNo]);
        if (!$order) {
            echo 'SUCCESS';
            exit;
        }

        // 金额允许 0.01 元误差（随机化金额场景）
        $notifyAmount = round((float) $amount, 2);
        $orderAmount = round((float) $order['pay_amount'], 2);
        if (abs($notifyAmount - $orderAmount) > 0.01) {
            echo 'FAIL: 金额不匹配';
            exit;
        }

        $this->completeOrder($order, $channelCode);

        echo 'SUCCESS';
        exit;
    }

    /**
     * 完成订单支付并自动发货
     */
    private function completeOrder($order, $channelCode)
    {
        $payTime = date('Y-m-d H:i:s');

        Db::execute(
            "UPDATE jz_order SET status = 1, pay_channel = ?, pay_time = ?, update_time = ? WHERE id = ? AND status = 0",
            [$channelCode, $payTime, $payTime, $order['id']]
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

            Db::execute(
                "UPDATE jz_goods SET sold = sold + ? WHERE id = ?",
                [$order['quantity'], $order['goods_id']]
            );

            Db::execute(
                "UPDATE jz_order SET deliver_content = ?, status = 2, update_time = ? WHERE id = ?",
                [$cardContent, $payTime, $order['id']]
            );
        }

        // 结算商户收入与手续费
        if ($order['merchant_id'] > 0) {
            $rateGroup = get_merchant_rate_group($order['merchant_id']);
            $fee = calculate_order_fee((float) $order['pay_amount'], $rateGroup);
            $income = round((float) $order['pay_amount'] - $fee, 2);

            $merchant = Db::fetch("SELECT balance FROM jz_merchant WHERE id = ?", [$order['merchant_id']]);
            $oldBalance = (float) ($merchant['balance'] ?? 0);
            $newBalance = round($oldBalance + $income, 2);

            Db::execute(
                "UPDATE jz_merchant SET balance = ?, update_time = ? WHERE id = ?",
                [$newBalance, $payTime, $order['merchant_id']]
            );

            // 资金流水：商户收入
            Db::insert('jz_finance_flow', [
                'merchant_id' => $order['merchant_id'],
                'order_id' => $order['id'],
                'type' => 'income',
                'amount' => $income,
                'balance' => $newBalance,
                'remark' => '订单收入 ' . $order['order_no'],
                'create_time' => $payTime,
            ]);

            // 资金流水：手续费
            if ($fee > 0) {
                Db::insert('jz_finance_flow', [
                    'merchant_id' => $order['merchant_id'],
                    'order_id' => $order['id'],
                    'type' => 'fee',
                    'amount' => $fee,
                    'balance' => $newBalance,
                    'remark' => '平台手续费 ' . $order['order_no'],
                    'create_time' => $payTime,
                ]);
            }
        }

        // 用户下单积分奖励
        if ($order['user_id'] > 0) {
            award_points($order['user_id'], 'order', $order['id'], '订单支付 ' . $order['order_no']);
        }
    }

    /**
     * 商户入驻申请页
     */
    public function merchantJoin()
    {
        $inviteCode = input('invite_code', '');
        $subsiteId = $this->getSubsiteId();

        // 当前分站信息，以及非分站访问时的可选分站列表
        $currentSubsite = $this->subsite;
        $subsites = [];
        if (!$currentSubsite) {
            $subsites = Db::query("SELECT id, name, domain_prefix FROM jz_subsite WHERE status = 1 ORDER BY id DESC");
        }

        $this->assign('title', '商户入驻');
        $this->assign('inviteCode', $inviteCode);
        $this->assign('subsiteId', $subsiteId);
        $this->assign('currentSubsite', $currentSubsite);
        $this->assign('subsites', $subsites);
        $this->fetch('index/merchant_join');
    }

    /**
     * 提交商户入驻申请
     */
    public function doMerchantJoin()
    {
        $username = trim(input('username', ''));
        $password = input('password', '');
        $passwordConfirm = input('password_confirm', '');
        $shopName = trim(input('shop_name', ''));
        $mobile = trim(input('mobile', ''));
        $inviteCodeStr = trim(input('invite_code', ''));
        $subsiteId = (int) input('subsite_id', $this->getSubsiteId());

        if (!$username || !$password || !$shopName || !$mobile) {
            json_error('请填写完整信息');
        }
        if (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
            json_error('账号为 4-20 位字母/数字/下划线');
        }
        if (strlen($password) < 6) {
            json_error('密码长度不能少于 6 位');
        }
        if ($password !== $passwordConfirm) {
            json_error('两次输入密码不一致');
        }
        if (!preg_match('/^1[3-9]\d{9}$/', $mobile)) {
            json_error('请输入正确的手机号');
        }

        // IP 黑名单
        if (is_ip_blacklisted()) {
            json_error('当前 IP 已被限制访问');
        }

        // 验证码校验
        if (captcha_required('join') && !captcha_verify(input('captcha', ''), 'join')) {
            json_error('验证码错误');
        }

        // 账号唯一性
        $exists = Db::fetch("SELECT id FROM jz_merchant WHERE username = ?", [$username]);
        if ($exists) {
            json_error('该账号已被注册');
        }

        // 邀请码校验
        $inviteCodeId = 0;
        $rateGroupId = 0;
        if ($inviteCodeStr) {
            $invite = Db::fetch(
                "SELECT * FROM jz_invite_code WHERE code = ? AND status = 1 AND (subsite_id = 0 OR subsite_id = ?)",
                [$inviteCodeStr, $subsiteId]
            );
            if (!$invite) {
                json_error('邀请码无效');
            }
            if ($invite['expire_time'] && $invite['expire_time'] < date('Y-m-d H:i:s')) {
                json_error('邀请码已过期');
            }
            if ($invite['max_uses'] > 0 && (int) $invite['used_count'] >= (int) $invite['max_uses']) {
                json_error('邀请码使用次数已达上限');
            }
            $inviteCodeId = $invite['id'];
            $rateGroupId = (int) $invite['rate_group_id'];
        }

        // 分站校验
        if ($subsiteId > 0) {
            $subsite = Db::fetch("SELECT id FROM jz_subsite WHERE id = ? AND status = 1", [$subsiteId]);
            if (!$subsite) {
                json_error('所选分站不存在或已关闭');
            }
        }

        // 生成唯一店铺ID
        $shopId = $this->generateShopId();

        $merchantId = Db::insert('jz_merchant', [
            'username' => $username,
            'password' => password_hash_custom($password),
            'shop_name' => $shopName,
            'shop_id' => $shopId,
            'subsite_id' => $subsiteId,
            'mobile' => $mobile,
            'rate_group_id' => $rateGroupId,
            'status' => 0,
            'invite_code_id' => $inviteCodeId,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        // 更新邀请码使用次数
        if ($inviteCodeId > 0) {
            Db::execute("UPDATE jz_invite_code SET used_count = used_count + 1 WHERE id = ?", [$inviteCodeId]);
        }

        json_success('入驻申请已提交，请等待审核', ['redirect' => url('index/merchantJoin')]);
    }

    /**
     * 生成唯一店铺ID
     */
    private function generateShopId()
    {
        $prefix = 'S' . date('Ymd');
        do {
            $shopId = $prefix . strtoupper(substr(uniqid(), -6)) . mt_rand(10, 99);
            $exists = Db::fetch("SELECT id FROM jz_merchant WHERE shop_id = ?", [$shopId]);
        } while ($exists);
        return $shopId;
    }

    /**
     * 积分中心
     */
    public function pointsCenter()
    {
        $contact = $this->getCurrentContact();
        $user = null;
        $logs = [];
        if ($contact) {
            $user = Db::fetch("SELECT * FROM jz_user WHERE mobile = ? OR nickname = ? LIMIT 1", [$contact, $contact]);
            if ($user) {
                $logs = Db::query(
                    "SELECT * FROM jz_points_log WHERE user_id = ? ORDER BY id DESC LIMIT 20",
                    [$user['id']]
                );
            }
        }

        $goods = Db::query(
            "SELECT * FROM jz_points_goods WHERE status = 1 ORDER BY sort ASC, id DESC LIMIT 12"
        );

        $typeMap = [
            'register' => '注册',
            'login' => '登录',
            'order' => '下单',
            'review' => '评价',
            'invite' => '邀请',
            'redeem' => '兑换',
            'system' => '系统',
        ];

        $this->assign('title', '积分中心');
        $this->assign('user', $user);
        $this->assign('goods', $goods);
        $this->assign('logs', $logs);
        $this->assign('typeMap', $typeMap);
        $this->fetch('index/points_center');
    }

    /**
     * 积分商品详情
     */
    public function pointsGoods()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            redirect(url('index/pointsCenter'));
        }

        $goods = Db::fetch("SELECT * FROM jz_points_goods WHERE id = ? AND status = 1", [$id]);
        if (!$goods) {
            redirect(url('index/pointsCenter'));
        }

        $contact = $this->getCurrentContact();
        $user = null;
        if ($contact) {
            $user = Db::fetch("SELECT * FROM jz_user WHERE mobile = ? OR nickname = ? LIMIT 1", [$contact, $contact]);
        }

        $this->assign('title', h($goods['title']));
        $this->assign('goods', $goods);
        $this->assign('user', $user);
        $this->fetch('index/points_goods');
    }

    /**
     * 兑换积分商品
     */
    public function pointsRedeem()
    {
        $contact = $this->getCurrentContact();
        if (!$contact) {
            json_error('请先登录');
        }

        $user = Db::fetch("SELECT * FROM jz_user WHERE mobile = ? OR nickname = ? LIMIT 1", [$contact, $contact]);
        if (!$user) {
            json_error('用户不存在');
        }

        $goodsId = (int) input('goods_id', 0);
        $quantity = max(1, (int) input('quantity', 1));
        $contactInfo = input('contact', '');

        $goods = Db::fetch("SELECT * FROM jz_points_goods WHERE id = ? AND status = 1", [$goodsId]);
        if (!$goods) {
            json_error('商品不存在或已下架');
        }
        if ($goods['stock'] < $quantity) {
            json_error('库存不足');
        }

        $needPoints = (int) $goods['points'] * $quantity;
        if ((int) $user['points'] < $needPoints) {
            json_error('积分不足');
        }

        $orderNo = generate_points_order_no();
        $afterPoints = (int) $user['points'] - $needPoints;

        Db::execute("UPDATE jz_user SET points = ? WHERE id = ?", [$afterPoints, $user['id']]);
        Db::execute("UPDATE jz_points_goods SET stock = stock - ?, sold = sold + ? WHERE id = ?", [$quantity, $quantity, $goodsId]);

        Db::insert('jz_points_order', [
            'order_no' => $orderNo,
            'user_id' => $user['id'],
            'points_goods_id' => $goodsId,
            'title' => $goods['title'],
            'points' => $needPoints,
            'quantity' => $quantity,
            'contact' => $contactInfo,
            'status' => 0,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        Db::insert('jz_points_log', [
            'user_id' => $user['id'],
            'type' => 'redeem',
            'points' => -$needPoints,
            'before_points' => (int) $user['points'],
            'after_points' => $afterPoints,
            'remark' => '兑换：' . $goods['title'],
            'related_id' => $goodsId,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        json_success('兑换成功，请等待发放', ['order_no' => $orderNo]);
    }

    /**
     * 积分流水
     */
    public function pointsLog()
    {
        $contact = $this->getCurrentContact();
        if (!$contact) {
            redirect(url('index/user'));
        }

        $user = Db::fetch("SELECT * FROM jz_user WHERE mobile = ? OR nickname = ? LIMIT 1", [$contact, $contact]);
        if (!$user) {
            redirect(url('index/user'));
        }

        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_points_log WHERE user_id = ?", [$user['id']]);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT * FROM jz_points_log WHERE user_id = ? ORDER BY id DESC LIMIT {$offset}, {$pageSize}",
            [$user['id']]
        );

        $typeMap = [
            'register' => '注册',
            'login' => '登录',
            'order' => '下单',
            'review' => '评价',
            'invite' => '邀请',
            'redeem' => '兑换',
            'system' => '系统',
        ];

        $this->assign('title', '积分明细');
        $this->assign('list', $list);
        $this->assign('typeMap', $typeMap);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('index/points_log');
    }
}
