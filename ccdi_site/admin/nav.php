<?php
/**
 * 后台管理 - 导航管理
 * 支持列表、添加、编辑、删除操作，树形结构展示
 */
define('SYSTEM_INIT', true);
require_once __DIR__ . '/../includes/init.php';
require_admin();

$action = get('action', 'list');
$id = (int)get('id', 0);
$page = max(1, (int)get('page', 1));
$per_page = ADMIN_ITEMS_PER_PAGE;
$message = '';
$error = '';

/**
 * 获取导航树形结构（用于列表展示）
 */
function get_nav_tree($parent_id = 0, $level = 0) {
    $items = db_fetch_all("SELECT * FROM nav_menu WHERE parent_id = ? ORDER BY sort_order ASC, id ASC", [$parent_id]);
    $tree = [];
    foreach ($items as $item) {
        $item['level'] = $level;
        $item['children'] = get_nav_tree($item['id'], $level + 1);
        $tree[] = $item;
    }
    return $tree;
}

/**
 * 扁平化树形结构为表格行
 */
function flatten_nav_tree($tree, &$result = []) {
    foreach ($tree as $item) {
        $children = $item['children'];
        unset($item['children']);
        $result[] = $item;
        if (!empty($children)) {
            flatten_nav_tree($children, $result);
        }
    }
    return $result;
}

/**
 * 获取父级导航下拉选项
 */
function get_parent_options($exclude_id = 0, $parent_id = 0, $level = 0) {
    $items = db_fetch_all("SELECT * FROM nav_menu WHERE parent_id = ? ORDER BY sort_order ASC, id ASC", [$parent_id]);
    $options = [];
    foreach ($items as $item) {
        if ($item['id'] == $exclude_id) continue;
        $prefix = $level > 0 ? str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . '├ ' : '';
        $options[] = ['id' => $item['id'], 'name' => $prefix . $item['name'], 'level' => $level];
        $sub_options = get_parent_options($exclude_id, $item['id'], $level + 1);
        $options = array_merge($options, $sub_options);
    }
    return $options;
}

// ==================== 删除操作 ====================
if ($action === 'delete' && $id > 0) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $error = '无效的请求方式';
    } elseif (!csrf_verify(post('csrf_token', ''))) {
        $error = '安全验证失败，请刷新页面后重试';
    } else {
        $nav = db_fetch("SELECT * FROM nav_menu WHERE id = ?", [$id]);
        if (!$nav) {
            $error = '导航项不存在';
        } else {
            // 将子导航的 parent_id 设为 0
            db_update('nav_menu', ['parent_id' => 0], 'parent_id = ?', [$id]);
            $result = db_delete('nav_menu', 'id = ?', [$id]);
            if ($result !== false) {
                add_log('nav_delete', "删除导航：{$nav['name']}");
                $message = '导航项删除成功';
                $action = 'list';
            } else {
                $error = '删除失败，请稍后重试';
            }
        }
    }
}

// ==================== 保存操作（添加/编辑） ====================
if ($action === 'save') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $error = '无效的请求方式';
    } elseif (!csrf_verify(post('csrf_token', ''))) {
        $error = '安全验证失败，请刷新页面后重试';
    } else {
        $name = trim(post('name', ''));
        $url = trim(post('url', ''));
        $parent_id = (int)post('parent_id', 0);
        $target = in_array(post('target', '_self'), ['_self', '_blank']) ? post('target') : '_self';
        $sort_order = (int)post('sort_order', 0);
        $status = post('status', '1') === '1' ? 1 : 0;

        if (empty($name)) {
            $error = '请输入导航名称';
        }

        if (empty($url)) {
            $error = '请输入链接地址';
        }

        if (empty($error)) {
            $data = [
                'parent_id' => $parent_id,
                'name' => $name,
                'url' => $url,
                'target' => $target,
                'sort_order' => $sort_order,
                'status' => $status
            ];

            if ($id > 0) {
                $result = db_update('nav_menu', $data, 'id = ?', [$id]);
                if ($result !== false) {
                    add_log('nav_update', "更新导航：{$name}");
                    $message = '导航项更新成功';
                } else {
                    $error = '更新失败，请稍后重试';
                }
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $new_id = db_insert('nav_menu', $data);
                if ($new_id) {
                    add_log('nav_create', "创建导航：{$name}");
                    $message = '导航项添加成功';
                    $id = $new_id;
                } else {
                    $error = '添加失败，请稍后重试';
                }
            }
        }

        if (empty($error)) {
            $action = 'list';
        } else {
            $action = ($id > 0) ? 'edit' : 'add';
        }
    }
}

