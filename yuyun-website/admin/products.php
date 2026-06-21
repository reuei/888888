<?php
/**
 * 语云科技 - 产品管理页面
 * 产品列表、新增/编辑弹窗、状态管理等
 */

session_start();
define('YUYUN_ROOT', dirname(__DIR__));
require_once YUYUN_ROOT . '/core/Functions.php';
require_admin();

// FontAwesome图标列表（常用）
$fontAwesomeIcons = [
    'fa-cloud', 'fa-server', 'fa-database', 'fa-shield-alt', 'fa-lock',
    'fa-code', 'fa-mobile-alt', 'fa-desktop', 'fa-laptop', 'fa-tablet-alt',
    'fa-cogs', 'fa-wrench', 'fa-tools', 'fa-rocket', 'fa-bolt',
    'fa-chart-line', 'fa-chart-bar', 'fa-chart-pie', 'fa-tachometer-alt', 'fa-analytics',
    'fa-envelope', 'fa-comments', 'fa-bell', 'fa-paper-plane', 'fa-sms',
    'fa-shopping-cart', 'fa-credit-card', 'fa-wallet', 'fa-coins', 'fa-dollar-sign',
    'fa-users', 'fa-user-tie', 'fa-user-shield', 'fa-handshake', 'fa-building',
    'fa-globe', 'fa-network-wired', 'fa-wifi', 'fa-broadcast-tower', 'fa-satellite',
    'fa-camera', 'fa-video', 'fa-microphone', 'fa-headphones', 'fa-volume-up'
];

// 获取产品数据
$products = get_content('products') ?: [];

// 处理AJAX操作
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json; charset=utf-8');

    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'create':
            $data = json_decode($_POST['data'] ?? '{}', true);
            if (empty($data['name'])) { echo json_encode(['code'=>400,'message'=>'产品名称不能为空']); exit; }
            $maxId = 0;
            foreach ($products as $p) { if (($p['id'] ?? 0) > $maxId) $maxId = $p['id']; }
            $data['id'] = $maxId + 1;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            $products[] = $data;
            save_content('products', $products);
            echo json_encode(['code'=>200, 'message'=>'创建成功', 'data'=>$data]);
            break;

        case 'update':
            $id = intval($_POST['id'] ?? 0);
            $data = json_decode($_POST['data'] ?? '{}', true);
            foreach ($products as &$p) {
                if ((int)$p['id'] === $id) {
                    $data['id'] = $id;
                    $data['updated_at'] = date('Y-m-d H:i:s');
                    $p = array_merge($p, $data);
                    break;
                }
            }
            unset($p);
            save_content('products', $products);
            echo json_encode(['code'=>200, 'message'=>'更新成功']);
            break;

        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            $products = array_values(array_filter($products, fn($p) => (int)$p['id'] !== $id));
            save_content('products', $products);
            echo json_encode(['code'=>200, 'message'=>'删除成功']);
            break;

        default:
            echo json_encode(['code'=>400, 'message'=>'未知操作']);
    }
    exit;
}

