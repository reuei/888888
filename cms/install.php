<?php
$installed = file_exists(__DIR__ . '/data/cms.db') && filesize(__DIR__ . '/data/cms.db') > 0;
if ($installed && !isset($_GET['reinstall'])) {
    header('Location: index.php');
    exit;
}

$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 2) {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';
        $email = trim($_POST['email'] ?? '');

        if (empty($username) || empty($password)) {
            $error = '管理员用户名和密码不能为空';
        } elseif (strlen($password) < 6) {
            $error = '密码长度不能少于6位';
        } elseif ($password !== $password2) {
            $error = '两次输入的密码不一致';
        } else {
            require_once __DIR__ . '/config/config.php';
            require_once __DIR__ . '/includes/db.php';

            $sql = file_get_contents(__DIR__ . '/install.sql');
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            foreach ($statements as $stmt) {
                if (!empty($stmt)) {
                    DB::getInstance()->exec($stmt);
                }
            }

            $defaultSettings = [
                'site_name' => '清廉在线',
                'site_title' => '清廉在线 - 党风廉政建设门户网站',
                'site_keywords' => '党风廉政,反腐倡廉,纪检监察,纪律检查',
                'site_description' => '清廉在线是党风廉政建设和反腐败工作的综合性门户网站',
                'footer_copyright' => '© ' . date('Y') . ' 清廉在线 版权所有',
                'footer_image' => '',
                'icp' => '',
                'contact_email' => 'contact@example.com',
            ];
            foreach ($defaultSettings as $k => $v) {
                DB::insert('settings', ['key' => $k, 'value' => $v]);
            }

            $categories = [
                ['name' => '要闻动态', 'slug' => 'yaowen', 'sort_order' => 1, 'type' => 'article'],
                ['name' => '审查调查', 'slug' => 'shencha', 'sort_order' => 2, 'type' => 'article'],
                ['name' => '巡视巡察', 'slug' => 'xunshi', 'sort_order' => 3, 'type' => 'article'],
                ['name' => '党纪法规', 'slug' => 'fagui', 'sort_order' => 4, 'type' => 'article'],
                ['name' => '监督举报', 'slug' => 'jubao', 'sort_order' => 5, 'type' => 'page'],
                ['name' => '视频中心', 'slug' => 'video', 'sort_order' => 6, 'type' => 'article'],
                ['name' => '文化之约', 'slug' => 'wenhua', 'sort_order' => 7, 'type' => 'article'],
            ];
            foreach ($categories as $cat) {
                DB::insert('categories', array_merge($cat, ['parent_id' => 0, 'show_in_menu' => 1]));
            }

            $subCats = [
                'shencha' => [
                    ['name' => '执纪审查', 'slug' => 'zhiji', 'sort_order' => 1],
                    ['name' => '党纪政务处分', 'slug' => 'chufen', 'sort_order' => 2],
                    ['name' => '国际追逃', 'slug' => 'zhuitao', 'sort_order' => 3],
                ],
                'xunshi' => [
                    ['name' => '巡视工作', 'slug' => 'xunshigz', 'sort_order' => 1],
                    ['name' => '巡察工作', 'slug' => 'xunshacg', 'sort_order' => 2],
                ],
                'fagui' => [
                    ['name' => '党内法规', 'slug' => 'dangnei', 'sort_order' => 1],
                    ['name' => '国家法律', 'slug' => 'guojia', 'sort_order' => 2],
                    ['name' => '纪法百科', 'slug' => 'baike', 'sort_order' => 3],
                ],
            ];
            foreach ($subCats as $parentSlug => $subs) {
                $parent = DB::fetchOne("SELECT id FROM categories WHERE slug=?", [$parentSlug]);
                if ($parent) {
                    foreach ($subs as $sub) {
                        DB::insert('categories', array_merge($sub, [
                            'parent_id' => $parent['id'],
                            'show_in_menu' => 1,
                            'type' => 'article',
                        ]));
                    }
                }
            }

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            DB::insert('users', [
                'username' => $username,
                'password' => $hashed,
                'nickname' => '超级管理员',
                'email' => $email,
                'role' => 'super_admin',
                'status' => 1,
            ]);

            $sampleTitles = [
                '省纪委监委召开全体干部大会 传达学习重要会议精神',
                '某市原副市长严重违纪违法被开除党籍和公职',
                '中央巡视组对多地开展巡视"回头看"反馈情况通报',
                '中国共产党纪律处分条例全文解读',
                '关于新形势下党内政治生活的若干准则学习要点',
                '追逃追赃工作取得新进展 多名外逃人员归案',
            ];
            $catIds = DB::fetchAll("SELECT id FROM categories WHERE parent_id=0 LIMIT 6");
            foreach ($sampleTitles as $idx => $title) {
                $catId = $catIds[$idx % count($catIds)]['id'];
                DB::insert('articles', [
                    'category_id' => $catId,
                    'title' => $title,
                    'summary' => '这是文章摘要内容，简要介绍文章的主要内容和核心要点，吸引读者进一步阅读全文。',
                    'content' => '<p>这是文章正文内容。这里可以放置详细的文章内容，包括段落、列表、引用等HTML格式内容。</p><p>党风廉政建设和反腐败斗争是党的建设的重大任务。我们要以永远在路上的执着把全面从严治党引向深入，开创全面从严治党新局面。</p><p>各级党组织和党员干部要深刻认识党风廉政建设和反腐败斗争的长期性、复杂性、艰巨性，保持战略定力，以越是艰险越向前的英雄气概和狭路相逢勇者胜的斗争精神，坚定不移抓下去。</p>',
                    'author' => '本站通讯员',
                    'source' => '本站原创',
                    'is_top' => $idx < 2 ? 1 : 0,
                    'status' => 1,
                    'views' => rand(100, 5000),
                ]);
            }

            $installLock = fopen(__DIR__ . '/data/install.lock', 'w');
            fwrite($installLock, date('Y-m-d H:i:s'));
            fclose($installLock);

            header('Location: install.php?step=3');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统安装 - 清廉在线</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Microsoft YaHei", Arial, sans-serif; background: #f5f5f5; color: #333; }
        .install-wrap { max-width: 700px; margin: 50px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .install-header { background: #c20000; color: #fff; padding: 25px 30px; border-radius: 8px 8px 0 0; }
        .install-header h1 { font-size: 22px; font-weight: normal; }
        .install-body { padding: 30px; }
        .steps { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        .step { flex: 1; text-align: center; color: #999; position: relative; }
        .step.active { color: #c20000; font-weight: bold; }
        .step.done { color: #52c41a; }
        .step-num { display: inline-block; width: 28px; height: 28px; line-height: 28px; border-radius: 50%; background: #eee; color: #999; margin-bottom: 5px; }
        .step.active .step-num { background: #c20000; color: #fff; }
        .step.done .step-num { background: #52c41a; color: #fff; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #555; font-size: 14px; }
        .form-group input { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .form-group input:focus { outline: none; border-color: #c20000; }
        .error { background: #fff1f0; color: #f5222d; padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ffa39e; }
        .success { background: #f6ffed; color: #52c41a; padding: 10px 15px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #b7eb8f; }
        .btn { display: inline-block; padding: 10px 30px; background: #c20000; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 15px; text-decoration: none; }
        .btn:hover { background: #a80000; }
        .check-list { list-style: none; }
        .check-list li { padding: 10px 0; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; }
        .check-list .pass { color: #52c41a; }
        .check-list .fail { color: #f5222d; }
        .tip { font-size: 13px; color: #999; margin-top: 5px; }
        .success-box { text-align: center; padding: 20px 0; }
        .success-box .icon { font-size: 60px; color: #52c41a; margin-bottom: 15px; }
        .success-box h3 { margin-bottom: 15px; color: #333; }
        .success-box p { color: #666; margin-bottom: 10px; }
        .btn-group { margin-top: 25px; }
        .btn-group .btn { margin: 0 5px; }
    </style>
</head>
<body>
    <div class="install-wrap">
        <div class="install-header">
            <h1>清廉在线 CMS 系统安装向导</h1>
        </div>
        <div class="install-body">
            <div class="steps">
                <div class="step <?php echo $step >= 1 ? ($step > 1 ? 'done' : 'active') : ''; ?>">
                    <div class="step-num">1</div>
                    <div>环境检测</div>
                </div>
                <div class="step <?php echo $step >= 2 ? ($step > 2 ? 'done' : 'active') : ''; ?>">
                    <div class="step-num">2</div>
                    <div>配置管理员</div>
                </div>
                <div class="step <?php echo $step >= 3 ? 'active' : ''; ?>">
                    <div class="step-num">3</div>
                    <div>安装完成</div>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="error"><?php echo e($error); ?></div>
            <?php endif; ?>

            <?php if ($step == 1): ?>
                <h3 style="margin-bottom:15px;">运行环境检测</h3>
                <ul class="check-list">
                    <li>
                        <span>PHP 版本 >= 7.0</span>
                        <span class="<?php echo version_compare(PHP_VERSION, '7.0', '>=') ? 'pass' : 'fail'; ?>">
                            <?php echo version_compare(PHP_VERSION, '7.0', '>=') ? '通过 ('.PHP_VERSION.')' : '不通过 ('.PHP_VERSION.')'; ?>
                        </span>
                    </li>
                    <li>
                        <span>PDO SQLite 扩展</span>
                        <span class="<?php echo extension_loaded('pdo_sqlite') ? 'pass' : 'fail'; ?>">
                            <?php echo extension_loaded('pdo_sqlite') ? '通过' : '不通过'; ?>
                        </span>
                    </li>
                    <li>
                        <span>data 目录写入权限</span>
                        <span class="<?php echo is_writable(__DIR__ . '/data') || @mkdir(__DIR__ . '/data', 0755, true) ? 'pass' : 'fail'; ?>">
                            <?php echo is_writable(__DIR__ . '/data') || @mkdir(__DIR__ . '/data', 0755, true) ? '通过' : '不通过，请设置data目录可写'; ?>
                        </span>
                    </li>
                    <li>
                        <span>uploads 目录写入权限</span>
                        <span class="<?php echo is_writable(__DIR__ . '/uploads') || @mkdir(__DIR__ . '/uploads', 0755, true) ? 'pass' : 'fail'; ?>">
                            <?php echo is_writable(__DIR__ . '/uploads') || @mkdir(__DIR__ . '/uploads', 0755, true) ? '通过' : '不通过，请设置uploads目录可写'; ?>
                        </span>
                    </li>
                </ul>
                <div class="btn-group" style="text-align:right;">
                    <a href="install.php?step=2" class="btn">下一步</a>
                </div>

            <?php elseif ($step == 2): ?>
                <h3 style="margin-bottom:15px;">配置管理员账号</h3>
                <form method="post">
                    <div class="form-group">
                        <label>管理员用户名 *</label>
                        <input type="text" name="username" value="<?php echo e($_POST['username'] ?? ''); ?>" required placeholder="请输入管理员用户名">
                        <div class="tip">用于登录后台管理系统</div>
                    </div>
                    <div class="form-group">
                        <label>管理员密码 *</label>
                        <input type="password" name="password" required placeholder="请输入密码，至少6位">
                    </div>
                    <div class="form-group">
                        <label>确认密码 *</label>
                        <input type="password" name="password2" required placeholder="请再次输入密码">
                    </div>
                    <div class="form-group">
                        <label>管理员邮箱</label>
                        <input type="email" name="email" value="<?php echo e($_POST['email'] ?? ''); ?>" placeholder="选填">
                    </div>
                    <div class="btn-group" style="text-align:right;">
                        <a href="install.php?step=1" style="color:#999; margin-right:15px; text-decoration:none;">上一步</a>
                        <button type="submit" class="btn">开始安装</button>
                    </div>
                </form>

            <?php elseif ($step == 3): ?>
                <div class="success-box">
                    <div class="icon">✓</div>
                    <h3>安装成功！</h3>
                    <p>系统已成功安装，请妥善保管您的管理员账号和密码。</p>
                    <p style="color:#f5222d;">安全提示：请删除 install.php 和 install.sql 文件，或重命名 install.lock 文件以防重装。</p>
                    <div class="btn-group">
                        <a href="index.php" class="btn">访问前台</a>
                        <a href="admin/login.php" class="btn" style="background:#333;">进入后台</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
