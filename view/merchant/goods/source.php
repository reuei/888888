<div class="breadcrumb">商品管理 / 货源广场</div>
<div class="page-header">
    <h2>货源广场</h2>
    <div>
        <a href="<?php echo url('merchant/goods'); ?>" class="btn btn-outline">返回商品列表</a>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('merchant/goods/source'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="搜索货源商品名称">
        <select name="category_id" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部分类</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?php echo $c['id']; ?>" <?php echo $categoryId === (string) $c['id'] ? 'selected' : ''; ?>><?php echo h($c['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>商品ID</th>
            <th>商品名称</th>
            <th>分类</th>
            <th>类型</th>
            <th>货源价</th>
            <th>销量</th>
            <th>提供商户</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="9" style="text-align: center; color: #64748B; padding: 40px;">暂无货源商品，您可以先将自有商品标记为货源</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['name']); ?></td>
            <td><?php echo h($item['category_name'] ?? '-'); ?></td>
            <td>
                <?php if ($item['type'] == 1): ?><span class="tag tag-blue">卡密</span>
                <?php elseif ($item['type'] == 2): ?><span class="tag tag-orange">人工</span>
                <?php else: ?><span class="tag tag-green">自动</span>
                <?php endif; ?>
            </td>
            <td>¥ <?php echo $item['price']; ?></td>
            <td><?php echo $item['sold']; ?></td>
            <td><?php echo h($item['shop_name'] ?? '-'); ?></td>
            <td>
                <?php if (in_array((int) $item['id'], $mySourceIds, true)): ?>
                <span class="tag tag-green">已对接</span>
                <?php else: ?>
                <span class="tag tag-orange">未对接</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if (in_array((int) $item['id'], $mySourceIds, true)): ?>
                <a href="<?php echo url('merchant/goods'); ?>" class="btn btn-sm btn-primary">去上架</a>
                <?php else: ?>
                <a href="javascript:;" class="btn btn-sm" onclick="openSourceModal(<?php echo $item['id']; ?>, '<?php echo h($item['name']); ?>', <?php echo $item['price']; ?>)">立即对接</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('merchant/goods/source') . '?page=' . ($page - 1) . '&keyword=' . urlencode($keyword) . '&category_id=' . $categoryId; ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('merchant/goods/source') . '?page=' . ($page + 1) . '&keyword=' . urlencode($keyword) . '&category_id=' . $categoryId; ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<div id="sourceModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 200; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: 8px; width: 420px; max-width: 90%; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <h3 style="font-size: 16px; margin-bottom: 16px;">对接货源：<span id="modalGoodsName"></span></h3>
        <div style="margin-bottom: 12px; color: #64748B; font-size: 13px;">
            货源成本价：<span style="color: #EF4444; font-weight: 600;">¥ <span id="modalSourcePrice"></span></span>
        </div>
        <form id="sourceForm">
            <input type="hidden" name="goods_id" id="modalGoodsId">
            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500;">您的销售售价</label>
                <input type="number" name="price" id="modalPrice" step="0.01" min="0.01" placeholder="请输入销售售价" style="width: 100%; padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="javascript:;" class="btn btn-outline" onclick="closeSourceModal()" style="background: #fff; color: #64748B; border-color: #CBD5E1;">取消</a>
                <button type="submit" class="btn" id="sourceSubmitBtn">确认对接</button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('sourceModal');

function openSourceModal(id, name, price) {
    document.getElementById('modalGoodsId').value = id;
    document.getElementById('modalGoodsName').textContent = name;
    document.getElementById('modalSourcePrice').textContent = price.toFixed(2);
    document.getElementById('modalPrice').value = '';
    modal.style.display = 'flex';
}

function closeSourceModal() {
    modal.style.display = 'none';
}

modal.addEventListener('click', (e) => {
    if (e.target === modal) closeSourceModal();
});

document.getElementById('sourceForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('sourceSubmitBtn');
    const form = e.target;
    const price = parseFloat(form.price.value);
    if (!price || price <= 0) {
        alert('请输入有效的销售售价');
        return;
    }

    btn.disabled = true;
    btn.textContent = '对接中...';
    const formData = new FormData(form);
    try {
        const res = await fetch('<?php echo url('merchant/goods/doSource'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) {
            location.reload();
        }
    } catch (err) {
        alert('请求失败');
    } finally {
        btn.disabled = false;
        btn.textContent = '确认对接';
    }
});
</script>
