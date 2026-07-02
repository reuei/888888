<div class="breadcrumb">分站管理 / 分站详情</div>
<div class="page-header">
    <h2><?php echo h($subsite['name']); ?></h2>
    <a href="<?php echo url('admin/subsite'); ?>" class="btn btn-outline">返回列表</a>
</div>

<div class="card" style="margin-bottom: 16px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; font-size: 14px;">
        <div><span style="color: #64748B;">分站ID：</span><?php echo $subsite['id']; ?></div>
        <div><span style="color: #64748B;">域名前缀：</span><?php echo h($subsite['domain_prefix']); ?></div>
        <div><span style="color: #64748B;">超管账号：</span><?php echo h($subsite['admin_name']); ?></div>
        <div><span style="color: #64748B;">二次认证：</span><?php echo $subsite['two_factor'] ? '<span class="tag tag-green">已开启</span>' : '<span class="tag">未开启</span>'; ?></div>
        <div><span style="color: #64748B;">费率分组：</span><?php echo h($subsite['rate_group_name'] ?? '-'); ?></div>
        <div><span style="color: #64748B;">结算周期：</span><?php echo h($subsite['settle_template']); ?></div>
        <div><span style="color: #64748B;">状态：</span>
            <?php if ($subsite['status'] == 1): ?>
            <span class="tag tag-green">正常</span>
            <?php elseif ($subsite['status'] == 2): ?>
            <span class="tag tag-orange">冻结</span>
            <?php else: ?>
            <span class="tag">关闭</span>
            <?php endif; ?>
        </div>
        <div><span style="color: #64748B;">创建时间：</span><?php echo $subsite['create_time']; ?></div>
    </div>
</div>

<div class="tabs" style="display: flex; border-bottom: 1px solid #E2E8F0; margin-bottom: 16px;">
    <?php
    $tabs = [
        'info' => '基础信息',
        'merchant' => '商户列表',
        'goods' => '商品列表',
        'order' => '订单列表',
        'finance' => '财务报表',
        'log' => '操作日志',
    ];
    foreach ($tabs as $key => $label):
    ?>
    <a href="<?php echo url('admin/subsite/detail') . '?id=' . $subsite['id'] . '&tab=' . $key; ?>" class="tab <?php echo $tab === $key ? 'active' : ''; ?>" style="padding: 12px 20px; color: <?php echo $tab === $key ? '#2563EB' : '#64748B'; ?>; border-bottom: 2px solid <?php echo $tab === $key ? '#2563EB' : 'transparent'; ?>; font-weight: 500;"><?php echo $label; ?></a>
    <?php endforeach; ?>
</div>

