<div class="container">
    <h2 style="font-size: 18px; margin-bottom: 16px;">积分明细</h2>
    <div class="card" style="padding: 0; overflow:hidden;">
        <?php if (empty($list)): ?>
        <div style="padding: 40px; text-align:center; color:#64748B;">暂无积分记录</div>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <div style="display:flex; justify-content:space-between; align-items:center; padding: 14px 16px; border-bottom: 1px solid #E2E8F0;">
            <div>
                <div style="font-size: 15px; color:#1F2937;"><?php echo $typeMap[$item['type']] ?? $item['type']; ?></div>
                <div style="font-size: 12px; color:#94A3B8; margin-top:2px;"><?php echo h($item['remark']); ?> · <?php echo $item['create_time']; ?></div>
            </div>
            <div style="font-weight:600; color: <?php echo $item['points'] >= 0 ? '#059669' : '#DC2626'; ?>">
                <?php echo ($item['points'] >= 0 ? '+' : '') . $item['points']; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <div style="display:flex; justify-content:center; gap:8px; margin-top: 16px;">
        <a href="<?php echo url('index/pointsLog') . '?page=' . ($page - 1); ?>" class="btn btn-sm btn-outline <?php echo $page <= 1 ? 'disabled' : ''; ?>">上一页</a>
        <span style="padding:5px 10px; color:#64748B;"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
        <a href="<?php echo url('index/pointsLog') . '?page=' . ($page + 1); ?>" class="btn btn-sm btn-outline <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">下一页</a>
    </div>
    <?php endif; ?>
</div>
