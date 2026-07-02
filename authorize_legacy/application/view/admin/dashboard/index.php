<div class="page-header">
    <h2>仪表盘</h2>
</div>

<div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); margin-bottom: 16px;">
    <div class="card" style="text-align:center;">
        <div style="font-size: 24px; font-weight: 600; color: #2563EB;"><?php echo $stats['total_users']; ?></div>
        <div style="color: #64748B; margin-top: 8px;">注册用户</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size: 24px; font-weight: 600; color: #2563EB;"><?php echo $stats['total_products']; ?></div>
        <div style="color: #64748B; margin-top: 8px;">授权产品</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size: 24px; font-weight: 600; color: #2563EB;"><?php echo $stats['total_plugins']; ?></div>
        <div style="color: #64748B; margin-top: 8px;">插件数量</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size: 24px; font-weight: 600; color: #2563EB;"><?php echo $stats['total_licenses']; ?></div>
        <div style="color: #64748B; margin-top: 8px;">授权码数量</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size: 24px; font-weight: 600; color: #2563EB;"><?php echo format_price($stats['total_amount']); ?></div>
        <div style="color: #64748B; margin-top: 8px;">总成交额</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size: 24px; font-weight: 600; color: #2563EB;"><?php echo format_price($stats['today_amount']); ?></div>
        <div style="color: #64748B; margin-top: 8px;">今日成交额</div>
    </div>
</div>

<div class="card">
    <div class="section-title">最近订单</div>
    <?php if (empty($recentOrders)): ?>
    <div class="empty-tip">暂无订单</div>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>订单号</th><th>用户</th><th>商品</th><th>金额</th><th>状态</th><th>时间</th></tr>
        </thead>
        <tbody>
            <?php foreach ($recentOrders as $item): ?>
            <tr>
                <td><?php echo h($item['order_no']); ?></td>
                <td><?php echo h($item['username'] ?: $item['nickname']); ?></td>
                <td><?php echo h($item['item_name']); ?></td>
                <td><?php echo format_price($item['pay_amount']); ?></td>
                <td><span class="tag <?php echo $item['status'] == 1 ? 'tag-green' : 'tag-orange'; ?>"><?php echo $item['status'] == 1 ? '已支付' : '待支付'; ?></span></td>
                <td><?php echo $item['create_time']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<div class="card">
    <div class="section-title">待审核插件</div>
    <?php if (empty($pendingPlugins)): ?>
    <div class="empty-tip">暂无待审核插件</div>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>插件</th><th>作者</th><th>版本</th><th>价格</th><th>操作</th></tr>
        </thead>
        <tbody>
            <?php foreach ($pendingPlugins as $item): ?>
            <tr>
                <td><?php echo h($item['name']); ?></td>
                <td><?php echo h($item['author']); ?></td>
                <td><?php echo h($item['version']); ?></td>
                <td><?php echo format_price($item['price']); ?></td>
                <td><a href="<?php echo url('admin/plugin/review', ['id' => $item['id']]); ?>" class="btn btn-sm">审核</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<div class="card">
    <div class="section-title">待处理充值</div>
    <?php if (empty($pendingRecharges)): ?>
    <div class="empty-tip">暂无待处理充值</div>
    <?php else: ?>
    <table>
        <thead>
            <tr><th>用户</th><th>金额</th><th>备注</th><th>时间</th><th>操作</th></tr>
        </thead>
        <tbody>
            <?php foreach ($pendingRecharges as $item): ?>
            <tr>
                <td><?php echo h($item['username'] ?: $item['nickname']); ?></td>
                <td><?php echo format_price($item['amount']); ?></td>
                <td><?php echo h($item['pay_remark']); ?></td>
                <td><?php echo $item['create_time']; ?></td>
                <td>
                    <button class="btn btn-sm approve" data-id="<?php echo $item['id']; ?>">到账</button>
                    <button class="btn btn-sm btn-outline reject" data-id="<?php echo $item['id']; ?>">拒绝</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.approve').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('确认已到账？')) return;
        const id = this.dataset.id;
        fetch('<?php echo url('admin/recharge/approve'); ?>', {method:'POST', body:new URLSearchParams({id})})
            .then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
document.querySelectorAll('.reject').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('确认拒绝？')) return;
        const id = this.dataset.id;
        fetch('<?php echo url('admin/recharge/reject'); ?>', {method:'POST', body:new URLSearchParams({id})})
            .then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
</script>
