<div class="breadcrumb">店铺设置 / 引导页</div>
<div class="page-header">
    <h2>引导页配置</h2>
</div>

<div class="card">
    <form id="guideForm" enctype="multipart/form-data">
        <div class="form-group">
            <label>引导页开关</label>
            <select name="guide_status" id="guideStatus" style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
                <option value="0" <?php echo ($merchant['guide_status'] ?? 0) == 0 ? 'selected' : ''; ?>>关闭</option>
                <option value="1" <?php echo ($merchant['guide_status'] ?? 0) == 1 ? 'selected' : ''; ?>>开启</option>
            </select>
            <p class="hint">开启后，用户访问店铺首页时先展示引导页。</p>
        </div>

        <div id="guidePanel" style="display: <?php echo ($merchant['guide_status'] ?? 0) == 1 ? 'block' : 'none'; ?>;">
            <div class="form-group">
                <label>引导页标题 <span style="color: #EF4444;">*</span></label>
                <input type="text" name="guide_title" value="<?php echo h($merchant['guide_title'] ?? ''); ?>" placeholder="如：欢迎访问本店" maxlength="100">
            </div>
            <div class="form-group">
                <label>引导页内容 <span style="color: #EF4444;">*</span></label>
                <textarea name="guide_content" rows="6" placeholder="支持 HTML，可填写店铺公告、活动说明等" style="width: 100%; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 6px;"><?php echo h($merchant['guide_content'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>背景图</label>
                <input type="file" name="guide_bg_image" accept="image/*">
                <?php if (!empty($merchant['guide_bg_image'])): ?>
                    <p class="hint">已上传：<a href="<?php echo base_url($merchant['guide_bg_image']); ?>" target="_blank">查看</a></p>
                <?php endif; ?>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div class="form-group">
                    <label>按钮文字 <span style="color: #EF4444;">*</span></label>
                    <input type="text" name="guide_button_text" value="<?php echo h($merchant['guide_button_text'] ?? '立即进入'); ?>" placeholder="立即进入" maxlength="50">
                </div>
                <div class="form-group">
                    <label>按钮链接</label>
                    <input type="text" name="guide_button_link" value="<?php echo h($merchant['guide_button_link'] ?? ''); ?>" placeholder="留空默认进入店铺首页">
                </div>
            </div>
        </div>

        <button type="submit" class="btn" id="saveBtn">保存配置</button>
    </form>
</div>

<script>
const guideStatus = document.getElementById('guideStatus');
const guidePanel = document.getElementById('guidePanel');

guideStatus.addEventListener('change', () => {
    guidePanel.style.display = guideStatus.value === '1' ? 'block' : 'none';
});

document.getElementById('guideForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = '保存中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('merchant/setting/saveGuide'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '保存配置';
    }
});
</script>
