<?php
/**
 * 关于我们
 */
$currentPage = 'about';
$pageTitle = '关于我们';
require_once 'includes/header.php';
$contact = $site_data['contact'] ?? [];
?>

<!-- 页面头部Banner -->
<div class="page-banner">
    <div class="container">
        <h1>关于语云科技</h1>
        <p>全球领先的云服务提供商，致力于为企业客户提供安全、稳定、高效的云计算解决方案</p>
        <div class="breadcrumbs">
            <a href="index.php">首页</a>
            <span> / </span>
            <span>关于我们</span>
        </div>
    </div>
</div>

<!-- 公司介绍 -->
<section class="content-section">
    <div class="container">
        <div class="content-grid">
            <div class="main-content">
                <h2>公司简介</h2>
                <p>语云科技（YuYun Tech）成立于2014年，是一家专注于云计算基础设施服务的全球化科技公司。我们致力于为企业客户提供安全、稳定、高效的云服务，助力企业数字化转型。</p>
                <p>作为全球领先的云服务提供商，语云科技在全球15+个地区部署了数据中心，服务超过50000家企业客户，服务可用性达到99.99%。我们拥有一支经验丰富、技术精湛的专业团队，为客户提供7x24小时专业支持。</p>

                <h3>我们的使命</h3>
                <p>以技术创新驱动企业数字化转型，让云计算惠及每一个组织。我们相信，优质的云服务应该是可靠、易用、经济的，让企业可以专注于核心业务的发展。</p>

                <h3>我们的愿景</h3>
                <p>成为全球最受信赖的云服务提供商，构建连接全球的数字基础设施。</p>

                <h3>核心价值观</h3>
                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                        <div>
                            <h4>安全第一</h4>
                            <p>将客户数据安全放在首位，构建完善的安全体系</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-award"></i></div>
                        <div>
                            <h4>品质至上</h4>
                            <p>追求卓越品质，为客户提供稳定可靠的服务</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-users"></i></div>
                        <div>
                            <h4>客户为中心</h4>
                            <p>以客户需求为导向，提供贴心的专业服务</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon"><i class="fas fa-lightbulb"></i></div>
                        <div>
                            <h4>持续创新</h4>
                            <p>不断创新技术，引领行业发展方向</p>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="sidebar">
                <div class="sidebar-box">
                    <h4><i class="fas fa-phone-alt" style="color:#ff6b35;"></i> 联系方式</h4>
                    <ul class="contact-list">
                        <li><i class="fas fa-phone-alt"></i> 销售热线：400-800-8541</li>
                        <li><i class="fas fa-envelope"></i> 邮箱：sales@yuyun-tech.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> 地址：青岛市市南区语云科技大厦</li>
                        <li><i class="fab fa-qq"></i> QQ：800888888</li>
                        <li><i class="fab fa-weixin"></i> 微信：yuyun_tech</li>
                        <li><i class="fas fa-users"></i> 官方群：123456789</li>
                    </ul>
                </div>

                <div class="sidebar-box">
                    <h4><i class="fas fa-info-circle" style="color:#1a73e8;"></i> 公司信息</h4>
                    <ul class="contact-list">
                        <li><i class="fas fa-calendar"></i> 成立时间：2014年</li>
                        <li><i class="fas fa-flag"></i> 总部：美国 · 中国青岛</li>
                        <li><i class="fas fa-user-tie"></i> 员工规模：500+</li>
                        <li><i class="fas fa-globe"></i> 数据中心：15+</li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- 公司地图 -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title fade-in">
            <h2>公司位置</h2>
            <p>欢迎莅临参观指导</p>
        </div>
        <div class="fade-in" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
            <div style="position:relative;padding-bottom:40%;min-height:300px;background:linear-gradient(135deg,#e3f0ff,#ffe8dc);">
                <iframe src="https://maps.google.com/maps?q=Qingdao&t=&z=10&ie=UTF8&iwloc=&output=embed" 
                        style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" 
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div style="padding:24px;display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;">
                <div><strong style="color:#1a73e8;">销售热线</strong><p style="margin-top:8px;color:#ff6b35;font-size:18px;font-weight:700;">400-800-8541</p></div>
                <div><strong style="color:#1a73e8;">公司地址</strong><p style="margin-top:8px;color:#4a4a4a;">中国·青岛市市南区语云科技大厦</p></div>
                <div><strong style="color:#1a73e8;">官方邮箱</strong><p style="margin-top:8px;color:#4a4a4a;">sales@yuyun-tech.com</p></div>
                <div><strong style="color:#1a73e8;">工作时间</strong><p style="margin-top:8px;color:#4a4a4a;">周一至周日 8:00-22:00</p></div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
