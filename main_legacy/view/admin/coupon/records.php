<div class="breadcrumb">营销 / 优惠券 / 领取记录</div>
<div class="page-header">
    <h2>优惠券领取记录</h2>
    <a href="<?php echo url('admin/coupon'); ?>" class="btn btn-outline">返回优惠券</a>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/coupon/records'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="用户昵称 / 手机号 / 优惠券名称">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>未使用</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>已使用</option>
            <option value="2" <?php echo $status === '2' ? 'selected' : ''; ?>>已过期</option>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>用户</th>
            <th>优惠券</th>
            <th>优惠内容</th>
            <th>状态</th>
            <th>领取时间</th>
            <th>过期时间</th>
            <th>使用时间</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="8" style="text-align: center; color: #64748B; padding: 40px;">暂无领取记录</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td>
                <div style="font-weight: 500;"><?php echo h($item['nickname'] ?? '-'); ?></div>
                <div style="color: #94A3B8; font-size: 12px;"><?php echo h($item['mobile'] ?? '-'); ?></div>
            </td>
            <td><?php echo h($item['coupon_name'] ?? '-'); ?></td>
            <td>
                <?php if ($item['type'] == 2): ?>
                折扣 <?php echo ($item['amount'] * 100) . '%'; ?>
                <?php else: ?>
                ¥ <?php echo $item['amount']; ?>
                <?php endif; ?>
                <div style="color: #94A3B8; font-size: 12px;">满 ¥<?php echo $item['min_amount']; ?></div>
            </td>
            <td>
                <?php if ($item['status'] == 0): ?>
                <span class="tag tag-blue">未使用</span>
                <?php elseif ($item['status'] == 1): ?>
                <span class="tag tag-green">已使用</span>
                <?php else: ?>
                <span class="tag">已过期</span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['create_time']; ?></td>
            <td><?php echo $item['expire_time'] ?: '-'; ?></td>
            <td><?php echo $item['use_time'] ?: '-'; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('admin/coupon/records') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('admin/coupon/records') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
