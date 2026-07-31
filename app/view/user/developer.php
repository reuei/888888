<?php if (($user['is_developer'] ?? 0) == 1): ?>
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="18" height="18" style="margin-right: 6px; color: var(--primary);"><use href="#i-plus"/></svg>
            提交插件
        </h3>
    </div>
    <form method="POST" action="/user/submitPlugin" data-ajax="true" id="pluginForm">
        <div class="two-col">
            <div class="form-group">
                <label class="form-label" for="plugin_name">插件名称</label>
                <input type="text" class="form-control" id="plugin_name" name="name" placeholder="请输入插件名称" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="plugin_price">价格 (¥)</label>
                <input type="number" class="form-control" id="plugin_price" name="price" step="0.01" min="0" placeholder="0 表示免费" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="plugin_desc">插件描述</label>
            <textarea class="form-control" id="plugin_desc" name="description" rows="3" placeholder="请输入插件描述" required></textarea>
        </div>
        <div class="form-group">
            <label class="form-label" for="plugin_file">插件文件</label>
            <input type="file" class="form-control" id="plugin_file" name="file" required>
            <div class="form-help">支持 .zip, .tar.gz 格式，最大 50MB</div>
        </div>
        <button type="submit" class="btn btn-primary">
            <svg width="14" height="14" style="margin-right: 4px;"><use href="#i-plus"/></svg>
            提交插件审核
        </button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <svg width="18" height="18" style="margin-right: 6px; color: var(--primary);"><use href="#i-plugin"/></svg>
            我的插件
        </h3>
    </div>
    <?php if (!empty($myPlugins)): ?>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>插件名称</th>
                    <th>描述</th>
                    <th>价格</th>
                    <th>状态</th>
                    <th>创建时间</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($myPlugins as $plugin): ?>
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 28px; height: 28px; border-radius: 6px; background: var(--primary-50); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                                <svg width="14" height="14"><use href="#i-plugin"/></svg>
                            </div>
                            <span><?= htmlspecialchars($plugin['name'] ?? '') ?></span>
                        </div>
                    </td>
                    <td style="color: var(--text-secondary); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($plugin['description'] ?? '') ?></td>
                    <td>
                        <?php if (($plugin['price'] ?? 0) == 0): ?>
                        <span class="badge badge-success">免费</span>
                        <?php else: ?>
                        <span style="color: var(--danger); font-weight: 600;">¥<?= number_format($plugin['price'] ?? 0, 2) ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php $status = $plugin['status'] ?? 0; ?>
                        <?php if ($status == 1): ?>
                        <span class="badge badge-success">已审核</span>
                        <?php elseif ($status == 2): ?>
                        <span class="badge badge-danger">已拒绝</span>
                        <?php else: ?>
                        <span class="badge badge-warning">待审核</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size: 13px;"><?= htmlspecialchars($plugin['created_at'] ?? '') ?></td>
                    <td>
                        <a href="/user/editPlugin?id=<?= $plugin['id'] ?? 0 ?>" class="btn btn-outline btn-sm">编辑</a>
                        <button class="btn btn-danger btn-sm" onclick="deletePlugin(<?= $plugin['id'] ?? 0 ?>)">删除</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon">
            <svg width="24" height="24"><use href="#i-plugin"/></svg>
        </div>
        <div class="empty-state-text">暂无插件</div>
        <div style="font-size: 13px; color: var(--text-muted);">提交您的第一个插件吧！</div>
    </div>
    <?php endif; ?>
</div>

<?php elseif ($devStatus === 'pending'): ?>
<div class="card" style="text-align: center; padding: 60px;">
    <div style="width: 64px; height: 64px; background: var(--warning-light); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
        <svg width="28" height="28" style="color: var(--warning);"><use href="#i-clock"/></svg>
    </div>
    <h3 style="font-size: 18px; font-weight: 600; color: var(--text); margin-bottom: 8px;">开发者申请审核中</h3>
    <p style="font-size: 14px; color: var(--text-secondary);">您的开发者申请正在审核中，请耐心等待管理员审核</p>
    <p style="font-size: 13px; color: var(--text-muted); margin-top: 16px;">审核通过后将自动开启开发者功能</p>
</div>

<?php else: ?>
<div class="card" style="text-align: center; padding: 60px;">
    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #7c3aed, #4f8cff); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
        <svg width="36" height="36" style="color: #fff;"><use href="#i-dev"/></svg>
    </div>
    <h3 style="font-size: 20px; font-weight: 600; color: var(--text); margin-bottom: 8px;">成为开发者</h3>
    <p style="font-size: 14px; color: var(--text-secondary); margin-bottom: 24px; max-width: 400px; margin-left: auto; margin-right: auto;">
        成为开发者后，您可以提交自己的插件到插件市场，获得更多收益
    </p>
    <form method="POST" action="/user/applyDeveloper" data-ajax="true">
        <button type="submit" class="btn btn-primary btn-lg">
            <svg width="16" height="16" style="margin-right: 6px;"><use href="#i-dev"/></svg>
            申请开发者权限
        </button>
    </form>
</div>
<?php endif; ?>

<script>
    function deletePlugin(id) {
        if (confirm('确定要删除这个插件吗？此操作不可撤销。')) {
            fetch('/user/deletePlugin', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            }).then(function(res) { return res.json(); })
              .then(function(data) {
                  if (data.success) {
                      location.reload();
                  } else {
                      alert(data.message || '删除失败');
                  }
              })
              .catch(function() { alert('删除失败'); });
        }
    }
</script>