// ==================== 列表视图 ====================
if ($action === 'list') {
    $nav_tree = get_nav_tree();
    $nav_list = flatten_nav_tree($nav_tree);

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title">导航管理</h2>
        <a href="<?php echo admin_url('nav.php?action=add'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> 添加导航
        </a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th width="40">ID</th>
                    <th>导航名称</th>
                    <th width="180">链接地址</th>
                    <th width="70">打开方式</th>
                    <th width="60">排序</th>
                    <th width="60">状态</th>
                    <th width="120">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($nav_list)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:#999;padding:40px;">暂无导航数据</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($nav_list as $n): ?>
                    <tr>
                        <td><?php echo $n['id']; ?></td>
                        <td>
                            <span style="padding-left: <?php echo $n['level'] * 24; ?>px;">
                                <?php if ($n['level'] > 0): ?>
                                    <span style="color:#ccc;margin-right:4px;">├</span>
                                <?php endif; ?>
                                <a href="<?php echo admin_url('nav.php?action=edit&id=' . $n['id']); ?>" title="<?php echo htmlspecialchars($n['name']); ?>">
                                    <?php echo htmlspecialchars($n['name']); ?>
                                </a>
                            </span>
                        </td>
                        <td>
                            <span class="nav-url" title="<?php echo htmlspecialchars($n['url']); ?>">
                                <?php echo htmlspecialchars(str_cut($n['url'], 30)); ?>
                            </span>
                        </td>
                        <td><?php echo $n['target'] === '_blank' ? '新窗口' : '当前页'; ?></td>
                        <td><?php echo $n['sort_order']; ?></td>
                        <td>
                            <span class="badge <?php echo $n['status'] ? 'badge-success' : 'badge-disabled'; ?>">
                                <?php echo $n['status'] ? '启用' : '禁用'; ?>
                            </span>
                        </td>
                        <td class="table-actions">
                            <a href="<?php echo admin_url('nav.php?action=edit&id=' . $n['id']); ?>" class="btn btn-sm btn-secondary" title="编辑">
                                <i class="fas fa-edit"></i> 编辑
                            </a>
                            <form method="post" action="<?php echo admin_url('nav.php?action=delete&id=' . $n['id']); ?>" style="display:inline;" onsubmit="return confirm('确定要删除导航「<?php echo htmlspecialchars(addslashes($n['name'])); ?>」吗？子导航将变为顶级导航。');">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-danger" title="删除">
                                    <i class="fas fa-trash"></i> 删除
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <style>
    .admin-page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .admin-page-title { margin: 0; font-size: 20px; color: #333; }
    .btn { display: inline-block; padding: 8px 20px; font-size: 14px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; transition: all 0.3s; }
    .btn-primary { background: #c41230; color: #fff; }
    .btn-primary:hover { background: #a00e28; }
    .btn-secondary { background: #f0f0f0; color: #333; }
    .btn-secondary:hover { background: #e0e0e0; }
    .btn-danger { background: #ff4d4f; color: #fff; }
    .btn-danger:hover { background: #e04345; }
    .btn-sm { padding: 4px 12px; font-size: 12px; }
    .alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; }
    .alert-success { background: #f6ffed; border: 1px solid #b7eb8f; color: #389e0d; }
    .alert-error { background: #fff2f0; border: 1px solid #ffccc7; color: #cf1322; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .badge-success { background: #f6ffed; color: #52c41a; }
    .badge-disabled { background: #f5f5f5; color: #999; }
    .table-container { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 20px; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: #fafafa; padding: 12px 14px; text-align: left; font-size: 13px; font-weight: 600; color: #555; border-bottom: 1px solid #e8e8e8; }
    .data-table td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f0f0f0; color: #333; vertical-align: middle; }
    .data-table tr:hover td { background: #fafafa; }
    .data-table a { color: #c41230; text-decoration: none; }
    .data-table a:hover { text-decoration: underline; }
    .table-actions { white-space: nowrap; }
    .table-actions form { display: inline-block; }
    .nav-url { color: #888; font-size: 12px; }
    .pagination { text-align: center; margin-top: 20px; }
    .pagination ul { display: inline-flex; list-style: none; padding: 0; margin: 0; gap: 4px; }
    .pagination li { display: inline; }
    .pagination a, .pagination span { display: inline-block; padding: 6px 12px; border-radius: 4px; font-size: 13px; color: #333; text-decoration: none; border: 1px solid #d9d9d9; background: #fff; }
    .pagination a:hover { border-color: #c41230; color: #c41230; }
    .pagination .active span { background: #c41230; color: #fff; border-color: #c41230; }
    .pagination li span { border: 1px solid #d9d9d9; }
    </style>

    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// ==================== 添加/编辑表单 ====================
if ($action === 'add' || $action === 'edit') {
    $nav = [
        'id' => 0,
        'parent_id' => 0,
        'name' => '',
        'url' => '',
        'target' => '_self',
        'sort_order' => 0,
        'status' => 1,
    ];

    if ($action === 'edit') {
        if ($id <= 0) {
            $error = '缺少导航ID';
        } else {
            $existing = db_fetch("SELECT * FROM nav_menu WHERE id = ?", [$id]);
            if (!$existing) {
                $error = '导航项不存在';
            } else {
                $nav = $existing;
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nav['parent_id'] = (int)post('parent_id', $nav['parent_id']);
        $nav['name'] = post('name', $nav['name']);
        $nav['url'] = post('url', $nav['url']);
        $nav['target'] = in_array(post('target', '_self'), ['_self', '_blank']) ? post('target') : '_self';
        $nav['sort_order'] = (int)post('sort_order', $nav['sort_order']);
        $nav['status'] = post('status', '1') === '1' ? 1 : 0;
    }

    $is_edit = ($action === 'edit' && $nav['id'] > 0);
    $form_title = $is_edit ? '编辑导航' : '添加导航';
    $parent_options = get_parent_options($is_edit ? $nav['id'] : 0);

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title"><?php echo $form_title; ?></h2>
        <a href="<?php echo admin_url('nav.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> 返回列表
        </a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo admin_url('nav.php?action=save' . ($is_edit ? '&id=' . $nav['id'] : '')); ?>" class="nav-form">
        <?php echo csrf_field(); ?>
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $nav['id']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="name">导航名称 <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($nav['name']); ?>" required placeholder="请输入导航名称" maxlength="50">
            </div>
            <div class="form-group form-group-side">
                <label for="parent_id">上级导航</label>
                <select id="parent_id" name="parent_id">
                    <option value="0">-- 顶级导航 --</option>
                    <?php foreach ($parent_options as $opt): ?>
                        <option value="<?php echo $opt['id']; ?>" <?php echo $nav['parent_id'] == $opt['id'] ? 'selected' : ''; ?>>
                            <?php echo $opt['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="form-hint">选择上级导航可创建子菜单</span>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="url">链接地址 <span class="required">*</span></label>
                <input type="text" id="url" name="url" value="<?php echo htmlspecialchars($nav['url']); ?>" required placeholder="如：/article.php?id=1 或 https://..." maxlength="500">
                <span class="form-hint">导航点击后跳转的链接地址</span>
            </div>
            <div class="form-group form-group-side">
                <label for="target">打开方式</label>
                <select id="target" name="target">
                    <option value="_self" <?php echo $nav['target'] === '_self' ? 'selected' : ''; ?>>当前页 (_self)</option>
                    <option value="_blank" <?php echo $nav['target'] === '_blank' ? 'selected' : ''; ?>>新窗口 (_blank)</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="sort_order">排序</label>
                <input type="number" id="sort_order" name="sort_order" value="<?php echo $nav['sort_order']; ?>" placeholder="数字越小越靠前" min="0" step="1">
                <span class="form-hint">数字越小越靠前</span>
            </div>
            <div class="form-group">
                <label for="status">状态</label>
                <select id="status" name="status">
                    <option value="1" <?php echo $nav['status'] == 1 ? 'selected' : ''; ?>>启用</option>
                    <option value="0" <?php echo $nav['status'] == 0 ? 'selected' : ''; ?>>禁用</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?php echo $is_edit ? '更新导航' : '添加导航'; ?>
            </button>
            <a href="<?php echo admin_url('nav.php'); ?>" class="btn btn-secondary">取消</a>
        </div>
    </form>

    <style>
    .admin-page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .admin-page-title { margin: 0; font-size: 20px; color: #333; }
    .btn { display: inline-block; padding: 8px 20px; font-size: 14px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; transition: all 0.3s; }
    .btn-primary { background: #c41230; color: #fff; }
    .btn-primary:hover { background: #a00e28; }
    .btn-secondary { background: #f0f0f0; color: #333; }
    .btn-secondary:hover { background: #e0e0e0; }
    .alert { padding: 12px 16px; border-radius: 4px; margin-bottom: 16px; font-size: 14px; }
    .alert-success { background: #f6ffed; border: 1px solid #b7eb8f; color: #389e0d; }
    .alert-error { background: #fff2f0; border: 1px solid #ffccc7; color: #cf1322; }
    .nav-form { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); padding: 24px; }
    .form-row { display: flex; gap: 20px; margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; flex: 1; }
    .form-group-main { flex: 2; }
    .form-group-side { flex: 1; }
    .form-group label { display: block; font-size: 14px; font-weight: 600; color: #555; margin-bottom: 6px; }
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group select,
    .form-group textarea { width: 100%; padding: 9px 12px; border: 1px solid #d9d9d9; border-radius: 4px; font-size: 14px; font-family: inherit; transition: border-color 0.3s; box-sizing: border-box; }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { border-color: #c41230; outline: none; box-shadow: 0 0 0 2px rgba(196,18,48,0.1); }
    .form-group textarea { resize: vertical; }
    .form-hint { display: block; font-size: 12px; color: #999; margin-top: 4px; }
    .required { color: #c41230; }
    .form-actions { margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f0f0; display: flex; gap: 12px; }
    </style>

    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// ==================== 其他未知操作，回退到列表 ====================
redirect(admin_url('nav.php'));