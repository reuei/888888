<div class="breadcrumb">系统设置 / 消息模板</div>
<div class="page-header">
    <h2>短信 / 邮件模板管理</h2>
    <button type="button" class="btn" onclick="openTemplateModal()">+ 新增模板</button>
</div>

<div class="card" style="margin-bottom: 16px;">
    <form method="get" action="<?php echo url('admin/message'); ?>" style="display: flex; gap: 12px; align-items: center;">
        <select name="type" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
            <option value="" <?php echo $type === '' ? 'selected' : ''; ?>>全部类型</option>
            <option value="email" <?php echo $type === 'email' ? 'selected' : ''; ?>>邮件模板</option>
            <option value="sms" <?php echo $type === 'sms' ? 'selected' : ''; ?>>短信模板</option>
        </select>
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="搜索模板名称/编码" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; min-width: 220px;">
        <button type="submit" class="btn btn-outline">筛选</button>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>类型</th>
                <th>编码</th>
                <th>名称</th>
                <th>主题</th>
                <th>变量</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td><?php echo $item['type'] === 'email' ? '<span class="tag tag-blue">邮件</span>' : '<span class="tag tag-green">短信</span>'; ?></td>
                <td><?php echo h($item['code']); ?></td>
                <td><?php echo h($item['name']); ?></td>
                <td><?php echo $item['type'] === 'email' ? h($item['title']) : '-'; ?></td>
                <td><?php echo h($item['variables'] ?: '-'); ?></td>
                <td><?php echo $item['status'] ? '<span class="tag tag-green">启用</span>' : '<span class="tag tag-orange">禁用</span>'; ?></td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline" onclick="editTemplate(<?php echo htmlspecialchars(json_encode($item, JSON_UNESCAPED_UNICODE), ENT_QUOTES); ?>)" style="margin-right: 6px;">编辑</button>
                    <button type="button" class="btn btn-sm btn-warning" onclick="openTestModal(<?php echo htmlspecialchars(json_encode($item, JSON_UNESCAPED_UNICODE), ENT_QUOTES); ?>)" style="margin-right: 6px;">测试</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteTemplate(<?php echo $item['id']; ?>, '<?php echo h($item['name']); ?>')">删除</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($list)): ?>
            <tr>
                <td colspan="8" style="text-align: center; color: #64748B;">暂无模板</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="templateModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:200; align-items:center; justify-content:center;">
    <div style="background:#fff; width:600px; max-width:95%; max-height: 90vh; overflow-y: auto; border-radius:8px; padding:24px;">
        <h3 style="margin-bottom:16px;">消息模板</h3>
        <form id="templateForm">
            <input type="hidden" name="id" id="templateId">
            <div style="display:flex; gap:12px; margin-bottom:12px;">
                <div style="flex:1;">
                    <label>模板类型</label>
                    <select name="type" id="templateType" required style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                        <option value="email">邮件模板</option>
                        <option value="sms">短信模板</option>
                    </select>
                </div>
                <div style="flex:1;">
                    <label>模板编码</label>
                    <input type="text" name="code" id="templateCode" required placeholder="如：register" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label>模板名称</label>
                <input type="text" name="name" id="templateName" required style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div id="emailTitleRow" style="margin-bottom:12px;">
                <label>邮件主题</label>
                <input type="text" name="title" id="templateTitle" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="margin-bottom:12px;">
                <label>模板内容</label>
                <textarea name="content" id="templateContent" rows="6" required style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;"></textarea>
                <div style="font-size: 12px; color: #64748B; margin-top: 4px;">使用 {var_name} 作为变量占位符，发送时会自动替换。</div>
            </div>
            <div style="margin-bottom:12px;">
                <label>可用变量（逗号分隔）</label>
                <input type="text" name="variables" id="templateVariables" placeholder="如：code,nickname,order_no" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="display:flex; gap:12px; margin-bottom:12px;">
                <div style="flex:1;">
                    <label>排序</label>
                    <input type="number" name="sort" id="templateSort" value="0" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                </div>
                <div style="flex:1;">
                    <label>状态</label>
                    <select name="status" id="templateStatus" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
                        <option value="1">启用</option>
                        <option value="0">禁用</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom:16px;">
                <label>说明</label>
                <input type="text" name="description" id="templateDescription" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="text-align:right;">
                <button type="button" class="btn btn-outline" onclick="closeTemplateModal()" style="margin-right:8px;">取消</button>
                <button type="submit" class="btn">保存</button>
            </div>
        </form>
    </div>
