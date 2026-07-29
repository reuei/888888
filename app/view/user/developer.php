<div class="user-breadcrumb">
    <span>用户中心</span> / <span>开发者选项</span>
</div>
<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 20px;">开发者选项</h1>

<?php if (($user['is_developer'] ?? 0) == 1): ?>
    <!-- Developer: Plugin Submission Form -->
    <div class="card" style="margin-bottom: 24px;">
        <div class="card-header">
            <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18"><use href="#i-plus"/></svg>提交插件
            </h3>
        </div>
        <form method="POST" action="/user/submitPlugin" data-ajax="true" id="pluginForm">
            <div class="form-group">
                <label class="form-label" for="plugin_name">插件名称</label>
                <input type="text" class="form-control" id="plugin_name" name="name" placeholder="请输入插件名称" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="plugin_desc">插件描述</label>
                <textarea class="form-control" id="plugin_desc" name="description" rows="3" placeholder="请输入插件描述" required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="plugin_price">价格 (¥)</label>
                <input type="number" class="form-control" id="plugin_price" name="price" step="0.01" min="0" placeholder="0 表示免费" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="plugin_file">插件文件</label>
                <input type="file" class="form-control" id="plugin_file" name="file" required>
            </div>
            <button type="submit" class="btn btn-primary">
                <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-plus"/></svg>提交插件
            </button>
        </form>
    </div>

    <!-- Developer's Existing Plugins -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18"><use href="#i-plugin"/></svg>我的插件
            </h3>
        </div>
        <?php if (!empty($myPlugins)): ?>
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>插件名称</th>
                        <th>描述</th>
                        <th>价格</th>
                        <th>状态</th>
                        <th>创建时间</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($myPlugins as $plugin): ?>
                    <tr>
                        <td><?= htmlspecialchars($plugin['name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($plugin['description'] ?? '') ?></td>
                        <td>
                            <?php if (($plugin['price'] ?? 0) == 0): ?>
                            <span class="badge badge-success">免费</span>
                            <?php else: ?>
                            ¥<?= number_format($plugin['price'] ?? 0, 2) ?>
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
                        <td><?= htmlspecialchars($plugin['created_at'] ?? '') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state" style="text-align: center; padding: 40px; color: #687690;">暂无插件</div>
        <?php endif; ?>
    </div>

<?php elseif ($devStatus === 'pending'): ?>
    <!-- Pending Application -->
    <div class="card" style="text-align: center; padding: 60px;">
        <svg width="48" height="48" style="color: #f59e0b; margin-bottom: 16px; display: block; margin-left: auto; margin-right: auto;"><use href="#i-clock"/></svg>
        <p style="font-size: 16px; color: #687690;">您的开发者申请正在审核中</p>
        <p style="font-size: 13px; color: #999; margin-top: 8px;">请耐心等待管理员审核</p>
    </div>

<?php else: ?>
    <!-- Not Applied -->
    <div class="card" style="text-align: center; padding: 60px;">
        <svg width="48" height="48" style="color: #c0c8d8; margin-bottom: 16px; display: block; margin-left: auto; margin-right: auto;"><use href="#i-dev"/></svg>
        <p style="font-size: 16px; color: #687690; margin-bottom: 20px;">您还未申请成为开发者</p>
        <form method="POST" action="/user/applyDeveloper" data-ajax="true">
            <button type="submit" class="btn btn-primary btn-lg">
                <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-dev"/></svg>申请开发者
            </button>
        </form>
    </div>
<?php endif; ?>