<div class="breadcrumb">营销 / 优惠券</div>
<div class="page-header">
    <h2>优惠券管理</h2>
    <div>
        <a href="<?php echo url('admin/coupon/stats'); ?>" class="btn btn-outline">统计报表</a>
        <a href="<?php echo url('admin/coupon/records'); ?>" class="btn btn-outline" style="margin-left: 8px;">领取记录</a>
        <a href="javascript:;" class="btn" style="margin-left: 8px;" onclick="openModal()">+ 新建优惠券</a>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/coupon'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="优惠券名称 / 券码">
        <select name="type" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部类型</option>
            <option value="1" <?php echo $type === '1' ? 'selected' : ''; ?>>满减</option>
            <option value="2" <?php echo $type === '2' ? 'selected' : ''; ?>>折扣</option>
            <option value="3" <?php echo $type === '3' ? 'selected' : ''; ?>>固定金额</option>
        </select>
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>启用</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>禁用</option>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>名称 / 券码</th>
            <th>类型</th>
            <th>优惠</th>
            <th>使用门槛</th>
            <th>发放 / 领取 / 使用</th>
            <th>有效期</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="9" style="text-align: center; color: #64748B; padding: 40px;">暂无优惠券</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td>
                <div style="font-weight: 500;"><?php echo h($item['name']); ?></div>
                <div style="color: #94A3B8; font-size: 12px;"><?php echo $item['code'] ? '券码：' . h($item['code']) : '领取券'; ?></div>
            </td>
            <td>
                <?php if ($item['type'] == 1): ?>
                <span class="tag tag-blue">满减</span>
                <?php elseif ($item['type'] == 2): ?>
                <span class="tag tag-orange">折扣</span>
                <?php else: ?>
                <span class="tag tag-green">固定金额</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($item['type'] == 2): ?>
                <?php echo ($item['amount'] * 100) . '%'; ?>
                <?php else: ?>
                ¥ <?php echo $item['amount']; ?>
                <?php endif; ?>
            </td>
            <td>满 ¥<?php echo $item['min_amount']; ?></td>
            <td>
                <?php echo $item['total_count'] ?: '∞'; ?> /
                <?php echo $item['receive_count']; ?> /
                <?php echo $item['used_count']; ?>
            </td>
            <td>
                <div style="font-size: 12px;"><?php echo $item['start_time'] ?: '不限'; ?></div>
                <div style="font-size: 12px; color: #94A3B8;">至 <?php echo $item['end_time'] ?: '不限'; ?></div>
            </td>
            <td>
                <?php if ($item['status'] == 1): ?>
                <span class="tag tag-green">启用</span>
                <?php else: ?>
                <span class="tag">禁用</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="javascript:;" class="btn btn-sm" onclick="openModal(<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>)">编辑</a>
                <?php if ($item['status'] == 1): ?>
                <a href="javascript:;" class="btn btn-sm btn-warning" onclick="toggleStatus(<?php echo $item['id']; ?>, 0)">禁用</a>
                <?php else: ?>
                <a href="javascript:;" class="btn btn-sm btn-success" onclick="toggleStatus(<?php echo $item['id']; ?>, 1)">启用</a>
                <?php endif; ?>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="deleteItem(<?php echo $item['id']; ?>, '<?php echo h($item['name']); ?>')">删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('admin/coupon') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status . '&type=' . $type; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('admin/coupon') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status . '&type=' . $type; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<div id="modal" class="card" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 560px; z-index: 200; max-height: 90vh; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3 id="modalTitle">新建优惠券</h3>
        <span style="cursor: pointer; font-size: 20px; color: #64748B;" onclick="closeModal()">×</span>
    </div>
    <form id="couponForm">
        <input type="hidden" name="id" id="editId" value="0">
        <div class="form-group">
            <label>优惠券名称</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div class="form-group">
            <label>券码（留空则为领取券）</label>
            <input type="text" name="code" id="code" placeholder="如：NEWUSER2024">
        </div>
        <div style="display: flex; gap: 12px;">
            <div class="form-group" style="flex: 1;">
                <label>类型</label>
                <select name="type" id="type" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;" onchange="toggleType()">
                    <option value="1">满减</option>
                    <option value="2">折扣</option>
                    <option value="3">固定金额</option>
                </select>
            </div>
            <div class="form-group" style="flex: 1;">
                <label id="amountLabel">优惠金额（元）</label>
                <input type="number" step="0.01" min="0" name="amount" id="amount" required>
            </div>
        </div>
        <div class="form-group">
            <label>最低使用金额（元）</label>
            <input type="number" step="0.01" min="0" name="min_amount" id="minAmount" value="0">
        </div>
        <div style="display: flex; gap: 12px;">
            <div class="form-group" style="flex: 1;">
                <label>发放总量（0为不限）</label>
                <input type="number" name="total_count" id="totalCount" min="0" value="0">
            </div>
            <div class="form-group" style="flex: 1;">
                <label>每人限领</label>
                <input type="number" name="limit_per_user" id="limitPerUser" min="1" value="1">
            </div>
        </div>
        <div style="display: flex; gap: 12px;">
            <div class="form-group" style="flex: 1;">
                <label>开始时间</label>
                <input type="datetime-local" name="start_time" id="startTime">
            </div>
            <div class="form-group" style="flex: 1;">
                <label>结束时间</label>
                <input type="datetime-local" name="end_time" id="endTime">
            </div>
        </div>
        <div style="display: flex; gap: 12px;">
            <div class="form-group" style="flex: 1;">
                <label>适用范围</label>
                <select name="scope" id="scope" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;" onchange="toggleScope()">
                    <option value="all">全部商品</option>
                    <option value="category">指定分类</option>
                    <option value="goods">指定商品</option>
                </select>
            </div>
            <div class="form-group" style="flex: 1;" id="scopeIdBox" style="display: none;">
                <label>范围ID</label>
                <input type="number" name="scope_id" id="scopeId" min="0" value="0" placeholder="分类ID / 商品ID">
            </div>
        </div>
        <div class="form-group">
            <label>状态</label>
            <select name="status" id="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;">
                <option value="1">启用</option>
                <option value="0">禁用</option>
            </select>
        </div>
        <button type="submit" class="btn" id="saveBtn">保存</button>
    </form>
