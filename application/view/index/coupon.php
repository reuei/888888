<style>
.coupon-hero {
    text-align: center;
    padding: 40px 20px;
    background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%);
    color: #fff;
    border-radius: 12px;
    margin-bottom: 24px;
}
.coupon-hero h1 { font-size: 28px; margin-bottom: 10px; }
.coupon-hero p { opacity: 0.9; font-size: 14px; }
.contact-box {
    max-width: 420px;
    margin: 0 auto 24px;
    display: flex;
    gap: 10px;
}
.contact-box input { flex: 1; }
.coupon-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
}
.coupon-card {
    background: #fff;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    position: relative;
}
.coupon-card::before {
    content: '';
    position: absolute;
    left: -8px;
    top: 50%;
    width: 16px;
    height: 16px;
    background: #F8FAFC;
    border-radius: 50%;
    border-right: 1px solid #E2E8F0;
}
.coupon-card::after {
    content: '';
    position: absolute;
    right: -8px;
    top: 50%;
    width: 16px;
    height: 16px;
    background: #F8FAFC;
    border-radius: 50%;
    border-left: 1px solid #E2E8F0;
}
.coupon-head {
    background: #EFF6FF;
    padding: 20px;
    text-align: center;
    border-bottom: 1px dashed #CBD5E1;
}
.coupon-amount { font-size: 32px; color: #2563EB; font-weight: 700; }
.coupon-amount small { font-size: 14px; }
.coupon-type {
    display: inline-block;
    margin-top: 8px;
    padding: 2px 10px;
    background: #2563EB;
    color: #fff;
    border-radius: 12px;
    font-size: 12px;
}
.coupon-body {
    padding: 16px;
    flex: 1;
}
.coupon-name { font-size: 16px; font-weight: 600; margin-bottom: 8px; }
.coupon-meta { color: #64748B; font-size: 13px; line-height: 1.8; }
.coupon-foot {
    padding: 12px 16px;
    border-top: 1px solid #F1F5F9;
}
.my-coupon {
    background: #F0FDF4;
    border: 1px solid #BBF7D0;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.my-coupon .info { font-size: 13px; }
.my-coupon .code {
    font-family: monospace;
    font-weight: 600;
    color: #059669;
    background: #fff;
    padding: 4px 10px;
    border-radius: 4px;
    border: 1px solid #BBF7D0;
}
</style>

<div class="coupon-hero">
    <h1>领券中心</h1>
    <p>先领券再下单，优惠多多</p>
</div>

<div class="card">
    <div class="contact-box">
        <input type="text" id="contactInput" placeholder="输入手机号 / QQ / 邮箱查询已领优惠券" value="<?php echo h($contact); ?>">
        <button type="button" class="btn" onclick="queryMine()">查询</button>
    </div>

    <?php if (!empty($myCoupons)): ?>
    <div class="section-title" style="margin-top: 0;">
        <span>我的优惠券</span>
    </div>
    <?php foreach ($myCoupons as $uc): ?>
    <div class="my-coupon">
        <div class="info">
            <div style="font-weight: 500;"><?php echo h($uc['coupon_name']); ?></div>
            <div style="color: #64748B; font-size: 12px;">
                <?php if ($uc['type'] == 2): ?>折扣 <?php echo ($uc['amount'] * 100) . '%'; ?><?php else: ?>¥<?php echo $uc['amount']; ?><?php endif; ?>
                · 满 ¥<?php echo $uc['min_amount']; ?>
                · <?php echo $uc['expire_time'] ? '有效期至 ' . $uc['expire_time'] : '长期有效'; ?>
            </div>
        </div>
        <span class="code"><?php echo h($uc['coupon_code']); ?></span>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <div class="section-title">
        <span>可领取优惠券</span>
    </div>

    <?php if (empty($list)): ?>
    <div class="empty-tip">暂无可用优惠券</div>
    <?php else: ?>
    <div class="coupon-grid">
        <?php foreach ($list as $item): ?>
        <div class="coupon-card" data-id="<?php echo $item['id']; ?>">
            <div class="coupon-head">
                <div class="coupon-amount">
                    <?php if ($item['type'] == 2): ?>
                    <?php echo ($item['amount'] * 100); ?><small>%</small>
                    <?php else: ?>
                    <small>¥</small><?php echo $item['amount']; ?>
                    <?php endif; ?>
                </div>
                <span class="coupon-type">
                    <?php if ($item['type'] == 1): ?>满减券<?php elseif ($item['type'] == 2): ?>折扣券<?php else: ?>固定券<?php endif; ?>
                </span>
            </div>
            <div class="coupon-body">
                <div class="coupon-name"><?php echo h($item['name']); ?></div>
                <div class="coupon-meta">
                    <div>满 ¥<?php echo $item['min_amount']; ?> 可用</div>
                    <div>剩余 <?php echo $item['total_count'] > 0 ? ($item['total_count'] - $item['receive_count']) : '不限'; ?> 张</div>
                    <div>每人限领 <?php echo $item['limit_per_user']; ?> 张</div>
                    <div>有效期：<?php echo $item['start_time'] ? date('m-d H:i', strtotime($item['start_time'])) : '不限'; ?> 至 <?php echo $item['end_time'] ? date('m-d H:i', strtotime($item['end_time'])) : '不限'; ?></div>
                </div>
            </div>
            <div class="coupon-foot">
                <button type="button" class="btn btn-block receive-btn" data-id="<?php echo $item['id']; ?>" data-name="<?php echo h($item['name']); ?>">立即领取</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
function queryMine() {
    const contact = document.getElementById('contactInput').value.trim();
    if (!contact) {
        alert('请输入联系方式');
        return;
    }
    location.href = '<?php echo url('index/coupon'); ?>?contact=' + encodeURIComponent(contact);
}

document.querySelectorAll('.receive-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const contact = document.getElementById('contactInput').value.trim();
        if (!contact) {
            alert('请先在上方填写联系方式');
            document.getElementById('contactInput').focus();
            return;
        }
        const couponId = btn.dataset.id;
        const name = btn.dataset.name;
        btn.disabled = true;
        btn.textContent = '领取中...';
        const form = new FormData();
        form.append('coupon_id', couponId);
        form.append('contact', contact);
        try {
            const res = await fetch('<?php echo url('index/receiveCoupon'); ?>', { method: 'POST', body: form });
            const data = await res.json();
            if (data.code === 0) {
                alert('领取成功，券码：' + data.data.coupon_code + '\n请在下单时填写该券码使用');
                location.href = '<?php echo url('index/coupon'); ?>?contact=' + encodeURIComponent(contact);
            } else {
                alert(data.msg);
                btn.disabled = false;
                btn.textContent = '立即领取';
            }
        } catch (err) {
            alert('请求失败');
            btn.disabled = false;
            btn.textContent = '立即领取';
        }
    });
});
</script>