<div class="card">
    <?php if ($tab === 'info'): ?>
    <h3 style="font-size: 16px; margin-bottom: 16px;">基础信息</h3>
    <table>
        <tr><th style="width: 160px;">项目</th><th>内容</th></tr>
        <tr><td>分站名称</td><td><?php echo h($subsite['name']); ?></td></tr>
        <tr><td>域名前缀</td><td><?php echo h($subsite['domain_prefix']); ?>.example.com</td></tr>
        <tr><td>超管账号</td><td><?php echo h($subsite['admin_name']); ?></td></tr>
        <tr><td>最后登录</td><td><?php echo $subsite['admin_last_login'] ?: '-'; ?></td></tr>
        <tr><td>费率分组</td><td><?php echo h($subsite['rate_group_name'] ?? '-'); ?></td></tr>
        <tr><td>结算周期模板</td><td><?php echo h($subsite['settle_template']); ?></td></tr>
        <tr><td>二次认证</td><td><?php echo $subsite['two_factor'] ? '已开启' : '未开启'; ?></td></tr>
        <tr><td>状态</td><td><?php echo $subsite['status'] == 1 ? '正常' : ($subsite['status'] == 2 ? '冻结' : '关闭'); ?></td></tr>
    </table>

    <?php elseif ($tab === 'merchant'): ?>
    <h3 style="font-size: 16px; margin-bottom: 16px;">商户列表</h3>
    <table>
        <tr><th>商户ID</th><th>店铺名</th><th>店铺ID</th><th>手机号</th><th>状态</th><th>开店时间</th></tr>
        <?php if (empty($data['merchant'])): ?>
        <tr><td colspan="6" style="text-align: center; color: #64748B; padding: 40px;">暂无商户</td></tr>
        <?php else: ?>
        <?php foreach ($data['merchant'] as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['shop_name']); ?></td>
            <td><?php echo h($item['shop_id']); ?></td>
            <td><?php echo h($item['mobile']); ?></td>
            <td><?php echo $item['status'] == 1 ? '<span class="tag tag-green">正常</span>' : '<span class="tag">其他</span>'; ?></td>
            <td><?php echo $item['create_time']; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php elseif ($tab === 'goods'): ?>
    <h3 style="font-size: 16px; margin-bottom: 16px;">商品列表</h3>
    <table>
        <tr><th>商品ID</th><th>商品名称</th><th>所属商户</th><th>售价</th><th>库存</th><th>状态</th></tr>
        <?php if (empty($data['goods'])): ?>
        <tr><td colspan="6" style="text-align: center; color: #64748B; padding: 40px;">暂无商品</td></tr>
        <?php else: ?>
        <?php foreach ($data['goods'] as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['name']); ?></td>
            <td><?php echo h($item['shop_name'] ?? '-'); ?></td>
            <td>¥ <?php echo $item['price']; ?></td>
            <td><?php echo $item['stock']; ?></td>
            <td><?php echo $item['status'] == 1 ? '<span class="tag tag-green">上架</span>' : '<span class="tag">下架</span>'; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php elseif ($tab === 'order'): ?>
    <h3 style="font-size: 16px; margin-bottom: 16px;">订单列表</h3>
    <table>
        <tr><th>订单号</th><th>商品</th><th>金额</th><th>支付渠道</th><th>状态</th><th>下单时间</th></tr>
        <?php if (empty($data['order'])): ?>
        <tr><td colspan="6" style="text-align: center; color: #64748B; padding: 40px;">暂无订单</td></tr>
        <?php else: ?>
        <?php foreach ($data['order'] as $item): ?>
        <tr>
            <td><?php echo h($item['order_no']); ?></td>
            <td><?php echo h($item['goods_name']); ?></td>
            <td>¥ <?php echo $item['total_amount']; ?></td>
            <td><?php echo h($item['pay_channel']); ?></td>
            <td><?php echo $item['status'] == 1 ? '<span class="tag tag-green">已支付</span>' : '<span class="tag">待支付</span>'; ?></td>
            <td><?php echo $item['create_time']; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php elseif ($tab === 'finance'): ?>
    <h3 style="font-size: 16px; margin-bottom: 16px;">财务报表</h3>
    <table>
        <tr><th>结算单号</th><th>金额</th><th>手续费</th><th>实结金额</th><th>渠道</th><th>状态</th><th>时间</th></tr>
        <?php if (empty($data['finance'])): ?>
        <tr><td colspan="7" style="text-align: center; color: #64748B; padding: 40px;">暂无结算记录</td></tr>
        <?php else: ?>
        <?php foreach ($data['finance'] as $item): ?>
        <tr>
            <td><?php echo h($item['settle_no']); ?></td>
            <td>¥ <?php echo $item['amount']; ?></td>
            <td>¥ <?php echo $item['fee']; ?></td>
            <td>¥ <?php echo $item['real_amount']; ?></td>
            <td><?php echo h($item['channel']); ?></td>
            <td>
                <?php if ($item['status'] == 2): ?>
                <span class="tag tag-green">成功</span>
                <?php elseif ($item['status'] == 1): ?>
                <span class="tag tag-blue">处理中</span>
                <?php elseif ($item['status'] == 3): ?>
                <span class="tag tag-red">失败</span>
                <?php else: ?>
                <span class="tag">待处理</span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['create_time']; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php elseif ($tab === 'log'): ?>
    <h3 style="font-size: 16px; margin-bottom: 16px;">操作日志</h3>
    <table>
        <tr><th>操作人</th><th>操作</th><th>内容</th><th>IP</th><th>时间</th></tr>
        <?php if (empty($data['log'])): ?>
        <tr><td colspan="5" style="text-align: center; color: #64748B; padding: 40px;">暂无日志</td></tr>
        <?php else: ?>
        <?php foreach ($data['log'] as $item): ?>
        <tr>
            <td><?php echo h($item['admin_name']); ?></td>
            <td><?php echo h($item['action']); ?></td>
            <td><?php echo h($item['content']); ?></td>
            <td><?php echo h($item['ip']); ?></td>
            <td><?php echo $item['create_time']; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
    <?php endif; ?>
</div>
