<div class="page-header">
    <div>
        <h1 class="page-title">支付通道</h1>
        <div class="page-subtitle">配置和管理系统支持的支付通道。</div>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openChannelModal()">
            <svg width="14" height="14" style="vertical-align:middle;margin-right:4px;"><use href="#i-plus"/></svg>
            添加通道
        </button>
    </div>
</div>

<div class="admin-card">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>通道名称</th>
                    <th>代码</th>
                    <th>手续费率</th>
                    <th>状态</th>
                    <th>更新时间</th>
                    <th style="width:180px;">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($channels)): ?>
                    <?php foreach ($channels as $ch): ?>
                    <tr>
                        <td><?= $ch['id'] ?? '' ?></td>
                        <td style="font-weight:500;"><?= htmlspecialchars($ch['name'] ?? '') ?></td>
                        <td><span class="badge badge-info"><?= htmlspecialchars($ch['code'] ?? '') ?></span></td>
                        <td><?= number_format(($ch['fee_rate'] ?? 0) * 100, 2) ?>%</td>
                        <td>
                            <?php if (($ch['status'] ?? 0) == 1): ?>
                                <span class="badge badge-success"><span class="tag-dot"></span>启用</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><span class="tag-dot"></span>禁用</span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:12px;color:var(--text-muted);"><?= htmlspecialchars($ch['updated_at'] ?? '') ?></td>
                        <td>
                            <div class="admin-actions-cell">
                                <button class="btn btn-outline btn-sm" onclick="editChannel(<?= $ch['id'] ?? 0 ?>, '<?= htmlspecialchars($ch['name'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($ch['code'] ?? '', ENT_QUOTES) ?>', <?= $ch['fee_rate'] ?? 0 ?>, <?= $ch['status'] ?? 1 ?>)">
                                    <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-edit"/></svg>
                                    编辑
                                </button>
                                <form method="POST" action="/admin/deleteChannel" data-ajax="true" style="display:inline;" onsubmit="return confirm('确认删除该通道？');">
                                    <input type="hidden" name="id" value="<?= $ch['id'] ?? 0 ?>">
                                    <button type="submit" class="btn btn-sm" style="background:var(--danger);color:#fff;">
                                        <svg width="12" height="12" style="vertical-align:middle;"><use href="#i-trash"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><svg><use href="#i-orders"/></svg></div><div class="empty-text">暂无支付通道</div></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="admin-modal-overlay" id="channelModal">
    <div class="admin-modal">
        <div class="admin-modal-header">
            <h3 id="channelModalTitle">添加支付通道</h3>
            <button class="admin-modal-close" onclick="closeChannelModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="admin-modal-body">
            <form method="POST" action="/admin/addChannel" data-ajax="true" id="channelForm">
                <input type="hidden" name="id" id="ch_id">
                <div class="admin-form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">通道名称</label>
                        <input type="text" class="form-control" name="name" id="ch_name" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">通道代码</label>
                        <input type="text" class="form-control" name="code" id="ch_code" required>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">手续费率 (0.02 = 2%)</label>
                        <input type="number" class="form-control" name="fee_rate" id="ch_fee" step="0.01" min="0" value="0">
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">配置 JSON</label>
                        <textarea class="form-control" name="config" id="ch_config" rows="4" placeholder='{"app_id":"xxx","app_secret":"xxx"}'></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">状态</label>
                        <select class="form-control" name="status" id="ch_status">
                            <option value="1">启用</option>
                            <option value="0">禁用</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">保存</button>
            </form>
        </div>
    </div>
</div>

<script>
function openChannelModal(){
    document.getElementById('ch_id').value = '';
    document.getElementById('ch_name').value = '';
    document.getElementById('ch_code').value = '';
    document.getElementById('ch_fee').value = '0';
    document.getElementById('ch_config').value = '';
    document.getElementById('ch_status').value = '1';
    document.getElementById('channelModalTitle').textContent = '添加支付通道';
    document.getElementById('channelForm').action = '/admin/addChannel';
    document.getElementById('channelModal').classList.add('show');
}
function editChannel(id, name, code, fee, status){
    document.getElementById('ch_id').value = id;
    document.getElementById('ch_name').value = name;
    document.getElementById('ch_code').value = code;
    document.getElementById('ch_fee').value = fee;
    document.getElementById('ch_status').value = status;
    document.getElementById('channelModalTitle').textContent = '编辑支付通道';
    document.getElementById('channelForm').action = '/admin/editChannel';
    document.getElementById('channelModal').classList.add('show');
}
function closeChannelModal(){ document.getElementById('channelModal').classList.remove('show'); }
</script>