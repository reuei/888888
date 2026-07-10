<div class="stat-grid stat-grid-4">
    <div class="stat-card stat-card-primary">
        <div class="stat-card-body">
            <div class="stat-card-label">总授权数</div>
            <div class="stat-card-num"><?= number_format($stats['total']) ?></div>
        </div>
    </div>
    <div class="stat-card stat-card-success">
        <div class="stat-card-body">
            <div class="stat-card-label">活跃授权</div>
            <div class="stat-card-num"><?= number_format($stats['active']) ?></div>
        </div>
    </div>
    <div class="stat-card stat-card-warning">
        <div class="stat-card-body">
            <div class="stat-card-label">已过期</div>
            <div class="stat-card-num"><?= number_format($stats['expired']) ?></div>
        </div>
    </div>
    <div class="stat-card stat-card-info">
        <div class="stat-card-body">
            <div class="stat-card-label">今日新增</div>
            <div class="stat-card-num"><?= number_format($stats['today']) ?></div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">系统信息</h3>
    </div>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">授权站版本</div>
            <div class="info-value">v1.1.1</div>
        </div>
        <div class="info-item">
            <div class="info-label">兼容客户端</div>
            <div class="info-value">v1.0.3 - v1.0.5</div>
        </div>
        <div class="info-item">
            <div class="info-label">API 端点</div>
            <div class="info-value">/license/api/*</div>
        </div>
        <div class="info-item">
            <div class="info-label">签名算法</div>
            <div class="info-value">HMAC-SHA256</div>
        </div>
    </div>
</div>
