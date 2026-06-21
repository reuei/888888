<?php
/**
 * 联系我们
 */
$currentPage = 'contact';
$pageTitle = '联系我们';
require_once 'includes/header.php';
$contact = $site_data['contact'] ?? [];
?>

<div class="page-banner">
    <div class="container">
        <h1>联系我们</h1>
        <p>专业的团队随时为您提供服务</p>
        <div class="breadcrumbs">
            <a href="index.php">首页</a>
            <span> / </span>
            <span>联系我们</span>
        </div>
    </div>
</div>

<section class="content-section">
    <div class="container">
        <div class="contact-grid">
            <!-- 联系信息 -->
            <div class="contact-info-box fade-in">
                <h3>联系信息</h3>
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fas fa-phone-alt"></i></div>
                    <div class="contact-item-content">
                        <strong>销售热线</strong>
                        <span>400-800-8541</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fas fa-headset"></i></div>
                    <div class="contact-item-content">
                        <strong>技术支持</strong>
                        <span>400-800-8541 转 2</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fas fa-envelope"></i></div>
                    <div class="contact-item-content">
                        <strong>商务邮箱</strong>
                        <span>sales@yuyun-tech.com</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="contact-item-content">
                        <strong>公司地址</strong>
                        <span>中国·青岛市市南区语云科技大厦</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fab fa-qq"></i></div>
                    <div class="contact-item-content">
                        <strong>QQ咨询</strong>
                        <span>800888888</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fab fa-weixin"></i></div>
                    <div class="contact-item-content">
                        <strong>微信咨询</strong>
                        <span>yuyun_tech</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon"><i class="fas fa-users"></i></div>
                    <div class="contact-item-content">
                        <strong>官方群聊</strong>
                        <span>QQ群：123456789</span>
                    </div>
                </div>
            </div>

            <!-- 留言表单 -->
            <div class="form-box fade-in">
                <h3 style="font-size:22px;font-weight:700;color:#1a1a2e;margin-bottom:8px;">在线咨询</h3>
                <p style="color:#6b6b6b;margin-bottom:24px;">填写以下信息，我们的顾问将尽快与您联系</p>
                <form class="ajax-form" method="post">
                    <div class="form-group">
                        <label class="form-label">您的姓名<span class="required">*</span></label>
                        <input type="text" class="form-input" name="name" required placeholder="请输入您的姓名">
                    </div>
                    <div class="form-group">
                        <label class="form-label">联系电话<span class="required">*</span></label>
                        <input type="tel" class="form-input" name="phone" required placeholder="请输入您的电话">
                    </div>
                    <div class="form-group">
                        <label class="form-label">电子邮箱</label>
                        <input type="email" class="form-input" name="email" placeholder="请输入您的邮箱">
                    </div>
                    <div class="form-group">
                        <label class="form-label">咨询内容<span class="required">*</span></label>
                        <textarea class="form-textarea" name="message" required placeholder="请简要描述您的需求" rows="5"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-paper-plane"></i> 提交咨询
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- 服务区域 -->
<section class="section section-alt">
    <div class="container">
        <div class="section-title fade-in">
            <h2>全球服务区域</h2>
            <p>我们在全球多个地区提供服务</p>
        </div>
        <div class="locations-info fade-in" style="grid-template-columns:repeat(auto-fit,minmax(240px,1fr));">
            <div class="location-card" style="background:#fff;border:1px solid #e9ecef;">
                <h4><i class="fas fa-map-marker-alt" style="color:#ff6b35;"></i>亚太地区</h4>
                <p>中国·青岛、北京、上海、深圳<br>韩国·首尔<br>新加坡</p>
            </div>
            <div class="location-card" style="background:#fff;border:1px solid #e9ecef;">
                <h4><i class="fas fa-map-marker-alt" style="color:#ff6b35;"></i>欧洲地区</h4>
                <p>英国·伦敦<br>法国·巴黎<br>德国·法兰克福</p>
            </div>
            <div class="location-card" style="background:#fff;border:1px solid #e9ecef;">
                <h4><i class="fas fa-map-marker-alt" style="color:#ff6b35;"></i>北美地区</h4>
                <p>美国·纽约<br>美国·华盛顿<br>美国·旧金山</p>
            </div>
            <div class="location-card" style="background:#fff;border:1px solid #e9ecef;">
                <h4><i class="fas fa-map-marker-alt" style="color:#ff6b35;"></i>其他区域</h4>
                <p>中东·迪拜、利雅得<br>俄罗斯·莫斯科、圣彼得堡<br>澳大利亚·悉尼</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
