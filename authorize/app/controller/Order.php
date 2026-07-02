<?php
/**
 * 订单控制器
 */

namespace app\controller;

use app\BaseController;
use app\Db;

class Order extends BaseController
{
    public function create()
    {
        require_user_login();
        $user = get_user();

        $itemType = input('type', 'product');
        $itemId = (int) input('id', 0);

        if ($itemType === 'product') {
            $item = Db::fetch("SELECT * FROM qef_product WHERE id = ? AND status = 1", [$itemId]);
        } else {
            $item = Db::fetch("SELECT * FROM qef_plugin WHERE id = ? AND status = 1", [$itemId]);
        }

        if (!$item) {
            throw new \Exception('商品不存在或已下架');
        }

        $quantity = max(1, (int) input('quantity', 1));
        $totalAmount = round((float) $item['price'] * $quantity, 2);

        $this->assign('title', '确认订单');
        $this->assign('itemType', $itemType);
        $this->assign('item', $item);
        $this->assign('quantity', $quantity);
        $this->assign('totalAmount', $totalAmount);
        $this->assign('user', $user);
        $this->fetch('order/create');
    }

    public function doCreate()
    {
        require_user_login();
        $user = get_user();

        $itemType = input('type', 'product');
        $itemId = (int) input('id', 0);
        $quantity = max(1, (int) input('quantity', 1));

        if ($itemType === 'product') {
            $item = Db::fetch("SELECT * FROM qef_product WHERE id = ? AND status = 1", [$itemId]);
        } else {
            $item = Db::fetch("SELECT * FROM qef_plugin WHERE id = ? AND status = 1", [$itemId]);
            $quantity = 1;
        }

        if (!$item) {
            json_error('商品不存在或已下架');
        }

        $totalAmount = round((float) $item['price'] * $quantity, 2);
        $orderNo = generate_order_no();

        $orderId = Db::insert('qef_order', [
            'order_no' => $orderNo,
            'user_id' => $user['id'],
            'item_type' => $itemType,
            'item_id' => $itemId,
            'item_name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $quantity,
            'total_amount' => $totalAmount,
            'pay_amount' => $totalAmount,
            'status' => 0,
            'create_time' => date('Y-m-d H:i:s'),
        ]);

        json_success('订单创建成功', ['redirect' => url('order/pay', ['order_no' => $orderNo])]);
    }

    public function pay()
    {
        require_user_login();
        $user = get_user();
        $orderNo = input('order_no', '');

        $order = Db::fetch("SELECT * FROM qef_order WHERE order_no = ? AND user_id = ?", [$orderNo, $user['id']]);
        if (!$order) {
            throw new \Exception('订单不存在');
        }
        if ($order['status'] != 0) {
            return redirect(url('user/order'));
        }

        $this->assign('title', '订单支付');
        $this->assign('order', $order);
        $this->assign('user', $user);
        $this->fetch('order/pay');
    }

    public function doPay()
    {
        require_user_login();
        $user = get_user();
        $orderNo = input('order_no', '');

        $order = Db::fetch("SELECT * FROM qef_order WHERE order_no = ? AND user_id = ? AND status = 0", [$orderNo, $user['id']]);
        if (!$order) {
            json_error('订单不存在或已支付');
        }

        if ((float) $user['balance'] < (float) $order['pay_amount']) {
            json_error('余额不足，请先充值');
        }

        $payTime = date('Y-m-d H:i:s');
        $licenseId = 0;

        Db::execute("UPDATE qef_user SET balance = balance - ? WHERE id = ?", [$order['pay_amount'], $user['id']]);

        if ($order['item_type'] === 'product') {
            $product = Db::fetch("SELECT * FROM qef_product WHERE id = ?", [$order['item_id']]);
            $expireTime = null;
            if ($product && (int) $product['valid_days'] > 0) {
                $expireTime = date('Y-m-d H:i:s', strtotime("+{$product['valid_days']} days"));
            }

            for ($i = 0; $i < $order['quantity']; $i++) {
                $authCode = generate_auth_code();
                while (Db::fetch("SELECT id FROM qef_license WHERE auth_code = ?", [$authCode])) {
                    $authCode = generate_auth_code();
                }
                $licenseId = Db::insert('qef_license', [
                    'auth_code' => $authCode,
                    'product_id' => $order['item_id'],
                    'user_id' => $user['id'],
                    'license_type' => $product['license_type'],
                    'status' => 1,
                    'expire_time' => $expireTime,
                    'create_time' => $payTime,
                ]);
            }
        } elseif ($order['item_type'] === 'plugin') {
            $pluginId = $order['item_id'];
            $exists = Db::fetch("SELECT id FROM qef_user_plugin WHERE user_id = ? AND plugin_id = ?", [$user['id'], $pluginId]);
            if (!$exists) {
                Db::insert('qef_user_plugin', [
                    'user_id' => $user['id'],
                    'plugin_id' => $pluginId,
                    'order_id' => $order['id'],
                    'create_time' => $payTime,
                ]);
            }
            Db::execute("UPDATE qef_plugin SET download_count = download_count + 1 WHERE id = ?", [$pluginId]);
        }

        Db::update('qef_order', [
            'status' => 1,
            'pay_channel' => 'balance',
            'pay_time' => $payTime,
            'license_id' => $licenseId,
        ], 'id = ?', [$order['id']]);

        $user = Db::fetch("SELECT * FROM qef_user WHERE id = ?", [$user['id']]);
        session('user', $user);

        json_success('支付成功', ['redirect' => url('user/order')]);
    }
}
