<div class="breadcrumb">商品管理 / 全平台商品列表</div>
<div class="page-header">
    <h2>全平台商品列表</h2>
    <div>
        <a href="#" class="btn btn-outline">批量下架</a>
        <a href="#" class="btn" style="margin-left: 8px;">违规下架</a>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/goods'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="搜索商品名称 / ID / 商户">
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>商品ID</th>
            <th>商品名称</th>
            <th>所属商户</th>
            <th>售价</th>
            <th>库存</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php if (empty($goods)): ?>
        <tr><td colspan="7" style="text-align: center; color: #64748B; padding: 40px;">暂无商品数据</td></tr>
        <?php else: ?>
        <?php foreach ($goods as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['name']); ?></td>
            <td><?php echo h($item['shop_name'] ?? '-'); ?></td>
            <td>¥ <?php echo $item['price']; ?></td>
            <td><?php echo $item['stock']; ?></td>
            <td>
                <?php if ($item['status'] == 1): ?>
                <span class="tag tag-green">上架中</span>
                <?php elseif ($item['status'] == 0): ?>
                <span class="tag">已下架</span>
                <?php else: ?>
                <span class="tag tag-red">违规下架</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="#" class="btn btn-sm">详情</a>
                <a href="#" class="btn btn-sm btn-warning">下架</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>
