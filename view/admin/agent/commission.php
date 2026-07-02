<div class="breadcrumb">代理分销 / 佣金记录</div>
<div class="page-header">
    <h2>佣金记录</h2>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/agent/commission'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="代理昵称 / 订单编号">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>待结算</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>已结算</option>
            <option value="2" <?php echo $status === '2' ? 'selected' : ''; ?>>已取消</option>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>获得佣金代理</th>
            <th>来源订单</th>
            <th>商品</th>
            <th>订单金额</th>
            <th>佣金</th>
            <th>层级</th>
            <th>状态</th>
            <th>时间</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="9" style="text-align: center; color: #64748B; padding: 40px;">暂无佣金记录</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td>
                <div style="font-weight: 500;"><?php echo h($item['nickname'] ?? '-'); ?></div>
                <div style="color: #94A3B8; font-size: 12px;"><?php echo h($item['mobile'] ?? '-'); ?></div>
            </td>
            <td><?php echo h($item['order_no'] ?: '-'); ?></td>
            <td><?php echo h($item['goods_name'] ?? '-'); ?></td>
            <td>¥ <?php echo $item['amount']; ?></td>
            <td style="color: #059669; font-weight: 500;">¥ <?php echo $item['commission']; ?></td>
            <td>第 <?php echo $item['level']; ?> 级</td>
            <td>
                <?php if ($item['status'] == 0): ?>
                <span class="tag tag-orange">待结算</span>
                <?php elseif ($item['status'] == 1): ?>
                <span class="tag tag-green">已结算</span>
                <?php else: ?>
                <span class="tag tag-red">已取消</span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['create_time']; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('admin/agent/commission') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('admin/agent/commission') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
