<div class="breadcrumb">资金管理 / 结算提现</div>
<div class="page-header">
    <h2>结算提现记录</h2>
    <a href="<?php echo url('merchant/finance'); ?>" class="btn btn-sm btn-outline">返回概览</a>
</div>

<div class="card">
    <form method="get" class="search-bar">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <?php foreach ($statusMap as $key => $name): ?>
            <option value="<?php echo h($key); ?>" <?php echo $status === (string) $key ? 'selected' : ''; ?>><?php echo h($name); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm">筛选</button>
        <a href="<?php echo url('merchant/finance/settle'); ?>" class="btn btn-sm btn-outline">重置</a>
    </form>

    <table>
        <thead>
            <tr>
                <th>结算单号</th>
                <th>提现金额</th>
                <th>到账金额</th>
                <th>渠道</th>
                <th>收款账号</th>
                <th>状态</th>
                <th>备注</th>
                <th>时间</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($list)): ?>
            <tr>
                <td colspan="8" style="text-align: center; color: #64748B;">暂无结算记录</td>
            </tr>
            <?php else: ?>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo h($item['settle_no']); ?></td>
                <td>¥<?php echo number_format($item['amount'], 2); ?></td>
                <td>¥<?php echo number_format($item['real_amount'], 2); ?></td>
                <td><?php echo h($item['channel']); ?></td>
                <td><?php echo h($item['account']); ?></td>
                <td>
                    <span class="tag <?php echo $item['status'] == 2 ? 'tag-green' : ($item['status'] == 3 ? 'tag-red' : ($item['status'] == 1 ? 'tag-blue' : 'tag-orange')); ?>">
                        <?php echo h($statusMap[$item['status']] ?? $item['status']); ?>
                    </span>
                </td>
                <td><?php echo h($item['remark']); ?></td>
                <td><?php echo h($item['create_time']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: center; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('merchant/finance/settle', ['status' => $status, 'page' => $page - 1]); ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 6px 12px; color: #64748B;"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('merchant/finance/settle', ['status' => $status, 'page' => $page + 1]); ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
