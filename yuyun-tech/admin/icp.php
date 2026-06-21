<?php
/**
 * 备案信息管理
 */
$active_page = 'icp';
require_once 'header.php';
$c = $site_data['icp'] ?? [];
?>
<div class="admin-content">
    <div class="admin-card">
        <h2><i class="fas fa-shield-alt" style="color:#00a86b;"></i> 备案信息管理</h2>
        <form method="post" id="form_icp" style="margin-top:20px;max-width:700px;">
            <input type="hidden" name="action" value="save_icp">
            <div class="form-group">
                <label class="form-label">ICP备案号</label>
                <input type="text" class="form-input" name="icp_number" value="<?php echo htmlspecialchars($c['icp_number'] ?? '鲁ICP备2024000000号-1'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">公安备案号</label>
                <input type="text" class="form-input" name="police_number" value="<?php echo htmlspecialchars($c['police_number'] ?? '鲁公网安备 37020000000000号'); ?>">
            </div>
            <div class="form-group">
                <label class="form-label">增值电信业务许可证号</label>
                <input type="text" class="form-input" name="license_number" value="<?php echo htmlspecialchars($c['license_number'] ?? 'B1-20240000'); ?>">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 保存</button>
        </form>
    </div>
</div>

<script>
document.getElementById('form_icp').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fetch('', { method: 'POST', body: fd })
        .then(r => r.json()).then(d => {
            if (d.code === 0) { showToast(d.msg || '保存成功', 'success'); setTimeout(()=>location.reload(), 800); }
            else { showToast(d.msg || '保存失败', 'danger'); }
        }).catch(() => showToast('请求失败', 'danger'));
});
</script>
</div>
</body>
</html>
