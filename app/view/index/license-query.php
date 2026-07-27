<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>授权查询 - QEEFG授权站</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background: #fff; color: #333; line-height: 1.6; }
        a { text-decoration: none; color: inherit; }
        .header { background: #fff; border-bottom: 1px solid #e5e5e5; padding: 0 20px; }
        .header-inner { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; height: 60px; }
        .logo { font-size: 20px; font-weight: 600; color: #1a1a1a; }
        .nav { display: flex; gap: 30px; }
        .nav a { color: #666; font-size: 14px; padding: 8px 0; }
        .nav a:hover { color: #1890ff; }
        .nav-btn { background: #1890ff; color: #fff; padding: 8px 20px; border: none; cursor: pointer; }
        .nav-btn:hover { background: #40a9ff; }
        .container { max-width: 800px; margin: 60px auto; padding: 0 20px; }
        .page-title { font-size: 28px; text-align: center; margin-bottom: 40px; color: #1a1a1a; }
        .query-box { background: #fafafa; padding: 40px; border: 1px solid #e5e5e5; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; color: #333; margin-bottom: 8px; }
        .form-control { width: 100%; padding: 12px 16px; font-size: 14px; border: 1px solid #d9d9d9; background: #fff; }
        .form-control:focus { outline: none; border-color: #1890ff; }
        .btn { display: inline-block; padding: 12px 32px; font-size: 16px; border: none; cursor: pointer; background: #1890ff; color: #fff; width: 100%; }
        .btn:hover { background: #40a9ff; }
        .result-box { margin-top: 30px; padding: 20px; border: 1px solid #e5e5e5; background: #fff; display: none; }
        .result-item { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
        .result-item:last-child { border-bottom: none; }
        .result-label { color: #666; }
        .result-value { color: #1a1a1a; font-weight: 500; }
        .status-active { color: #52c41a; }
        .status-inactive { color: #ff4d4f; }
        .footer { padding: 40px 20px; background: #1a1a1a; color: #fff; text-align: center; margin-top: 60px; }
        .footer a { color: #1890ff; }
        @media (max-width: 768px) {
            .header-inner { flex-direction: column; height: auto; padding: 15px 0; }
            .nav { margin-top: 15px; flex-wrap: wrap; justify-content: center; gap: 15px; }
            .container { margin: 30px auto; }
            .query-box { padding: 20px; }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-inner">
            <a href="/" class="logo">QEEFG授权站</a>
            <nav class="nav">
                <a href="/">首页</a>
                <a href="/license-query">授权查询</a>
                <a href="/documents">文档中心</a>
                <a href="/login" class="nav-btn">登录</a>
                <a href="/register" class="nav-btn" style="background: #fff; color: #1890ff; border: 1px solid #1890ff;">注册</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <h1 class="page-title">授权查询</h1>
        <div class="query-box">
            <div class="form-group">
                <label>请输入授权码</label>
                <input type="text" class="form-control" id="license_key" placeholder="请输入您的授权码">
            </div>
            <button class="btn" onclick="queryLicense()">查询授权</button>
            <div class="result-box" id="result">
                <div class="result-item">
                    <span class="result-label">授权码</span>
                    <span class="result-value" id="r-key">-</span>
                </div>
                <div class="result-item">
                    <span class="result-label">产品名称</span>
                    <span class="result-value" id="r-product">-</span>
                </div>
                <div class="result-item">
                    <span class="result-label">授权状态</span>
                    <span class="result-value" id="r-status">-</span>
                </div>
                <div class="result-item">
                    <span class="result-label">到期时间</span>
                    <span class="result-value" id="r-expire">-</span>
                </div>
                <div class="result-item">
                    <span class="result-label">绑定域名</span>
                    <span class="result-value" id="r-domain">-</span>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> QEEFG授权站 All Rights Reserved. <a href="/admin/login">管理后台</a></p>
    </footer>

    <script>
        function queryLicense() {
            const key = document.getElementById('license_key').value.trim();
            if (!key) {
                alert('请输入授权码');
                return;
            }
            // 模拟查询结果
            document.getElementById('result').style.display = 'block';
            document.getElementById('r-key').textContent = key;
            document.getElementById('r-product').textContent = '示例产品';
            document.getElementById('r-status').innerHTML = '<span class="status-active">有效</span>';
            document.getElementById('r-expire').textContent = '永久';
            document.getElementById('r-domain').textContent = '未绑定';
        }
    </script>
</body>
</html>