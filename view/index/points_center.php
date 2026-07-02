<div class="container">
    <div class="points-header" style="background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%); border-radius: 12px; padding: 24px; color: #fff; margin-bottom: 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <div style="font-size: 13px; opacity: 0.9;">我的积分</div>
                <div style="font-size: 36px; font-weight: 700; margin-top: 4px;"><?php echo $user ? number_format($user['points']) : '0'; ?></div>
                <div style="font-size: 13px; opacity: 0.9; margin-top: 4px;">成长值 <?php echo $user ? number_format($user['growth_value']) : '0'; ?></div>
            </div>
            <a href="<?php echo url('index/pointsLog'); ?>" style="color:#fff; text-decoration:underline; font-size: 14px;">积分明细</a>
        </div>
    </div>

    <h3 style="margin-bottom: 12px; font-size: 16px;">积分兑换</h3>
    <?php if (empty($goods)): ?>
    <div class="card" style="text-align:center; padding: 40px; color: #64748B;">暂无积分商品</div>
    <?php else: ?>
    <div class="goods-grid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; margin-bottom: 20px;">
        <?php foreach ($goods as $item): ?>
        <a href="<?php echo url('index/pointsGoods', ['id' => $item['id']]); ?>" class="card" style="display:block; padding: 12px; text-decoration:none;">
            <?php if ($item['image']): ?>
            <img src="<?php echo base_url($item['image']); ?>" style="width:100%; height:120px; object-fit:cover; border-radius:6px; margin-bottom:8px;">
            <?php endif; ?>
            <div style="font-weight:500; color:#1F2937; font-size:14px; line-height:1.4; min-height:40px;"><?php echo h($item['title']); ?></div>
            <div style="color:#EF4444; font-weight:600; margin-top:6px;"><?php echo $item['points']; ?> 积分</div>
            <div style="color:#94A3B8; font-size:12px; margin-top:2px;">库存 <?php echo $item['stock']; ?></div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <h3 style="margin-bottom: 12px; font-size: 16px;">最近积分动态</h3>
    <div class="card" style="padding: 0; overflow:hidden;">
        <?php if (empty($logs)): ?>
        <div style="padding: 24px; text-align:center; color:#64748B;">暂无积分记录</div>
        <?php else: ?>
        <?php foreach ($logs as $log): ?>
        <div style="display:flex; justify-content:space-between; align-items:center; padding: 12px 16px; border-bottom: 1px solid #E2E8F0;">
            <div>
                <div style="font-size: 14px; color:#1F2937;"><?php echo $typeMap[$log['type']] ?? $log['type']; ?></div>
                <div style="font-size: 12px; color:#94A3B8; margin-top:2px;"><?php echo h($log['remark']); ?></div>
            </div>
            <div style="font-weight:600; color: <?php echo $log['points'] >= 0 ? '#059669' : '#DC2626'; ?>">
                <?php echo ($log['points'] >= 0 ? '+' : '') . $log['points']; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
