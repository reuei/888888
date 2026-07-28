<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 24px;">控制台</h1>

<div class="stat-grid">
    <div class="stat-card">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #4f8cff, #3868ff); display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" style="color:#fff;"><use href="#i-user"/></svg>
            </div>
            <div>
                <div class="stat-value"><?= $stats['users'] ?? 0 ?></div>
                <div class="stat-label">注册用户</div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #52c41a, #389e0d); display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" style="color:#fff;"><use href="#i-box"/></svg>
            </div>
            <div>
                <div class="stat-value"><?= $stats['products'] ?? 0 ?></div>
                <div class="stat-label">产品数量</div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #fa8c16, #d46b08); display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" style="color:#fff;"><use href="#i-key"/></svg>
            </div>
            <div>
                <div class="stat-value"><?= $stats['licenses'] ?? 0 ?></div>
                <div class="stat-label">授权总数</div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #722ed1, #531dab); display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" style="color:#fff;"><use href="#i-orders"/></svg>
            </div>
            <div>
                <div class="stat-value"><?= $stats['orders'] ?? 0 ?></div>
                <div class="stat-label">订单总数</div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #eb2f96, #c41d7f); display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" style="color:#fff;"><use href="#i-wallet"/></svg>
            </div>
            <div>
                <div class="stat-value">¥<?= number_format($stats['revenue'] ?? 0, 2) ?></div>
                <div class="stat-label">总收入</div>
            </div>
        </div>
    </div>
</div>