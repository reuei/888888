<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">数据库状态</h3>
    </div>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">驱动</div>
            <div class="info-value"><?= h($info['driver']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">版本</div>
            <div class="info-value"><?= h($info['version']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">字符集</div>
            <div class="info-value"><?= h($info['charset']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">主机</div>
            <div class="info-value"><?= h($info['host']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">数据库</div>
            <div class="info-value"><?= h($info['database']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">表数量</div>
            <div class="info-value"><?= h($info['table_count']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">数据大小</div>
            <div class="info-value"><?= h($info['total_size']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">慢查询</div>
            <div class="info-value"><?= h($info['slow_queries']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">连接数</div>
            <div class="info-value"><?= h($info['connections']) ?> / <?= h($info['max_connections']) ?></div>
        </div>
    </div>
</div>
