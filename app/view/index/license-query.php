<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>授权查询 - QEEFG授权站</title>
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
                    <li><a href="/license-query" class="active">授权查询</a></li>
                    <li><a href="/documents">文档中心</a></li>
                    <li><a href="/user/products">产品列表</a></li>
                </ul>
                <div class="nav-actions">
                    <?php if ($user): ?>
                        <a href="/user/dashboard" class="btn btn-secondary">用户中心</a>
                        <a href="/logout" class="btn btn-sm">退出</a>
                    <?php else: ?>
                        <a href="/login" class="btn btn-secondary">登录</a>
                        <a href="/register" class="btn btn-primary">注册</a>
                    <?php endif; ?>
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

    <div class="container" style="padding: 60px 20px;">
        <div class="page-title">
            <h1>授权查询</h1>
            <p>输入授权密钥查询授权详情</p>
        </div>

        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <form method="get" action="/license-query">
                <div class="form-group">
                    <label>授权密钥</label>
                    <input type="text" name="license_key" value="<?php echo htmlspecialchars($licenseKey ?? ''); ?>" placeholder="请输入授权密钥" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">查询授权</button>
            </form>

            <?php if ($result): ?>
            <div style="margin-top: 30px; padding: 20px; background: #f8f9ff; border-radius: 12px;">
                <h3 style="color: #667eea; margin-bottom: 20px;">授权信息</h3>
                
                <div style="display: grid; gap: 12px;">
                    <div>
                        <span style="color: #64748b; font-size: 14px;">授权密钥</span>
                        <div style="font-family: monospace; color: #667eea; font-size: 16px; word-break: break-all; margin-top: 4px;">
                            <?php echo $result['license_key']; ?>
                        </div>
                    </div>
                    
                    <div>
                        <span style="color: #64748b; font-size: 14px;">产品名称</span>
                        <div style="color: #1e293b; font-size: 16px; margin-top: 4px;">
                            <?php echo $result['product_name']; ?>
                        </div>
                    </div>
                    
                    <div>
                        <span style="color: #64748b; font-size: 14px;">所属用户</span>
                        <div style="color: #1e293b; font-size: 16px; margin-top: 4px;">
                            <?php echo $result['user_name']; ?>
                        </div>
                    </div>
                    
                    <div>
                        <span style="color: #64748b; font-size: 14px;">硬件ID</span>
                        <div style="color: #1e293b; font-size: 16px; margin-top: 4px;">
                            <?php echo $result['hardware_id'] ?: '未绑定'; ?>
                        </div>
                    </div>
                    
                    <div>
                        <span style="color: #64748b; font-size: 14px;">到期时间</span>
                        <div style="color: #1e293b; font-size: 16px; margin-top: 4px;">
                            <?php echo $result['expire_date'] ?: '永久有效'; ?>
                        </div>
                    </div>
                    
                    <div>
                        <span style="color: #64748b; font-size: 14px;">状态</span>
                        <div style="margin-top: 4px;">
                            <?php if ($result['status'] == 1): ?>
                                <span class="status-badge status-active">有效</span>
                            <?php else: ?>
                                <span class="status-badge status-inactive">已禁用</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div>
                        <span style="color: #64748b; font-size: 14px;">创建时间</span>
                        <div style="color: #1e293b; font-size: 16px; margin-top: 4px;">
                            <?php echo $result['created_at']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php elseif ($licenseKey): ?>
            <div style="margin-top: 30px; padding: 20px; background: #fee2e2; border-radius: 12px; color: #dc2626;">
                未找到该授权密钥，请检查输入是否正确。
            </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2024 QEEFG授权站. 保留所有权利.</p>
            </div>
        </div>
    </footer>

    <script src="/static/js/main.js"></script>
</body>
</html>
