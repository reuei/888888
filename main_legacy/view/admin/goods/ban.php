<div class="breadcrumb">商品管理 / 禁售目录</div>
<div class="page-header">
    <h2>禁售目录</h2>
    <a href="<?php echo url('admin/goods'); ?>" class="btn btn-outline">商品列表</a>
</div>

<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;">添加禁售关键词</h3>
    <form id="banForm" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">关键词</label>
            <input type="text" name="keyword" required placeholder="如：违禁品名称" style="width: 220px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">类型</label>
            <select name="type" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
                <option value="goods">商品名</option>
                <option value="category">类目</option>
            </select>
        </div>
        <button type="submit" class="btn" id="saveBtn">添加</button>
    </form>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/goods/ban'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="搜索关键词">
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>关键词</th>
            <th>类型</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="5" style="text-align: center; color: #64748B; padding: 40px;">暂无禁售关键词</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><code style="font-family: monospace; background: #F1F5F9; padding: 2px 6px; border-radius: 4px;"><?php echo h($item['keyword']); ?></code></td>
            <td><?php echo $item['type'] == 'category' ? '类目' : '商品名'; ?></td>
            <td><?php echo $item['create_time']; ?></td>
            <td>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="deleteBan(<?php echo $item['id']; ?>)">删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<script>
document.getElementById('banForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    btn.disabled = true;
    btn.textContent = '添加中...';
    const formData = new FormData(e.target);
    try {
        const res = await fetch('<?php echo url('admin/goods/banSave'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '添加';
    }
});

async function deleteBan(id) {
    if (!confirm('确认删除该禁售关键词？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('admin/goods/banDelete'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
