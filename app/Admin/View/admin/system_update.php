<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">系统更新</h3>
    </div>
    <div class="update-info">
        <div class="update-row">
            <span class="update-label">当前版本</span>
            <span class="update-value">v<?= h($info['current_version']) ?></span>
        </div>
        <div class="update-row">
            <span class="update-label">最新版本</span>
            <span class="update-value">v<?= h($info['latest_version']) ?></span>
        </div>
        <div class="update-row">
            <span class="update-label">授权版本</span>
            <span class="update-value">v<?= h($info['license_version']) ?></span>
        </div>
        <div class="update-row">
            <span class="update-label">检测时间</span>
            <span class="update-value"><?= h($info['update_time']) ?></span>
        </div>
        <div class="update-actions">
            <button class="btn btn-primary" id="checkUpdateBtn">检查更新</button>
            <button class="btn btn-line" id="applyUpdateBtn">立即更新</button>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">更新日志</h3>
    </div>
    <ul class="changelog">
        <?php foreach ($info['changelog'] as $log): ?>
        <li><?= h($log) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
