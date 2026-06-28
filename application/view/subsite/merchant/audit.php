<div class="breadcrumb">商户管理 / 分站入驻审核</div>
<div class="page-header">
    <h2>分站入驻审核</h2>
    <a href="<?php echo url('subsite/merchant'); ?>" class="btn btn-outline">商户列表</a>
</div>

<div class="card">
    <table>
        <tr>
            <th>商户ID</th>
            <th>店铺名</th>
            <th>账号</th>
            <th>手机号</th>
            <th>入驻方式</th>
            <th>注册时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="7" style="text-align: center; color: #64748B; padding: 40px;">暂无待审核商户</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['shop_name']); ?></td>
            <td><?php echo h($item['username']); ?></td>
            <td><?php echo h($item['mobile']); ?></td>
            <td><?php echo $item['register_type']; ?></td>
            <td><?php echo $item['create_time']; ?></td>
            <td>
                <a href="javascript:;" class="btn btn-sm btn-success" onclick="auditPass(<?php echo $item['id']; ?>)">通过</a>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="auditReject(<?php echo $item['id']; ?>)">驳回</a>
                <a href="javascript:;" class="btn btn-sm btn-outline" onclick="addRemark(<?php echo $item['id']; ?>)">备注</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('subsite/merchant/audit') . '?page=' . ($page - 1); ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('subsite/merchant/audit') . '?page=' . ($page + 1); ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
async function auditPass(id) {
    const remark = prompt('审核备注（可选）：') || '';
    if (!confirm('确认通过该商户入驻申请？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('remark', remark);
    const res = await fetch('<?php echo url('subsite/merchant/auditPass'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function auditReject(id) {
    const remark = prompt('驳回原因（必填）：');
    if (!remark) return;
    if (!confirm('确认驳回该商户入驻申请？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('remark', remark);
    const res = await fetch('<?php echo url('subsite/merchant/auditReject'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function addRemark(id) {
    const remark = prompt('请输入备注内容：');
    if (!remark) return;
    const form = new FormData();
    form.append('id', id);
    form.append('remark', remark);
    const res = await fetch('<?php echo url('subsite/merchant/auditRemark'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
}
</script>
