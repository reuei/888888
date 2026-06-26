<div class="breadcrumb">商品管理 / 商品列表</div>
<div class="page-header">
    <h2>商品列表</h2>
    <div>
        <a href="<?php echo url('merchant/goods/import'); ?>" class="btn btn-outline">批量导入卡密</a>
        <a href="<?php echo url('merchant/goods/create'); ?>" class="btn" style="margin-left: 8px;">新增商品</a>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('merchant/goods'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="搜索商品名称">
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>商品ID</th>
            <th>商品名称</th>
            <th>分类</th>
            <th>售价</th>
            <th>库存</th>
            <th>销量</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?php if (empty($goods)): ?>
        <tr><td colspan="8" style="text-align: center; color: #64748B; padding: 40px;">暂无商品，请先新增商品</td></tr>
        <?php else: ?>
        <?php foreach ($goods as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td><?php echo h($item['name']); ?></td>
            <td><?php echo $item['category_id']; ?></td>
            <td>¥ <?php echo $item['price']; ?></td>
            <td><?php echo $item['stock']; ?></td>
            <td><?php echo $item['sold']; ?></td>
            <td>
                <?php if ($item['status'] == 1): ?>
                <span class="tag tag-green">上架中</span>
                <?php else: ?>
                <span class="tag">已下架</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="#" class="btn btn-sm btn-primary">编辑</a>
                <a href="#" class="btn btn-sm">卡密</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>
