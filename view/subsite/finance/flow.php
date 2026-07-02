<div class="breadcrumb">财务结算 / 分站资金流水</div>
<div class="page-header">
    <h2>分站资金流水</h2>
    <a href="<?php echo url('subsite/finance/settle'); ?>" class="btn btn-outline">结算管理</a>
</div>

<div class="card" style="margin-bottom: 16px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px;">
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">流水笔数</div>
            <div style="font-size: 20px; font-weight: 600; color: #1F2937;"><?php echo $stat['total_count'] ?? 0; ?></div>
        </div>
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">总收入</div>
            <div style="font-size: 20px; font-weight: 600; color: #10B981;">¥ <?php echo number_format($stat['total_income'] ?? 0, 2); ?></div>
        </div>
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">总结算</div>
            <div style="font-size: 20px; font-weight: 600; color: #2563EB;">¥ <?php echo number_format($stat['total_settle'] ?? 0, 2); ?></div>
        </div>
        <div style="text-align: center; padding: 12px; background: #F8FAFC; border-radius: 6px;">
            <div style="color: #64748B; font-size: 12px; margin-bottom: 4px;">总手续费</div>
            <div style="font-size: 20px; font-weight: 600; color: #F59E0B;">¥ <?php echo number_format($stat['total_fee'] ?? 0, 2); ?></div>
        </div>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('subsite/finance/flow'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="商户名 / 订单ID">
        <select name="type" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部类型</option>
            <?php foreach ($typeMap as $k => $v): ?>
            <option value="<?php echo $k; ?>" <?php echo $type === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>流水ID</th>
            <th>商户</th>
            <th>类型</th>
            <th>金额</th>
            <th>余额</th>
            <th>订单ID</th>
            <th>备注</th>
            <th>时间</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="8" style="text-align: center; color: #64748B; padding: 40px;">暂无流水记录</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['shop_name'] ?? '-'); ?></td>
            <td>
                <?php
                $typeColors = ['income' => 'tag-green', 'refund' => 'tag-orange', 'fee' => 'tag', 'freeze' => 'tag-red', 'unfreeze' => 'tag-blue', 'settle' => 'tag-blue'];
                $color = $typeColors[$item['type']] ?? 'tag';
                ?>
                <span class="tag <?php echo $color; ?>"><?php echo $typeMap[$item['type']] ?? $item['type']; ?></span>
            </td>
            <td style="color: <?php echo in_array($item['type'], ['income', 'unfreeze']) ? '#059669' : '#DC2626'; ?>">¥ <?php echo $item['amount']; ?></td>
            <td>¥ <?php echo $item['balance']; ?></td>
            <td><?php echo $item['order_id'] ?: '-'; ?></td>
            <td><?php echo h($item['remark'] ?: '-'); ?></td>
            <td><?php echo $item['create_time']; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('subsite/finance/flow') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&type=' . $type; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('subsite/finance/flow') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&type=' . $type; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
