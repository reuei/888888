<div class="breadcrumb">资金管理 / 资金流水</div>
<div class="page-header">
    <h2>资金流水</h2>
</div>

<div class="card">
    <form method="get" class="search-bar">
        <select name="type" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部类型</option>
            <?php foreach ($typeMap as $key => $name): ?>
            <option value="<?php echo h($key); ?>" <?php echo $type === $key ? 'selected' : ''; ?>><?php echo h($name); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm">筛选</button>
        <a href="<?php echo url('merchant/finance/flow'); ?>" class="btn btn-sm btn-outline">重置</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>时间</th>
                <th>类型</th>
                <th>金额</th>
                <th>余额</th>
                <th>关联订单</th>
                <th>备注</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list)): ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #64748B;">暂无流水记录</td>
            </tr>
            <?php else: ?>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo h($item['create_time']); ?></td>
                <td>
                    <span class="tag <?php echo $item['type'] === 'income' ? 'tag-green' : ($item['type'] === 'fee' ? 'tag-red' : ($item['type'] === 'settle' ? 'tag-orange' : 'tag-blue')); ?>">
                        <?php echo h($typeMap[$item['type']] ?? $item['type']); ?>
                    </span>
                </td>
                <td>¥<?php echo number_format($item['amount'], 2); ?></td>
                <td>¥<?php echo number_format($item['balance'], 2); ?></td>
                <td><?php echo $item['order_id'] > 0 ? h($item['order_id']) : '-'; ?></td>
                <td><?php echo h($item['remark']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: center; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('merchant/finance/flow', ['type' => $type, 'page' => $page - 1]); ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 6px 12px; color: #64748B;"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('merchant/finance/flow', ['type' => $type, 'page' => $page + 1]); ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
