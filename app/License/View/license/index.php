<section class="license-hero">
    <div class="hero-inner">
        <div class="hero-text">
            <div class="hero-tag">授权系统 v1.1.1</div>
            <h1 class="hero-title">玄武授权中心</h1>
            <p class="hero-subtitle">为玄武发卡 v1.0.5 提供统一的授权管理与验证服务</p>
            <div class="hero-actions">
                <a href="#api-doc" class="btn btn-primary">API 文档</a>
                <a href="/license/admin" class="btn btn-line">进入后台</a>
            </div>
        </div>
        <div class="hero-stats">
            <div class="stat">
                <div class="stat-num"><?= number_format($stats['total_licenses']) ?></div>
                <div class="stat-label">总授权数</div>
            </div>
            <div class="stat">
                <div class="stat-num"><?= number_format($stats['active_licenses']) ?></div>
                <div class="stat-label">活跃授权</div>
            </div>
            <div class="stat">
                <div class="stat-num"><?= number_format($stats['total_domains']) ?></div>
                <div class="stat-label">绑定域名</div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="api-doc">
    <div class="section-head">
        <h2 class="section-title">API 接口</h2>
        <p class="section-sub">所有接口接受 POST 表单请求，返回 JSON</p>
    </div>
    <div class="api-grid">
        <div class="api-card">
            <div class="api-method">POST</div>
            <div class="api-path">/license/api/check</div>
            <div class="api-desc">检查授权码有效性，返回授权详情</div>
            <pre class="api-code">参数: license, domain, product, version</pre>
        </div>
        <div class="api-card">
            <div class="api-method">POST</div>
            <div class="api-path">/license/api/verify</div>
            <div class="api-desc">验证域名是否已绑定到授权</div>
            <pre class="api-code">参数: license, domain</pre>
        </div>
        <div class="api-card">
            <div class="api-method">POST</div>
            <div class="api-path">/license/api/activate</div>
            <div class="api-desc">激活并绑定新域名</div>
            <pre class="api-code">参数: license, domain</pre>
        </div>
        <div class="api-card">
            <div class="api-method">POST</div>
            <div class="api-path">/license/api/heartbeat</div>
            <div class="api-desc">客户端心跳上报</div>
            <pre class="api-code">参数: license, domain</pre>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="section-head">
        <h2 class="section-title">版本对应</h2>
    </div>
    <div class="version-table">
        <div class="vt-row vt-head">
            <div>客户端版本</div>
            <div>授权协议</div>
            <div>状态</div>
        </div>
        <div class="vt-row">
            <div>玄武发卡 v1.0.5</div>
            <div>v1.1.1</div>
            <div><span class="badge badge-success">当前</span></div>
        </div>
        <div class="vt-row">
            <div>玄武发卡 v1.0.4</div>
            <div>v1.1.0</div>
            <div><span class="badge">兼容</span></div>
        </div>
        <div class="vt-row">
            <div>玄武发卡 v1.0.3</div>
            <div>v1.0.x</div>
            <div><span class="badge">已停止</span></div>
        </div>
    </div>
</section>
