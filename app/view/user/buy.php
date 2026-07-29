<div class="user-breadcrumb">
    <span>用户中心</span> / <span>购买确认</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">购买确认</h1>

<!-- Product Info -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-box"/></svg>产品信息
        </h3>
    </div>
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <div style="font-size: 18px; font-weight: 600; color: var(--text); margin-bottom: 8px;"><?= htmlspecialchars($product['name'] ?? '') ?></div>
            <div style="font-size: 14px; color: var(--text-secondary);"><?= htmlspecialchars($product['description'] ?? '暂无描述') ?></div>
        </div>
        <div style="text-align: right; flex-shrink: 0;">
            <?php if (($product['price'] ?? 0) == 0): ?>
            <span style="font-size: 28px; font-weight: 800; color: #10b981;">免费</span>
            <?php else: ?>
            <span style="font-size: 28px; font-weight: 800; color: #e74c3c;">¥<?= number_format($product['price'] ?? 0, 2) ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- User Info -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-user"/></svg>账户信息
        </h3>
    </div>
    <div style="display: flex; gap: 40px;">
        <div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">邮箱</div>
            <div style="font-size: 14px; color: var(--text);"><?= htmlspecialchars($user['email'] ?? '') ?></div>
        </div>
        <div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px;">账户余额</div>
            <div style="font-size: 14px; color: var(--text); font-weight: 600;">¥<?= number_format($user['balance'] ?? 0, 2) ?></div>
        </div>
    </div>
</div>

<!-- Payment -->
<?php if (($product['price'] ?? 0) == 0): ?>
    <!-- Free Product -->
    <div class="card">
        <form method="POST" action="/user/confirmBuy" data-ajax="true">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?? 0 ?>">
            <button type="submit" class="btn btn-primary btn-lg btn-block">
                <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-check"/></svg>确认获取
            </button>
        </form>
    </div>
<?php else: ?>
    <!-- Paid Product -->
    <?php if (($user['balance'] ?? 0) >= ($product['price'] ?? 0)): ?>
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18"><use href="#i-wallet"/></svg>余额支付
            </h3>
        </div>
        <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 16px;">使用账户余额直接支付，无需选择支付渠道。</p>
        <form method="POST" action="/user/confirmBuy" data-ajax="true">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?? 0 ?>">
            <input type="hidden" name="payment_method" value="balance">
            <button type="submit" class="btn btn-primary btn-lg btn-block">
                立即支付 ¥<?= number_format($product['price'] ?? 0, 2) ?>
            </button>
        </form>
    </div>
    <?php else: ?>
    <div class="card" style="margin-bottom: 24px; background: #fff2f0; border-color: #ffccc7;">
        <p style="color: #cf1322; font-size: 14px;">余额不足，当前余额 ¥<?= number_format($user['balance'] ?? 0, 2) ?>，还需 ¥<?= number_format(($product['price'] ?? 0) - ($user['balance'] ?? 0), 2) ?>。请选择其他支付方式。</p>
    </div>
    <?php endif; ?>

    <!-- Payment Channels -->
    <?php if (!empty($paymentChannels)): ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18"><use href="#i-wallet"/></svg>选择支付方式
            </h3>
        </div>
        <form method="POST" action="/user/confirmBuy" data-ajax="true" id="payForm">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?? 0 ?>">
            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                <?php foreach ($paymentChannels as $channel): ?>
                <label style="display: flex; align-items: center; gap: 12px; padding: 14px 16px; border: 1px solid var(--border); cursor: pointer; transition: border-color var(--transition-fast);" class="payment-channel-option">
                    <input type="radio" name="payment_method" value="<?= $channel['id'] ?? 0 ?>" style="accent-color: var(--primary);" <?= ($channel === reset($paymentChannels)) ? 'checked' : '' ?>>
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 500; color: var(--text);"><?= htmlspecialchars($channel['name'] ?? '') ?></div>
                        <?php if (!empty($channel['description'])): ?>
                        <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;"><?= htmlspecialchars($channel['description']) ?></div>
                        <?php endif; ?>
                    </div>
                </label>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block">
                立即支付 ¥<?= number_format($product['price'] ?? 0, 2) ?>
            </button>
        </form>
    </div>
    <?php endif; ?>
<?php endif; ?>