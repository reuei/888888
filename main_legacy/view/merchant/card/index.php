<div class="breadcrumb">商品管理 / 卡密管理</div>
<div class="page-header">
    <h2>卡密管理</h2>
    <div>
        <a href="<?php echo url('merchant/goods/import'); ?>" class="btn">批量导入卡密</a>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('merchant/card'); ?>">
        <select name="goods_id" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部卡密商品</option>
            <?php foreach ($goodsList as $g): ?>
            <option value="<?php echo $g['id']; ?>" <?php echo $goodsId === (int) $g['id'] ? 'selected' : ''; ?>><?php echo h($g['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" style="padding: 8px 12px; border: 1px solid #CBD5E1; border-radius: 6px; font-size: 14px;">
            <option value="">全部状态</option>
            <option value="0" <?php echo $status === '0' ? 'selected' : ''; ?>>未售出</option>
            <option value="1" <?php echo $status === '1' ? 'selected' : ''; ?>>已售出</option>
        </select>
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="卡密内容 / 订单号">
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>所属商品</th>
            <th>卡密内容</th>
            <th>状态</th>
            <th>订单号</th>
            <th>售出时间</th>
            <th>入库时间</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="8" style="text-align: center; color: #64748B; padding: 40px;">暂无卡密</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['goods_name'] ?? '-'); ?></td>
            <td><code><?php echo h($item['content']); ?></code></td>
            <td>
                <?php if ($item['status'] == 0): ?><span class="tag tag-green">未售出</span>
                <?php else: ?><span class="tag">已售出</span>
                <?php endif; ?>
            </td>
            <td><?php echo h($item['order_id'] ?: '-'); ?></td>
            <td><?php echo $item['sale_time'] ?: '-'; ?></td>
            <td><?php echo $item['create_time']; ?></td>
            <td>
                <?php if ($item['status'] == 0): ?>
                <a href="javascript:;" class="btn btn-sm btn-danger" onclick="deleteCard(<?php echo $item['id']; ?>, '<?php echo h(substr($item['content'], 0, 20)); ?>')">删除</a>
                <?php else: ?>
                -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <?php if ($totalPages > 1): ?>
    <div style="display: flex; justify-content: flex-end; gap: 8px; margin-top: 16px;">
        <?php if ($page > 1): ?>
        <a href="<?php echo url('merchant/card') . '?page=' . ($page - 1) . '&goods_id=' . $goodsId . '&status=' . $status . '&keyword=' . urlencode($keyword); ?>" class="btn btn-sm btn-outline">上一页</a>
        <?php endif; ?>
        <span style="padding: 5px 10px; color: #64748B; font-size: 13px;"><?php echo $page; ?> / <?php echo $totalPages; ?> 共 <?php echo $total; ?> 条</span>
        <?php if ($page < $totalPages): ?>
        <a href="<?php echo url('merchant/card') . '?page=' . ($page + 1) . '&goods_id=' . $goodsId . '&status=' . $status . '&keyword=' . urlencode($keyword); ?>" class="btn btn-sm btn-outline">下一页</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<script>
async function deleteCard(id, preview) {
    if (!confirm('确认删除卡密「' + preview + '...」？')) return;
    const form = new FormData();
    form.append('id', id);
    const res = await fetch('<?php echo url('merchant/card/delete'); ?>', { method: 'POST', body: form });
    const data = await res.json();
    alert(data.msg);
    if (data.code === 0) location.reload();
}
</script>
