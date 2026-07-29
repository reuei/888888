<h1 class="page-title" style="font-size: 24px; color: #1a1a2e; margin-bottom: 24px;">文件管理</h1>

<!-- Logo Upload -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-image"/></svg>网站 Logo
        </h3>
    </div>
    <div style="display: flex; align-items: flex-start; gap: 24px; flex-wrap: wrap;">
        <div style="flex-shrink: 0;">
            <div style="width: 180px; height: 60px; border: 1px dashed var(--border); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; background: var(--bg-tertiary); overflow: hidden;">
                <?php if (!empty($siteSettings['logo'])): ?>
                <img src="<?= htmlspecialchars($siteSettings['logo']) ?>" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                <?php else: ?>
                <span style="color: var(--text-muted); font-size: 13px;">暂无 Logo</span>
                <?php endif; ?>
            </div>
        </div>
        <div style="flex: 1; min-width: 240px;">
            <form method="POST" action="/admin/uploadLogo" enctype="multipart/form-data" data-ajax="true">
                <div class="form-group">
                    <label class="form-label" for="logo_file">上传 Logo</label>
                    <input type="file" class="form-control" id="logo_file" name="logo" accept="image/*" style="padding: 8px;">
                    <div class="help-text" style="font-size: 12px; color: #687690; margin-top: 4px;">建议尺寸：180×60，支持 PNG、JPG、SVG</div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-upload"/></svg>上传 Logo
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Favicon Upload -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title" style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18"><use href="#i-image"/></svg>网站 Favicon
        </h3>
    </div>
    <div style="display: flex; align-items: flex-start; gap: 24px; flex-wrap: wrap;">
        <div style="flex-shrink: 0;">
            <div style="width: 64px; height: 64px; border: 1px dashed var(--border); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; background: var(--bg-tertiary); overflow: hidden;">
                <?php if (!empty($siteSettings['favicon'])): ?>
                <img src="<?= htmlspecialchars($siteSettings['favicon']) ?>" alt="Favicon" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                <?php else: ?>
                <span style="color: var(--text-muted); font-size: 12px;">暂无</span>
                <?php endif; ?>
            </div>
        </div>
        <div style="flex: 1; min-width: 240px;">
            <form method="POST" action="/admin/uploadFavicon" enctype="multipart/form-data" data-ajax="true">
                <div class="form-group">
                    <label class="form-label" for="favicon_file">上传 Favicon</label>
                    <input type="file" class="form-control" id="favicon_file" name="favicon" accept="image/*,.ico" style="padding: 8px;">
                    <div class="help-text" style="font-size: 12px; color: #687690; margin-top: 4px;">建议尺寸：64×64，支持 PNG、ICO</div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" style="vertical-align: middle; margin-right: 4px;"><use href="#i-upload"/></svg>上传 Favicon
                </button>
            </form>
        </div>
    </div>
</div>