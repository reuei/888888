<div class="screen-grid">
    <div class="screen-header">
        <div class="screen-time" id="screenTime">--:--:--</div>
        <h1 class="screen-title">玄武发卡 · 数据大屏</h1>
        <div class="screen-meta">授权 v1.1.1 · 系统 v1.0.5</div>
    </div>

    <div class="screen-row">
        <div class="screen-stat">
            <div class="screen-stat-label">总订单数</div>
            <div class="screen-stat-num"><?= number_format($stats['total_orders']) ?></div>
        </div>
        <div class="screen-stat">
            <div class="screen-stat-label">总营收</div>
            <div class="screen-stat-num">¥<?= number_format($stats['total_income']) ?></div>
        </div>
        <div class="screen-stat">
            <div class="screen-stat-label">注册用户</div>
            <div class="screen-stat-num"><?= number_format($stats['total_users']) ?></div>
        </div>
        <div class="screen-stat">
            <div class="screen-stat-label">店铺总数</div>
            <div class="screen-stat-num"><?= number_format($stats['total_shops']) ?></div>
        </div>
    </div>

    <div class="screen-row screen-row-3">
        <div class="screen-panel">
            <h3>订单分类</h3>
            <ul class="rank-list">
                <li><span class="rank-num">1</span><span>视频会员</span><span class="rank-value">¥38,560</span></li>
                <li><span class="rank-num">2</span><span>游戏点卡</span><span class="rank-value">¥24,820</span></li>
                <li><span class="rank-num">3</span><span>音乐会员</span><span class="rank-value">¥15,340</span></li>
                <li><span class="rank-num">4</span><span>软件激活</span><span class="rank-value">¥9,820</span></li>
                <li><span class="rank-num">5</span><span>学习教育</span><span class="rank-value">¥5,460</span></li>
            </ul>
        </div>
        <div class="screen-panel">
            <h3>地域分布</h3>
            <ul class="rank-list">
                <li><span class="rank-num">1</span><span>广东</span><span class="rank-value">2,580</span></li>
                <li><span class="rank-num">2</span><span>浙江</span><span class="rank-value">1,890</span></li>
                <li><span class="rank-num">3</span><span>江苏</span><span class="rank-value">1,650</span></li>
                <li><span class="rank-num">4</span><span>北京</span><span class="rank-value">1,420</span></li>
                <li><span class="rank-num">5</span><span>上海</span><span class="rank-value">1,380</span></li>
            </ul>
        </div>
        <div class="screen-panel">
            <h3>系统状态</h3>
            <ul class="status-list">
                <li><span class="status-dot ok"></span>授权服务：正常</li>
                <li><span class="status-dot ok"></span>数据库：连接正常</li>
                <li><span class="status-dot ok"></span>缓存服务：正常</li>
                <li><span class="status-dot ok"></span>支付通道：在线</li>
                <li><span class="status-dot warn"></span>短信通道：限流</li>
            </ul>
        </div>
    </div>
</div>

<script>
setInterval(function() {
    var el = document.getElementById('screenTime');
    if (el) el.textContent = new Date().toLocaleTimeString('zh-CN', {hour12: false});
}, 1000);
</script>
