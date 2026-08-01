<?php
require_once __DIR__ . '/includes/config.php';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>页面未找到 - 森企动力</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Microsoft YaHei', sans-serif;
            background: linear-gradient(135deg, #0d47c2 0%, #1a5fff 50%, #00d4ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .error-container { max-width: 600px; }
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 0 8px 32px rgba(0,0,0,0.3);
            animation: float 3s ease-in-out infinite;
        }
        .error-title { font-size: 2rem; margin-bottom: 1rem; }
        .error-desc { opacity: 0.85; margin-bottom: 2rem; font-size: 1.1rem; }
        .error-btn {
            display: inline-block;
            padding: 14px 40px;
            background: white;
            color: #1a5fff;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .error-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h1 class="error-title">页面走丢了</h1>
        <p class="error-desc">抱歉，您访问的页面不存在或已被移除。</p>
        <a href="<?php echo SITE_URL; ?>" class="error-btn">返回首页 →</a>
    </div>
</body>
</html>