</div>
<div id="modalOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4); z-index: 199;" onclick="closeModal()"></div>

<script>
function toggleType() {
    const type = document.getElementById('type').value;
    const label = document.getElementById('amountLabel');
    label.textContent = type == '2' ? '折扣率（0-1）' : (type == '3' ? '固定金额（元）' : '优惠金额（元）');
}

function toggleScope() {
    const scope = document.getElementById('scope').value;
    document.getElementById('scopeIdBox').style.display = scope == 'all' ? 'none' : 'block';
}

function openModal(item) {
    document.getElementById('modal').style.display = 'block';
    document.getElementById('modalOverlay').style.display = 'block';
    if (item) {
        document.getElementById('modalTitle').textContent = '编辑优惠券';
        document.getElementById('editId').value = item.id;
        document.getElementById('name').value = item.name;
        document.getElementById('code').value = item.code;
        document.getElementById('type').value = item.type;
        document.getElementById('amount').value = item.amount;
        document.getElementById('minAmount').value = item.min_amount;
        document.getElementById('totalCount').value = item.total_count;
        document.getElementById('limitPerUser').value = item.limit_per_user;
        document.getElementById('startTime').value = item.start_time ? item.start_time.replace(' ', 'T') : '';
        document.getElementById('endTime').value = item.end_time ? item.end_time.replace(' ', 'T') : '';
        document.getElementById('scope').value = item.scope;
        document.getElementById('scopeId').value = item.scope_id;
        document.getElementById('status').value = item.status;
    } else {
        document.getElementById('modalTitle').textContent = '新建优惠券';
        document.getElementById('couponForm').reset();
        document.getElementById('editId').value = '0';
    }
    toggleType();
    toggleScope();
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
    document.getElementById('modalOverlay').style.display = 'none';
}

document.getElementById('couponForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = '保存中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('admin/coupon/save'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '保存';
    }
});

async function toggleStatus(id, status) {
    if (!confirm(status == 1 ? '确认启用？' : '确认禁用？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('status', status);
    const res = await fetch('<?php echo url('admin/coupon/toggleStatus'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function deleteItem(id, name) {
    if (!confirm('确认删除优惠券「' + name + '」？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('admin/coupon/delete'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
