<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>意见反馈 - QEEFG授权站</title>
    <link rel="stylesheet" href="/static/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="/" class="logo">
                <div class="logo-icon">Q</div>
                <div class="logo-text">QEEFG授权站</div>
            </a>
            <nav class="nav">
                <ul class="nav-links">
                    <li><a href="/">首页</a></li>
                    <li><a href="/license-query">授权查询</a></li>
                    <li><a href="/documents">文档中心</a></li>
                </ul>
                <div class="nav-actions">
                    <button class="icon-btn theme-toggle" id="theme-toggle">☀️</button>
                    <button class="icon-btn lang-toggle" id="lang-toggle">CN</button>
                    <div class="burger-menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li><a href="/user/dashboard">🏠 首页</a></li>
            <li><a href="/user/workplace">📋 工作台</a></li>
            <li><a href="/user/products">🛒 产品列表</a></li>
            <li><a href="/user/my-products">💼 我的产品</a></li>
            <li><a href="/user/balance">💰 余额管理</a></li>
            <li><a href="/user/settings">⚙️ 账户设置</a></li>
            <li><a href="/user/feedback" class="active">📝 意见反馈</a></li>
            <li><a href="/logout">🚪 退出登录</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="page-title">
            <h1>意见反馈</h1>
            <p>告诉我们您的想法和建议</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>提交反馈</h3>
            </div>
            
            <form method="post" action="/user/submitFeedback" id="feedback-form">
                <div class="form-group">
                    <label>反馈内容</label>
                    <textarea name="content" placeholder="请详细描述您的问题或建议（至少10个字符）" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">提交反馈</button>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>反馈记录</h3>
            </div>
            
            <?php if ($feedbacks): ?>
            <div style="display: flex; flex-direction: column; gap: 16px;">
                <?php foreach ($feedbacks as $feedback): ?>
                <div style="padding: 20px; background: #f8f9ff; border-radius: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="color: #64748b; font-size: 14px;"><?php echo date('Y-m-d H:i', strtotime($feedback['created_at'])); ?></span>
                        <?php if ($feedback['status'] == 1): ?>
                            <span class="status-badge status-active">已回复</span>
                        <?php else: ?>
                            <span class="status-badge status-pending">待处理</span>
                        <?php endif; ?>
                    </div>
                    <p style="color: #1e293b; margin-bottom: 12px; line-height: 1.6;"><?php echo htmlspecialchars($feedback['content']); ?></p>
                    <?php if ($feedback['reply']): ?>
                    <div style="padding: 12px; background: #e0f2fe; border-radius: 8px; border-left: 4px solid #0ea5e9;">
                        <span style="color: #0284c7; font-weight: bold; font-size: 14px;">管理员回复:</span>
                        <p style="color: #0c4a6e; margin-top: 8px; font-size: 14px;"><?php echo htmlspecialchars($feedback['reply']); ?></p>
                        <span style="color: #075985; font-size: 12px; display: block; margin-top: 8px;"><?php echo $feedback['replied_at'] ? date('Y-m-d H:i', strtotime($feedback['replied_at'])) : ''; ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #64748b;">
                暂无反馈记录
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="/static/js/main.js"></script>
    <script>
        document.getElementById('feedback-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const content = this.querySelector('textarea').value.trim();
            if (content.length < 10) {
                showToast('反馈内容至少需要10个字符', 'error');
                return;
            }
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.code === 200) {
                    showToast(data.msg, 'success');
                    setTimeout(() => {
                        window.location.href = data.data.redirect;
                    }, 1000);
                } else {
                    showToast(data.msg, 'error');
                }
            })
            .catch(err => {
                showToast('提交失败', 'error');
            });
        });
    </script>
</body>
</html>
