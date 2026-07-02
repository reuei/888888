<div class="breadcrumb">系统设置 / 数据备份</div>
<div class="page-header">
    <h2>数据备份与恢复</h2>
    <button type="button" class="btn" onclick="openBackupModal()">+ 立即备份</button>
</div>

<div class="card" style="margin-bottom: 16px; background: #F0FDF4; border-color: #BBF7D0;">
    <div style="font-weight: 500; margin-bottom: 8px; color: #166534;">自动备份接口</div>
    <div style="font-size: 13px; color: #166534; margin-bottom: 8px;">在服务器 crontab 中配置以下 URL，即可实现定时自动备份（建议每日凌晨执行）。</div>
    <div style="display: flex; gap: 8px; align-items: center;">
        <input type="text" id="cronUrl" value="<?php echo h(base_url('cron/backup?key=' . backup_get_cron_key())); ?>" readonly style="flex:1; padding: 8px 12px; border: 1px solid #86EFAC; border-radius: 6px; background: #fff; font-size: 13px;">
        <button type="button" class="btn btn-sm btn-outline" onclick="copyCronUrl()">复制</button>
    </div>
</div>

<div class="card" style="margin-bottom: 16px;">
    <form method="get" action="<?php echo url('admin/backup'); ?>" style="display: flex; gap: 12px; align-items: center;">
        <select name="type" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
            <option value="0" <?php echo $type === 0 ? 'selected' : ''; ?>>全部类型</option>
            <option value="1" <?php echo $type === 1 ? 'selected' : ''; ?>>手动备份</option>
            <option value="2" <?php echo $type === 2 ? 'selected' : ''; ?>>自动备份</option>
        </select>
        <button type="submit" class="btn btn-outline">筛选</button>
    </form>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>备份名称</th>
                <th>类型</th>
                <th>文件大小</th>
                <th>状态</th>
                <th>操作人</th>
                <th>备份时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo $item['id']; ?></td>
                <td>
                    <div style="font-weight: 500;"><?php echo h($item['name']); ?></div>
                    <?php if ($item['remark']): ?>
                    <div style="font-size: 12px; color: #64748B;"><?php echo h($item['remark']); ?></div>
                    <?php endif; ?>
                </td>
                <td><?php echo $item['type'] == 1 ? '手动备份' : '自动备份'; ?></td>
                <td><?php echo format_size($item['file_size']); ?></td>
                <td>
                    <?php if ($item['status'] == 0): ?>
                    <span class="tag tag-green">正常</span>
                    <?php elseif ($item['status'] == 1): ?>
                    <span class="tag tag-orange">已恢复</span>
                    <?php else: ?>
                    <span class="tag">已删除</span>
                    <?php endif; ?>
                </td>
                <td><?php echo h($item['operator_name'] ?: '系统'); ?></td>
                <td><?php echo $item['create_time']; ?></td>
                <td>
                    <?php if ($item['status'] == 0): ?>
                    <a href="<?php echo url('admin/backup/download', ['id' => $item['id']]); ?>" class="btn btn-sm btn-outline">下载</a>
                    <button type="button" class="btn btn-sm btn-warning" onclick="restoreBackup(<?php echo $item['id']; ?>, '<?php echo h($item['name']); ?>')">恢复</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteBackup(<?php echo $item['id']; ?>, '<?php echo h($item['name']); ?>')">删除</button>
                    <?php else: ?>
                    <span style="color: #94A3B8; font-size: 12px;">不可操作</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($list)): ?>
            <tr>
                <td colspan="8" style="text-align: center; color: #64748B;">暂无备份记录</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
<div style="display: flex; justify-content: center; gap: 8px; margin-top: 24px;">
    <?php if ($page > 1): ?>
    <a href="<?php echo url('admin/backup') . '?page=' . ($page - 1) . '&type=' . $type; ?>" class="btn btn-outline">上一页</a>
    <?php endif; ?>
    <span style="padding: 8px 16px; color: #64748B;"><?php echo $page; ?> / <?php echo $totalPages; ?></span>
    <?php if ($page < $totalPages): ?>
    <a href="<?php echo url('admin/backup') . '?page=' . ($page + 1) . '&type=' . $type; ?>" class="btn btn-outline">下一页</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<div id="backupModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:200; align-items:center; justify-content:center;">
    <div style="background:#fff; width:480px; max-width:90%; border-radius:8px; padding:24px;">
        <h3 style="margin-bottom:16px;">新建备份</h3>
        <form id="backupForm">
            <div style="margin-bottom:12px;">
                <label>备份名称</label>
                <input type="text" name="name" placeholder="例如：升级前备份" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;">
            </div>
            <div style="margin-bottom:16px;">
                <label>备注</label>
                <textarea name="remark" rows="3" placeholder="可选：填写备份说明" style="width:100%; padding:8px; border:1px solid #CBD5E1; border-radius:6px;"></textarea>
            </div>
            <div style="background:#FFFBEB; border:1px solid #FDE68A; border-radius:6px; padding:12px; margin-bottom:16px; font-size:13px; color:#92400E;">
                提示：备份过程可能需要一些时间，请耐心等待。建议定期进行手动备份，尤其是在系统升级或重大操作前。
            </div>
            <div style="text-align:right;">
                <button type="button" class="btn btn-outline" onclick="closeBackupModal()" style="margin-right:8px;">取消</button>
                <button type="submit" class="btn" id="backupSubmitBtn">开始备份</button>
            </div>
        </form>
    </div>
</div>

<script>
function copyCronUrl() {
    const input = document.getElementById('cronUrl');
    input.select();
    document.execCommand('copy');
    alert('已复制到剪贴板');
}

function openBackupModal() {
    document.getElementById('backupForm').reset();
    document.getElementById('backupModal').style.display = 'flex';
}
function closeBackupModal() {
    document.getElementById('backupModal').style.display = 'none';
}

document.getElementById('backupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('backupSubmitBtn');
    btn.disabled = true;
    btn.textContent = '备份中...';

    const form = new FormData(this);
    fetch('<?php echo url("admin/backup/create"); ?>', {
        method: 'POST',
        body: form
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) location.reload();
    }).catch(() => {
        alert('请求失败');
    }).finally(() => {
        btn.disabled = false;
        btn.textContent = '开始备份';
    });
});

function restoreBackup(id, name) {
    if (!confirm('警告：恢复备份将覆盖当前数据库全部数据！\n确认恢复备份：' + name + '？')) return;
    if (!confirm('再次确认：此操作不可逆，当前数据将被替换为备份时的状态。是否继续？')) return;

    fetch('<?php echo url("admin/backup/restore"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) location.reload();
    });
}

function deleteBackup(id, name) {
    if (!confirm('确认删除备份：' + name + '？删除后文件将无法恢复。')) return;
    fetch('<?php echo url("admin/backup/delete"); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    }).then(r => r.json()).then(res => {
        alert(res.msg);
        if (res.code === 0) location.reload();
    });
}
</script>
