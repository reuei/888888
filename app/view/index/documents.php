<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文档中心 - QEEFG授权站</title>
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
        .container { max-width: 1000px; margin: 60px auto; padding: 0 20px; }
        .page-title { font-size: 28px; text-align: center; margin-bottom: 40px; color: #1a1a1a; }
        .doc-section { margin-bottom: 40px; }
        .doc-section h2 { font-size: 20px; color: #1a1a1a; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #e5e5e5; }
        .doc-item { background: #fafafa; padding: 20px; margin-bottom: 16px; border: 1px solid #e5e5e5; }
        .doc-item h3 { font-size: 16px; color: #1890ff; margin-bottom: 10px; }
        .doc-item p { font-size: 14px; color: #666; }
        .doc-item pre { background: #f5f5f5; padding: 15px; margin-top: 10px; overflow-x: auto; font-size: 13px; border: 1px solid #e5e5e5; }
        .doc-item code { background: #f5f5f5; padding: 2px 6px; font-size: 13px; }
        .footer { padding: 40px 20px; background: #1a1a1a; color: #fff; text-align: center; margin-top: 60px; }
        .footer a { color: #1890ff; }
        @media (max-width: 768px) {
            .header-inner { flex-direction: column; height: auto; padding: 15px 0; }
            .nav { margin-top: 15px; flex-wrap: wrap; justify-content: center; gap: 15px; }
            .container { margin: 30px auto; }
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
        <h1 class="page-title">文档中心</h1>

        <div class="doc-section">
            <h2>快速开始</h2>
            <div class="doc-item">
                <h3>1. 注册账号</h3>
                <p>点击右上角"注册"按钮，填写用户名、邮箱和密码完成注册。</p>
            </div>
            <div class="doc-item">
                <h3>2. 购买授权</h3>
                <p>登录后进入产品中心，选择需要购买的产品，完成支付即可获得授权。</p>
            </div>
            <div class="doc-item">
                <h3>3. 绑定域名</h3>
                <p>在"我的产品"中绑定您的域名，即可开始使用授权服务。</p>
            </div>
        </div>

        <div class="doc-section">
            <h2>API接口</h2>
            <div class="doc-item">
                <h3>授权验证接口</h3>
                <p>用于验证用户授权是否有效。</p>
                <pre>POST /api/license/verify
参数:
- license_key: 授权码
- domain: 域名

返回:
{
    "code": 200,
    "msg": "授权有效",
    "data": {
        "product_id": 1,
        "expire_time": "2099-12-31",
        "status": 1
    }
}</pre>
            </div>
            <div class="doc-item">
                <h3>授权信息查询</h3>
                <p>查询授权的详细信息。</p>
                <pre>GET /api/license/info
参数:
- license_key: 授权码

返回:
{
    "code": 200,
    "data": {
        "license_key": "XXXXX-XXXXX-XXXXX",
        "product_name": "产品名称",
        "domain": "example.com",
        "expire_time": "2099-12-31",
        "status": 1
    }
}</pre>
            </div>
        </div>

        <div class="doc-section">
            <h2>常见问题</h2>
            <div class="doc-item">
                <h3>授权码忘记了怎么办？</h3>
                <p>登录后在"我的产品"页面可以查看所有授权码。</p>
            </div>
            <div class="doc-item">
                <h3>如何更换绑定的域名？</h3>
                <p>在"我的产品"中点击"解绑"按钮，然后重新绑定新域名即可。</p>
            </div>
            <div class="doc-item">
                <h3>授权到期后怎么办？</h3>
                <p>授权到期后需要重新购买，或联系客服续费。</p>
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>© <?php echo date('Y'); ?> QEEFG授权站 All Rights Reserved. <a href="/admin/login">管理后台</a></p>
    </footer>
</body>
</html>