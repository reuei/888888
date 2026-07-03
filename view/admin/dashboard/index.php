<?php
/**
 * 后台仪表盘（重写版）
 * 依赖 layout/admin.php 与 /static/css/style.css
 * 保留 $kpi 变量
 */
// 数值清洗（用于数字滚动动画；非数字则不启用动画）
$_num = function ($v) {
    $v = preg_replace('/[^0-9.]/', '', (string)$v);
    return is_numeric($v) ? $v : '';
};
?>
<div class="breadcrumb">
    <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-home"></use></svg>
    首页 / 仪表盘
</div>
<div class="page-header">
    <h2>仪表盘</h2>
    <div class="flex gap-3">
        <a href="#" class="btn btn-ghost btn-sm">
            <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-download"></use></svg>
            导出报表
        </a>
        <a href="<?php echo url('admin/order'); ?>" class="btn btn-sm">
            <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-search"></use></svg>
            快捷查单
        </a>
    </div>
</div>

<!-- 数据卡片 -->
<div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 16px; margin-bottom: 16px;">
    <div class="stat-card reveal" style="--accent:#2563EB; --accent-soft:#EFF6FF;">
        <div class="stat-icon"><svg class="icon icon-lg" aria-hidden="true"><use href="#icon-finance"></use></svg></div>
        <div class="stat-label">总交易额</div>
        <div class="stat-value">¥ <span data-count="<?php echo h($_num($kpi['total_amount'])); ?>" data-prefix="" data-decimals="2"><?php echo h($kpi['total_amount']); ?></span></div>
        <div class="stat-trend up">
            <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-trending-up"></use></svg>
            ↑ 12.5% 环比
        </div>
    </div>
    <div class="stat-card reveal" style="--accent:#10B981; --accent-soft:#ECFDF5;">
        <div class="stat-icon"><svg class="icon icon-lg" aria-hidden="true"><use href="#icon-order"></use></svg></div>
        <div class="stat-label">总订单量</div>
        <div class="stat-value"><span data-count="<?php echo h($_num($kpi['total_orders'])); ?>"><?php echo h($kpi['total_orders']); ?></span></div>
        <div class="stat-trend up">
            <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-trending-up"></use></svg>
            ↑ 8.3% 环比
        </div>
    </div>
    <div class="stat-card reveal" style="--accent:#F59E0B; --accent-soft:#FFFBEB;">
        <div class="stat-icon"><svg class="icon icon-lg" aria-hidden="true"><use href="#icon-payment"></use></svg></div>
        <div class="stat-label">平台抽成收入</div>
        <div class="stat-value">¥ <span data-count="<?php echo h($_num($kpi['platform_income'])); ?>" data-decimals="2"><?php echo h($kpi['platform_income']); ?></span></div>
        <div class="stat-trend down">
            <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-trending-down"></use></svg>
            ↓ 2.1% 环比
        </div>
    </div>
    <div class="stat-card reveal" style="--accent:#EF4444; --accent-soft:#FEF2F2;">
        <div class="stat-icon"><svg class="icon icon-lg" aria-hidden="true"><use href="#icon-user"></use></svg></div>
        <div class="stat-label">商户 / 用户</div>
        <div class="stat-value"><?php echo h($kpi['merchant_count']); ?> / <?php echo h($kpi['user_count']); ?></div>
        <div class="stat-trend" style="color: var(--text-muted);">
            <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-merchant"></use></svg>
            累计入驻
        </div>
    </div>
</div>

<!-- 图表 + Top 商家 -->
<div class="grid" style="grid-template-columns: 2fr 1fr; gap: 16px; align-items: start;">
    <div class="card reveal">
        <div class="card-header">
            <h3>交易趋势</h3>
            <div class="flex gap-2">
                <span class="tag tag-blue tag-dot">近 7 天</span>
                <span class="tag tag-dot">近 30 天</span>
            </div>
        </div>
        <div style="height: 280px; background: linear-gradient(180deg, #F8FAFC, #F1F5F9); border: 1px dashed var(--border); border-radius: var(--radius); display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--text-muted); gap: 12px; position: relative; overflow: hidden;">
            <svg class="icon icon-xl" aria-hidden="true" style="color: var(--primary-100); width:48px;height:48px;"><use href="#icon-stat"></use></svg>
            <span style="font-size:13px;">折线图占位（后续接入 ECharts 数据）</span>
            <!-- 装饰性柱形 -->
            <div style="display:flex; align-items:flex-end; gap:8px; height:90px; margin-top:8px; opacity:.6;">
                <div style="width:14px;height:40%;background:linear-gradient(180deg,#93C5FD,#2563EB);border-radius:4px 4px 0 0;"></div>
                <div style="width:14px;height:65%;background:linear-gradient(180deg,#93C5FD,#2563EB);border-radius:4px 4px 0 0;"></div>
                <div style="width:14px;height:50%;background:linear-gradient(180deg,#93C5FD,#2563EB);border-radius:4px 4px 0 0;"></div>
                <div style="width:14px;height:80%;background:linear-gradient(180deg,#93C5FD,#2563EB);border-radius:4px 4px 0 0;"></div>
                <div style="width:14px;height:60%;background:linear-gradient(180deg,#93C5FD,#2563EB);border-radius:4px 4px 0 0;"></div>
                <div style="width:14px;height:95%;background:linear-gradient(180deg,#93C5FD,#2563EB);border-radius:4px 4px 0 0;"></div>
                <div style="width:14px;height:72%;background:linear-gradient(180deg,#93C5FD,#2563EB);border-radius:4px 4px 0 0;"></div>
            </div>
        </div>
    </div>
    <div class="card reveal">
        <div class="card-header">
            <h3>商家交易额 Top 5</h3>
            <span class="tag tag-orange">榜单</span>
        </div>
        <div class="table-wrap" style="border:none;">
            <table>
                <thead><tr><th>店铺</th><th style="text-align:right;">交易额</th></tr></thead>
                <tbody>
                    <tr><td>极速卡密店</td><td style="text-align:right; font-weight:600;">¥ 128,400</td></tr>
                    <tr><td>游戏点卡专卖</td><td style="text-align:right; font-weight:600;">¥ 96,200</td></tr>
                    <tr><td>会员共享站</td><td style="text-align:right; font-weight:600;">¥ 74,500</td></tr>
                    <tr><td>软件授权中心</td><td style="text-align:right; font-weight:600;">¥ 62,100</td></tr>
                    <tr><td>影视会员店</td><td style="text-align:right; font-weight:600;">¥ 48,900</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 最近订单 + 快捷操作 -->
