<div class="breadcrumb">用户管理 / <a href="<?php echo url('admin/user'); ?>">用户列表</a> / 用户详情</div>
<div class="page-header">
    <h2>用户详情</h2>
    <div>
        <?php if ($user['status'] == 1): ?>
        <a href="javascript:;" class="btn btn-danger" onclick="toggleStatus(<?php echo $user['id']; ?>, 0)">禁用账号</a>
        <?php else: ?>
        <a href="javascript:;" class="btn btn-success" onclick="toggleStatus(<?php echo $user['id']; ?>, 1)">启用账号</a>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;">用户信息</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 14px;">
        <div><span style="color: #64748B;">用户ID：</span><?php echo $user['id']; ?></div>
        <div><span style="color: #64748B;">昵称：</span><?php echo h($user['nickname'] ?: '-'); ?></div>
        <div><span style="color: #64748B;">手机号：</span><?php echo h($user['mobile'] ?: '-'); ?></div>
        <div><span style="color: #64748B;">所属分站：</span><?php echo h($user['subsite_name'] ?? '-'); ?></div>
        <div><span style="color: #64748B;">等级分组：</span><?php echo h($user['group_name'] ?? '-'); ?></div>
        <div><span style="color: #64748B;">余额：</span>¥ <?php echo $user['balance']; ?></div>
        <div><span style="color: #64748B;">状态：</span>
            <?php if ($user['status'] == 1): ?>
            <span class="tag tag-green">正常</span>
            <?php else: ?>
            <span class="tag tag-red">禁用</span>
            <?php endif; ?>
        </div>
        <div><span style="color: #64748B;">注册时间：</span><?php echo $user['create_time']; ?></div>
        <div><span style="color: #64748B;">更新时间：</span><?php echo $user['update_time']; ?></div>
    </div>
</div>

<div class="card">
    <h3 style="font-size: 16px; margin-bottom: 16px;">最近订单</h3>
    <table>
        <tr>
            <th>订单号</th>
            <th>商品</th>
            <th>金额</th>
            <th>状态</th>
            <th>时间</th>
        </tr>
        <?php if (empty($orders)): ?>
        <tr><td colspan="5" style="text-align: center; color: #64748B; padding: 30px;">暂无订单</td></tr>
        <?php else: ?>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td><?php echo h($o['order_no']); ?></td>
            <td><?php echo h($o['goods_name']); ?></td>
            <td>¥ <?php echo $o['total_amount']; ?></td>
            <td><?php echo $o['status']; ?></td>
            <td><?php echo $o['create_time']; ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<script>
async function toggleStatus(id, status) {
    const label = status ? '启用' : '禁用';
    if (!confirm('确认' + label + '该用户？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('status', status);
    const res = await fetch('<?php echo url('admin/user/toggleStatus'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
