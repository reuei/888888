<div class="user-layout">
    <div class="user-sidebar">
        <a href="<?php echo url('user'); ?>">个人中心</a>
        <a href="<?php echo url('user/license'); ?>" class="active">我的授权</a>
        <a href="<?php echo url('user/plugin'); ?>">我的插件</a>
        <a href="<?php echo url('user/order'); ?>">我的订单</a>
        <a href="<?php echo url('user/recharge'); ?>">余额充值</a>
        <a href="<?php echo url('user/profile'); ?>">修改资料</a>
        <a href="<?php echo url('user/password'); ?>">修改密码</a>
    </div>
    <div class="user-content">
        <div class="card">
            <h2 style="margin-bottom: 20px;">我的授权</h2>
            <?php if (empty($list)): ?>
            <div class="empty-tip">暂无授权</div>
            <?php else: ?>
            <table>
                <thead>
                    <tr><th>产品</th><th>授权码</th><th>类型</th><th>域名</th><th>状态</th><th>过期时间</th><th>操作</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $item): ?>
                    <tr>
                        <td><?php echo h($item['product_name']); ?></td>
                        <td><?php echo h($item['auth_code']); ?></td>
                        <td><?php echo $item['license_type'] === 'domain' ? '域名授权' : '授权码'; ?></td>
                        <td>
                            <?php if ($item['license_type'] === 'domain'): ?>
                                <?php echo h($item['auth_domain'] ?: '未绑定'); ?>
                                <br>
                                <input type="text" class="domain-input" data-id="<?php echo $item['id']; ?>" placeholder="输入域名" value="<?php echo h($item['auth_domain']); ?>" style="width:120px;">
                                <button class="btn btn-sm bind-domain" data-id="<?php echo $item['id']; ?>">绑定</button>
                                <button class="btn btn-sm btn-outline unbind-domain" data-id="<?php echo $item['id']; ?>">解绑</button>
                            <?php else: ?>-<?php endif; ?>
                        </td>
                        <td><span class="tag <?php echo $item['status'] == 1 ? 'tag-green' : 'tag-red'; ?>"><?php echo $item['status'] == 1 ? '正常' : '禁用'; ?></span></td>
                        <td><?php echo $item['expire_time'] ? $item['expire_time'] : '永久'; ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline transfer" data-id="<?php echo $item['id']; ?>">转让</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php echo pagination($total, $page, 10, url('user/license', ['page' => '{page}'])); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.bind-domain').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const domain = document.querySelector('.domain-input[data-id="' + id + '"]').value;
        fetch('<?php echo url('user/bindDomain'); ?>', {
            method: 'POST',
            body: new URLSearchParams({id, domain})
        }).then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
document.querySelectorAll('.unbind-domain').forEach(btn => {
    btn.addEventListener('click', function() {
        if (!confirm('确认解绑域名？')) return;
        const id = this.dataset.id;
        fetch('<?php echo url('user/unbindDomain'); ?>', {
            method: 'POST',
            body: new URLSearchParams({id})
        }).then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
document.querySelectorAll('.transfer').forEach(btn => {
    btn.addEventListener('click', function() {
        const target = prompt('请输入目标用户账号');
        if (!target) return;
        const id = this.dataset.id;
        fetch('<?php echo url('user/transfer'); ?>', {
            method: 'POST',
            body: new URLSearchParams({id, target_username: target})
        }).then(r => r.json()).then(res => { alert(res.msg); if (res.code === 0) location.reload(); });
    });
});
</script>
