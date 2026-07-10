<div class="panel">
    <div class="panel-head">
        <h3 class="panel-title">发布消息</h3>
    </div>
    <form id="messageForm" class="form">
        <div class="form-group">
            <label>消息类型</label>
            <select name="type">
                <option value="system">系统消息</option>
                <option value="order">订单消息</option>
                <option value="finance">财务消息</option>
                <option value="activity">活动消息</option>
            </select>
        </div>
        <div class="form-group">
            <label>消息标题</label>
            <input type="text" name="title" data-validate="title" data-min="2" data-max="100" required>
            <p class="form-hint">2-100个字符</p>
        </div>
        <div class="form-group">
            <label>消息内容</label>
            <textarea name="content" rows="6" data-validate="content" data-min="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">发布消息</button>
    </form>
</div>