</div>

<div id="testModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:201; align-items:center; justify-content:center;">
    <div style="background:#fff; width:480px; max-width:90%; border-radius:8px; padding:24px;">
        <h3 style="margin-bottom:16px;">测试发送</h3>
        <form id="testForm">
            <input type="hidden" name="id" id="testId">
            <div style="margin-bottom:12px;">
                <label id="testRecipientLabel">收件人邮箱</label>
                <input type="text" name="recipient" id="testRecipient" required style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="margin-bottom:12px;">
                <label>模板变量（JSON 格式）</label>
                <textarea name="vars" id="testVars" rows="4" placeholder='{"code":"123456","nickname":"测试用户"}' style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;"></textarea>
            </div>
            <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:6px; padding:12px; margin-bottom:16px; font-size:13px; color:#92400E;">
                请先在“系统设置”中配置好邮件或短信网关，否则测试将失败。
            </div>
            <div style="text-align:right;">
                <button type="button" class="btn btn-outline" onclick="closeTestModal()" style="margin-right:8px;">取消</button>
                <button type="submit" class="btn" id="testSubmitBtn">发送测试</button>
            </div>
        </form>
    </div>
</div>

<script>
function openTemplateModal() {
    document.getElementById('templateForm').reset();
    document.getElementById('templateId').value = '';
    document.getElementById('templateModal').style.display = 'flex';
    toggleEmailTitle();
}
function closeTemplateModal() {
    document.getElementById('templateModal').style.display = 'none';
}
function toggleEmailTitle() {
    const type = document.getElementById('templateType').value;
    document.getElementById('emailTitleRow').style.display = type === 'email' ? 'block' : 'none';
}
document.getElementById('templateType').addEventListener('change', toggleEmailTitle);

function editTemplate(item) {
    openTemplateModal();
    document.getElementById('templateId').value = item.id;
    document.getElementById('templateType').value = item.type;
    document.getElementById('templateCode').value = item.code;
    document.getElementById('templateName').value = item.name;
    document.getElementById('templateTitle').value = item.title;
    document.getElementById('templateContent').value = item.content;
    document.getElementById('templateVariables').value = item.variables;
    document.getElementById('templateSort').value = item.sort;
    document.getElementById('templateStatus').value = item.status;
    document.getElementById('templateDescription').value = item.description;
    toggleEmailTitle();
}

function deleteTemplate(id, name) {
    if (!confirm('确认删除模板：' + name + '？')) return;
    fetch('<?php echo url("admin/message/delete"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) location.reload();
    });
}

document.getElementById('templateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = new FormData(this);
    fetch('<?php echo url("admin/message/save"); ?>', {
        method: 'POST',
        body: form
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) location.reload();
    });
});

function openTestModal(item) {
    document.getElementById('testForm').reset();
    document.getElementById('testId').value = item.id;
    document.getElementById('testRecipientLabel').textContent = item.type === 'email' ? '收件人邮箱' : '收件人手机号';
    document.getElementById('testRecipient').placeholder = item.type === 'email' ? 'test@example.com' : '13800138000';
    document.getElementById('testModal').style.display = 'flex';
}
function closeTestModal() {
    document.getElementById('testModal').style.display = 'none';
}

document.getElementById('testForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('testSubmitBtn');
    btn.disabled = true;
    btn.textContent = '发送中...';

    const form = new FormData(this);
    fetch('<?php echo url("admin/message/test"); ?>', {
        method: 'POST',
        body: form
    }).then(r => r.json()).then(res => {
        alert(res.msg);
    }).catch(() => {
        alert('请求失败');
    }).finally(() => {
        btn.disabled = false;
        btn.textContent = '发送测试';
    });
});
</script>
