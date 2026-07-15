<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$installed = file_exists(__DIR__ . '/data/cms.db') && filesize(__DIR__ . '/data/cms.db') > 0;
if ($installed && !isset($_GET['reinstall'])) {
    header('Location: index.php');
    exit;
}

$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step == 2) {
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
        $sql = file_get_contents(__DIR__ . '/install.sql');
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $stmt) {
            if (!empty($stmt)) DB::getInstance()->exec($stmt);
        }

        // 默认设置
        $defaultSettings = [
            'site_name' => '人民检察',
            'site_title' => '人民检察 - 人民检察院法律监督信息公开平台',
            'site_keywords' => '人民检察,人民检察院,检察监督,公益诉讼,检务公开,职务犯罪,刑事检察,民事检察,行政检察',
            'site_description' => '人民检察网是人民检察院面向社会公众的官方信息公开平台',
            'footer_copyright' => '© ' . date('Y') . ' 人民检察  版权所有  主办单位：人民检察院',
            'footer_image' => '',
            'icp' => '',
            'contact_email' => 'contact@example.com',
        ];
        foreach ($defaultSettings as $k => $v) {
            DB::insert('settings', ['key' => $k, 'value' => $v]);
        }

        // 顶级栏目
        $categories = [
            ['name' => '检察要闻', 'slug' => 'yaowen', 'sort_order' => 1],
            ['name' => '审查起诉', 'slug' => 'shencha', 'sort_order' => 2],
            ['name' => '公益诉讼', 'slug' => 'xunshi', 'sort_order' => 3],
            ['name' => '法律法规', 'slug' => 'fagui', 'sort_order' => 4],
            ['name' => '检察视频', 'slug' => 'video', 'sort_order' => 5],
            ['name' => '检察文化', 'slug' => 'wenhua', 'sort_order' => 6],
        ];
        foreach ($categories as $cat) {
            DB::insert('categories', array_merge($cat, ['parent_id' => 0, 'type' => 'article', 'show_in_menu' => 1]));
        }

        // 子栏目
        $subCats = [
            'shencha' => [
                ['name' => '职务犯罪检察', 'slug' => 'zhiji', 'sort_order' => 1],
                ['name' => '刑事检察', 'slug' => 'chufen', 'sort_order' => 2],
                ['name' => '民事行政检察', 'slug' => 'zhuitao', 'sort_order' => 3],
            ],
            'xunshi' => [
                ['name' => '生态环境领域', 'slug' => 'xunshigz', 'sort_order' => 1],
                ['name' => '食品药品安全', 'slug' => 'xunshacg', 'sort_order' => 2],
            ],
            'fagui' => [
                ['name' => '检察法律', 'slug' => 'dangnei', 'sort_order' => 1],
                ['name' => '司法解释', 'slug' => 'guojia', 'sort_order' => 2],
                ['name' => '普法专栏', 'slug' => 'baike', 'sort_order' => 3],
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

        // 超级管理员
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        DB::insert('users', [
            'username' => $username,
            'password' => $hashed,
            'nickname' => '超级管理员',
            'email' => $email,
            'role' => 'super_admin',
            'status' => 1,
        ]);

        // 示例文章
        $sampleArticles = [
            ['检察要闻', '最高人民检察院召开新闻发布会通报公益诉讼工作开展情况', '新时代检察机关深入践行习近平法治思想，依法履行公益诉讼检察职能，办理了一大批生态环境和资源保护、食品药品安全、国有财产保护、国有土地使用权出让、英烈权益保护等领域公益诉讼案件。'],
            ['检察要闻', '某厅级干部涉嫌受贿案一审宣判 检察机关依法履行法律监督职责', '检察机关依法对涉嫌受贿的某厅级干部提起公诉，经法院公开审理，认定其利用职务便利非法收受他人财物，数额特别巨大，依法作出有罪判决。'],
            ['检察要闻', '检察机关开展司法救助专项活动 切实保障困难群众合法权益', '检察机关深入开展"司法救助助力全面推进乡村振兴"专项活动，重点关注因案致贫、因案返贫的困难群众，积极开展国家司法救助工作。'],
            ['审查起诉', '关于深入推进检察公开听证工作的实施意见', '为全面深化检务公开，保障人民群众的知情权、参与权、监督权，推动检察公开听证工作常态化、规范化开展，制定本意见。'],
            ['审查起诉', '中华人民共和国刑事诉讼法（修订）要点解读', '本次刑诉法修订完善了认罪认罚从宽制度、速裁程序、监察与刑事诉讼衔接等多项重要制度，对推进刑事司法治理体系和治理能力现代化具有重要意义。'],
            ['公益诉讼', '检察机关部署"守护美好生活"专项监督活动', '聚焦生态环境和食品药品安全两个重点领域，最高人民检察院部署全国检察机关开展为期三年的"守护美好生活"专项监督活动，依法保护人民群众合法权益。'],
            ['法律法规', '中华人民共和国人民检察院组织法', '本法规定了人民检察院的性质、任务、职权、机构设置、组成人员任免等内容，是检察机关依法履职的根本组织保障。'],
            ['检察文化', '新时代检察精神的价值内涵与实践路径', '检察精神是检察文化的核心，新时代检察精神体现为忠诚、为民、担当、公正、廉洁的核心价值追求，是激励检察人员依法履职的强大精神动力。'],
        ];
        $catMap = [];
        $allCats = DB::fetchAll("SELECT id, name FROM categories");
        foreach ($allCats as $c) $catMap[$c['name']] = $c['id'];

        foreach ($sampleArticles as $idx => $a) {
            $catId = isset($catMap[$a[0]]) ? $catMap[$a[0]] : ($catMap['检察要闻'] ?? 0);
            if (!$catId) continue;
            DB::insert('articles', [
                'category_id' => $catId,
                'title' => $a[1],
                'summary' => $a[2],
                'content' => '<p>' . $a[2] . '</p><p>检察机关深入贯彻习近平法治思想，坚持以人民为中心的发展思想，依法履行法律监督职责，为推进国家治理体系和治理能力现代化贡献检察力量。</p><p>下一步，检察机关将继续加强自身建设，深化司法体制改革，全面提升检察工作质效，努力让人民群众在每一个司法案件中感受到公平正义。</p>',
                'author' => '本站通讯员',
                'source' => '人民检察网',
                'is_top' => $idx < 2 ? 1 : 0,
                'status' => 1,
                'views' => rand(100, 5000),
                'publish_time' => date('Y-m-d H:i:s', time() - rand(0, 30 * 86400)),
            ]);
        }

        // 写入安装锁
        @mkdir(__DIR__ . '/data', 0755, true);
        $lockFile = fopen(__DIR__ . '/data/install.lock', 'w');
        fwrite($lockFile, date('Y-m-d H:i:s'));
        fclose($lockFile);

        header('Location: install.php?step=3');
        exit;
    }
}

