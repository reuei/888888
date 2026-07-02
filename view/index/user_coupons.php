<style>
.coupon-filter {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.coupon-filter a {
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 13px;
    background: #F1F5F9;
    color: #475569;
}
.coupon-filter a.active { background: #2563EB; color: #fff; }
.coupon-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px; }
.coupon-card {
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.coupon-card .top { display: flex; justify-content: space-between; align-items: flex-start; }
.coupon-card .name { font-weight: 600; color: #1F2937; }
.coupon-card .value { font-size: 20px; color: #EF4444; font-weight: 700; }
.coupon-card .meta { font-size: 12px; color: #64748B; }
.coupon-card .code { font-family: monospace; background: #F8FAFC; padding: 6px 10px; border-radius: 4px; font-size: 13px; }
</style>

<div class="card">
    <div class="section-title">
        <span>我的优惠券</span>
        <a href="<?php echo url('index/user'); ?>">返回个人中心</a>
    </div>

    <div class="coupon-filter">
        <a href="<?php echo url('index/userCoupons'); ?>" class="<?php echo $status === '' ? 'active' : ''; ?>">全部</a>
        <a href="<?php echo url('index/userCoupons', ['status' => 0]); ?>" class="<?php echo $status === '0' ? 'active' : ''; ?>">未使用</a>
        <a href="<?php echo url('index/userCoupons', ['status' => 1]); ?>" class="<?php echo $status === '1' ? 'active' : ''; ?>">已使用</a>
        <a href="<?php echo url('index/userCoupons', ['status' => 2]); ?>" class="<?php echo $status === '2' ? 'active' : ''; ?>">已过期</a>
    </div>

    <div class="coupon-list">
        <?php if (empty($list)): ?>
        <div class="empty-tip" style="grid-column: 1 / -1;">暂无优惠券</div>
        <?php else: ?>
        <?php
        $statusMap = [
            0 => ['未使用', 'tag-green'],
            1 => ['已使用', 'tag-blue'],
            2 => ['已过期', 'tag-red'],
        ];
        $typeMap = [1 => '满减券', 2 => '折扣券', 3 => '固定金额券'];
        ?>
        <?php foreach ($list as $item): ?>
        <?php $info = $statusMap[$item['status']] ?? ['未知', 'tag']; ?>
        <div class="coupon-card">
            <div class="top">
                <div class="name"><?php echo h($item['coupon_name'] ?: '优惠券'); ?></div>
                <span class="tag <?php echo $info[1]; ?>"><?php echo $info[0]; ?></span>
            </div>
            <div class="value">
                <?php if ((int) $item['type'] === 2): ?>
                <?php echo $item['amount']; ?> 折
                <?php else: ?>
                ¥<?php echo $item['amount']; ?>
                <?php endif; ?>
                <span style="font-size: 12px; color: #64748B; font-weight: normal; margin-left: 6px;"><?php echo $typeMap[$item['type']] ?? '优惠券'; ?></span>
            </div>
            <div class="meta">
                <?php if ((float) $item['min_amount'] > 0): ?>
                满 ¥<?php echo $item['min_amount']; ?> 可用
                <?php else: ?>
                无门槛
                <?php endif; ?>
                · 有效期至 <?php echo $item['expire_time'] ?: '长期有效'; ?>
            </div>
            <div class="code"><?php echo h($item['coupon_code']); ?></div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
