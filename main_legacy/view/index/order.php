<style>
.query-card {
    max-width: 520px;
    margin: 0 auto;
}
.order-detail {
    max-width: 720px;
    margin: 0 auto;
}
.order-status {
    text-align: center;
    padding: 24px;
    border-bottom: 1px solid #E2E8F0;
    margin-bottom: 20px;
}
.order-status .status-text { font-size: 20px; font-weight: 600; margin-bottom: 8px; }
.order-status .status-desc { color: #64748B; font-size: 13px; }
.deliver-box {
    background: #F0FDF4;
    border: 1px solid #BBF7D0;
    border-radius: 8px;
    padding: 16px;
    margin-top: 16px;
}
.deliver-box h4 { color: #166534; margin-bottom: 10px; }
.deliver-box pre {
    white-space: pre-wrap;
    word-break: break-all;
    background: #fff;
    padding: 12px;
    border-radius: 6px;
    font-family: monospace;
    font-size: 13px;
    color: #1F2937;
}
.info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #F1F5F9; }
.info-row:last-child { border-bottom: none; }
</style>

<?php if ($queryMode || !$order): ?>
<div class="card query-card">
    <div class="section-title" style="justify-content: center;">
        <span>查询订单</span>
    </div>
    <form id="queryForm">
        <div class="form-group">
            <label>订单号</label>
            <input type="text" name="order_no" placeholder="请输入订单号">
        </div>
        <div style="text-align: center; color: #94A3B8; margin-bottom: 12px;">— 或 —</div>
        <div class="form-group">
            <label>联系方式</label>
            <input type="text" name="contact" placeholder="下单时填写的联系方式">
        </div>
        <button type="submit" class="btn btn-block" id="queryBtn">查询</button>
    </form>
</div>
<?php else: ?>
<div class="card order-detail">
    <div class="order-status">
        <?php
        $statusMap = [
            0 => ['待支付', '请尽快完成支付'],
            1 => ['已支付', '正在为您发货'],
            2 => ['已发货', '请查收卡密/商品'],
            3 => ['已完成', '订单已完成'],
            4 => ['退款中', '订单正在退款处理'],
            5 => ['已关闭', '订单已关闭'],
        ];
        $statusInfo = $statusMap[$order['status']] ?? ['未知', ''];
        $statusColors = [0 => '#D97706', 1 => '#2563EB', 2 => '#059669', 3 => '#059669', 4 => '#EF4444', 5 => '#64748B'];
        ?>
        <div class="status-text" style="color: <?php echo $statusColors[$order['status']] ?? '#475569'; ?>"><?php echo $statusInfo[0]; ?></div>
        <div class="status-desc"><?php echo $statusInfo[1]; ?></div>
    </div>

    <?php if ($order['status'] == 0): ?>
    <div style="text-align: center; margin-bottom: 20px;">
        <a href="<?php echo url('index/pay', ['order_no' => $order['order_no']]); ?>" class="btn btn-lg">立即支付</a>
    </div>
    <?php endif; ?>

    <?php if ($order['status'] == 2 && $order['deliver_content']): ?>
    <div class="deliver-box">
        <h4>发货内容</h4>
        <pre id="deliverContent"><?php echo h($order['deliver_content']); ?></pre>
        <button type="button" class="btn btn-sm" onclick="copyDeliver()" style="margin-top: 10px;">复制卡密</button>
    </div>
    <?php endif; ?>

    <div style="margin-top: 20px;">
        <div class="info-row"><span>订单编号</span><span><?php echo h($order['order_no']); ?></span></div>
        <div class="info-row"><span>商品名称</span><span><?php echo h($order['goods_name']); ?></span></div>
        <div class="info-row"><span>单价</span><span>¥<?php echo $order['price']; ?></span></div>
        <div class="info-row"><span>数量</span><span><?php echo $order['quantity']; ?></span></div>
        <div class="info-row"><span>应付金额</span><span style="color:#EF4444; font-weight:600;">¥<?php echo $order['pay_amount']; ?></span></div>
        <div class="info-row"><span>支付方式</span><span><?php echo $order['pay_channel'] ?: '-'; ?></span></div>
        <div class="info-row"><span>支付时间</span><span><?php echo $order['pay_time'] ?: '-'; ?></span></div>
        <div class="info-row"><span>联系方式</span><span><?php echo h($order['contact']); ?></span></div>
        <div class="info-row"><span>下单时间</span><span><?php echo $order['create_time']; ?></span></div>
    </div>
</div>
<?php endif; ?>

<script>
document.getElementById('queryForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('queryBtn');
    btn.disabled = true;
    btn.textContent = '查询中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('index/queryOrder'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.code === 0 && data.data.redirect) {
            location.href = data.data.redirect;
        } else {
            alert(data.msg);
        }
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '查询';
    }
});

function copyDeliver() {
    const text = document.getElementById('deliverContent').innerText;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => alert('已复制'));
    } else {
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        alert('已复制');
    }
}
</script>
