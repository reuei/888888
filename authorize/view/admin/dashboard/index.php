<div class="page-header fade-in-up">
    <div>
        <h2><i data-icon="dashboard" class="svg-icon-lg" style="vertical-align:-4px;margin-right:6px;"></i>仪表盘</h2>
        <div class="page-sub">平台运营数据概览</div>
    </div>
</div>

<!-- 数据统计卡片 -->
<div class="grid-stats fade-in-up">
    <div class="stat-card">
        <div class="stat-icon purple"><i data-icon="user"></i></div>
        <div class="stat-value" data-count="<?php echo (int)$stats['total_users']; ?>">0</div>
        <div class="stat-label">注册用户</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i data-icon="product"></i></div>
        <div class="stat-value" data-count="<?php echo (int)$stats['total_products']; ?>">0</div>
        <div class="stat-label">授权产品</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i data-icon="plugin"></i></div>
        <div class="stat-value" data-count="<?php echo (int)$stats['total_plugins']; ?>">0</div>
        <div class="stat-label">插件数量</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i data-icon="license"></i></div>
        <div class="stat-value" data-count="<?php echo (int)$stats['total_licenses']; ?>">0</div>
        <div class="stat-label">授权码数量</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i data-icon="dollar"></i></div>
        <div class="stat-value" data-count="<?php echo (float)$stats['total_amount']; ?>" data-prefix="<?php echo h(site_config('currency_unit', '¥')); ?>">0</div>
        <div class="stat-label">总成交额</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i data-icon="dollar"></i></div>
        <div class="stat-value" data-count="<?php echo (float)$stats['today_amount']; ?>" data-prefix="<?php echo h(site_config('currency_unit', '¥')); ?>">0</div>
        <div class="stat-label">今日成交额</div>
    </div>
</div>

<!-- 最近订单 -->
<div class="card fade-in-up">
    <div class="section-title">
        <span><i data-icon="order" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>最近订单</span>
    </div>
    <?php if (empty($recentOrders)): ?>
    <div class="empty-tip">暂无订单</div>
    <?php else: ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>订单号</th><th>用户</th><th>商品</th><th>金额</th><th>状态</th><th>时间</th></tr>
        </thead>
        <tbody>
            <?php foreach ($recentOrders as $item): ?>
            <tr>
                <td><span style="font-family:monospace;"><?php echo h($item['order_no']); ?></span></td>
                <td><?php echo h($item['username'] ?: $item['nickname']); ?></td>
                <td><?php echo h($item['item_name']); ?></td>
                <td><span class="item-price" style="font-size:14px;"><?php echo format_price($item['pay_amount']); ?></span></td>
                <td><span class="tag <?php echo $item['status'] == 1 ? 'tag-green' : 'tag-orange'; ?>"><?php echo $item['status'] == 1 ? '已支付' : '待支付'; ?></span></td>
                <td><span style="color:#9ca3af;font-size:12px;"><?php echo $item['create_time']; ?></span></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<!-- 待审核插件 -->
<div class="card fade-in-up">
    <div class="section-title">
        <span><i data-icon="plugin" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>待审核插件</span>
    </div>
    <?php if (empty($pendingPlugins)): ?>
    <div class="empty-tip">暂无待审核插件</div>
    <?php else: ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>插件</th><th>作者</th><th>版本</th><th>价格</th><th>操作</th></tr>
        </thead>
        <tbody>
            <?php foreach ($pendingPlugins as $item): ?>
            <tr>
                <td><?php echo h($item['name']); ?></td>
                <td><?php echo h($item['author']); ?></td>
                <td><span class="tag tag-purple"><?php echo h($item['version']); ?></span></td>
                <td><span class="item-price" style="font-size:14px;"><?php echo format_price($item['price']); ?></span></td>
                <td><a href="<?php echo url('admin/plugin/review', ['id' => $item['id']]); ?>" class="btn btn-sm"><i data-icon="check" class="svg-icon-sm"></i>审核</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<!-- 待处理充值 -->
<div class="card fade-in-up">
    <div class="section-title">
        <span><i data-icon="recharge" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>待处理充值</span>
    </div>
    <?php if (empty($pendingRecharges)): ?>
    <div class="empty-tip">暂无待处理充值</div>
    <?php else: ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>用户</th><th>金额</th><th>备注</th><th>时间</th><th>操作</th></tr>
        </thead>
        <tbody>
            <?php foreach ($pendingRecharges as $item): ?>
            <tr>
                <td><?php echo h($item['username'] ?: $item['nickname']); ?></td>
                <td><span class="item-price" style="font-size:14px;"><?php echo format_price($item['amount']); ?></span></td>
                <td><?php echo h($item['pay_remark']); ?></td>
                <td><span style="color:#9ca3af;font-size:12px;"><?php echo $item['create_time']; ?></span></td>
                <td>
                    <button class="btn btn-sm btn-success approve" data-id="<?php echo $item['id']; ?>"><i data-icon="check" class="svg-icon-sm"></i>到账</button>
                    <button class="btn btn-sm btn-outline reject" data-id="<?php echo $item['id']; ?>"><i data-icon="close" class="svg-icon-sm"></i>拒绝</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.approve').forEach(function(btn) {
    btn.addEventListener('click', function() {
        if (!confirm('确认已到账？')) return;
        var id = this.dataset.id;
        var self = this;
        if (window.QEEFG) QEEFG.setLoading(self, true);
        fetch('<?php echo url('admin/recharge/approve'); ?>', {method:'POST', body:new URLSearchParams({id:id})})
            .then(function(r){ return r.json(); }).then(function(res){
                if (window.QEEFG) { QEEFG.setLoading(self, false); QEEFG.toast(res.msg, res.code === 0 ? 'success' : 'error'); }
                else { alert(res.msg); }
                if (res.code === 0) setTimeout(function(){ location.reload(); }, 600);
            }).catch(function(){
                if (window.QEEFG) { QEEFG.setLoading(self, false); QEEFG.toastError('网络异常'); }
            });
    });
});
document.querySelectorAll('.reject').forEach(function(btn) {
    btn.addEventListener('click', function() {
        if (!confirm('确认拒绝？')) return;
        var id = this.dataset.id;
        var self = this;
        if (window.QEEFG) QEEFG.setLoading(self, true);
        fetch('<?php echo url('admin/recharge/reject'); ?>', {method:'POST', body:new URLSearchParams({id:id})})
            .then(function(r){ return r.json(); }).then(function(res){
                if (window.QEEFG) { QEEFG.setLoading(self, false); QEEFG.toast(res.msg, res.code === 0 ? 'success' : 'error'); }
                else { alert(res.msg); }
                if (res.code === 0) setTimeout(function(){ location.reload(); }, 600);
            }).catch(function(){
                if (window.QEEFG) { QEEFG.setLoading(self, false); QEEFG.toastError('网络异常'); }
            });
    });
});
</script>
