<div class="breadcrumb">代理分销 / 佣金结算</div>
<div class="page-header">
    <h2>佣金结算</h2>
    <a href="javascript:;" class="btn" onclick="openModal()">+ 发起结算</a>
</div>

<div class="card" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 16px;">
    <div style="text-align: center;">
        <div style="font-size: 24px; font-weight: 600; color: #2563EB;"><?php echo $stats['total'] ?? 0; ?></div>
        <div style="color: #64748B; font-size: 13px;">结算单数</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 24px; font-weight: 600; color: #2563EB;">¥ <?php echo $stats['total_amount'] ?? '0.00'; ?></div>
        <div style="color: #64748B; font-size: 13px;">结算总额</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 24px; font-weight: 600; color: #D97706;">¥ <?php echo $stats['pending_amount'] ?? '0.00'; ?></div>
        <div style="color: #64748B; font-size: 13px;">待处理</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 24px; font-weight: 600; color: #059669;">¥ <?php echo $stats['paid_amount'] ?? '0.00'; ?></div>
        <div style="color: #64748B; font-size: 13px;">已成功</div>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/agent/settle'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="代理昵称 / 手机号 / 结算单号">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>待处理</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>处理中</option>
            <option value="2" <?php echo $status === '2' ? 'selected' : ''; ?>>成功</option>
            <option value="3" <?php echo $status === '3' ? 'selected' : ''; ?>>失败</option>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>结算单号</th>
            <th>代理</th>
            <th>结算金额</th>
            <th>手续费</th>
            <th>实际到账</th>
            <th>渠道 / 账号</th>
            <th>状态</th>
            <th>时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="9" style="text-align: center; color: #64748B; padding: 40px;">暂无结算记录</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo h($item['settle_no']); ?></td>
            <td>
                <div style="font-weight: 500;"><?php echo h($item['nickname'] ?? '-'); ?></div>
                <div style="color: #94A3B8; font-size: 12px;"><?php echo h($item['mobile'] ?? '-'); ?></div>
            </td>
            <td>¥ <?php echo $item['amount']; ?></td>
            <td>¥ <?php echo $item['fee']; ?></td>
            <td>¥ <?php echo $item['real_amount']; ?></td>
            <td>
                <div><?php echo h($item['channel'] ?: '-'); ?></div>
                <div style="color: #94A3B8; font-size: 12px;"><?php echo h($item['account'] ?: '-'); ?></div>
            </td>
            <td>
                <?php if ($item['status'] == 0): ?>
                <span class="tag tag-orange">待处理</span>
                <?php elseif ($item['status'] == 1): ?>
                <span class="tag tag-blue">处理中</span>
                <?php elseif ($item['status'] == 2): ?>
                <span class="tag tag-green">成功</span>
                <?php else: ?>
                <span class="tag tag-red">失败</span>
                <?php endif; ?>
            </td>
            <td>
                <div><?php echo $item['create_time']; ?></div>
                <?php if ($item['pay_time']): ?>
                <div style="color: #059669; font-size: 12px;">打款：<?php echo $item['pay_time']; ?></div>
                <?php endif; ?>
            </td>
            <td>
                <?php if (in_array((int) $item['status'], [0, 1, 3], true)): ?>
                <a href="javascript:;" class="btn btn-sm btn-success" onclick="settlePay(<?php echo $item['id']; ?>, 2)">打款成功</a>
                <?php endif; ?>
                <?php if ($item['status'] == 0): ?>
                <a href="javascript:;" class="btn btn-sm btn-warning" onclick="settlePay(<?php echo $item['id']; ?>, 1)">标记处理</a>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="settlePay(<?php echo $item['id']; ?>, 3)">驳回</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('admin/agent/settle') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('admin/agent/settle') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<div id="modal" class="card" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90%; max-width: 480px; z-index: 200; max-height: 90vh; overflow-y: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h3>发起结算</h3>
        <span style="cursor: pointer; font-size: 20px; color: #64748B;" onclick="closeModal()">×</span>
    </div>
    <form id="settleForm">
        <div class="form-group">
            <label>选择代理</label>
            <select name="user_id" id="userId" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px; width: 100%;" required>
                <option value="">请选择代理</option>
                <?php
                $agents = Db::query("SELECT au.user_id, u.nickname, u.mobile, au.pending_commission FROM jz_agent_user au LEFT JOIN jz_user u ON au.user_id = u.id WHERE au.status = 1 AND au.pending_commission > 0 ORDER BY au.pending_commission DESC LIMIT 200");
                foreach ($agents as $a):
                ?>
                <option value="<?php echo $a['user_id']; ?>"><?php echo h($a['nickname'] ?: '-'); ?> (<?php echo h($a['mobile'] ?: '-'); ?>) 待结算 ¥<?php echo $a['pending_commission']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>结算金额</label>
            <input type="number" step="0.01" min="0.01" name="amount" id="amount" required>
        </div>
        <div class="form-group">
            <label>结算渠道</label>
            <input type="text" name="channel" placeholder="如：支付宝 / 微信 / 银行卡">
        </div>
        <div class="form-group">
            <label>收款账号</label>
            <input type="text" name="account" placeholder="收款账号">
        </div>
        <div class="form-group">
            <label>备注</label>
            <input type="text" name="remark">
        </div>
        <button type="submit" class="btn" id="saveBtn">创建结算单</button>
    </form>
</div>
<div id="modalOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4); z-index: 199;" onclick="closeModal()"></div>

<script>
function openModal() {
    document.getElementById('modal').style.display = 'block';
    document.getElementById('modalOverlay').style.display = 'block';
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
    document.getElementById('modalOverlay').style.display = 'none';
}

document.getElementById('settleForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = '创建中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('admin/agent/settleCreate'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '创建结算单';
    }
});

async function settlePay(id, status) {
    const labels = { 1: '标记为处理中', 2: '确认打款成功', 3: '驳回该结算单' };
    if (!confirm(labels[status] + '？')) return;
    const remark = prompt('请输入备注（可选）：') || '';
    const form = new FormData();
    form.append('id', id);
    form.append('status', status);
    form.append('remark', remark);
    const res = await fetch('<?php echo url('admin/agent/settlePay'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
