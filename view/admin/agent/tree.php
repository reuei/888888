<div class="breadcrumb">代理分销 / 代理树</div>
<div class="page-header">
    <h2>代理树</h2>
</div>

<div class="card" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 16px;">
    <div style="text-align: center;">
        <div style="font-size: 24px; font-weight: 600; color: #2563EB;"><?php echo $stats['total'] ?? 0; ?></div>
        <div style="color: #64748B; font-size: 13px;">代理总数</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 24px; font-weight: 600; color: #059669;">¥ <?php echo $stats['total_commission'] ?? '0.00'; ?></div>
        <div style="color: #64748B; font-size: 13px;">累计佣金</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 24px; font-weight: 600; color: #D97706;">¥ <?php echo $stats['pending_commission'] ?? '0.00'; ?></div>
        <div style="color: #64748B; font-size: 13px;">待结算佣金</div>
    </div>
    <div style="text-align: center;">
        <div style="font-size: 24px; font-weight: 600; color: #2563EB;">¥ <?php echo $stats['settled_commission'] ?? '0.00'; ?></div>
        <div style="color: #64748B; font-size: 13px;">已结算佣金</div>
    </div>
</div>

<div class="card">
    <form class="search-bar" method="get" action="<?php echo url('admin/agent/tree'); ?>">
        <input type="text" name="keyword" value="<?php echo h($keyword); ?>" placeholder="昵称 / 手机号 / 邀请码">
        <button type="submit" class="btn">搜索</button>
    </form>

    <table>
        <tr>
            <th>代理ID</th>
            <th>用户信息</th>
            <th>层级</th>
            <th>上级</th>
            <th>下级数量</th>
            <th>累计佣金</th>
            <th>待结算</th>
            <th>已结算</th>
            <th>邀请码</th>
            <th>操作</th>
        </tr>
        <?php if (empty($list)): ?>
        <tr><td colspan="10" style="text-align: center; color: #64748B; padding: 40px;">暂无代理数据</td></tr>
        <?php else: ?>
        <?php foreach ($list as $item): ?>
        <tr>
            <td><?php echo $item['id']; ?></td>
            <td>
                <div style="font-weight: 500;"><?php echo h($item['nickname'] ?? '-'); ?></div>
                <div style="color: #94A3B8; font-size: 12px;"><?php echo h($item['mobile'] ?? '-'); ?></div>
            </td>
            <td>第 <?php echo $item['level']; ?> 级</td>
            <td><?php echo h($item['parent_name'] ?? '无'); ?></td>
            <td><?php echo $item['child_count']; ?></td>
            <td>¥ <?php echo $item['total_commission']; ?></td>
            <td style="color: #D97706;">¥ <?php echo $item['pending_commission']; ?></td>
            <td style="color: #059669;">¥ <?php echo $item['settled_commission']; ?></td>
            <td><span class="tag tag-blue"><?php echo h($item['invite_code'] ?: '-'); ?></span></td>
            <td>
                <a href="javascript:;" class="btn btn-sm" onclick="showPath('<?php echo h($item['path']); ?>')">路径</a>
                <a href="<?php echo url('admin/agent/settle') . '?keyword=' . urlencode($item['nickname']); ?>" class="btn btn-sm btn-outline">结算</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<script>
function showPath(path) {
    alert('代理路径：' + (path || '0'));
}
</script>
