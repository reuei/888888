<div class="page-head">
    <h1 class="page-title">账户充值</h1>
</div>
<div class="balance-card">
    <div class="balance-label">当前余额</div>
    <div class="balance-num">¥<?= format_money($user['balance'] ?? 0) ?></div>
</div>
<div class="panel">
    <form id="rechargeForm" class="form">
        <div class="form-group">
            <label>充值金额</label>
            <div class="amount-grid">
                <label class="amount-item"><input type="radio" name="amount" value="10"><span>¥10</span></label>
                <label class="amount-item"><input type="radio" name="amount" value="50"><span>¥50</span></label>
                <label class="amount-item"><input type="radio" name="amount" value="100" checked><span>¥100</span></label>
                <label class="amount-item"><input type="radio" name="amount" value="200"><span>¥200</span></label>
                <label class="amount-item"><input type="radio" name="amount" value="500"><span>¥500</span></label>
                <label class="amount-item"><input type="radio" name="amount" value="1000"><span>¥1000</span></label>
            </div>
        </div>
        <div class="form-group">
            <label>支付方式</label>
            <div class="channel-grid">
                <label class="channel-item"><input type="radio" name="channel" value="alipay" checked><span>支付宝</span></label>
                <label class="channel-item"><input type="radio" name="channel" value="wxpay"><span>微信支付</span></label>
                <label class="channel-item"><input type="radio" name="channel" value="qqpay"><span>QQ钱包</span></label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">立即充值</button>
    </form>
</div>
