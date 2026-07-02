<div class="breadcrumb">运营管理 / 广告位</div>
<div class="page-header">
    <h2>广告位管理</h2>
</div>

<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;">新增/编辑广告</h3>
    <form id="adForm" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
        <input type="hidden" name="id" id="adId" value="0">
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">广告标题</label>
            <input type="text" name="title" id="adTitle" required style="width: 200px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">图片 URL</label>
            <input type="text" name="image" id="adImage" required style="width: 260px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">跳转链接</label>
            <input type="text" name="link" id="adLink" style="width: 220px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">广告位</label>
            <select name="position" id="adPosition" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
                <?php foreach ($positionMap as $k => $v): ?>
                <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">排序</label>
            <input type="number" name="sort" id="adSort" value="0" style="width: 90px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
        </div>
        <div>
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">状态</label>
            <select name="status" id="adStatus" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
                <option value="1">启用</option>
                <option value="0">禁用</option>
            </select>
        </div>
        <button type="submit" class="btn" id="saveBtn">保存</button>
        <button type="button" class="btn btn-outline" id="resetBtn" style="display: none;">取消</button>
    </form>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/ad'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="广告标题">
        <select name="position" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
            <option value="">全部位置</option>
            <?php foreach ($positionMap as $k => $v): ?>
            <option value="<?php echo $k; ?>" <?php echo $position === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
            <option value="">全部状态</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>启用</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>禁用</option>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>标题</th>
            <th>位置</th>
            <th>排序</th>
            <th>图片预览</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="8" style="text-align: center; color: #64748B; padding: 40px;">暂无广告</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['title']); ?></td>
            <td><?php echo $positionMap[$item['position']] ?? $item['position']; ?></td>
            <td><?php echo $item['sort']; ?></td>
            <td>
                <?php if ($item['image']): ?>
                <img src="<?php echo h($item['image']); ?>" style="height: 40px; border-radius: 4px; cursor: pointer;" onclick="window.open('<?php echo h($item['image']); ?>')">
                <?php else: ?>
                -
                <?php endif; ?>
            </td>
            <td>
                <?php if ($item['status']): ?>
                <span class="tag tag-green">启用</span>
                <?php else: ?>
                <span class="tag">禁用</span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['create_time']; ?></td>
            <td>
                <a href="javascript:;" class="btn btn-sm btn-outline" onclick="editAd(<?php echo $item['id']; ?>, '<?php echo h(addslashes($item['title'])); ?>', '<?php echo h(addslashes($item['image'])); ?>', '<?php echo h(addslashes($item['link'])); ?>', '<?php echo $item['position']; ?>', <?php echo $item['sort']; ?>, <?php echo $item['status']; ?>)">编辑</a>
                <?php if ($item['status']): ?>
                <a href="javascript:;" class="btn btn-sm btn-warning" onclick="toggleStatus(<?php echo $item['id']; ?>, 0)">禁用</a>
                <?php else: ?>
                <a href="javascript:;" class="btn btn-sm btn-success" onclick="toggleStatus(<?php echo $item['id']; ?>, 1)">启用</a>
                <?php endif; ?>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="deleteAd(<?php echo $item['id']; ?>, '<?php echo h(addslashes($item['title'])); ?>')">删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('admin/ad') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&position=' . $position . '&status=' . $status; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('admin/ad') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&position=' . $position . '&status=' . $status; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
const form = document.getElementById('adForm');
const saveBtn = document.getElementById('saveBtn');
const resetBtn = document.getElementById('resetBtn');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    saveBtn.disabled = true;
    saveBtn.textContent = '保存中...';
    const formData = new FormData(form);
    try {
        const res = await fetch('<?php echo url('admin/ad/save'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = '保存';
    }
});

function editAd(id, title, image, link, position, sort, status) {
    document.getElementById('adId').value = id;
    document.getElementById('adTitle').value = title;
    document.getElementById('adImage').value = image;
    document.getElementById('adLink').value = link;
    document.getElementById('adPosition').value = position;
    document.getElementById('adSort').value = sort;
    document.getElementById('adStatus').value = status;
    saveBtn.textContent = '更新';
    resetBtn.style.display = 'inline-block';
}

resetBtn.addEventListener('click', () => {
    form.reset();
    document.getElementById('adId').value = 0;
    saveBtn.textContent = '保存';
    resetBtn.style.display = 'none';
});

async function toggleStatus(id, status) {
    const form = new FormData();
    form.append('id', id);
    form.append('status', status);
    const res = await fetch('<?php echo url('admin/ad/status'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function deleteAd(id, title) {
    if (!confirm('确认删除广告「' + title + '」？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('admin/ad/delete'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
