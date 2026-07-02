<div class="breadcrumb">资金管理 / 资金概览</div>
<div class="page-header">
    <h2>资金概览</h2>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 20px;">
    <div class="card" style="text-align: center;">
        <div style="font-size: 13px; color: #64748B; margin-bottom: 6px;">可用余额</div>
        <div style="font-size: 24px; font-weight: 600; color: #10B981;">¥<?php echo number_format($merchant['balance'] ?? 0, 2); ?></div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 13px; color: #64748B; margin-bottom: 6px;">冻结余额</div>
        <div style="font-size: 24px; font-weight: 600; color: #F59E0B;">¥<?php echo number_format($merchant['frozen_balance'] ?? 0, 2); ?></div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 13px; color: #64748B; margin-bottom: 6px;">累计收入</div>
        <div style="font-size: 24px; font-weight: 600; color: #2563EB;">¥<?php echo number_format($stat['total_income'] ?? 0, 2); ?></div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 13px; color: #64748B; margin-bottom: 6px;">累计手续费</div>
        <div style="font-size: 24px; font-weight: 600; color: #EF4444;">¥<?php echo number_format($stat['total_fee'] ?? 0, 2); ?></div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 13px; color: #64748B; margin-bottom: 6px;">累计提现</div>
        <div style="font-size: 24px; font-weight: 600; color: #475569;">¥<?php echo number_format($stat['total_settle'] ?? 0, 2); ?></div>
    </div>
    <div class="card" style="text-align: center;">
        <div style="font-size: 13px; color: #64748B; margin-bottom: 6px;">待处理提现</div>
        <div style="font-size: 24px; font-weight: 600; color: #D97706;">¥<?php echo number_format($pendingSettle['total'] ?? 0, 2); ?></div>
    </div>
</div>

<div class="card" style="max-width: 640px;">
    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">申请提现</h3>
    <form id="withdrawForm">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px;">
            <div class="form-group">
                <label>提现金额</label>
                <input type="number" name="amount" step="0.01" min="0.01" placeholder="请输入提现金额" required>
            </div>
            <div class="form-group">
                <label>结算渠道</label>
                <select name="channel" required>
                    <option value="">请选择</option>
                    <option value="alipay">支付宝</option>
                    <option value="wxpay">微信支付</option>
                    <option value="bank">银行卡</option>
                    <option value="usdt">USDT</option>
                </select>
            </div>
            <div class="form-group">
                <label>收款账号</label>
                <input type="text" name="account" placeholder="账号 / 卡号 / 地址" required>
            </div>
            <div class="form-group">
                <label>收款人姓名</label>
                <input type="text" name="account_name" placeholder="真实姓名（可选）">
            </div>
        </div>
        <button type="submit" class="btn" id="withdrawBtn">提交提现申请</button>
    </form>
</div>

<div class="card">
    <div class="page-header" style="margin-bottom: 12px;">
        <h3 style="font-size: 16px; font-weight: 600;">最近资金流水</h3>
        <a href="<?php echo url('merchant/finance/flow'); ?>" class="btn btn-sm btn-outline">查看全部</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>时间</th>
                <th>类型</th>
                <th>金额</th>
                <th>余额</th>
                <th>备注</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recentFlow)): ?>
            <tr>
                <td colspan="5" style="text-align: center; color: #64748B;">暂无流水记录</td>
            </tr>
            <?php else: ?>
            <?php foreach ($recentFlow as $item): ?>
            <tr>
                <td><?php echo h($item['create_time']); ?></td>
                <td>
                    <span class="tag <?php echo $item['type'] === 'income' ? 'tag-green' : ($item['type'] === 'fee' ? 'tag-red' : ($item['type'] === 'settle' ? 'tag-orange' : 'tag-blue')); ?>">
                        <?php echo h($typeMap[$item['type']] ?? $item['type']); ?>
                    </span>
                </td>
                <td>¥<?php echo number_format($item['amount'], 2); ?></td>
                <td>¥<?php echo number_format($item['balance'], 2); ?></td>
                <td><?php echo h($item['remark']); ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('withdrawForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('withdrawBtn');
    btn.disabled = true;
    btn.textContent = '提交中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('merchant/finance/applyWithdraw'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '提交提现申请';
    }
});
</script>
