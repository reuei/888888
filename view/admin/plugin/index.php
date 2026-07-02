<div class="breadcrumb">开放 API / 插件管理</div>
<div class="page-header">
    <h2>插件 / 机器人对接</h2>
    <button type="button" class="btn" onclick="openPluginModal()">+ 新增插件</button>
</div>

<div class="card" style="margin-bottom: 16px; background: #EFF6FF; border-color: #BFDBFE;">
    <div style="font-size: 13px; color: #1E40AF;">
        插件回调地址：<code>/plugin/webhook?code=插件编码&token=配置的Token</code><br>
        支持事件：order_created（订单创建）、order_paid（订单支付）、order_delivered（订单发货）。
    </div>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>编码</th>
                <th>名称</th>
                <th>类型</th>
                <th>监听事件</th>
                <th>回调 URL</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <?php $config = json_decode($item['config'] ?? '{}', true); ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo h($item['code']); ?></td>
                <td><?php echo h($item['name']); ?></td>
                <td><?php echo $item['type'] === 'webhook' ? 'Webhook' : '机器人'; ?></td>
                <td><?php echo h($item['event_types']); ?></td>
                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo h($config['url'] ?? '-'); ?></td>
                <td><?php echo $item['status'] ? '<span class="tag tag-green">启用</span>' : '<span class="tag tag-orange">禁用</span>'; ?></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline" onclick="editPlugin(<?php echo htmlspecialchars(json_encode($item, JSON_UNESCAPED_UNICODE), ENT_QUOTES); ?>)">编辑</button>
                    <button type="button" class="btn btn-sm btn-outline" onclick="location.href='<?php echo url('admin/plugin/log', ['plugin_id' => $item['id']]); ?>'">日志</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deletePlugin(<?php echo $item['id']; ?>)">删除</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($list)): ?>
            <tr><td colspan="8" style="text-align: center; color: #64748B;">暂无插件配置</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="pluginModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:200; align-items:center; justify-content:center;">
    <div style="background:#fff; width:520px; max-width:95%; border-radius:8px; padding:24px;">
        <h3 style="margin-bottom:16px;">插件配置</h3>
        <form id="pluginForm">
            <input type="hidden" name="id" id="pluginId">
            <div style="display:flex; gap:12px; margin-bottom:12px;">
                <div style="flex:1;">
                    <label>插件编码</label>
                    <input type="text" name="code" id="pluginCode" required placeholder="如：telegram_bot" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                </div>
                <div style="flex:1;">
                    <label>插件类型</label>
                    <select name="type" id="pluginType" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                        <option value="webhook">Webhook</option>
                        <option value="bot">机器人</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label>插件名称</label>
                <input type="text" name="name" id="pluginName" required style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="margin-bottom:12px;">
                <label>回调 URL</label>
                <input type="text" name="config_url" id="configUrl" placeholder="http://..." style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="margin-bottom:12px;">
                <label>通信 Secret（用于 Webhook 签名）</label>
                <input type="text" name="config_secret" id="configSecret" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="margin-bottom:12px;">
                <label>回调 Token（用于校验插件身份）</label>
                <input type="text" name="config_token" id="configToken" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="margin-bottom:12px;">
                <label>监听事件（逗号分隔）</label>
                <input type="text" name="event_types" id="pluginEventTypes" placeholder="order_created,order_paid,order_delivered" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="margin-bottom:16px;">
                <label>状态</label>
                <select name="status" id="pluginStatus" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                    <option value="1">启用</option>
                    <option value="0">禁用</option>
                </select>
            </div>
            <div style="text-align:right;">
                <button type="button" class="btn btn-outline" onclick="closePluginModal()" style="margin-right:8px;">取消</button>
                <button type="submit" class="btn">保存</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPluginModal() {
    document.getElementById('pluginForm').reset();
    document.getElementById('pluginId').value = '';
    document.getElementById('pluginCode').disabled = false;
    document.getElementById('pluginModal').style.display = 'flex';
}
function closePluginModal() {
    document.getElementById('pluginModal').style.display = 'none';
}
function editPlugin(item) {
    openPluginModal();
    document.getElementById('pluginId').value = item.id;
    document.getElementById('pluginCode').value = item.code;
    document.getElementById('pluginCode').disabled = true;
    document.getElementById('pluginType').value = item.type;
    document.getElementById('pluginName').value = item.name;
    document.getElementById('pluginEventTypes').value = item.event_types;
    document.getElementById('pluginStatus').value = item.status;

    var config = item.config ? JSON.parse(item.config) : {};
    document.getElementById('configUrl').value = config.url || '';
    document.getElementById('configSecret').value = config.secret || '';
    document.getElementById('configToken').value = config.token || '';
}
function deletePlugin(id) {
    if (!confirm('确认删除该插件配置？')) return;
    fetch('<?php echo url("admin/plugin/delete"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) location.reload();
    });
}
document.getElementById('pluginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('<?php echo url("admin/plugin/save"); ?>', { method: 'POST', body: new FormData(this) })
        .then(r => r.json()).then(res => {
            alert(res.msg);
            if (res.code === 0) location.reload();
        });
});
</script>
