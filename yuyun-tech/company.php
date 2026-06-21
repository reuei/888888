<?php
/**
 * 公司简介
 */
$currentPage = 'company';
$pageTitle = '公司简介';
require_once 'includes/header.php';
$certificates = $site_data['certificates'] ?? [];
?>

<div class="page-banner">
    <div class="container">
        <h1>公司简介</h1>
        <p>了解语云科技的发展历程、业务范围与成就</p>
        <div class="breadcrumbs">
            <a href="index.php">首页</a>
            <span> / </span>
            <span>公司简介</span>
        </div>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="content-grid">
            <div class="main-content">
                <h2>语云科技简介</h2>
                <p>语云科技是一家专注于云计算基础设施和相关服务的全球化科技公司。自2014年成立以来，我们始终以"让云计算更简单"为使命，通过持续的技术创新和服务优化，为各行业客户提供可靠、安全、高效的云服务。</p>
                <p>公司总部位于美国，并在中国青岛设有区域总部。目前，我们在中东、欧洲、俄罗斯、韩国、东南亚、澳大利亚以及北美等地区均设有数据中心节点，服务客户遍及全球。</p>

                <h3>发展历程</h3>
                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-rocket"></i></div>
                        <div><h4>2014年 - 公司成立</h4><p>语云科技在美国成立，开始提供基础云服务</p></div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-globe"></i></div>
                        <div><h4>2016年 - 中国市场</h4><p>进入中国市场，在青岛设立区域总部</p></div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-server"></i></div>
                        <div><h4>2018年 - 全球布局</h4><p>在欧洲、中东、东南亚部署数据中心节点</p></div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-trophy"></i></div>
                        <div><h4>2020年 - 行业认可</h4><p>获得多项行业认证，服务客户突破20000家</p></div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-flag"></i></div>
                        <div><h4>2022年 - 扩展北美</h4><p>在纽约、华盛顿、旧金山设立数据中心</p></div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-star"></i></div>
                        <div><h4>2024年 - 持续创新</h4><p>服务企业客户突破50000家，推出AI云服务</p></div>
                    </div>
                </div>

                <h3>业务范围</h3>
                <p>语云科技提供完整的云计算产品和服务，主要包括：</p>
                <ul>
                    <li><strong>云服务器（ECS）</strong>：弹性可扩展的计算服务，按需付费</li>
                    <li><strong>云数据库（RDS）</strong>：支持多种数据库引擎的高可用服务</li>
                    <li><strong>CDN加速</strong>：全球节点加速服务，提升网站访问速度</li>
                    <li><strong>对象存储（OSS）</strong>：海量、安全、低成本的对象存储服务</li>
                    <li><strong>SSL证书服务</strong>：一站式SSL/TLS证书申请与部署</li>
                    <li><strong>企业邮箱</strong>：专业的企业级邮箱服务</li>
                    <li><strong>安全防护</strong>：DDoS防护、WAF、主机安全等安全服务</li>
                    <li><strong>AI智能服务</strong>：机器学习、深度学习训练服务</li>
                </ul>

                <h3>资质与荣誉</h3>
                <p>语云科技已获得多项权威机构的认证和荣誉，包括：</p>
                <div class="certs-grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));margin-top:24px;">
                    <?php foreach ($certificates as $cert): ?>
                    <div class="cert-card">
                        <div class="cert-thumb">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h4><?php echo htmlspecialchars($cert['name'] ?? ''); ?></h4>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside class="sidebar">
                <div class="sidebar-box">
                    <h4><i class="fas fa-building" style="color:#1a73e8;"></i> 公司概况</h4>
                    <ul class="contact-list">
                        <li><i class="fas fa-signature"></i> 公司名称：语云科技美国有限公司</li>
                        <li><i class="fas fa-briefcase"></i> 公司性质：外商独资</li>
                        <li><i class="fas fa-calendar"></i> 成立时间：2014年</li>
                        <li><i class="fas fa-user-tie"></i> 员工规模：500+</li>
                        <li><i class="fas fa-flag"></i> 总部：美国</li>
                        <li><i class="fas fa-map-marker"></i> 中国区总部：青岛</li>
                        <li><i class="fas fa-globe"></i> 数据中心：15+地区</li>
                    </ul>
                </div>

                <div class="sidebar-box">
                    <h4><i class="fas fa-phone-alt" style="color:#ff6b35;"></i> 联系我们</h4>
                    <ul class="contact-list">
                        <li><i class="fas fa-phone-alt"></i> 400-800-8541</li>
                        <li><i class="fas fa-envelope"></i> sales@yuyun-tech.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> 青岛市市南区</li>
                    </ul>
                    <a href="contact.php" class="btn btn-primary btn-block" style="margin-top:16px;">立即咨询</a>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
