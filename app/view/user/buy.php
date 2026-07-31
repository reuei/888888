<div style="display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start;">
    <div>
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3 class="card-title">
                    <svg width="18" height="18" style="margin-right: 6px; color: var(--primary);"><use href="#i-box"/></svg>
                    产品信息
                </h3>
            </div>
            <div style="display: flex; gap: 20px; align-items: flex-start;">
                <div style="width: 80px; height: 80px; border-radius: 20px; background: var(--primary-gradient); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="40" height="40"><use href="#i-box"/></svg>
                </div>
                <div style="flex: 1;">
                    <h3 style="font-size: 20px; font-weight: 600; color: var(--text); margin: 0 0 8px;"><?= htmlspecialchars($product['name'] ?? '') ?></h3>
                    <p style="font-size: 14px; color: var(--text-secondary); margin: 0; line-height: 1.6;"><?= htmlspecialchars($product['description'] ?? '暂无描述') ?></p>
                </div>
            </div>
            <?php if (!empty($product['features'])): ?>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-light);">
                <div style="font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 12px;">产品特性</div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
                    <?php foreach ($product['features'] as $feature): ?>
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text-secondary);">
                        <svg width="14" height="14" style="color: var(--success);"><use href="#i-check"/></svg>
                        <?= htmlspecialchars($feature) ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3 class="card-title">
                    <svg width="18" height="18" style="margin-right: 6px; color: var(--primary);"><use href="#i-user"/></svg>
                    账户信息
                </h3>
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                <div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">邮箱</div>
                    <div style="font-size: 14px; color: var(--text); font-weight: 500;"><?= htmlspecialchars($user['email'] ?? '') ?></div>
                </div>
                <div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">账户余额</div>
                    <div style="font-size: 16px; color: var(--primary); font-weight: 600;">¥<?= number_format($user['balance'] ?? 0, 2) ?></div>
                </div>
                <div>
                    <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 4px;">用户名</div>
                    <div style="font-size: 14px; color: var(--text); font-weight: 500;"><?= htmlspecialchars($user['username'] ?? '') ?></div>
                </div>
            </div>
        </div>

        <?php if (($product['price'] ?? 0) == 0): ?>
        <div class="card" style="text-align: center; padding: 40px;">
            <div style="width: 64px; height: 64px; background: var(--success-light); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <svg width="28" height="28" style="color: var(--success);"><use href="#i-check"/></svg>
            </div>
            <h3 style="font-size: 18px; font-weight: 600; color: var(--text); margin-bottom: 8px;">免费产品</h3>
            <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 20px;">您即将获取一个免费产品</p>
            <form method="POST" action="/user/confirmBuy" data-ajax="true">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?? 0 ?>">
                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    <svg width="16" height="16" style="margin-right: 6px;"><use href="#i-check"/></svg>
                    确认获取
                </button>
            </form>
        </div>

        <?php else: ?>
        <?php if (($user['balance'] ?? 0) >= ($product['price'] ?? 0)): ?>
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h3 class="card-title">
                    <svg width="18" height="18" style="margin-right: 6px; color: var(--primary);"><use href="#i-wallet"/></svg>
                    余额支付
                </h3>
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: var(--primary-50); border-radius: var(--radius); margin-bottom: 20px;">
                <div>
                    <div style="font-size: 13px; color: var(--text-secondary);">使用余额支付</div>
                    <div style="font-size: 20px; font-weight: 600; color: var(--primary);">¥<?= number_format($product['price'] ?? 0, 2) ?></div>
                </div>
                <div style="font-size: 13px; color: var(--text-secondary);">
                    支付后余额：¥<?= number_format(($user['balance'] ?? 0) - ($product['price'] ?? 0), 2) ?>
                </div>
            </div>
            <form method="POST" action="/user/confirmBuy" data-ajax="true">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?? 0 ?>">
                <input type="hidden" name="payment_method" value="balance">
                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    立即支付 ¥<?= number_format($product['price'] ?? 0, 2) ?>
                </button>
            </form>
        </div>
        <?php else: ?>
        <div class="card" style="margin-bottom: 24px; background: var(--danger-light); border-color: var(--danger-light);">
            <div style="display: flex; align-items: center; gap: 12px; color: var(--danger);">
                <svg width="20" height="20"><use href="#i-warn"/></svg>
                <span style="font-size: 14px; font-weight: 500;">余额不足</span>
            </div>
            <p style="font-size: 13px; color: var(--text-secondary); margin-top: 8px; margin-bottom: 0;">
                当前余额 ¥<?= number_format($user['balance'] ?? 0, 2) ?>，还需 ¥<?= number_format(($product['price'] ?? 0) - ($user['balance'] ?? 0), 2) ?>
            </p>
        </div>
        <?php endif; ?>

        <?php if (!empty($paymentChannels)): ?>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <svg width="18" height="18" style="margin-right: 6px; color: var(--primary);"><use href="#i-credit"/></svg>
                    选择支付方式
                </h3>
            </div>
            <form method="POST" action="/user/confirmBuy" data-ajax="true" id="payForm">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?? 0 ?>">
                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                    <?php foreach ($paymentChannels as $key => $channel): ?>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="<?= $channel['id'] ?? $key ?>" style="accent-color: var(--primary);" <?= $key === 0 ? 'checked' : '' ?>>
                        <div class="payment-option-info">
                            <div class="payment-option-name"><?= htmlspecialchars($channel['name'] ?? '') ?></div>
                            <?php if (!empty($channel['description'])): ?>
                            <div class="payment-option-desc"><?= htmlspecialchars($channel['description']) ?></div>
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
    </div>

    <div>
        <div class="buy-summary-card">
            <div style="font-size: 13px; opacity: 0.9; margin-bottom: 8px;">订单摘要</div>
            <div class="buy-summary-product"><?= htmlspecialchars($product['name'] ?? '') ?></div>
            <div class="buy-summary-price">
                <?php if (($product['price'] ?? 0) == 0): ?>
                    免费
                <?php else: ?>
                    ¥<?= number_format($product['price'] ?? 0, 2) ?>
                <?php endif; ?>
            </div>
            <div class="buy-summary-user">
                <div class="buy-summary-user-item">
                    <div class="label">账户</div>
                    <div class="value"><?= htmlspecialchars($user['username'] ?? '') ?></div>
                </div>
                <div class="buy-summary-user-item">
                    <div class="label">余额</div>
                    <div class="value">¥<?= number_format($user['balance'] ?? 0, 2) ?></div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-top: 16px;">
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 12px; color: var(--text-muted);">
                    <svg width="14" height="14" style="flex-shrink: 0; margin-top: 1px;"><use href="#i-check"/></svg>
                    <span>购买后可随时在"我的产品"中下载</span>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 12px; color: var(--text-muted);">
                    <svg width="14" height="14" style="flex-shrink: 0; margin-top: 1px;"><use href="#i-check"/></svg>
                    <span>支持7天无理由退款</span>
                </div>
                <div style="display: flex; align-items: flex-start; gap: 8px; font-size: 12px; color: var(--text-muted);">
                    <svg width="14" height="14" style="flex-shrink: 0; margin-top: 1px;"><use href="#i-check"/></svg>
                    <span>终身免费更新</span>
                </div>
            </div>
        </div>
    </div>
</div>
