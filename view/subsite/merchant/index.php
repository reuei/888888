<div class="breadcrumb">商户管理 / 分站商户列表</div>
<div class="page-header">
    <h2>分站商户列表</h2>
    <div>
        <a href="<?php echo url('subsite/merchant/audit'); ?>" class="btn btn-outline">入驻审核</a>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('subsite/merchant'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="店铺名 / 店铺ID / 账号">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>正常</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>待审核</option>
            <option value="2" <?php echo $status === '2' ? 'selected' : ''; ?>>封禁</option>
            <option value="3" <?php echo $status === '3' ? 'selected' : ''; ?>>冻结</option>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>商户ID</th>
            <th>店铺信息</th>
            <th>余额 / 冻结</th>
            <th>入驻方式</th>
            <th>状态</th>
            <th>开店时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="7" style="text-align: center; color: #64748B; padding: 40px;">暂无商户数据</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td>
                <div style="font-weight: 500;"><?php echo h($item['shop_name']); ?></div>
                <div style="color: #94A3B8; font-size: 12px;"><?php echo h($item['shop_id']); ?></div>
            </td>
            <td>
                <div>¥ <?php echo $item['balance']; ?></div>
                <div style="color: #94A3B8; font-size: 12px;">冻结 ¥ <?php echo $item['frozen_balance']; ?></div>
            </td>
            <td><?php echo $item['invite_code_id'] > 0 ? '<span class="tag tag-blue">邀请码</span>' : '<span class="tag">自助注册</span>'; ?></td>
            <td>
                <?php if ($item['status'] == 1): ?>
                <span class="tag tag-green">正常</span>
                <?php elseif ($item['status'] == 0): ?>
                <span class="tag tag-orange">待审核</span>
                <?php elseif ($item['status'] == 2): ?>
                <span class="tag tag-red">封禁</span>
                <?php else: ?>
                <span class="tag tag-orange">冻结</span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['open_time'] ?: '-'; ?></td>
            <td>
                <a href="javascript:;" class="btn btn-sm" onclick="alert('详情页开发中')">详情</a>
                <div class="dropdown" style="display: inline-block; position: relative;">
                    <button type="button" class="btn btn-sm btn-outline" onclick="toggleMenu(this)">更多 ▾</button>
                    <div class="dropdown-menu" style="display: none; position: absolute; right: 0; top: 100%; background: #fff; border: 1px solid #E2E8F0; border-radius: 6px; min-width: 150px; z-index: 10;">
                        <?php if ($item['status'] == 2 || $item['status'] == 3): ?>
                        <a href="javascript:;" onclick="setStatus(<?php echo $item['id']; ?>, 1)">恢复正常</a>
                        <?php else: ?>
                        <a href="javascript:;" onclick="setStatus(<?php echo $item['id']; ?>, 2)">封禁</a>
                        <a href="javascript:;" onclick="setStatus(<?php echo $item['id']; ?>, 3)">冻结</a>
                        <?php endif; ?>
                        <a href="javascript:;" onclick="forceOffline(<?php echo $item['id']; ?>)">强制下线商品</a>
                        <a href="javascript:;" onclick="freezeFunds(<?php echo $item['id']; ?>, 'freeze')">冻结资金</a>
                        <a href="javascript:;" onclick="freezeFunds(<?php echo $item['id']; ?>, 'unfreeze')">解冻资金</a>
                    </div>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('subsite/merchant') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('subsite/merchant') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">下一页</a>
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
    color: #8B5CF6;
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

async function setStatus(id, status) {
    const labels = { 1: '恢复正常', 2: '封禁', 3: '冻结' };
    const remark = prompt('请输入备注/原因（可选）：') || '';
    if (!confirm('确认' + labels[status] + '该商户？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('status', status);
    form.append('remark', remark);
    const res = await fetch('<?php echo url('subsite/merchant/toggleStatus'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function forceOffline(id) {
    if (!confirm('确认强制下线该商户所有在售商品？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('subsite/merchant/forceOffline'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function freezeFunds(id, action) {
    const label = action === 'freeze' ? '冻结' : '解冻';
    const amount = prompt('请输入' + label + '金额：');
    if (!amount || isNaN(amount) || amount <= 0) return;
    if (!confirm('确认' + label + ' ¥' + amount + '？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('amount', amount);
    form.append('action', action);
    const res = await fetch('<?php echo url('subsite/merchant/freezeFunds'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
