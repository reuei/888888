<div class="user-layout">
    <!-- 侧边导航 -->
    <aside class="user-sidebar fade-in-up">
        <div class="user-side-header">
            <div class="user-side-avatar"><?php echo h(mb_substr($currentUser['nickname'] ?: $currentUser['username'], 0, 1)); ?></div>
            <div style="min-width:0;">
                <div style="font-weight:600;font-size:13px;color:#1e1b2e;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo h($currentUser['nickname'] ?: $currentUser['username']); ?></div>
                <div style="font-size:11px;color:#9ca3af;">个人中心</div>
            </div>
        </div>
        <a href="<?php echo url('user'); ?>" class="active">
            <i data-icon="dashboard"></i><span>个人中心</span>
        </a>
        <a href="<?php echo url('user/license'); ?>">
            <i data-icon="license"></i><span>我的授权</span>
        </a>
        <a href="<?php echo url('user/plugin'); ?>">
            <i data-icon="plugin"></i><span>我的插件</span>
        </a>
        <a href="<?php echo url('user/order'); ?>">
            <i data-icon="order"></i><span>我的订单</span>
        </a>
        <a href="<?php echo url('user/recharge'); ?>">
            <i data-icon="recharge"></i><span>余额充值</span>
        </a>
        <a href="<?php echo url('user/profile'); ?>">
            <i data-icon="user"></i><span>修改资料</span>
        </a>
        <a href="<?php echo url('user/password'); ?>">
            <i data-icon="key"></i><span>修改密码</span>
        </a>
    </aside>

    <!-- 内容区 -->
    <div class="user-content">
        <!-- 欢迎卡片 -->
        <div class="user-welcome fade-in-up">
            <h2>欢迎回来，<?php echo h($currentUser['nickname'] ?: $currentUser['username']); ?> 👋</h2>
            <p>在这里管理您的授权、插件与订单，开启高效的数字资产之旅</p>
        </div>

        <!-- 数据统计 -->
        <div class="grid-stats fade-in-up">
            <div class="stat-card">
                <div class="stat-icon purple"><i data-icon="license"></i></div>
                <div class="stat-value" data-count="<?php echo (int)$licenseCount; ?>">0</div>
                <div class="stat-label">我的授权</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i data-icon="plugin"></i></div>
                <div class="stat-value" data-count="<?php echo (int)$pluginCount; ?>">0</div>
                <div class="stat-label">我的插件</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon orange"><i data-icon="order"></i></div>
                <div class="stat-value" data-count="<?php echo (int)$orderCount; ?>">0</div>
                <div class="stat-label">我的订单</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i data-icon="dollar"></i></div>
                <div class="stat-value" data-count="<?php echo (float)$currentUser['balance']; ?>" data-prefix="<?php echo h(site_config('currency_unit', '¥')); ?>">0</div>
                <div class="stat-label">账户余额</div>
            </div>
        </div>

        <!-- 快捷操作 -->
        <div class="card fade-in-up">
            <div class="section-title">
                <span><i data-icon="cart" class="svg-icon-sm" style="vertical-align:-2px;margin-right:4px;"></i>快捷操作</span>
            </div>
            <div class="grid-features">
                <a class="feature-card" href="<?php echo url('product'); ?>">
                    <div class="feature-icon"><i data-icon="product"></i></div>
                    <h3>购买授权</h3>
                    <p>浏览并购买授权产品</p>
                </a>
                <a class="feature-card" href="<?php echo url('plugin'); ?>">
                    <div class="feature-icon"><i data-icon="plugin"></i></div>
                    <h3>获取插件</h3>
                    <p>探索插件市场</p>
                </a>
                <a class="feature-card" href="<?php echo url('user/recharge'); ?>">
                    <div class="feature-icon"><i data-icon="recharge"></i></div>
                    <h3>余额充值</h3>
                    <p>为账户充值</p>
                </a>
                <a class="feature-card" href="<?php echo url('user/license'); ?>">
                    <div class="feature-icon"><i data-icon="license"></i></div>
                    <h3>我的授权</h3>
                    <p>管理授权码</p>
                </a>
            </div>
        </div>
    </div>
</div>
