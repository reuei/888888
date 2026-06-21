<?php
/**
 * 国际版 - 跳转到 cloud.loveym.cloud
 */
$redirectUrl = 'https://cloud.loveym.cloud';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>YuYun Tech - International Version</title>
<meta http-equiv="refresh" content="2;url=<?php echo $redirectUrl; ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #0f1b3d 0%, #1a2d5c 50%, #0f1b3d 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    padding: 20px;
}
.container {
    text-align: center;
    max-width: 600px;
}
.logo {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #1a73e8, #ff6b35);
    border-radius: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: 800;
    margin-bottom: 32px;
    box-shadow: 0 8px 24px rgba(26, 115, 232, 0.4);
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}
h1 {
    font-size: 42px;
    font-weight: 700;
    margin-bottom: 16px;
    background: linear-gradient(135deg, #fff, #e0e8ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
p {
    font-size: 18px;
    color: rgba(255,255,255,0.8);
    margin-bottom: 12px;
    line-height: 1.6;
}
.spinner {
    display: inline-block;
    width: 40px;
    height: 40px;
    border: 4px solid rgba(255,255,255,0.2);
    border-top-color: #ff6b35;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 32px 0;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
.btn {
    display: inline-block;
    padding: 14px 36px;
    background: #ff6b35;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    transition: all 0.3s;
    margin-top: 20px;
    box-shadow: 0 4px 12px rgba(255,107,53,0.3);
}
.btn:hover {
    background: #e55a2b;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255,107,53,0.4);
    color: #fff;
}
.url {
    display: inline-block;
    background: rgba(255,255,255,0.1);
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 14px;
    color: #ff6b35;
    margin-top: 16px;
    word-break: break-all;
}
.back {
    display: inline-block;
    margin-top: 20px;
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    font-size: 14px;
}
.back:hover { color: #ff6b35; }
</style>
</head>
<body>
<div class="container">
    <div class="logo">Y</div>
    <h1>YuYun Tech International</h1>
    <p>正在跳转到国际版官网...</p>
    <p>Redirecting to international version...</p>
    <div class="spinner"></div>
    <div class="url"><i class="fas fa-globe"></i> <?php echo $redirectUrl; ?></div>
    <div>
        <a href="<?php echo $redirectUrl; ?>" class="btn">立即访问 / Visit Now</a>
    </div>
    <div>
        <a href="index.php" class="back">&larr; 返回中文版 / Back to Chinese Version</a>
    </div>
</div>
</body>
</html>
