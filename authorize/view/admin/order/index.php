<div class="page-header">
    <h2>订单管理</h2>
</div>

<div class="card">
    <form method="get" action="<?php echo url('admin/order'); ?>" class="search-bar">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="订单号/商品名">
        <select name="status">
            <option value="">全部状态</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>待支付</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>已支付</option>
            <option value="2" <?php echo $status === '2' ? 'selected' : ''; ?>>已取消</option>
        </select>
        <button type="submit" class="btn btn-sm">搜索</button>
    </form>

    <?php if (empty($list)): ?>
    <div class="empty-tip">暂无订单</div>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>订单号</th><th>用户</th><th>商品</th><th>类型</th><th>金额</th><th>状态</th><th>时间</th><th>操作</th></tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo h($item['order_no']); ?></td>
                <td><?php echo h($item['username'] ?: $item['nickname'] ?: '-'); ?></td>
                <td><?php echo h($item['item_name']); ?></td>
                <td><?php echo $item['item_type'] === 'product' ? '授权产品' : '插件'; ?></td>
                <td><?php echo format_price($item['pay_amount']); ?></td>
                <td>
                    <?php if ($item['status'] == 0): ?><span class="tag tag-orange">待支付</span>
                    <?php elseif ($item['status'] == 1): ?><span class="tag tag-green">已支付</span>
                    <?php else: ?><span class="tag tag-red">已取消</span><?php endif; ?>
                </td>
                <td><?php echo $item['create_time']; ?></td>
                <td><a href="<?php echo url('admin/order/detail', ['id' => $item['id']]); ?>" class="btn btn-sm">详情</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo pagination($total, $page, 15, url('admin/order', ['keyword' => $keyword, 'status' => $status, 'page' => '{page}'])); ?>
    <?php endif; ?>
</div>
