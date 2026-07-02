<div class="breadcrumb">运营管理 / 文章公告</div>
<div class="page-header">
    <h2>文章公告列表</h2>
    <a href="<?php echo url('admin/article/create'); ?>" class="btn">发布公告</a>
</div>

<div class="card" style="margin-bottom: 16px;">
    <h3 style="font-size: 16px; margin-bottom: 16px;">快速编辑</h3>
    <form id="articleForm">
        <input type="hidden" name="id" id="articleId" value="0">
        <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">标题</label>
                <input type="text" name="title" id="articleTitle" required style="width: 280px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">分类</label>
                <select name="category" id="articleCategory" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
                    <?php foreach ($categoryMap as $k => $v): ?>
                    <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">排序</label>
                <input type="number" name="sort" id="articleSort" value="0" style="width: 90px; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">状态</label>
                <select name="status" id="articleStatus" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
                    <option value="1">启用</option>
                    <option value="0">禁用</option>
                </select>
            </div>
            <button type="submit" class="btn" id="saveBtn">保存</button>
            <button type="button" class="btn btn-outline" id="resetBtn" style="display: none;">取消</button>
        </div>
        <div style="margin-top: 12px;">
            <label style="display: block; margin-bottom: 6px; font-size: 13px; color: #64748B;">内容</label>
            <textarea name="content" id="articleContent" required rows="4" style="width: 100%; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;"></textarea>
        </div>
    </form>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/article'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="标题 / 内容">
        <select name="category" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px;">
            <option value="">全部分类</option>
            <?php foreach ($categoryMap as $k => $v): ?>
            <option value="<?php echo $k; ?>" <?php echo $category === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>标题</th>
            <th>分类</th>
            <th>排序</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="7" style="text-align: center; color: #64748B; padding: 40px;">暂无文章</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['title']); ?></td>
            <td><?php echo $categoryMap[$item['category']] ?? $item['category']; ?></td>
            <td><?php echo $item['sort']; ?></td>
            <td>
                <?php if ($item['status']): ?>
                <span class="tag tag-green">启用</span>
                <?php else: ?>
                <span class="tag">禁用</span>
                <?php endif; ?>
            </td>
            <td><?php echo $item['create_time']; ?></td>
            <td>
                <a href="javascript:;" class="btn btn-sm btn-outline" onclick="editArticle(<?php echo $item['id']; ?>, '<?php echo h(addslashes($item['title'])); ?>', '<?php echo $item['category']; ?>', <?php echo $item['sort']; ?>, <?php echo $item['status']; ?>, '<?php echo h(addslashes($item['content'])); ?>')">编辑</a>
                <?php if ($item['status']): ?>
                <a href="javascript:;" class="btn btn-sm btn-warning" onclick="toggleStatus(<?php echo $item['id']; ?>, 0)">禁用</a>
                <?php else: ?>
                <a href="javascript:;" class="btn btn-sm btn-success" onclick="toggleStatus(<?php echo $item['id']; ?>, 1)">启用</a>
                <?php endif; ?>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="deleteArticle(<?php echo $item['id']; ?>, '<?php echo h(addslashes($item['title'])); ?>')">删除</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('admin/article') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&category=' . $category; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('admin/article') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&category=' . $category; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
const form = document.getElementById('articleForm');
const saveBtn = document.getElementById('saveBtn');
const resetBtn = document.getElementById('resetBtn');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    saveBtn.disabled = true;
    saveBtn.textContent = '保存中...';
    const formData = new FormData(form);
    try {
        const res = await fetch('<?php echo url('admin/article/save'); ?>', { method: 'POST', body: formData });
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

function editArticle(id, title, category, sort, status, content) {
    document.getElementById('articleId').value = id;
    document.getElementById('articleTitle').value = title;
    document.getElementById('articleCategory').value = category;
    document.getElementById('articleSort').value = sort;
    document.getElementById('articleStatus').value = status;
    document.getElementById('articleContent').value = content;
    saveBtn.textContent = '更新';
    resetBtn.style.display = 'inline-block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

resetBtn.addEventListener('click', () => {
    form.reset();
    document.getElementById('articleId').value = 0;
    saveBtn.textContent = '保存';
    resetBtn.style.display = 'none';
});

async function toggleStatus(id, status) {
    const form = new FormData();
    form.append('id', id);
    form.append('status', status);
    const res = await fetch('<?php echo url('admin/article/status'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}

async function deleteArticle(id, title) {
    if (!confirm('确认删除文章「' + title + '」？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('admin/article/delete'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
