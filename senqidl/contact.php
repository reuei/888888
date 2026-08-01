<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$currentPage = 'contact';
$settings = DB::getSetting();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $messageText = trim($_POST['message'] ?? '');

    $errors = [];
    if (empty($name)) $errors[] = '请填写您的姓名';
    if (empty($phone)) $errors[] = '请填写您的电话';
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = '请输入有效的邮箱地址';
    if (empty($messageText)) $errors[] = '请填写留言内容';

    if (empty($errors)) {
        $contactData = [
            'id' => time(),
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'message' => $messageText,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'created_at' => date('Y-m-d H:i:s')
        ];

        $contactList = [];
        $contactFile = DATA_PATH . '/contact.json';
        if (file_exists($contactFile)) {
            $existing = json_decode(file_get_contents($contactFile), true);
            if (is_array($existing)) {
                $contactList = $existing;
            }
        }
        $contactList[] = $contactData;
        file_put_contents($contactFile, json_encode($contactList, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $message = '感谢您的留言！我们的顾问将尽快与您联系。';
        $messageType = 'success';
    } else {
        $message = implode('；', $errors);
        $messageType = 'error';
    }
}

$faqData = [
    ['q' => '你们提供哪些服务？', 'a' => '我们提供品牌网站建设、SEO优化、外贸营销、品牌设计、小程序开发、数字营销等全链路品牌数字化解决方案。'],
    ['q' => '项目周期一般是多久？', 'a' => '根据项目规模和复杂度不同，周期也会有所差异。一般来说，企业官网项目为2-4周，SEO优化项目为3-6个月，外贸营销项目为6-12个月。我们会在项目启动前提供详细的时间规划。'],
    ['q' => '你们的收费标准是怎样的？', 'a' => '我们根据客户的具体需求提供定制化报价。不同服务类型、项目规模、需求复杂度都会影响报价。欢迎通过电话或在线咨询获取详细报价。'],
    ['q' => '是否提供售后服务？', 'a' => '是的，所有项目均提供长期售后服务，包括技术支持、功能维护、数据监控等。我们承诺7x24小时响应客户需求。'],
    ['q' => '可以只选择部分服务吗？', 'a' => '当然可以。我们支持灵活的服务组合，您可以根据实际需求选择单项服务或组合服务。我们的顾问会根据您的情况提供最适合的方案。']
];

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="page-hero" style="background: linear-gradient(135deg, var(--bg-darker) 0%, var(--primary-dark) 50%, var(--primary) 100%); padding: 6rem 0 4rem; margin-top: var(--header-height); color: var(--white); position: relative; overflow: hidden;">
    <div style="position: absolute; inset: 0; background: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); opacity: 0.5;"></div>
    <div class="container" style="position: relative; z-index: 1;">
        <div style="text-align: center;">
            <h1 style="font-size: clamp(2rem, 5vw, 3rem); font-weight: 800; margin-bottom: 1rem; color: var(--white);">联系我们</h1>
            <p style="font-size: 1.1rem; color: rgba(255,255,255,0.9); margin-bottom: 1.5rem;">我们期待与您的合作</p>
            <div class="breadcrumbs" style="background: transparent; border: none; padding: 0;">
                <ul class="breadcrumbs-list" style="justify-content: center;">
                    <li><a href="<?php echo siteUrl(); ?>" style="color: rgba(255,255,255,0.8);">首页</a></li>
                    <li><span class="current" style="color: var(--white);">联系我们</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Contact Info Cards Section -->
<section class="section" style="background: var(--bg); padding: 3rem 0;">
    <div class="container">
        <div class="grid grid-3" style="gap: 2rem;">
            <div class="contact-info-card reveal" style="text-align: center; padding: 2.5rem 1.5rem; background: var(--surface); border-radius: var(--radius-lg); border: 1px solid var(--border-light); transition: var(--transition);">
                <div style="width: 72px; height: 72px; background: linear-gradient(135deg, rgba(26,95,255,0.1), rgba(0,212,255,0.1)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.75rem;">📞</div>
                <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--heading); margin-bottom: 0.75rem;">电话咨询</h4>
                <p style="color: var(--primary); font-weight: 600; font-size: 1.1rem; margin: 0 0 0.5rem;"><?php echo h($settings['contact_phone'] ?? ''); ?></p>
                <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">工作日 9:00 - 18:00</p>
            </div>
            <div class="contact-info-card reveal" style="text-align: center; padding: 2.5rem 1.5rem; background: var(--surface); border-radius: var(--radius-lg); border: 1px solid var(--border-light); transition: var(--transition);">
                <div style="width: 72px; height: 72px; background: linear-gradient(135deg, rgba(26,95,255,0.1), rgba(0,212,255,0.1)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.75rem;">✉️</div>
                <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--heading); margin-bottom: 0.75rem;">邮箱联系</h4>
                <p style="color: var(--primary); font-weight: 600; font-size: 1rem; margin: 0 0 0.5rem; word-break: break-all;"><?php echo h($settings['contact_email'] ?? ''); ?></p>
                <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">24小时内回复</p>
            </div>
            <div class="contact-info-card reveal" style="text-align: center; padding: 2.5rem 1.5rem; background: var(--surface); border-radius: var(--radius-lg); border: 1px solid var(--border-light); transition: var(--transition);">
                <div style="width: 72px; height: 72px; background: linear-gradient(135deg, rgba(26,95,255,0.1), rgba(0,212,255,0.1)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.75rem;">📍</div>
                <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--heading); margin-bottom: 0.75rem;">公司地址</h4>
                <p style="color: var(--primary); font-weight: 600; font-size: 1rem; margin: 0 0 0.5rem;"><?php echo h($settings['contact_address'] ?? ''); ?></p>
                <p style="color: var(--text-light); font-size: 0.9rem; margin: 0;">欢迎预约到访</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="section" style="background: var(--bg-alt);">
    <div class="container">
        <div class="contact-layout" style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: start;">
            <!-- Contact Info -->
            <div class="contact-info" style="background: linear-gradient(135deg, var(--primary-dark), var(--primary)); color: var(--white); padding: 2.5rem; border-radius: var(--radius-lg); height: 100%;">
                <h3 style="color: var(--white); font-size: 1.5rem; margin-bottom: 1rem;">联系方式</h3>
                <p style="color: rgba(255,255,255,0.9); margin-bottom: 2rem;">欢迎通过以下方式与我们联系，我们的顾问将竭诚为您服务。</p>
                <ul class="contact-info-list" style="list-style: none; padding: 0; margin: 0;">
                    <li style="display: flex; align-items: center; gap: 0.875rem; padding: 0.875rem 0; border-bottom: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.9);">
                        <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.15); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">📞</div>
                        <div>
                            <div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-bottom: 0.25rem;">电话</div>
                            <div style="font-weight: 600;"><?php echo h($settings['contact_phone'] ?? ''); ?></div>
                        </div>
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.875rem; padding: 0.875rem 0; border-bottom: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.9);">
                        <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.15); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">✉️</div>
                        <div>
                            <div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-bottom: 0.25rem;">邮箱</div>
                            <div style="font-weight: 600; word-break: break-all;"><?php echo h($settings['contact_email'] ?? ''); ?></div>
                        </div>
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.875rem; padding: 0.875rem 0; border-bottom: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.9);">
                        <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.15); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">📍</div>
                        <div>
                            <div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-bottom: 0.25rem;">地址</div>
                            <div style="font-weight: 600;"><?php echo h($settings['contact_address'] ?? ''); ?></div>
                        </div>
                    </li>
                    <li style="display: flex; align-items: center; gap: 0.875rem; padding: 0.875rem 0; color: rgba(255,255,255,0.9);">
                        <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.15); border-radius: var(--radius); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">🕐</div>
                        <div>
                            <div style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-bottom: 0.25rem;">工作时间</div>
                            <div style="font-weight: 600;">周一至周五 9:00 - 18:00</div>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Contact Form -->
            <div class="contact-form" style="background: var(--surface); padding: 2.5rem; border-radius: var(--radius-lg); box-shadow: var(--shadow);">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--heading); margin-bottom: 1rem;">在线留言</h3>
                <p style="color: var(--text-light); margin-bottom: 1.5rem;">请填写以下表单，我们会尽快与您联系。</p>

                <?php if ($message): ?>
                <div class="form-alert" style="padding: 1rem 1.25rem; border-radius: var(--radius); margin-bottom: 1.5rem; background: <?php echo $messageType === 'success' ? 'rgba(40,167,69,0.1)' : 'rgba(220,53,69,0.1)'; ?>; color: <?php echo $messageType === 'success' ? 'var(--success)' : 'var(--danger)'; ?>; border: 1px solid <?php echo $messageType === 'success' ? 'rgba(40,167,69,0.3)' : 'rgba(220,53,69,0.3)'; ?>;">
                    <?php echo h($message); ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo siteUrl('contact.php'); ?>" class="validate-form">
                    <div class="form-group">
                        <label class="form-label" for="name">姓名 <span class="required">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" required data-required placeholder="请输入您的姓名" value="<?php echo h($_POST['name'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">电话 <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" class="form-control" required data-required placeholder="请输入您的联系电话" value="<?php echo h($_POST['phone'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">邮箱</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="请输入您的邮箱地址" value="<?php echo h($_POST['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="message">留言 <span class="required">*</span></label>
                        <textarea id="message" name="message" class="form-control" rows="5" required data-required data-minlength="10" placeholder="请详细描述您的需求，以便我们为您提供更好的服务..."><?php echo h($_POST['message'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" name="contact_submit" class="btn btn-primary btn-lg btn-block" style="width: 100%;">提交咨询</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Map Placeholder Section -->
<section class="section" style="background: var(--bg); padding: 3rem 0;">
    <div class="container">
        <div style="border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--shadow-sm); background: var(--bg-alt);">
            <div id="map-placeholder" style="width: 100%; height: 400px; background: linear-gradient(135deg, var(--bg-alt), var(--bg)); display: flex; align-items: center; justify-content: center; position: relative;">
                <div style="text-align: center; color: var(--text-light);">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🗺️</div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--heading); margin-bottom: 0.5rem;">公司位置</h3>
                    <p style="font-size: 1rem; color: var(--text-light); margin: 0;"><?php echo h($settings['contact_address'] ?? '中国·深圳'); ?></p>
                    <p style="font-size: 0.9rem; color: var(--text-muted); margin-top: 0.5rem;">（地图位置预留，可后续接入地图API）</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section" style="background: var(--bg-alt);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">常见问题</h2>
            <p class="section-subtitle">以下是客户常问的问题，如有其他疑问欢迎联系我们</p>
            <div class="section-divider"></div>
        </div>
        <div class="faq-list" style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach ($faqData as $index => $faq): ?>
            <div class="faq-item reveal" style="background: var(--surface); border-radius: var(--radius-lg); border: 1px solid var(--border-light); overflow: hidden;">
                <button class="faq-question" style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 1.5rem; background: none; border: none; cursor: pointer; text-align: left; font-size: 1.05rem; font-weight: 600; color: var(--heading); font-family: inherit;">
                    <span><?php echo h($faq['q']); ?></span>
                    <span class="faq-icon" style="width: 28px; height: 28px; background: var(--bg-alt); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; color: var(--primary); transition: transform 0.3s ease;">+</span>
                </button>
                <div class="faq-answer" style="padding: 0 1.5rem; max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.4s ease;">
                    <p style="color: var(--text-light); line-height: 1.8; padding-bottom: 1.25rem; margin: 0;"><?php echo h($faq['a']); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
document.querySelectorAll('.faq-question').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var item = btn.parentElement;
        var answer = item.querySelector('.faq-answer');
        var icon = btn.querySelector('.faq-icon');
        var isOpen = answer.style.maxHeight && answer.style.maxHeight !== '0px';
        
        if (isOpen) {
            answer.style.maxHeight = '0';
            answer.style.paddingTop = '0';
            answer.style.paddingBottom = '0';
            icon.textContent = '+';
        } else {
            answer.style.maxHeight = answer.scrollHeight + 'px';
            answer.style.paddingTop = '0';
            answer.style.paddingBottom = '1.25rem';
            icon.textContent = '−';
        }
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>