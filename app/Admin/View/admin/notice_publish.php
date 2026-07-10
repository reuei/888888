<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">发布公告</h3>
    </div>
    <form id="noticeForm" class="form">
        <div class="form-group">
            <label>公告标题</label>
            <input type="text" name="title" data-validate="title" data-min="2" data-max="100" required>
        </div>
        <div class="form-group">
            <label>公告内容</label>
            <textarea name="content" rows="10" data-validate="content" data-min="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">发布公告</button>
    </form>
</div>
