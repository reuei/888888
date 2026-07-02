<?php
/**
 * Migrated from main_legacy/controller/app/controller/Plugin.php
 */
namespace app\controller;

/**
 * 插件回调 / 机器人接口
 */
class Plugin extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->disableLayout();
    }

    /**
     * 接收插件 Webhook 回调（用于机器人回传处理结果）
     * URL: /plugin/webhook?code=xxx&token=xxx
     */
    public function webhook()
    {
        $code = input('code', '');
        $token = input('token', '');

        $plugin = Db::fetch("SELECT * FROM jz_plugin WHERE code = ? AND status = 1", [$code]);
        if (!$plugin) {
            json_error('插件不存在或已禁用');
        }

        $config = json_decode($plugin['config'] ?? '{}', true);
        if (empty($config['token']) || $config['token'] !== $token) {
            json_error('Token 校验失败');
        }

        $input = file_get_contents('php://input');
        $payload = json_decode($input, true);
        if (!$payload) {
            $payload = $_POST;
        }

        // 示例：机器人回传卡密自动发货
        if (!empty($payload['action']) && $payload['action'] === 'deliver' && !empty($payload['order_no'])) {
            $orderNo = trim($payload['order_no']);
            $cards = $payload['cards'] ?? [];
            $this->autoDeliver($orderNo, $cards);
        }

        Db::insert('jz_plugin_log', [
            'plugin_id' => $plugin['id'],
            'event_type' => $payload['action'] ?? 'webhook',
            'payload' => $input,
            'response' => 'received',
            'status' => 1,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        json_success('接收成功');
    }

    /**
     * 自动发货处理
     */
    private function autoDeliver($orderNo, array $cards)
    {
        $order = Db::fetch("SELECT * FROM jz_order WHERE order_no = ? AND status = 1", [$orderNo]);
        if (!$order) {
            return;
        }

        $goods = Db::fetch("SELECT * FROM jz_goods WHERE id = ?", [$order['goods_id']]);
        if (!$goods || $goods['type'] != 1) {
            return;
        }

        if (empty($cards)) {
            // 尝试从库存取卡密
            $stockCards = Db::query("SELECT * FROM jz_card WHERE goods_id = ? AND status = 0 ORDER BY id ASC LIMIT ?", [$goods['id'], $order['quantity']]);
            $cards = $stockCards;
        }

        if (count($cards) < $order['quantity']) {
            return;
        }

        foreach ($cards as $i => $card) {
            $cardId = $card['id'] ?? 0;
            if ($cardId > 0) {
                Db::execute(
                    "UPDATE jz_card SET status = 1, order_id = ?, sell_time = ? WHERE id = ?",
                    [$order['id'], date('Y-m-d H:i:s'), $cardId]
                );
            } else {
                Db::insert('jz_card', [
                    'goods_id' => $goods['id'],
                    'merchant_id' => $goods['merchant_id'],
                    'card_no' => $card['card_no'] ?? '',
                    'card_password' => $card['card_password'] ?? '',
                    'status' => 1,
                    'order_id' => $order['id'],
                    'sell_time' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        Db::execute(
            "UPDATE jz_order SET status = 2, deliver_time = ? WHERE id = ?",
            [date('Y-m-d H:i:s'), $order['id']]
        );

        plugin_trigger('order_delivered', ['order_id' => $order['id'], 'order_no' => $orderNo]);
    }
}