<div class="grid" style="grid-template-columns: 2fr 1fr; gap: 16px; margin-top: 16px; align-items: start;">
    <div class="card reveal">
        <div class="card-header">
            <h3>最近订单</h3>
            <a href="<?php echo url('admin/order'); ?>" class="section-more">
                全部订单
                <svg class="icon icon-sm" aria-hidden="true"><use href="#icon-arrow-right"></use></svg>
            </a>
        </div>
        <div class="table-wrap" style="border:none;">
            <table>
                <thead>
                    <tr>
                        <th>订单号</th>
                        <th>商品</th>
                        <th>金额</th>
                        <th>状态</th>
                        <th style="text-align:right;">时间</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#20260703001</td>
                        <td>Steam 充值卡 ¥100</td>
                        <td style="font-weight:600;">¥ 95.00</td>
                        <td><span class="tag tag-green tag-dot">已完成</span></td>
                        <td style="text-align:right; color:var(--text-muted);">2 分钟前</td>
                    </tr>
                    <tr>
                        <td>#20260703002</td>
                        <td>Netflix 月度会员</td>
                        <td style="font-weight:600;">¥ 38.00</td>
                        <td><span class="tag tag-blue tag-dot">已发货</span></td>
                        <td style="text-align:right; color:var(--text-muted);">8 分钟前</td>
                    </tr>
                    <tr>
                        <td>#20260703003</td>
                        <td>Office 365 订阅</td>
                        <td style="font-weight:600;">¥ 120.00</td>
                        <td><span class="tag tag-orange tag-dot">待支付</span></td>
                        <td style="text-align:right; color:var(--text-muted);">15 分钟前</td>
                    </tr>
                    <tr>
                        <td>#20260703004</td>
                        <td>Spotify 年卡</td>
                        <td style="font-weight:600;">¥ 78.00</td>
                        <td><span class="tag tag-green tag-dot">已完成</span></td>
                        <td style="text-align:right; color:var(--text-muted);">22 分钟前</td>
                    </tr>
                    <tr>
                        <td>#20260703005</td>
                        <td>ChatGPT Plus 月卡</td>
                        <td style="font-weight:600;">¥ 96.00</td>
                        <td><span class="tag tag-red tag-dot">已退款</span></td>
                        <td style="text-align:right; color:var(--text-muted);">35 分钟前</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card reveal">
        <div class="card-header"><h3>快捷操作</h3></div>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <a href="<?php echo url('admin/goods'); ?>" class="btn btn-ghost btn-sm" style="flex-direction:column; padding:14px 8px; gap:6px;">
                <svg class="icon icon-lg" aria-hidden="true" style="color:var(--primary)"><use href="#icon-goods"></use></svg>
                <span style="font-size:13px;">商品管理</span>
            </a>
            <a href="<?php echo url('admin/order'); ?>" class="btn btn-ghost btn-sm" style="flex-direction:column; padding:14px 8px; gap:6px;">
                <svg class="icon icon-lg" aria-hidden="true" style="color:var(--success)"><use href="#icon-order"></use></svg>
                <span style="font-size:13px;">订单管理</span>
            </a>
            <a href="<?php echo url('admin/merchant'); ?>" class="btn btn-ghost btn-sm" style="flex-direction:column; padding:14px 8px; gap:6px;">
                <svg class="icon icon-lg" aria-hidden="true" style="color:var(--warning)"><use href="#icon-merchant"></use></svg>
                <span style="font-size:13px;">商户管理</span>
            </a>
            <a href="<?php echo url('admin/finance/flow'); ?>" class="btn btn-ghost btn-sm" style="flex-direction:column; padding:14px 8px; gap:6px;">
                <svg class="icon icon-lg" aria-hidden="true" style="color:var(--info)"><use href="#icon-finance"></use></svg>
                <span style="font-size:13px;">资金流水</span>
            </a>
            <a href="<?php echo url('admin/article/create'); ?>" class="btn btn-ghost btn-sm" style="flex-direction:column; padding:14px 8px; gap:6px;">
                <svg class="icon icon-lg" aria-hidden="true" style="color:#8B5CF6"><use href="#icon-article"></use></svg>
                <span style="font-size:13px;">发布公告</span>
            </a>
            <a href="<?php echo url('admin/setting'); ?>" class="btn btn-ghost btn-sm" style="flex-direction:column; padding:14px 8px; gap:6px;">
                <svg class="icon icon-lg" aria-hidden="true" style="color:var(--text-muted)"><use href="#icon-setting"></use></svg>
                <span style="font-size:13px;">系统设置</span>
            </a>
        </div>
    </div>
</div>
