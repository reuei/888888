<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 24px;">支付通道</h1>

<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-credit-card"/></svg>通道列表
        </h3>
        <button class="btn btn-primary" onclick="openAddChannelModal()">
            <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-plus"/></svg>添加通道
        </button>
    </div>
    <?php if (!empty($channels)): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 16px; margin-top: 8px;">
        <?php foreach ($channels as $ch): ?>
        <div class="card" style="margin-bottom: 0;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <h4 style="font-size: 15px; font-weight: 600; color: var(--text);"><?= htmlspecialchars($ch['name'] ?? '') ?></h4>
                <span class="badge <?= ($ch['status'] ?? 1) == 1 ? 'badge-success' : 'badge-danger' ?>">
                    <?= ($ch['status'] ?? 1) == 1 ? '启用' : '禁用' ?>
                </span>
            </div>
            <div style="font-size: 13px; color: var(--text-secondary); line-height: 1.8;">
                <div>类型：<span class="badge badge-info"><?= htmlspecialchars($ch['type'] ?? '易支付') ?></span></div>
                <div>费率：<?= htmlspecialchars($ch['rate'] ?? '0') ?>%</div>
                <div>创建时间：<?= htmlspecialchars($ch['created_at'] ?? '') ?></div>
            </div>
            <div style="margin-top: 12px; display: flex; gap: 8px;">
                <a href="javascript:void(0)" class="btn btn-primary btn-sm" onclick="openEditChannelModal(<?= $ch['id'] ?? 0 ?>, '<?= htmlspecialchars($ch['name'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($ch['type'] ?? '易支付', ENT_QUOTES) ?>', '<?= htmlspecialchars($ch['merchant_id'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($ch['secret_key'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($ch['gateway_url'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($ch['rate'] ?? '0', ENT_QUOTES) ?>')">
                    <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-edit"/></svg> 编辑
                </a>
                <form method="POST" action="/admin/deletePaymentChannel" data-ajax="true" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $ch['id'] ?? 0 ?>">
                    <button type="submit" class="btn btn-sm" style="background: #ff4d4f; color: #fff;" onclick="return confirm('确认删除该支付通道?')">
                        <svg width="12" height="12" style="vertical-align: middle;"><use href="#i-trash"/></svg> 删除
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state" style="text-align: center; padding: 60px; color: #687690;">暂无支付通道数据</div>
    <?php endif; ?>
</div>

<!-- Add/Edit Channel Modal -->
<div class="announcement-modal" id="channelModal">
    <div class="am-overlay" onclick="closeChannelModal()"></div>
    <div class="am-dialog" style="max-width: 500px;">
        <div class="am-header">
            <h3 id="channelModalTitle">添加通道</h3>
            <button class="am-close" onclick="closeChannelModal()"><svg width="18" height="18"><use href="#i-close"/></svg></button>
        </div>
        <div class="am-body">
            <form method="POST" action="/admin/addPaymentChannel" data-ajax="true" id="channelForm">
                <input type="hidden" name="id" id="channel_id">
                <div class="form-group">
                    <label class="form-label" for="channel_name">通道名称</label>
                    <input type="text" class="form-control" id="channel_name" name="name" placeholder="请输入通道名称" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="channel_type">通道类型</label>
                    <select class="form-control" id="channel_type" name="type">
                        <option value="易支付">易支付</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="channel_merchant_id">商户ID</label>
                    <input type="text" class="form-control" id="channel_merchant_id" name="merchant_id" placeholder="请输入商户ID" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="channel_secret_key">密钥</label>
                    <input type="text" class="form-control" id="channel_secret_key" name="secret_key" placeholder="请输入密钥" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="channel_gateway_url">网关地址</label>
                    <input type="url" class="form-control" id="channel_gateway_url" name="gateway_url" placeholder="https://pay.example.com/submit.php" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="channel_rate">费率 (%)</label>
                    <input type="number" class="form-control" id="channel_rate" name="rate" step="0.01" min="0" value="0" placeholder="0" style="max-width: 150px;">
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <span id="channelSubmitText">添加通道</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openAddChannelModal() {
    document.getElementById('channel_id').value = '';
    document.getElementById('channel_name').value = '';
    document.getElementById('channel_type').value = '易支付';
    document.getElementById('channel_merchant_id').value = '';
    document.getElementById('channel_secret_key').value = '';
    document.getElementById('channel_gateway_url').value = '';
    document.getElementById('channel_rate').value = '0';
    document.getElementById('channelModalTitle').textContent = '添加通道';
    document.getElementById('channelSubmitText').textContent = '添加通道';
    document.getElementById('channelForm').action = '/admin/addPaymentChannel';
    document.getElementById('channelModal').classList.add('show');
}

function openEditChannelModal(id, name, type, merchantId, secretKey, gatewayUrl, rate) {
    document.getElementById('channel_id').value = id;
    document.getElementById('channel_name').value = name;
    document.getElementById('channel_type').value = type;
    document.getElementById('channel_merchant_id').value = merchantId;
    document.getElementById('channel_secret_key').value = secretKey;
    document.getElementById('channel_gateway_url').value = gatewayUrl;
    document.getElementById('channel_rate').value = rate;
    document.getElementById('channelModalTitle').textContent = '编辑通道';
    document.getElementById('channelSubmitText').textContent = '保存修改';
    document.getElementById('channelForm').action = '/admin/editPaymentChannel';
    document.getElementById('channelModal').classList.add('show');
}

function closeChannelModal() {
    document.getElementById('channelModal').classList.remove('show');
}
</script>