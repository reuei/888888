<?php
require __DIR__ . '/includes/config.php';
if (template_include('company.php')) exit;
$pageTitle = '公司简介';
require __DIR__ . '/includes/header.php';
?>
<section class="page-banner">
    <div class="container">
        <h1>公司简介</h1>
        <p>发展历程 · 企业文化 · 核心优势</p>
    </div>
</section>
<section class="section bg-white">
    <div class="container">
        <div style="max-width:900px;margin:0 auto">
            <h2 style="font-size:26px;margin-bottom:16px">公司介绍</h2>
            <p style="color:var(--text-2);line-height:1.9;margin-bottom:28px">
                <?php echo e(setting('company_name','语云科技（美国）有限公司')) ?> 是一家全球化云计算与数字化服务提供商。我们依托自建数据中心与全球合作伙伴网络，为企业客户提供云服务器、裸金属、CDN 加速、DDoS 防护、企业邮箱以及 IDC 财务系统授权等一站式解决方案。
            </p>
            <h3 style="margin:24px 0 12px">发展历程</h3>
            <ul style="color:var(--text-2);line-height:2;padding-left:20px;margin-bottom:28px">
                <li><strong>2018</strong> — 语云科技在美国硅谷成立，开启云计算基础设施布局。</li>
                <li><strong>2020</strong> — 北京、青岛运营中心成立，服务中国本土企业客户。</li>
                <li><strong>2022</strong> — 全球节点突破 30 个，覆盖亚太、欧洲、北美及中东。</li>
                <li><strong>2024</strong> — 推出魔方财务正版授权服务，助力 IDC 与云服务商数字化转型。</li>
            </ul>
            <h3 style="margin:24px 0 12px">企业文化</h3>
            <p style="color:var(--text-2);line-height:1.9;margin-bottom:28px">
                我们坚持“客户为先、技术驱动、全球协作”的价值观，致力于让每一家企业都能享受到稳定、安全、普惠的云计算能力。
            </p>
            <h3 style="margin:24px 0 12px">核心优势</h3>
            <div class="card-grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr));margin-top:20px">
                <div class="product-card">
                    <div class="icon"><i class="fa-solid fa-network-wired"></i></div>
                    <h3>全球网络</h3>
                    <p>多地域、多运营商骨干网络，智能调度。</p>
                </div>
                <div class="product-card">
                    <div class="icon"><i class="fa-solid fa-shield-virus"></i></div>
                    <h3>安全防护</h3>
                    <p>T 级 DDoS 清洗与 Web 应用防火墙。</p>
                </div>
                <div class="product-card">
                    <div class="icon"><i class="fa-solid fa-headset"></i></div>
                    <h3>7×24 支持</h3>
                    <p>专业技术团队全天候响应。</p>
                </div>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
