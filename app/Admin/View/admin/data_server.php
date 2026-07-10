<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">服务器信息</h3>
    </div>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">操作系统</div>
            <div class="info-value"><?= h($info['os']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">PHP版本</div>
            <div class="info-value"><?= h($info['php_version']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Web服务器</div>
            <div class="info-value"><?= h($info['server']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">内存限制</div>
            <div class="info-value"><?= h($info['memory_limit']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">最大执行时间</div>
            <div class="info-value"><?= h($info['max_execution_time']) ?>s</div>
        </div>
        <div class="info-item">
            <div class="info-label">上传文件大小</div>
            <div class="info-value"><?= h($info['upload_max']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">POST大小</div>
            <div class="info-value"><?= h($info['post_max']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">时区</div>
            <div class="info-value"><?= h($info['timezone']) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">当前时间</div>
            <div class="info-value"><?= h($info['current_time']) ?></div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">PHP扩展</h3>
    </div>
    <div class="ext-grid">
        <?php foreach ($info['extensions'] as $name => $loaded): ?>
        <div class="ext-item <?= $loaded ? 'ext-on' : 'ext-off' ?>">
            <span class="ext-dot"></span>
            <span class="ext-name"><?= h($name) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>
