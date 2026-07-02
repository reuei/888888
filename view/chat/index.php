<div class="card" style="max-width: 720px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #E2E8F0;">
        <div>
            <h2 style="font-size: 18px;">在线客服</h2>
            <?php if ($merchant): ?>
                <p style="font-size: 13px; color: #64748B; margin-top: 4px;">咨询商户：<?php echo h($merchant['shop_name']); ?></p>
            <?php else: ?>
                <p style="font-size: 13px; color: #64748B; margin-top: 4px;">请选择一个商户进行咨询</p>
            <?php endif; ?>
        </div>
        <?php if ($session && $session['status'] == 0): ?>
            <span class="tag tag-orange">会话已关闭</span>
        <?php endif; ?>
    </div>

    <?php if (!$merchantId): ?>
        <div style="text-align: center; padding: 40px; color: #64748B;">
            <p>未指定咨询商户，请从商品详情页或商户店铺页进入客服。</p>
        </div>
    <?php else: ?>
        <div id="chatBox" style="height: 420px; overflow-y: auto; border: 1px solid #E2E8F0; border-radius: 8px; padding: 16px; background: #F8FAFC; margin-bottom: 16px;">
            <?php if (empty($messages)): ?>
                <div style="text-align: center; color: #94A3B8; padding-top: 80px;">
                    <p>您好，有什么可以帮您？</p>
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                <?php $msgAlign = $msg['sender_type'] == 0 ? 'justify-content: flex-end;' : 'justify-content: flex-start;'; ?>
                <div style="margin-bottom: 12px; display: flex; <?php echo $msgAlign; ?>">
                    <?php
                        if ($msg['sender_type'] == 0) {
                            $bubbleStyle = 'background: #2563EB; color: #fff; border-bottom-right-radius: 2px;';
                        } elseif ($msg['sender_type'] == 2) {
                            $bubbleStyle = 'background: #FEF3C7; color: #92400E;';
                        } else {
                            $bubbleStyle = 'background: #fff; color: #1F2937; border: 1px solid #E2E8F0; border-bottom-left-radius: 2px;';
                        }
                    ?>
                    <div style="max-width: 75%; padding: 10px 14px; border-radius: 8px; font-size: 14px; line-height: 1.5; <?php echo $bubbleStyle; ?>">
                        <div><?php echo nl2br(h($msg['content'])); ?></div>
                        <div style="font-size: 12px; opacity: 0.8; margin-top: 4px; text-align: right;"><?php echo h($msg['create_time']); ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!$session || $session['status'] == 1): ?>
        <form id="chatForm" style="display: flex; flex-direction: column; gap: 12px;">
            <input type="hidden" name="merchant_id" value="<?php echo $merchantId; ?>">
            <input type="hidden" name="session_id" value="<?php echo $sessionId; ?>">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <input type="text" name="user_name" value="<?php echo h($session['user_name'] ?? ''); ?>" placeholder="您的称呼" maxlength="20">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <input type="text" name="contact" value="<?php echo h($session['contact'] ?? $contact); ?>" placeholder="联系方式（手机/邮箱）" maxlength="50">
                </div>
            </div>
            <div style="display: flex; gap: 12px;">
                <textarea name="content" rows="2" placeholder="输入您的问题..." style="flex: 1; resize: vertical;" required></textarea>
                <button type="submit" class="btn" id="sendBtn" style="align-self: flex-end;">发送</button>
            </div>
        </form>
        <?php else: ?>
            <div style="text-align: center; color: #64748B; padding: 16px; background: #F1F5F9; border-radius: 8px;">
                会话已结束，如需咨询请重新发起。
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php if ($merchantId): ?>
<script>
const chatBox = document.getElementById('chatBox');
const sessionIdInput = document.querySelector('input[name="session_id"]');
let sessionId = parseInt(sessionIdInput.value) || 0;
let lastId = <?php echo !empty($messages) ? (int) end($messages)['id'] : 0; ?>;
chatBox.scrollTop = chatBox.scrollHeight;

function appendMessage(senderType, content, time) {
    const emptyTip = chatBox.querySelector('[style*="padding-top: 80px"]');
    if (emptyTip) emptyTip.parentElement.remove();

    const div = document.createElement('div');
    div.style.marginBottom = '12px';
    div.style.display = 'flex';
    div.style.justifyContent = senderType === 0 ? 'flex-end' : 'flex-start';

    const bubble = document.createElement('div');
    bubble.style.maxWidth = '75%';
    bubble.style.padding = '10px 14px';
    bubble.style.borderRadius = '8px';
    bubble.style.fontSize = '14px';
    bubble.style.lineHeight = '1.5';
    if (senderType === 0) {
        bubble.style.background = '#2563EB';
        bubble.style.color = '#fff';
        bubble.style.borderBottomRightRadius = '2px';
    } else if (senderType === 2) {
        bubble.style.background = '#FEF3C7';
        bubble.style.color = '#92400E';
    } else {
        bubble.style.background = '#fff';
        bubble.style.color = '#1F2937';
        bubble.style.border = '1px solid #E2E8F0';
        bubble.style.borderBottomLeftRadius = '2px';
    }
    bubble.innerHTML = '<div>' + content.replace(/\n/g, '<br>') + '</div><div style="font-size:12px;opacity:0.8;margin-top:4px;text-align:right;">' + time + '</div>';

    div.appendChild(bubble);
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}

document.getElementById('chatForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('sendBtn');
    const form = e.target;
    const content = form.content.value.trim();
    if (!content) return;

    btn.disabled = true;
    const formData = new FormData(form);
    try {
        const res = await fetch('<?php echo url('chat/send'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.code === 0) {
            appendMessage(0, content, '<?php echo date('Y-m-d H:i:s'); ?>');
            form.content.value = '';
            if (data.data.session_id) {
                sessionId = data.data.session_id;
                sessionIdInput.value = sessionId;
                // 更新 URL 便于刷新后继续当前会话
                const url = new URL(location.href);
                url.searchParams.set('session_id', sessionId);
                history.replaceState(null, '', url.toString());
            }
            lastId++;
        } else {
            alert(data.msg);
        }
    } catch (err) {
        alert('发送失败');
    } finally {
        btn.disabled = false;
    }
});

// 轮询商户回复
if (sessionId > 0) {
    setInterval(async () => {
        try {
            const res = await fetch('<?php echo url('chat/poll'); ?>?session_id=' + sessionId + '&last_id=' + lastId);
            const data = await res.json();
            if (data.code === 0 && data.data.messages.length > 0) {
                data.data.messages.forEach(msg => {
                    appendMessage(parseInt(msg.sender_type), msg.content, msg.create_time);
                    lastId = Math.max(lastId, parseInt(msg.id));
                });
            }
        } catch (err) {
            // 忽略轮询错误
        }
    }, 5000);
}
</script>
<?php endif; ?>
