<div class="breadcrumb">商品管理 / <a href="<?php echo url('subsite/goods'); ?>">分站商品列表</a> / 商品详情</div>
<div class="page-header">
    <h2>分站商品详情</h2>
    <a href="<?php echo url('subsite/goods'); ?>" class="btn btn-outline">返回列表</a>
</div>

<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;">商品信息</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 14px;">
        <div><span style="color: #64748B;">商品ID：</span><?php echo $goods['id']; ?></div>
        <div><span style="color: #64748B;">商品名称：</span><?php echo h($goods['name']); ?></div>
        <div><span style="color: #64748B;">所属商户：</span><?php echo h($goods['shop_name'] ?? '-'); ?></div>
        <div><span style="color: #64748B;">商品分类：</span><?php echo h($goods['category_name'] ?? '-'); ?></div>
        <div><span style="color: #64748B;">商品类型：</span><?php echo $goods['type'] == 1 ? '卡密' : ($goods['type'] == 2 ? '人工' : '自动'); ?></div>
        <div><span style="color: #64748B;">售价：</span>¥ <?php echo $goods['price']; ?></div>
        <div><span style="color: #64748B;">原价：</span>¥ <?php echo $goods['original_price'] ?: '-'; ?></div>
        <div><span style="color: #64748B;">当前库存：</span><?php echo $goods['stock']; ?></div>
        <div><span style="color: #64748B;">库存预警线：</span><?php echo $goods['low_stock']; ?></div>
        <div><span style="color: #64748B;">累计销量：</span><?php echo $goods['sold']; ?></div>
        <div><span style="color: #64748B;">状态：</span>
            <?php if ($goods['status'] == 1): ?>
            <span class="tag tag-green">上架中</span>
            <?php else: ?>
            <span class="tag">已下架</span>
            <?php endif; ?>
        </div>
        <div><span style="color: #64748B;">创建时间：</span><?php echo $goods['create_time']; ?></div>
    </div>
</div>

<?php if ($goods['type'] == 1): ?>
<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;">卡密库存</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px;">
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">未售出</div>
            <div style="font-size: 20px; font-weight: 600; color: #10B981;"><?php echo $cardStats['unsold'] ?? 0; ?></div>
        </div>
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">已售出</div>
            <div style="font-size: 20px; font-weight: 600; color: #2563EB;"><?php echo $cardStats['sold'] ?? 0; ?></div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <h3 style="font-size: 16px; margin-bottom: 16px;">最近订单</h3>
    <table>
        <tr>
            <th>订单号</th>
            <th>金额</th>
            <th>支付渠道</th>
            <th>状态</th>
            <th>下单时间</th>
        </tr>
        <?php if (empty($orders)): ?>
        <tr><td colspan="5" style="text-align: center; color: #64748B; padding: 40px;">暂无订单</td></tr>
        <?php else: ?>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td><?php echo h($o['order_no']); ?></td>
            <td>¥ <?php echo $o['total_amount']; ?></td>
            <td><?php echo h($o['pay_channel'] ?: '-'); ?></td>
            <td>
                <?php
                $statusMap = [0 => '待支付', 1 => '已支付', 2 => '已发货', 3 => '已完成', 4 => '退款中', 5 => '已关闭'];
                $statusColors = [0 => 'tag-orange', 1 => 'tag-blue', 2 => 'tag-green', 3 => 'tag-green', 4 => 'tag-orange', 5 => 'tag'];
                ?>
                <span class="tag <?php echo $statusColors[$o['status']] ?? 'tag'; ?>"><?php echo $statusMap[$o['status']] ?? '未知'; ?></span>
            </td>
            <td><?php echo $o['create_time']; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>
