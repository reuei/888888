<div class="breadcrumb">财务结算 / 结算打款</div>
<div class="page-header">
    <h2>结算打款</h2>
    <div>
        <a href="<?php echo url('admin/finance/flow'); ?>" class="btn btn-outline">资金流水</a>
        <a href="<?php echo url('admin/finance/rate'); ?>" class="btn" style="margin-left: 8px;">费率分组</a>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/finance/settle'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="结算单号 / 商户名">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <?php foreach ($statusMap as $k => $v): ?>
            <option value="<?php echo $k; ?>" <?php echo $status === (string)$k ? 'selected' : ''; ?>><?php echo $v; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>结算单号</th>
            <th>商户</th>
            <th>结算金额</th>
            <th>手续费</th>
            <th>实付金额</th>
            <th>打款渠道</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="9" style="text-align: center; color: #64748B; padding: 40px;">暂无结算记录</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo h($item['settle_no']); ?></td>
            <td><?php echo h($item['shop_name'] ?? '-'); ?></td>
            <td>¥ <?php echo $item['amount']; ?></td>
            <td>¥ <?php echo $item['fee']; ?></td>
            <td>¥ <?php echo $item['real_amount']; ?></td>
            <td><?php echo h($item['channel'] ?: '-'); ?></td>
            <td>
                <?php
                $statusColors = [0 => 'tag-orange', 1 => 'tag-blue', 2 => 'tag-green', 3 => 'tag-red'];
                $color = $statusColors[$item['status']] ?? 'tag';
                ?>
                <span class="tag <?php echo $color; ?>"><?php echo $statusMap[$item['status']] ?? '未知'; ?></span>
            </td>
            <td><?php echo $item['create_time']; ?></td>
            <td>
                <?php if ($item['status'] != 2): ?>
                <div class="dropdown" style="display: inline-block; position: relative;">
                    <button type="button" class="btn btn-sm btn-outline" onclick="toggleMenu(this)">更新状态 ▾</button>
                    <div class="dropdown-menu" style="display: none; position: absolute; right: 0; top: 100%; background: #fff; border: 1px solid #E2E8F0; border-radius: 6px; min-width: 120px; z-index: 10;">
                        <?php if ($item['status'] == 0): ?>
                        <a href="javascript:;" onclick="updateSettle(<?php echo $item['id']; ?>, 1)">标记处理中</a>
                        <?php endif; ?>
                        <a href="javascript:;" onclick="updateSettle(<?php echo $item['id']; ?>, 2)">标记成功</a>
                        <a href="javascript:;" onclick="updateSettle(<?php echo $item['id']; ?>, 3)">标记失败</a>
                    </div>
                </div>
                <?php else: ?>
                <span class="tag tag-green">已完成</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('admin/finance/settle') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('admin/finance/settle') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.dropdown-menu a {
    display: block;
    padding: 8px 12px;
    font-size: 13px;
    color: #475569;
    white-space: nowrap;
}
.dropdown-menu a:hover {
    background: #F1F5F9;
    color: #2563EB;
}
</style>

<script>
function toggleMenu(btn) {
    const menu = btn.nextElementSibling;
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

document.addEventListener('click', (e) => {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-menu').forEach(m => m.style.display = 'none');
    }
});

async function updateSettle(id, status) {
    const labels = { 1: '处理中', 2: '成功', 3: '失败' };
    const remark = prompt('请输入备注（可选）：') || '';
    if (!confirm('确认标记为' + labels[status] + '？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('status', status);
    form.append('remark', remark);
    const res = await fetch('<?php echo url('admin/finance/settleUpdate'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
