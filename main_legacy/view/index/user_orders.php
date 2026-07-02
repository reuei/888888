<style>
.order-filter {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.order-filter a {
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 13px;
    background: #F1F5F9;
    color: #475569;
}
.order-filter a.active { background: #2563EB; color: #fff; }
.order-list { display: flex; flex-direction: column; gap: 12px; }
.order-item {
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    padding: 16px;
}
.order-item .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #F1F5F9;
}
.order-item .no { font-size: 13px; color: #64748B; }
.order-item .body { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
.order-item .name { font-weight: 500; color: #1F2937; margin-bottom: 4px; }
.order-item .meta { font-size: 12px; color: #64748B; }
.order-item .amount { font-size: 18px; color: #EF4444; font-weight: 600; }
.order-item .actions { margin-top: 12px; display: flex; gap: 8px; justify-content: flex-end; }
</style>

<div class="card">
    <div class="section-title">
        <span>我的订单</span>
        <a href="<?php echo url('index/user'); ?>">返回个人中心</a>
    </div>

    <div class="order-filter">
        <a href="<?php echo url('index/userOrders'); ?>" class="<?php echo $status === '' ? 'active' : ''; ?>">全部</a>
        <a href="<?php echo url('index/userOrders', ['status' => 0]); ?>" class="<?php echo $status === '0' ? 'active' : ''; ?>">待支付</a>
        <a href="<?php echo url('index/userOrders', ['status' => 2]); ?>" class="<?php echo $status === '2' ? 'active' : ''; ?>">已发货</a>
        <a href="<?php echo url('index/userOrders', ['status' => 3]); ?>" class="<?php echo $status === '3' ? 'active' : ''; ?>">已完成</a>
    </div>

    <div class="order-list">
        <?php if (empty($list)): ?>
        <div class="empty-tip">暂无订单</div>
        <?php else: ?>
        <?php
        $statusMap = [
            0 => ['待支付', '#D97706'],
            1 => ['已支付', '#2563EB'],
            2 => ['已发货', '#059669'],
            3 => ['已完成', '#059669'],
            4 => ['退款中', '#EF4444'],
            5 => ['已关闭', '#64748B'],
        ];
        ?>
        <?php foreach ($list as $item): ?>
        <?php $info = $statusMap[$item['status']] ?? ['未知', '#475569']; ?>
        <div class="order-item">
            <div class="header">
                <span class="no"><?php echo h($item['order_no']); ?></span>
                <span class="tag" style="color: <?php echo $info[1]; ?>; background: <?php echo $info[1]; ?>15;"><?php echo $info[0]; ?></span>
            </div>
            <div class="body">
                <div>
                    <div class="name"><?php echo h($item['goods_name']); ?></div>
                    <div class="meta"><?php echo h($item['shop_name'] ?: '官方店铺'); ?> · <?php echo h($item['create_time']); ?></div>
                </div>
                <div class="amount">¥<?php echo $item['pay_amount']; ?></div>
            </div>
            <div class="actions">
                <a href="<?php echo url('index/order', ['no' => $item['order_no']]); ?>" class="btn btn-sm btn-outline">查看详情</a>
                <?php if ($item['status'] == 0): ?>
                <a href="<?php echo url('index/pay', ['order_no' => $item['order_no']]); ?>" class="btn btn-sm btn-success">去支付</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('index/userOrders') . '?status=' . urlencode($status) . '&page=' . ($page - 1); ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('index/userOrders') . '?status=' . urlencode($status) . '&page=' . ($page + 1); ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
