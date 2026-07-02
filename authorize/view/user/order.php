<div class="user-layout">
    <div class="user-sidebar">
        <a href="<?php echo url('user'); ?>">个人中心</a>
        <a href="<?php echo url('user/license'); ?>">我的授权</a>
        <a href="<?php echo url('user/plugin'); ?>">我的插件</a>
        <a href="<?php echo url('user/order'); ?>" class="active">我的订单</a>
        <a href="<?php echo url('user/recharge'); ?>">余额充值</a>
        <a href="<?php echo url('user/profile'); ?>">修改资料</a>
        <a href="<?php echo url('user/password'); ?>">修改密码</a>
    </div>
    <div class="user-content">
        <div class="card">
            <div class="section-title">我的订单</div>
            <form method="get" action="<?php echo url('user/order'); ?>" class="search-bar" style="margin-bottom: 16px;">
                <select name="status">
                    <option value="">全部状态</option>
                    <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>待支付</option>
                    <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>已支付</option>
                    <option value="2" <?php echo $status === '2' ? 'selected' : ''; ?>>已取消</option>
                </select>
                <button type="submit" class="btn btn-sm">筛选</button>
            </form>
            <?php if (empty($list)): ?>
            <div class="empty-tip">暂无订单</div>
            <?php else: ?>
            <table>
                <thead>
                    <tr><th>订单号</th><th>商品</th><th>类型</th><th>金额</th><th>状态</th><th>时间</th><th>操作</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $item): ?>
                    <tr>
                        <td><?php echo h($item['order_no']); ?></td>
                        <td><?php echo h($item['item_name']); ?></td>
                        <td><?php echo $item['item_type'] === 'product' ? '授权产品' : '插件'; ?></td>
                        <td><?php echo format_price($item['pay_amount']); ?></td>
                        <td>
                            <?php if ($item['status'] == 0): ?><span class="tag tag-orange">待支付</span>
                            <?php elseif ($item['status'] == 1): ?><span class="tag tag-green">已支付</span>
                            <?php else: ?><span class="tag tag-red">已取消</span><?php endif; ?>
                        </td>
                        <td><?php echo $item['create_time']; ?></td>
                        <td>
                            <?php if ($item['status'] == 0): ?>
                            <a href="<?php echo url('order/pay', ['order_no' => $item['order_no']]); ?>" class="btn btn-sm">去支付</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php echo pagination($total, $page, 10, url('user/order', ['status' => $status, 'page' => '{page}'])); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