$envChecks = [
    'PHP 版本 ≥ 7.0' => version_compare(PHP_VERSION, '7.0', '>='),
    'PDO SQLite 扩展' => extension_loaded('pdo_sqlite'),
    'data 目录可写' => is_writable(__DIR__ . '/data') || @mkdir(__DIR__ . '/data', 0755, true),
    'uploads 目录可写' => is_writable(__DIR__ . '/uploads') || @mkdir(__DIR__ . '/uploads', 0755, true),
    'config 目录可读' => is_readable(__DIR__ . '/config'),
];
$envOk = !in_array(false, $envChecks, true);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>系统安装向导 - 人民检察</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: linear-gradient(135deg, #0a2540 0%, #1e3a5f 100%); min-height: 100vh; }
    </style>
</head>
<body>
    <div class="install-bg">
        <div class="install-card">
            <div class="install-head">
                <div style="display:flex; justify-content:center; margin-bottom:14px;">
                    <svg viewBox="0 0 100 100" width="48" height="48">
                        <path d="M50 12 L58 24 L72 22 L70 36 L82 42 L72 50 L74 64 L60 62 L50 74 L40 62 L26 64 L28 50 L18 42 L30 36 L28 22 L42 24 Z" fill="#c9a227"/>
                        <text x="50" y="58" text-anchor="middle" fill="#0a2540" font-size="20" font-weight="bold" font-family="serif">检</text>
                    </svg>
                </div>
                <h1>人民检察 V7.0</h1>
                <p>系统安装向导</p>
            </div>

            <div class="install-steps">
                <div class="install-step <?php echo $step >= 1 ? 'on' : ''; ?> <?php echo $step > 1 ? 'done' : ''; ?>">
                    <div class="dot">1</div>
                    <div>环境检测</div>
                </div>
                <div class="install-step <?php echo $step >= 2 ? 'on' : ''; ?> <?php echo $step > 2 ? 'done' : ''; ?>">
                    <div class="dot">2</div>
                    <div>账号配置</div>
                </div>
                <div class="install-step <?php echo $step >= 3 ? 'on' : ''; ?>">
                    <div class="dot">3</div>
                    <div>完成</div>
                </div>
            </div>

            <div class="install-body">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo e($error); ?></div>
                <?php endif; ?>

                <?php if ($step == 1): ?>
                    <h3 style="color:var(--pk-blue); font-size:16px; margin-bottom:16px; font-family:var(--pk-font-serif);">运行环境检测</h3>
                    <div style="background:var(--pk-gray-50); border:1px solid var(--pk-gray-200); border-radius:var(--pk-radius); overflow:hidden;">
                        <?php foreach ($envChecks as $name => $pass): ?>
                        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; border-bottom:1px solid var(--pk-gray-100);">
                            <span style="color:var(--pk-gray-700); font-size:14px;"><?php echo e($name); ?></span>
                            <span style="font-weight:600; color:<?php echo $pass ? 'var(--pk-success)' : 'var(--pk-error)'; ?>;">
                                <?php echo $pass ? '✓ 通过' : '✗ 不通过'; ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!$envOk): ?>
                    <div class="alert alert-warning mt-20">
                        环境检测未通过，请联系服务器管理员解决以上问题后再继续安装。
                    </div>
                    <?php endif; ?>

                <?php elseif ($step == 2): ?>
                    <h3 style="color:var(--pk-blue); font-size:16px; margin-bottom:16px; font-family:var(--pk-font-serif);">配置超级管理员账号</h3>
                    <form method="post">
                        <div class="form-row">
                            <label>管理员用户名 <span class="req">*</span></label>
                            <input type="text" name="username" value="<?php echo e($_POST['username'] ?? ''); ?>" required placeholder="3-20个字符" data-validate="username">
                            <div class="form-tip"></div>
                        </div>
                        <div class="form-row">
                            <label>管理员密码 <span class="req">*</span></label>
                            <input type="password" name="password" required placeholder="至少6位字符" data-validate="password">
                            <div class="form-tip"></div>
                        </div>
                        <div class="form-row">
                            <label>确认密码 <span class="req">*</span></label>
                            <input type="password" name="password2" required placeholder="请再次输入密码" data-validate="confirm_password">
                            <div class="form-tip"></div>
                        </div>
                        <div class="form-row">
                            <label>管理员邮箱</label>
                            <input type="email" name="email" value="<?php echo e($_POST['email'] ?? ''); ?>" placeholder="可选填" data-validate="email">
                            <div class="form-tip"></div>
                        </div>
                    </form>

                <?php elseif ($step == 3): ?>
                    <div style="text-align:center; padding:30px 0;">
                        <div style="width:80px; height:80px; border-radius:50%; background:var(--pk-success); color:#fff; font-size:42px; display:flex; align-items:center; justify-content:center; margin:0 auto 18px;">✓</div>
                        <h3 style="color:var(--pk-blue); font-size:22px; margin-bottom:12px; font-family:var(--pk-font-serif);">安装成功</h3>
                        <p style="color:var(--pk-gray-600); margin-bottom:8px;">系统已成功安装，请妥善保管您的管理员账号和密码</p>
                        <p style="color:var(--pk-error); font-size:12px; margin-bottom:24px;">安全提示：建议删除 install.php 与 install.sql 文件以防被恶意重装</p>
                        <div style="display:flex; gap:10px; justify-content:center;">
                            <a href="index.php" class="btn btn-gold">访问前台首页</a>
                            <a href="admin/login.php" class="btn">进入管理后台</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($step == 1): ?>
            <div class="install-foot">
                <a href="../" style="color:var(--pk-gray-500);">返回</a>
                <?php if ($envOk): ?>
                    <a href="install.php?step=2" class="btn">下一步</a>
                <?php else: ?>
                    <button class="btn" disabled style="opacity:0.5;">环境异常</button>
                <?php endif; ?>
            </div>
            <?php elseif ($step == 2): ?>
            <div class="install-foot">
                <a href="install.php?step=1" class="btn btn-ghost">上一步</a>
                <button type="submit" form="installForm" class="btn" onclick="document.querySelector('form').submit();">开始安装</button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
</body>
</html>