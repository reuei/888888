<?php
/**
 * 语云科技 - 员工管理页面
 * 员工卡片列表、新增/编辑、头像上传、社交链接
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/core/Functions.php';
require_admin();

$staffList = get_content('staff') ?: [];
$msg = '';

// 处理AJAX操作
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            $data = json_decode($_POST['data'] ?? '{}', true);
            if (empty($data['name'])) { echo json_encode(['code'=>400,'message'=>'姓名不能为空']); exit; }
            $maxId = 0;
            foreach ($staffList as $s) { if (($s['id'] ?? 0) > $maxId) $maxId = $s['id']; }
            $data['id'] = $maxId + 1;
            $data['created_at'] = date('Y-m-d H:i:s');
            $staffList[] = $data;
            save_content('staff', $staffList);
            echo json_encode(['code'=>200,'message'=>'添加成功','data'=>$data]);
            break;

        case 'update':
            $id = intval($_POST['id'] ?? 0);
            $data = json_decode($_POST['data'] ?? '{}', true);
            foreach ($staffList as &$s) {
                if ((int)$s['id'] === $id) {
                    $data['id'] = $id;
                    $s = array_merge($s, $data);
                    break;
                }
            }
            unset($s);
            save_content('staff', $staffList);
            echo json_encode(['code'=>200,'message'=>'更新成功']);
            break;

        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            $staffList = array_values(array_filter($staffList, fn($s) => (int)$s['id'] !== $id));
            save_content('staff', $staffList);
            echo json_encode(['code'=>200,'message'=>'删除成功']);
            break;

        default:
            echo json_encode(['code'=>400,'message'=>'未知操作']);
    }
    exit;
}

// 排序
usort($staffList, fn($a, $b) => ($a['sort_order'] ?? 99) <=> ($b['sort_order'] ?? 99));
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>员工管理 - 语云科技后台</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
    <style>
        .staff-card-admin {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 24px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
        }

        .staff-card-admin:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
        }

        .staff-avatar-lg {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 16px;
            border: 3px solid var(--card-border);
            display: block;
            margin-left:auto; margin-right:auto;
            background: var(--table-header);
        }

        .staff-name-lg {
            font-size: 17px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .staff-position-lg {
            font-size: 13px;
            color: var(--primary-color);
            margin-bottom: 10px;
            font-weight: 500;
        }

        .staff-bio-text {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 14px;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .staff-social-row {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 14px;
        }

        .staff-social-row a {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            transition: all 0.2s;
            font-size: 13px;
        }

        .staff-social-row a:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .staff-actions-row {
            display: flex;
            gap: 8px;
            justify-content: center;
            padding-top: 14px;
            border-top: 1px solid var(--card-border);
        }
    </style>
</head>
<body>
    <!-- 侧边栏 -->
    <?php include __DIR__ . '/_sidebar.php'; ?>

    <!-- 顶部导航 -->
    <header class="header">
        <div class="header-left">
            <button class="mobile-menu-btn" onclick="document.getElementById('sidebar').classList.add('mobile-show'); document.querySelector('.sidebar-overlay').classList.add('show');"><i class="fas fa-bars"></i></button>
            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fas fa-home"></i></a>
                <span class="breadcrumb-separator">/</span>
                <span>员工管理</span>
            </div>
        </div>
        <div class="header-right">
            <div class="user-dropdown">
                <div class="user-avatar"><?php echo mb_substr($_SESSION['admin_name'] ?? '管', 0, 1); ?></div>
                <div class="user-info"><div class="name"><?php echo e($_SESSION['admin_name'] ?? '管理员'); ?></div><div class="role">超级管理员</div></div>
            </div>
        </div>
    </header>

    <!-- 主内容区 -->
    <main class="main-content">
        <?php if ($msg): ?>
            <div style="background:rgba(40,167,69,0.12); border:1px solid rgba(40,167,69,0.3); color:#51cf66; padding:12px 16px; border-radius:8px; margin-bottom:20px;">
                <i class="fas fa-check-circle"></i> <?php echo e($msg); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-users"></i> 员工团队管理</h3>
                <div style="display:flex; gap:10px;">
                    <span style="color:var(--text-muted); font-size:13px; align-self:center;">共 <?php echo count($staffList); ?> 名成员</span>
                    <button class="btn btn-primary btn-sm" onclick="openStaffModal()">
                        <i class="fas fa-plus"></i> 添加成员
                    </button>
                </div>
            </div>

            <?php if (empty($staffList)): ?>
                <div class="empty-state">
                    <i class="fas fa-user-friends"></i>
                    <h3>暂无员工信息</h3>
                    <p>点击上方按钮添加团队成员</p>
                </div>
            <?php else: ?>
                <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(270px, 1fr)); gap:20px;">
                    <?php foreach ($staffList as $staff): ?>
                        <div class="staff-card-admin">
                            <?php if (!empty($staff['avatar'])): ?>
                                <img src="<?php echo e($staff['avatar']); ?>" alt="<?php echo e($staff['name']); ?>" class="staff-avatar-lg">
                            <?php else: ?>
                                <div class="staff-avatar-lg" style="display:flex;align-items:center;justify-content:center;font-size:32px;color:var(--text-muted);background:linear-gradient(135deg,var(--primary-color),var(--accent-color));color:white;font-weight:bold;">
                                    <?php echo mb_substr($staff['name'] ?? '?', 0, 1); ?>
                                </div>
                            <?php endif; ?>

                            <h4 class="staff-name-lg"><?php echo e($staff['name'] ?? '未命名'); ?></h4>
                            <p class="staff-position-lg"><?php echo e($staff['position'] ?? ''); ?></p>

                            <?php if (!empty($staff['bio'])): ?>
                                <p class="staff-bio-text"><?php echo e(mb_substr($staff['bio'], 0, 80)); ?></p>
                            <?php endif; ?>

                            <?php if (!empty($staff['social']) && is_array($staff['social'])): ?>
                                <div class="staff-social-row">
                                    <?php foreach (['wechat','qq','email','linkedin','github','weibo'] as $social): ?>
                                        <?php if (!empty($staff['social'][$social])): ?>
                                            <a href="<?php echo e($staff['social'][$social]); ?>" target="_blank"
                                               title="<?php echo ucfirst($social); ?>" style="font-size:14px;">
                                                <i class="fab fas fa-<?php
                                                    echo match($social) {
                                                        'wechat' => 'weixin',
                                                        'qq' => 'qq',
                                                        'email' => 'envelope',
                                                        'linkedin' => 'linkedin-in',
                                                        'github' => 'github',
                                                        'weibo' => 'weibo',
                                                        default => 'link'
                                                    };
                                                ?>"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="staff-actions-row">
                                <button class="action-btn edit" onclick="editStaff(<?php echo htmlspecialchars(json_encode($staff), ENT_QUOTES); ?>)" title="编辑">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn delete" data-delete data-name="成员「<?php echo e($staff['name']); ?>」" data-id="<?php echo $staff['id']; ?>" title="删除">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- 新增/编辑模态框 -->
    <div class="modal-overlay" id="staffModal">
        <div class="modal modal-lg">
            <div class="modal-header">
                <h3 id="staffModalTitle">添加团队成员</h3>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <form id="staffForm" onsubmit="return saveStaff(event)">
                <div class="modal-body">
                    <input type="hidden" name="id" id="staffId">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">姓名 <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="姓名">
                        </div>
                        <div class="form-group">
                            <label class="form-label">职位 <span class="required">*</span></label>
                            <input type="text" name="position" class="form-control" required placeholder="如：高级工程师、产品经理">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">头像上传</label>
                        <div style="display:flex; gap:16px; align-items:center;">
                            <div style="width:80px;height:80px;border-radius:50%;border:2px dashed var(--input-border);display:flex;align-items:center;justify-content:center;overflow:hidden;background:var(--input-bg);" id="avatarPreviewBox">
                                <i class="fas fa-user" style="font-size:28px; color:var(--text-muted);" id="avatarPlaceholder"></i>
                                <img src="" id="avatarPreviewImg" style="display:none;width:100%;height:100%;object-fit:cover;">
                            </div>
                            <div style="flex:1;">
                                <div class="upload-area" data-upload="staffAvatar" style="padding:16px;">
                                    <i class="fas fa-camera"></i>
                                    <span>点击上传头像</span>
                                    <input type="file" id="staffAvatar" name="avatar" accept="image/*" style="display:none;">
                                </div>
                                <input type="hidden" name="avatar_path" id="staffAvatarPath">
                                <p class="form-help">建议尺寸 200x200px，正方形效果最佳</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">个人简介</label>
                        <textarea name="bio" class="form-control" rows="3" placeholder="简短介绍这位成员的背景和专长"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">社交链接（可选）</label>
                        <div class="form-row">
                            <div>
                                <small style="color:var(--text-muted); display:block; margin-bottom:4px;"><i class="fab fa-weixin"></i> 微信</small>
                                <input type="text" name="social_wechat" class="form-control" placeholder="微信号或二维码URL">
                            </div>
                            <div>
                                <small style="color:var(--text-muted); display:block; margin-bottom:4px;"><i class="fab fa-qq"></i> QQ</small>
                                <input type="text" name="social_qq" class="form-control" placeholder="QQ号或链接">
                            </div>
                        </div>
                        <div class="form-row" style="margin-top:12px;">
                            <div>
                                <small style="color:var(--text-muted); display:block; margin-bottom:4px;"><i class="fas fa-envelope"></i> 邮箱</small>
                                <input type="email" name="social_email" class="form-control" placeholder="Email地址">
                            </div>
                            <div>
                                <small style="color:var(--text-muted); display:block; margin-bottom:4px;"><i class="fab fa-linkedin"></i> LinkedIn</small>
                                <input type="url" name="social_linkedin" class="form-control" placeholder="LinkedIn主页">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">排序权重</label>
                        <input type="number" name="sort_order" class="form-control" value="0" min="0" style="max-width:200px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('staffModal').closest('.modal-overlay').classList.remove('show')">取消</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 保存</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
    <script>
        function openStaffModal() {
            document.getElementById('staffModalTitle').textContent = '添加团队成员';
            document.getElementById('staffForm').reset();
            document.getElementById('staffId').value = '';
            document.getElementById('avatarPreviewImg').style.display = 'none';
            document.getElementById('avatarPlaceholder').style.display = '';
            Modal.open('staffModal');
        }

        function editStaff(data) {
            document.getElementById('staffModalTitle').textContent = '编辑成员信息';
            const form = document.getElementById('staffForm');
            Object.keys(data).forEach(k => {
                const f = form.querySelector(`[name="${k}"]`);
                if (f && f.type !== 'file') f.value = data[k] || '';
            });

            // 处理社交链接
            if (data.social && typeof data.social === 'object') {
                Object.keys(data.social).forEach(key => {
                    const f = form.querySelector(`[name="social_${key}"]`);
                    if (f) f.value = data.social[key];
                });
            }

            // 头像预览
            if (data.avatar) {
                const img = document.getElementById('avatarPreviewImg');
                img.src = data.avatar;
                img.style.display = '';
                document.getElementById('avatarPlaceholder').style.display = 'none';
                document.getElementById('staffAvatarPath').value = data.avatar;
            }

            Modal.open('staffModal');
        }

        async function saveStaff(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            const data = {};
            ['name','position','bio','sort_order'].forEach(k => {
                let v = formData.get(k);
                if (v !== null && v !== '') data[k] = v;
            });

            // 收集社交链接
            data.social = {};
            ['wechat','qq','email','linkedin'].forEach(s => {
                const v = formData.get(`social_${s}`);
                if (v) data.social[s] = v;
            });

            const id = formData.get('id');
            const action = id ? 'update' : 'create';

            // 处理头像上传
            const fileInput = form.querySelector('#staffAvatar');
            if (fileInput && fileInput.files.length > 0) {
                try {
                    Toast.info('正在上传头像...');
                    const uploadData = new FormData();
                    uploadData.append('file', fileInput.files[0]);
                    const resp = await fetch('../api/upload.php', { method:'POST', body:uploadData }).then(r=>r.json());
                    if (resp.code === 200) {
                        data.avatar = resp.data.path;
                        Toast.success('头像上传成功');
                    } else throw new Error(resp.message);
                } catch(err) {
                    Toast.error(err.message);
                    return false;
                }
            } else if (formData.get('avatar_path')) {
                data.avatar = formData.get('avatar_path');
            }

            const postData = new FormData();
            postData.append('action', action);
            if (id) postData.append('id', id);
            postData.append('data', JSON.stringify(data));

            try {
                const resp = await fetch('?ajax=1', { method:'POST', body:postData }).then(r=>r.json());
                if (resp.code === 200) {
                    Toast.success(resp.message);
                    setTimeout(() => location.reload(), 800);
                } else {
                    Toast.error(resp.message);
                }
            } catch(err) {
                Toast.error('网络错误');
            }
            return false;
        }

        // 头像预览
        document.getElementById('staffAvatar')?.addEventListener('change', function() {
            if (this.files?.[0]) {
                const reader = new FileReader();
                reader.onload = ev => {
                    const img = document.getElementById('avatarPreviewImg');
                    img.src = ev.target.result;
                    img.style.display = '';
                    document.getElementById('avatarPlaceholder').style.display = 'none';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // 删除操作
        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', () => {
                Modal.confirm('确认删除', `确定要移除${btn.dataset.name}吗？`, async () => {
                    const fd = new FormData();
                    fd.append('action', 'delete');
                    fd.append('id', btn.dataset.id);
                    const resp = await fetch('?ajax=1', { method:'POST', body:fd }).then(r=>r.json());
                    Toast.success(resp.message);
                    setTimeout(() => location.reload(), 500);
                });
            });
        });
    </script>
</body>
</html>