// 排序显示
usort($products, fn($a, $b) => ($a['sort_order'] ?? 0) <=> ($b['sort_order'] ?? 0));
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>产品管理 - 语云科技后台</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">
</head>
<body>
    <!-- 侧边栏 -->
    <?php include __DIR__ . '/_sidebar.php'; ?>

    <!-- 顶部导航 -->
    <header class="header">
        <div class="header-left">
            <button class="mobile-menu-btn" onclick="document.getElementById('sidebar').classList.add('mobile-show'); document.querySelector('.sidebar-overlay').classList.add('show');">
                <i class="fas fa-bars"></i>
            </button>
            <div class="breadcrumb">
                <a href="dashboard.php"><i class="fas fa-home"></i></a>
                <span class="breadcrumb-separator">/</span>
                <span>产品管理</span>
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
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-box"></i> 产品服务管理</h3>
                <button class="btn btn-primary btn-sm" onclick="openProductModal()">
                    <i class="fas fa-plus"></i> 新增产品
                </button>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <p style="color:var(--text-secondary); font-size:14px;">
                    共 <strong><?php echo count($products); ?></strong> 个产品服务
                </p>
                <div class="search-box">
                    <input type="text" placeholder="搜索产品..." data-search>
                    <i class="fas fa-search"></i>
                </div>
            </div>

            <div class="table-container">
                <table class="data-table" id="productTable">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th width="50">图标</th>
                            <th>名称</th>
                            <th>价格</th>
                            <th>特性标签</th>
                            <th width="80">状态</th>
                            <th width="70">排序</th>
                            <th width="140">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr><td colspan="8" class="text-center text-muted" style="padding:40px;">
                                <i class="fas fa-inbox" style="font-size:36px; opacity:0.2;"></i><br><br>暂无产品数据，点击右上角添加
                            </td></tr>
                        <?php else:
                            foreach ($products as $product):
                                $statusClass = ($product['status'] ?? 'active') === 'active' ? 'badge-success' : 'badge-secondary';
                                $statusText = ($product['status'] ?? 'active') === 'active' ? '上架' : '下架';
                                $features = is_string($product['features']) ? json_decode($product['features'], true) : ($product['features'] ?? []);
                        ?>
                            <tr data-id="<?php echo $product['id']; ?>">
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <?php if (!empty($product['icon'])): ?>
                                        <i class="fab fas <?php echo e($product['icon']); ?>" style="font-size:22px; color:var(--primary-color);"></i>
                                    <?php else: ?>
                                        <i class="fas fa-box" style="font-size:18px; color:var(--text-muted);"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo e($product['name'] ?? '-'); ?></strong>
                                    <?php if (!empty($product['description'])): ?>
                                        <br><small style="color:var(--text-muted);"><?php echo e(mb_substr($product['description'], 0, 40)); ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($product['price'])): ?>
                                        <strong style="color:var(--accent-color);">¥<?php echo e($product['price']); ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">面议</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($features) && is_array($features)): ?>
                                        <?php foreach (array_slice($features, 0, 3) as $feature): ?>
                                            <span class="badge badge-info" style="margin-right:4px;"><?php echo e($feature); ?></span>
                                        <?php endforeach; ?>
                                        <?php if (count($features) > 3): ?>
                                            <span class="badge badge-secondary">+<?php echo count($features) - 3; ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                                <td><?php echo $product['sort_order'] ?? 0; ?></td>
                                <td>
                                    <div class="table-actions">
                                        <button class="action-btn edit" title="编辑" onclick="editProduct(<?php echo htmlspecialchars(json_encode($product), ENT_QUOTES); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn delete" title="删除"
                                            data-delete data-name="产品「<?php echo e($product['name'] ?? ''); ?>」"
                                            data-id="<?php echo $product['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- 新增/编辑产品模态框 -->
    <div class="modal-overlay" id="productModal">
        <div class="modal modal-lg">
            <div class="modal-header">
                <h3 id="productModalTitle">新增产品</h3>
                <button type="button" class="modal-close">&times;</button>
            </div>
            <form id="productForm" onsubmit="return saveProduct(event)">
                <div class="modal-body">
                    <input type="hidden" name="id" id="productId">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">产品名称 <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="请输入产品或服务名称">
                        </div>
                        <div class="form-group">
                            <label class="form-label">价格</label>
                            <div class="input-wrapper" style="position:relative;">
                                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%); color:var(--accent-color); font-weight:bold;">¥</span>
                                <input type="text" name="price" class="form-control" placeholder="如：9999 或 留空表示面议" style="padding-left:28px;">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">产品描述</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="简要描述产品功能与特点"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group" style="flex:2;">
                            <label class="form-label">图标选择 (FontAwesome)</label>
                            <div style="display:flex; gap:8px;">
                                <input type="text" name="icon" class="form-control" id="iconInput" placeholder="fa-cloud" value="fa-cloud" style="flex:1;">
                                <div style="width:44px;height:42px;border-radius:8px;background:var(--input-bg);border:1px solid var(--input-border); display:flex;align-items:center;justify-content:center;" id="iconPreview">
                                    <i class="fas fa-cloud" style="color:var(--primary-color); font-size:20px;"></i>
                                </div>
                            </div>
                            <p class="form-help">输入FontAwesome图标类名，如 fa-server、fa-database 等</p>

                            <!-- 常用图标快捷选择 -->
                            <div style="margin-top:10px; padding:12px; background:var(--table-header); border-radius:8px;">
                                <p style="font-size:12px; color:var(--text-muted); margin-bottom:8px;">常用图标：</p>
                                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                                    <?php foreach (array_slice($fontAwesomeIcons, 0, 24) as $icon): ?>
                                        <button type="button" class="icon-pick-btn" style="
                                            width:36px; height:32px; border:1px solid var(--input-border); border-radius:6px;
                                            background:var(--card-bg); cursor:pointer; transition:all 0.2s;
                                            display:flex; align-items:center; justify-content:center; color:var(--text-secondary);
                                        " data-icon="<?php echo $icon; ?>" onclick="pickIcon(this)">
                                            <i class="fas <?php echo $icon; ?>"></i>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">状态</label>
                            <select name="status" class="form-control">
                                <option value="active">上架</option>
                                <option value="inactive">下架</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">排序权重</label>
                            <input type="number" name="sort_order" class="form-control" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">&nbsp;</label>
                            <p class="form-help" style="padding-top:8px;">数字越小越靠前</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">特性标签 (JSON数组)</label>
                        <textarea name="features" class="form-control" rows="2" placeholder='["7x24小时", "SSL加密", "免费试用"]'></textarea>
                        <p class="form-help">JSON格式数组，每个元素为一个特性描述</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('productModal').closest('.modal-overlay').classList.remove('show')">取消</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 保存</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
    <script>
        function openProductModal() {
            document.getElementById('productModalTitle').textContent = '新增产品';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            pickIcon(document.querySelector('[data-icon="fa-cloud"]'));
            Modal.open('productModal');
        }

        function editProduct(data) {
            document.getElementById('productModalTitle').textContent = '编辑产品';
            const form = document.getElementById('productForm');
            Object.keys(data).forEach(k => {
                const f = form.querySelector(`[name="${k}"]`);
                if (f && f.type !== 'file') f.value = data[k] || '';
            });
            // 更新图标预览
            const iconVal = data.icon || 'fa-cloud';
            document.getElementById('iconInput').value = iconVal;
            updateIconPreview(iconVal);
            Modal.open('productModal');
        }

        function pickIcon(btn) {
            const icon = btn.dataset.icon;
            document.getElementById('iconInput').value = icon;
            updateIconPreview(icon);

            // 高亮选中
            document.querySelectorAll('.icon-pick-btn').forEach(b => b.style.borderColor = '');
            btn.style.borderColor = 'var(--primary-color)';
            btn.style.background = 'rgba(0,102,204,0.1)';
            btn.style.color = 'var(--primary-color)';
        }

        function updateIconPreview(iconClass) {
            const preview = document.getElementById('iconPreview');
            preview.innerHTML = `<i class="fas ${iconClass}" style="color:var(--primary-color); font-size:20px;"></i>`;
        }

        // 图标输入框实时更新预览
        document.getElementById('iconInput')?.addEventListener('input', function() {
            updateIconPreview(this.value || 'fa-question');
        });

        async function saveProduct(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            const data = {};
            ['name','description','icon','price','status','sort_order','features'].forEach(k => {
                let val = formData.get(k);
                if (k === 'features' && val) {
                    try { val = JSON.parse(val); } catch(e) { val = [val]; }
                }
                if (val !== null && val !== '') data[k] = val;
            });

            const id = formData.get('id');
            const action = id ? 'update' : 'create';

            const postData = new FormData();
            postData.append('action', action);
            if (id) postData.append('id', id);
            postData.append('data', JSON.stringify(data));

            try {
                Toast.info('正在保存...');
                const resp = await fetch('?ajax=1', { method:'POST', body:postData }).then(r=>r.json());
                if (resp.code === 200) {
                    Toast.success(resp.message);
                    setTimeout(() => location.reload(), 800);
                } else {
                    Toast.error(resp.message);
                }
            } catch(err) {
                Toast.error('网络错误：' + err.message);
            }
            return false;
        }

        // 删除操作
        document.querySelectorAll('[data-delete]').forEach(btn => {
            btn.addEventListener('click', () => {
                Modal.confirm('确认删除', `确定要删除${btn.dataset.name}吗？`, async () => {
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
