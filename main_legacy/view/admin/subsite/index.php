<div class="breadcrumb">分站管理 / 分站列表</div>
<div class="page-header">
    <h2>分站列表</h2>
    <a href="<?php echo url('admin/subsite/create'); ?>" class="btn">新建分站</a>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/subsite'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="分站名称 / 域名前缀">
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>正常</option>
            <option value="2" <?php echo $status === '2' ? 'selected' : ''; ?>>冻结</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>关闭</option>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>分站ID</th>
            <th>分站名称</th>
            <th>域名前缀</th>
            <th>超管账号</th>
            <th>商户 / 商品</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="8" style="text-align: center; color: #64748B; padding: 40px;">暂无分站数据</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['name']); ?></td>
            <td><?php echo h($item['domain_prefix']); ?></td>
            <td>
                <?php echo h($item['admin_name'] ?? '-'); ?>
                <?php if (!empty($item['admin_last_login'])): ?>
                    <br><span style="color: #94A3B8; font-size: 12px;"><?php echo $item['admin_last_login']; ?></span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['merchant_count']; ?> / <?php echo $item['goods_count']; ?></td>
            <td>
                <?php if ($item['status'] == 1): ?>
                <span class="tag tag-green">正常</span>
                <?php elseif ($item['status'] == 2): ?>
                <span class="tag tag-orange">冻结</span>
                <?php else: ?>
                <span class="tag">关闭</span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['create_time']; ?></td>
            <td>
                <a href="<?php echo url('admin/subsite/detail') . '?id=' . $item['id']; ?>" class="btn btn-sm">详情</a>
                <a href="<?php echo url('admin/subsite/detail') . '?id=' . $item['id'] . '&tab=merchant'; ?>" class="btn btn-sm btn-outline">商户</a>
                <div class="dropdown" style="display: inline-block; position: relative;">
                    <button type="button" class="btn btn-sm btn-outline" onclick="toggleMenu(this)">更多 ▾</button>
                    <div class="dropdown-menu" style="display: none; position: absolute; right: 0; top: 100%; background: #fff; border: 1px solid #E2E8F0; border-radius: 6px; min-width: 140px; z-index: 10;">
                        <a href="javascript:;" onclick="resetPwd(<?php echo $item['id']; ?>)">重置密码</a>
                        <a href="javascript:;" onclick="toggle2fa(<?php echo $item['id']; ?>, <?php echo $item['two_factor'] ? 0 : 1; ?>)">
                            <?php echo $item['two_factor'] ? '关闭二次认证' : '开启二次认证'; ?>
                        </a>
                        <?php if ($item['status'] != 1): ?>
                        <a href="javascript:;" onclick="toggleStatus(<?php echo $item['id']; ?>, 1)">恢复正常</a>
                        <?php else: ?>
                        <a href="javascript:;" onclick="toggleStatus(<?php echo $item['id']; ?>, 2)">冻结分站</a>
                        <a href="javascript:;" onclick="toggleStatus(<?php echo $item['id']; ?>, 0)">关闭分站</a>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('admin/subsite') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('admin/subsite') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&status=' . $status; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.dropdown-menu a {
    display: block;
    padding: 8px 12px;
    font-size: 13px;
    color: #475569;
    white-space: nowrap;
}
.dropdown-menu a:hover {
    background: #F1F5F9;
    color: #2563EB;
}
</style>

<script>
function toggleMenu(btn) {
    const menu = btn.nextElementSibling;
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

document.addEventListener('click', (e) => {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-menu').forEach(m => m.style.display = 'none');
    }
});

async function resetPwd(id) {
    const pwd = prompt('请输入新密码（至少 6 位）：');
    if (!pwd || pwd.length < 6) return;
    const form = new FormData();
    form.append('id', id);
    form.append('password', pwd);
    const res = await fetch('<?php echo url('admin/subsite/resetPassword'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
}

async function toggle2fa(id, enabled) {
    if (!confirm(enabled ? '确认开启二次认证？' : '确认关闭二次认证？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('enabled', enabled);
    const res = await fetch('<?php echo url('admin/subsite/toggle2fa'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function toggleStatus(id, status) {
    const labels = { 0: '关闭', 1: '恢复正常', 2: '冻结' };
    if (!confirm('确认' + labels[status] + '该分站？')) return;
    const form = new FormData();
    form.append('id', id);
    form.append('status', status);
    const res = await fetch('<?php echo url('admin/subsite/toggleStatus'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
