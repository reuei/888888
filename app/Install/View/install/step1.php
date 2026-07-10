<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>玄武发卡 v1.0.5 安装向导</title>
    <link rel="stylesheet" href="/static/css/app.css?v=1.0.5">
</head>
<body class="install-body">
    <div class="install-page">
        <header class="install-head">
            <span class="brand-mark"></span>
            <h1>玄武发卡 v1.0.5</h1>
            <p>自研轻量MVC框架 · 授权站 v1.1.1</p>
        </header>

        <div class="install-steps">
            <div class="install-step <?= $step == 1 ? 'active' : ($step > 1 ? 'done' : '') ?>">
                <span class="step-num">1</span><span>环境检测</span>
            </div>
            <div class="install-step <?= $step == 2 ? 'active' : ($step > 2 ? 'done' : '') ?>">
                <span class="step-num">2</span><span>配置数据库</span>
            </div>
            <div class="install-step <?= $step == 3 ? 'active' : '' ?>">
                <span class="step-num">3</span><span>完成</span>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= h($error) ?></div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
        <div class="install-panel">
            <h2 class="panel-title">环境检测</h2>
            <table class="data-table">
                <thead><tr><th>检测项</th><th>状态</th></tr></thead>
                <tbody>
                <?php foreach ($env as $name => $ok): ?>
                <tr>
                    <td><?= h($name) ?></td>
                    <td>
                        <?php if ($ok): ?>
                            <span class="badge badge-success">通过</span>
                        <?php else: ?>
                            <span class="badge badge-danger">未通过</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <div class="install-actions">
                <a href="?step=2" class="btn btn-primary">下一步</a>
            </div>
        </div>
        <?php elseif ($step == 2): ?>
        <div class="install-panel">
            <h2 class="panel-title">配置数据库</h2>
            <form id="installForm" class="form">
                <div class="form-group">
                    <label>数据库主机</label>
                    <input type="text" name="db_host" value="127.0.0.1" required>
                </div>
                <div class="form-group">
                    <label>端口</label>
                    <input type="number" name="db_port" value="3306" required>
                </div>
                <div class="form-group">
                    <label>数据库名</label>
                    <input type="text" name="db_name" placeholder="不存在则自动创建" required>
                </div>
                <div class="form-group">
                    <label>用户名</label>
                    <input type="text" name="db_user" required>
                </div>
                <div class="form-group">
                    <label>密码</label>
                    <input type="password" name="db_pass">
                </div>
                <div class="form-divider"></div>
                <h3 class="form-section">管理员账号</h3>
                <div class="form-group">
                    <label>账号</label>
                    <input type="text" name="admin_user" value="admin" required>
                </div>
                <div class="form-group">
                    <label>密码</label>
                    <input type="password" name="admin_pass" value="admin888" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block" id="submitBtn">立即安装</button>
            </form>
        </div>
        <?php elseif ($step == 3): ?>
        <div class="install-panel install-success">
            <div class="success-icon">✓</div>
            <h2 class="panel-title">安装完成</h2>
            <p>为了安全，请删除 install 目录</p>
            <div class="install-actions">
                <a href="/admin/login" class="btn btn-primary">进入后台</a>
                <a href="/" class="btn btn-line">访问首页</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <script src="/static/js/app.js?v=1.0.5"></script>
    <script>
    var form = document.getElementById('installForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.textContent = '安装中...';
            var data = new FormData(form);
            fetch('/install/step2', { method: 'POST', body: data })
                .then(r => r.json())
                .then(res => {
                    if (res.code === 0) {
                        alert(res.msg);
                        location.href = '?step=3';
                    } else {
                        alert(res.msg);
                        btn.disabled = false;
                        btn.textContent = '立即安装';
                    }
                })
                .catch(err => {
                    alert('网络错误');
                    btn.disabled = false;
                    btn.textContent = '立即安装';
                });
        });
    }
    </script>
</body>
</html>
