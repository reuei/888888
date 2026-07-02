<div class="breadcrumb">开放 API / API 密钥</div>
<div class="page-header">
    <h2>API 密钥管理</h2>
    <button type="button" class="btn" onclick="openApiModal()">+ 新增密钥</button>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>App ID</th>
                <th>关联商户</th>
                <th>权限</th>
                <th>调用次数</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo h($item['name']); ?></td>
                <td><?php echo h($item['app_id']); ?></td>
                <td><?php echo h($item['shop_name'] ?: '总站'); ?></td>
                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo h($item['permissions'] ?: '全部'); ?></td>
                <td><?php echo $item['request_count']; ?></td>
                <td><?php echo $item['status'] ? '<span class="tag tag-green">启用</span>' : '<span class="tag tag-orange">禁用</span>'; ?></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline" onclick="editApi(<?php echo htmlspecialchars(json_encode($item, JSON_UNESCAPED_UNICODE), ENT_QUOTES); ?>)">编辑</button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="resetSecret(<?php echo $item['id']; ?>)">重置密钥</button>
                    <button type="button" class="btn btn-sm btn-outline" onclick="location.href='<?php echo url('admin/api/log', ['app_id' => $item['app_id']]); ?>'">日志</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteApi(<?php echo $item['id']; ?>)">删除</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($list)): ?>
            <tr><td colspan="8" style="text-align: center; color: #64748B;">暂无 API 密钥</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="apiModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:200; align-items:center; justify-content:center;">
    <div style="background:#fff; width:520px; max-width:95%; border-radius:8px; padding:24px;">
        <h3 style="margin-bottom:16px;">API 密钥</h3>
        <form id="apiForm">
            <input type="hidden" name="id" id="apiId">
            <div style="margin-bottom:12px;">
                <label>密钥名称</label>
                <input type="text" name="name" id="apiName" required style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="margin-bottom:12px;">
                <label>关联商户（可选）</label>
                <select name="merchant_id" id="apiMerchant" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                    <option value="0">总站</option>
                    <?php foreach ($merchants as $m): ?>
                    <option value="<?php echo $m['id']; ?>"><?php echo h($m['shop_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="margin-bottom:12px;">
                <label>权限列表（逗号分隔，* 表示全部）</label>
                <input type="text" name="permissions" id="apiPermissions" placeholder="goods,goodsDetail,createOrder,orderQuery,cards" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="margin-bottom:12px;">
                <label>允许 IP（逗号分隔，留空表示不限）</label>
                <input type="text" name="ips" id="apiIps" placeholder="如：127.0.0.1,192.168.1.1" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="margin-bottom:16px;">
                <label>状态</label>
                <select name="status" id="apiStatus" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                    <option value="1">启用</option>
                    <option value="0">禁用</option>
                </select>
            </div>
            <div style="text-align:right;">
                <button type="button" class="btn btn-outline" onclick="closeApiModal()" style="margin-right:8px;">取消</button>
                <button type="submit" class="btn">保存</button>
            </div>
        </form>
    </div>
</div>

<script>
function openApiModal() {
    document.getElementById('apiForm').reset();
    document.getElementById('apiId').value = '';
    document.getElementById('apiModal').style.display = 'flex';
}
function closeApiModal() {
    document.getElementById('apiModal').style.display = 'none';
}
function editApi(item) {
    openApiModal();
    document.getElementById('apiId').value = item.id;
    document.getElementById('apiName').value = item.name;
    document.getElementById('apiMerchant').value = item.merchant_id;
    document.getElementById('apiPermissions').value = item.permissions;
    document.getElementById('apiIps').value = item.ips;
    document.getElementById('apiStatus').value = item.status;
}
function resetSecret(id) {
    if (!confirm('重置后将生成新的 App Secret，旧签名将失效，是否继续？')) return;
    fetch('<?php echo url("admin/api/resetSecret"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    }).then(r => r.json()).then(res => {
        if (res.code === 0) {
            alert('新的 App Secret：' + res.data.secret + '\n请妥善保存，关闭后无法再次查看完整密钥。');
            location.reload();
        } else {
            alert(res.msg);
        }
    });
}
function deleteApi(id) {
    if (!confirm('确认删除该 API 密钥？')) return;
    fetch('<?php echo url("admin/api/delete"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) location.reload();
    });
}
document.getElementById('apiForm').addEventListener('submit', function(e) {
    e.preventDefault();
    fetch('<?php echo url("admin/api/save"); ?>', { method: 'POST', body: new FormData(this) })
        .then(r => r.json()).then(res => {
            alert(res.msg);
            if (res.code === 0) location.reload();
        });
});
</script>
