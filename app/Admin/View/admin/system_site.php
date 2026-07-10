<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">站点配置</h3>
    </div>
    <form id="siteForm" class="form">
        <div class="form-group">
            <label>站点名称</label>
            <input type="text" name="site_name" value="<?= h($configs['site_name']) ?>" required>
        </div>
        <div class="form-group">
            <label>站点标题</label>
            <input type="text" name="site_title" value="<?= h($configs['site_title']) ?>">
        </div>
        <div class="form-group">
            <label>关键词</label>
            <input type="text" name="site_keywords" value="<?= h($configs['site_keywords']) ?>">
        </div>
        <div class="form-group">
            <label>描述</label>
            <textarea name="site_description" rows="3"><?= h($configs['site_description']) ?></textarea>
        </div>
        <div class="form-group">
            <label>备案号</label>
            <input type="text" name="site_icp" value="<?= h($configs['site_icp']) ?>">
        </div>
        <div class="form-group">
            <label>版权信息</label>
            <input type="text" name="site_copyright" value="<?= h($configs['site_copyright']) ?>">
        </div>
        <button type="submit" class="btn btn-primary">保存配置</button>
    </form>
</div>
