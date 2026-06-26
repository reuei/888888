<?php
require __DIR__ . '/includes/config.php';
if (template_include('company.php')) exit;
$pageTitle = __('page_company');
require __DIR__ . '/includes/header.php';
?>
<section class="page-banner">
    <div class="container">
        <h1><?php echo __('company_title') ?></h1>
        <p><?php echo __('company_sub') ?></p>
    </div>
</section>
<section class="section bg-white">
    <div class="container">
        <div style="max-width:900px;margin:0 auto">
            <div class="text-center">
                <div class="ip-illustration" style="width:120px;height:120px;margin-bottom:24px"><svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="10" width="32" height="32" rx="2"/><path d="M8 18h32"/><path d="M18 18v24"/><path d="M30 18v24"/><path d="M14 10V6h20v4"/><circle cx="22" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="26" cy="12" r="1" fill="currentColor" stroke="none"/></svg></div>
            </div>
            <h2 style="font-size:26px;margin-bottom:16px"><?php echo __('company_intro') ?></h2>
            <p style="color:var(--text-2);line-height:1.9;margin-bottom:28px">
                <?php echo e(setting('company_name','语云科技（美国）有限公司')) ?> <?php echo __('company_desc') ?>
            </p>
            <h3 style="margin:24px 0 12px"><?php echo __('history_title') ?></h3>
            <ul style="color:var(--text-2);line-height:2;padding-left:20px;margin-bottom:28px">
                <li><strong>2018</strong> — 语云科技在美国硅谷成立，开启云计算基础设施布局。</li>
                <li><strong>2020</strong> — 北京、青岛运营中心成立，服务中国本土企业客户。</li>
                <li><strong>2022</strong> — 全球节点突破 30 个，覆盖亚太、欧洲、北美及中东。</li>
                <li><strong>2024</strong> — 推出魔方财务正版授权服务，助力 IDC 与云服务商数字化转型。</li>
            </ul>
            <h3 style="margin:24px 0 12px"><?php echo __('culture_title') ?></h3>
            <p style="color:var(--text-2);line-height:1.9;margin-bottom:28px"><?php echo __('culture_desc') ?></p>
            <h3 style="margin:24px 0 12px"><?php echo __('advantages_title') ?></h3>
            <div class="card-grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr));margin-top:20px">
                <div class="product-card">
                    <div class="icon"><i class="iconfont icon-cloud icon-2xl"></i></div>
                    <h3><?php echo __('global_network') ?></h3>
                    <p><?php echo __('global_network_desc') ?></p>
                </div>
                <div class="product-card">
                    <div class="icon"><i class="iconfont icon-shield icon-2xl"></i></div>
                    <h3><?php echo __('security_protection') ?></h3>
                    <p><?php echo __('security_protection_desc') ?></p>
                </div>
                <div class="product-card">
                    <div class="icon"><i class="iconfont icon-headset icon-2xl"></i></div>
                    <h3><?php echo __('support_247') ?></h3>
                    <p><?php echo __('support_247_desc') ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
