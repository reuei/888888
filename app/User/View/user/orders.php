<div class="page-head">
    <h1 class="page-title">我的订单</h1>
    <div class="page-filter">
        <a href="/user/orders" class="filter-tab <?= $status == -1 ? 'active' : '' ?>">全部</a>
        <a href="/user/orders?status=0" class="filter-tab <?= $status == 0 ? 'active' : '' ?>">待支付</a>
        <a href="/user/orders?status=1" class="filter-tab <?= $status == 1 ? 'active' : '' ?>">已完成</a>
        <a href="/user/orders?status=4" class="filter-tab <?= $status == 4 ? 'active' : '' ?>">已关闭</a>
    </div>
</div>
<div class="panel">
    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <div class="empty-icon"></div>
            <p>暂无订单</p>
        </div>
    <?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>订单号</th>
                <th>商品</th>
                <th>金额</th>
                <th>状态</th>
                <th>时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
                <td><?= h($o['order_no']) ?></td>
                <td><?= h($o['goods_name']) ?></td>
                <td>¥<?= format_money($o['amount']) ?></td>
                <td>
                    <?php $st = (int) $o['status']; ?>
                    <span class="badge badge-<?= $st === 1 ? 'success' : ($st === 0 ? 'warning' : 'default') ?>">
                        <?= $st === 1 ? '已完成' : ($st === 0 ? '待支付' : ($st === 2 ? '已发货' : '已关闭')) ?>
                    </span>
                </td>
                <td class="muted"><?= format_time($o['create_time']) ?></td>
                <td>
                    <?php if ($st === 0): ?>
                        <a href="#" class="link">立即支付</a>
                    <?php else: ?>
                        <a href="#" class="link">查看详情</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
