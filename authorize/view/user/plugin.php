<div class="user-layout">
    <div class="user-sidebar">
        <a href="<?php echo url('user'); ?>">个人中心</a>
        <a href="<?php echo url('user/license'); ?>">我的授权</a>
        <a href="<?php echo url('user/plugin'); ?>" class="active">我的插件</a>
        <a href="<?php echo url('user/order'); ?>">我的订单</a>
        <a href="<?php echo url('user/recharge'); ?>">余额充值</a>
        <a href="<?php echo url('user/profile'); ?>">修改资料</a>
        <a href="<?php echo url('user/password'); ?>">修改密码</a>
    </div>
    <div class="user-content">
        <div class="card">
            <div class="section-title">
                <span>我的插件</span>
                <a href="<?php echo url('user/pluginUpload'); ?>" class="btn btn-sm">上传插件</a>
            </div>
            <?php if (empty($list)): ?>
            <div class="empty-tip">暂无插件</div>
            <?php else: ?>
            <table>
                <thead>
                    <tr><th>插件名称</th><th>版本</th><th>MD5</th><th>购买时间</th><th>操作</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $item): ?>
                    <tr>
                        <td><?php echo h($item['name']); ?></td>
                        <td><?php echo h($item['version']); ?></td>
                        <td><?php echo h($item['file_md5']); ?></td>
                        <td><?php echo $item['create_time']; ?></td>
                        <td>
                            <a class="btn btn-sm" href="<?php echo base_url($item['file_path']); ?>" download>下载</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php echo pagination($total, $page, 10, url('user/plugin', ['page' => '{page}'])); ?>
            <?php endif; ?>
        </div>
    </div>
</div>
