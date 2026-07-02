<?php
/**
 * Migrated from main_legacy/controller/app/controller/merchant/Card.php
 */
namespace app\controller\merchant;

/**
 * 商户后台 - 卡密管理
 */
class Card extends Controller
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
     * 卡密列表
     */
    public function index()
    {
        $merchant = session('merchant_user');
        $goodsId = (int) input('goods_id', 0);
        $status = input('status', '');
        $keyword = input('keyword', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = 'c.merchant_id = ?';
        $params = [$merchant['id']];

        if ($goodsId > 0) {
            $where .= ' AND c.goods_id = ?';
            $params[] = $goodsId;
        }
        if ($status !== '') {
            $where .= ' AND c.status = ?';
            $params[] = (int) $status;
        }
        if ($keyword) {
            $where .= ' AND (c.content LIKE ? OR c.order_id LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $count = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_card c WHERE {$where}",
            $params
        );
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT c.*, g.name AS goods_name
             FROM jz_card c
             LEFT JOIN jz_goods g ON c.goods_id = g.id
             WHERE {$where}
             ORDER BY c.id DESC
             LIMIT {$offset}, {$pageSize}",
            $params
        );

        $goodsList = Db::query(
            "SELECT id, name FROM jz_goods WHERE merchant_id = ? AND type = 1 ORDER BY id DESC",
            [$merchant['id']]
        );

        $this->assign('title', '卡密管理');
        $this->assign('list', $list);
        $this->assign('goodsList', $goodsList);
        $this->assign('goodsId', $goodsId);
        $this->assign('status', $status);
        $this->assign('keyword', $keyword);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('merchant/card/index');
    }

    /**
     * 删除卡密
     */
    public function delete()
    {
        $merchant = session('merchant_user');
        $id = (int) input('id', 0);

        if (!$id) {
            json_error('参数错误');
        }

        $card = Db::fetch("SELECT * FROM jz_card WHERE id = ? AND merchant_id = ?", [$id, $merchant['id']]);
        if (!$card) {
            json_error('卡密不存在');
        }
        if ($card['status'] == 1) {
            json_error('已售出的卡密不能删除');
        }

        Db::execute("DELETE FROM jz_card WHERE id = ?", [$id]);

        // 扣减库存
        Db::execute("UPDATE jz_goods SET stock = stock - 1 WHERE id = ? AND stock > 0", [$card['goods_id']]);

        json_success('卡密已删除');
    }

    /**
     * 批量删除未售出卡密
     */
    public function batchDelete()
    {
        $merchant = session('merchant_user');
        $goodsId = (int) input('goods_id', 0);

        if (!$goodsId) {
            json_error('请选择商品');
        }

        $goods = Db::fetch("SELECT id FROM jz_goods WHERE id = ? AND merchant_id = ?", [$goodsId, $merchant['id']]);
        if (!$goods) {
            json_error('商品不存在');
        }

        $count = Db::fetch(
            "SELECT COUNT(*) AS total FROM jz_card WHERE goods_id = ? AND merchant_id = ? AND status = 0",
            [$goodsId, $merchant['id']]
        );
        $total = (int) ($count['total'] ?? 0);
        if ($total == 0) {
            json_error('没有可删除的未售出卡密');
        }

        Db::execute(
            "DELETE FROM jz_card WHERE goods_id = ? AND merchant_id = ? AND status = 0",
            [$goodsId, $merchant['id']]
        );
        Db::execute("UPDATE jz_goods SET stock = stock - ? WHERE id = ? AND stock >= ?", [$total, $goodsId, $total]);

        json_success('已清空 ' . $total . ' 条未售出卡密');
    }
}
