<div class="breadcrumb">客服管理 / <a href="<?php echo url('merchant/chat'); ?>">咨询列表</a> / 回复会话</div>
<div class="page-header">
    <h2>回复会话</h2>
</div>

<?php if (empty($session)): ?>
<div class="card">
    <p style="text-align: center; color: #64748B;">会话不存在或无权访问</p>
</div>
<?php else: ?>
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #E2E8F0;">
        <div>
            <strong>访客：</strong><?php echo h($session['user_name'] ?: '游客' . $session['id']); ?>
            <?php if ($session['contact']): ?>
                <span style="margin-left: 16px; color: #64748B;">联系方式：<?php echo h($session['contact']); ?></span>
            <?php endif; ?>
        </div>
        <?php if ($session['status'] == 1): ?>
            <button class="btn btn-sm btn-danger" onclick="closeSession()">关闭会话</button>
        <?php else: ?>
            <span class="tag tag-orange">已关闭</span>
        <?php endif; ?>
    </div>

    <div id="chatBox" style="height: 400px; overflow-y: auto; border: 1px solid #E2E8F0; border-radius: 8px; padding: 16px; background: #F8FAFC; margin-bottom: 16px;">
        <?php foreach ($messages as $msg): ?>
        <div style="margin-bottom: 12px; display: flex; <?php echo $msg['sender_type'] == 1 ? 'justify-content: flex-end;' : 'justify-content: flex-start;'; ?>">
            <div style="max-width: 70%; padding: 10px 14px; border-radius: 8px; font-size: 14px; line-height: 1.5; <?php
                if ($msg['sender_type'] == 1) {
                    echo 'background: #10B981; color: #fff; border-bottom-right-radius: 2px;';
                } elseif ($msg['sender_type'] == 2) {
                    echo 'background: #FEF3C7; color: #92400E;';
                } else {
                    echo 'background: #fff; color: #1F2937; border: 1px solid #E2E8F0; border-bottom-left-radius: 2px;';
                }
            ?>">
                <div><?php echo nl2br(h($msg['content'])); ?></div>
                <div style="font-size: 12px; opacity: 0.8; margin-top: 4px; text-align: right;"><?php echo h($msg['create_time']); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if ($session['status'] == 1): ?>
    <form id="replyForm" style="display: flex; gap: 12px;">
        <input type="hidden" name="session_id" value="<?php echo $session['id']; ?>">
        <textarea name="content" rows="2" placeholder="输入回复内容..." style="flex: 1; padding: 10px 12px; border: 1px solid #CBD5E1; border-radius: 6px; resize: vertical;" required></textarea>
        <button type="submit" class="btn" id="sendBtn" style="align-self: flex-end;">发送</button>
    </form>
    <?php endif; ?>
</div>

<script>
const chatBox = document.getElementById('chatBox');
const sessionId = <?php echo $session['id']; ?>;
let lastId = <?php echo !empty($messages) ? (int) end($messages)['id'] : 0; ?>;
chatBox.scrollTop = chatBox.scrollHeight;

<?php if ($session['status'] == 1): ?>
document.getElementById('replyForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('sendBtn');
    const form = e.target;
    const content = form.content.value.trim();
    if (!content) return;

    btn.disabled = true;
    const formData = new FormData(form);
    try {
        const res = await fetch('<?php echo url('merchant/chat/send'); ?>', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.code === 0) {
            appendMessage(1, content, '<?php echo date('Y-m-d H:i:s'); ?>');
            form.content.value = '';
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

function appendMessage(senderType, content, time) {
    const div = document.createElement('div');
    div.style.marginBottom = '12px';
    div.style.display = 'flex';
    div.style.justifyContent = senderType === 1 ? 'flex-end' : 'flex-start';

    const bubble = document.createElement('div');
    bubble.style.maxWidth = '70%';
    bubble.style.padding = '10px 14px';
    bubble.style.borderRadius = '8px';
    bubble.style.fontSize = '14px';
    bubble.style.lineHeight = '1.5';
    if (senderType === 1) {
        bubble.style.background = '#10B981';
        bubble.style.color = '#fff';
        bubble.style.borderBottomRightRadius = '2px';
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

// 轮询新消息
setInterval(async () => {
    try {
        const res = await fetch('<?php echo url('merchant/chat/poll'); ?>?session_id=' + sessionId + '&last_id=' + lastId);
        const data = await res.json();
        if (data.code === 0 && data.data.messages.length > 0) {
            data.data.messages.forEach(msg => {
                appendMessage(msg.sender_type, msg.content, msg.create_time);
                lastId = Math.max(lastId, parseInt(msg.id));
            });
        }
    } catch (err) {
        // 忽略轮询错误
    }
}, 5000);
<?php endif; ?>

async function closeSession() {
    if (!confirm('确定要关闭该会话吗？')) return;
    try {
        const res = await fetch('<?php echo url('merchant/chat/close'); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + sessionId
        });
        const data = await res.json();
        alert(data.msg);
        if (data.code === 0) location.reload();
    } catch (err) {
        alert('请求失败');
    }
}
</script>
<?php endif; ?>
