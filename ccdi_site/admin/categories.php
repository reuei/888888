<?php
/**
 * 后台管理 - 分类管理
 * 支持列表、添加、编辑、删除操作
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

// ==================== 删除操作 ====================
if ($action === 'delete' && $id > 0) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $error = '无效的请求方式';
    } elseif (!csrf_verify(post('csrf_token', ''))) {
        $error = '安全验证失败，请刷新页面后重试';
    } else {
        $category = db_fetch("SELECT * FROM categories WHERE id = ?", [$id]);
        if (!$category) {
            $error = '分类不存在';
        } else {
            // 检查是否有子分类
            $has_children = db_fetch("SELECT id FROM categories WHERE parent_id = ? LIMIT 1", [$id]);
            if ($has_children) {
                $error = '该分类下存在子分类，无法删除';
            } else {
                // 检查是否有文章关联
                $has_articles = db_fetch("SELECT id FROM articles WHERE category_id = ? LIMIT 1", [$id]);
                if ($has_articles) {
                    $error = '该分类下存在文章，无法删除';
                } else {
                    $result = db_delete('categories', 'id = ?', [$id]);
                    if ($result !== false) {
                        add_log('category_delete', "删除分类：{$category['name']}");
                        $message = '分类删除成功';
                        $action = 'list';
                    } else {
                        $error = '删除失败，请稍后重试';
                    }
                }
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
        $slug = trim(post('slug', ''));
        $description = trim(post('description', ''));
        $parent_id = (int)post('parent_id', 0);
        $sort_order = (int)post('sort_order', 0);
        $status = post('status', '1') === '1' ? 1 : 0;
        $is_nav = post('is_nav', '0') === '1' ? 1 : 0;
        $icon = trim(post('icon', ''));

        // 验证分类名称
        if (empty($name)) {
            $error = '请输入分类名称';
        }

        // 自动生成 slug
        if (empty($slug) && !empty($name)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9\x{4e00}-\x{9fa5}]+/u', '-', $name));
            $slug = trim($slug, '-');
        }
        if (empty($slug)) {
            $slug = 'category-' . time();
        }

        // 检查 slug 唯一性
        if (empty($error)) {
            if ($id > 0) {
                $existing = db_fetch("SELECT id FROM categories WHERE slug = ? AND id != ?", [$slug, $id]);
            } else {
                $existing = db_fetch("SELECT id FROM categories WHERE slug = ?", [$slug]);
            }
            if ($existing) {
                $slug .= '-' . time();
            }
        }

        // 防止将分类的 parent_id 设置为自己
        if ($id > 0 && $parent_id == $id) {
            $error = '不能将分类的上级设为自己';
        }

        if (empty($error)) {
            $data = [
                'parent_id' => $parent_id,
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'icon' => $icon,
                'sort_order' => $sort_order,
                'status' => $status,
                'is_nav' => $is_nav,
            ];

            if ($id > 0) {
                // 编辑模式
                $result = db_update('categories', $data, 'id = ?', [$id]);
                if ($result !== false) {
                    add_log('category_update', "更新分类：{$name}");
                    $message = '分类更新成功';
                } else {
                    $error = '更新失败，请稍后重试';
                }
            } else {
                // 添加模式
                $data['created_at'] = date('Y-m-d H:i:s');
                $new_id = db_insert('categories', $data);
                if ($new_id) {
                    add_log('category_create', "创建分类：{$name}");
                    $message = '分类添加成功';
                    $id = $new_id;
                } else {
                    $error = '添加失败，请稍后重试';
                }
            }
        }

        // 保存成功后跳转到列表
        if (empty($error)) {
            $action = 'list';
        } else {
            // 保持在编辑表单
            $action = ($id > 0) ? 'edit' : 'add';
        }
    }
}

// ==================== 列表视图（默认） ====================
if ($action === 'list') {
    $total = db_count('categories');
    $offset = ($page - 1) * $per_page;
    $categories = db_fetch_all(
        "SELECT * FROM categories ORDER BY sort_order ASC, id ASC LIMIT ? OFFSET ?",
        [$per_page, $offset]
    );

    // 构建分类层级树
    $cat_map = [];
    $cat_tree = [];
    foreach ($categories as $cat) {
        $cat_map[$cat['id']] = $cat;
        $cat_map[$cat['id']]['children'] = [];
    }
    foreach ($cat_map as $cid => $cat) {
        if ($cat['parent_id'] > 0 && isset($cat_map[$cat['parent_id']])) {
            $cat_map[$cat['parent_id']]['children'][] = $cat;
        } elseif ($cat['parent_id'] == 0) {
            $cat_tree[] = $cat;
        }
    }

    // 获取所有分类用于 parent_id 显示
    $all_categories = db_fetch_all("SELECT id, name, parent_id FROM categories ORDER BY sort_order ASC, id ASC");

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title">分类管理</h2>
        <a href="<?php echo admin_url('categories.php?action=add'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> 添加分类
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
                    <th>分类名称</th>
                    <th width="120">别名</th>
                    <th width="80">排序</th>
                    <th width="80">状态</th>
                    <th width="80">导航显示</th>
                    <th width="140">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($cat_tree)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:#999;padding:40px;">暂无分类数据</td>
                    </tr>
                <?php else: ?>
                    <?php
                    // 递归渲染分类行
                    function render_category_rows($cats, $level = 0) {
                        foreach ($cats as $cat):
                            $prefix = $level > 0 ? str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . '└ ' : '';
                    ?>
                    <tr>
                        <td><?php echo $cat['id']; ?></td>
                        <td>
                            <strong><?php echo $prefix . htmlspecialchars($cat['name']); ?></strong>
                            <?php if (!empty($cat['description'])): ?>
                                <br><small style="color:#999;"><?php echo htmlspecialchars(str_cut($cat['description'], 50)); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                        <td><?php echo $cat['sort_order']; ?></td>
                        <td>
                            <span class="badge <?php echo $cat['status'] ? 'badge-success' : 'badge-warning'; ?>">
                                <?php echo $cat['status'] ? '启用' : '禁用'; ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?php echo $cat['is_nav'] ? 'badge-info' : 'badge-default'; ?>">
                                <?php echo $cat['is_nav'] ? '是' : '否'; ?>
                            </span>
                        </td>
                        <td class="table-actions">
                            <a href="<?php echo admin_url('categories.php?action=edit&id=' . $cat['id']); ?>" class="btn btn-sm btn-secondary" title="编辑">
                                <i class="fas fa-edit"></i> 编辑
                            </a>
                            <form method="post" action="<?php echo admin_url('categories.php?action=delete&id=' . $cat['id']); ?>" style="display:inline;" onsubmit="return confirm('确定要删除分类「<?php echo htmlspecialchars(addslashes($cat['name'])); ?>」吗？此操作不可恢复。');">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-sm btn-danger" title="删除">
                                    <i class="fas fa-trash"></i> 删除
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php
                            if (!empty($cat['children'])) {
                                render_category_rows($cat['children'], $level + 1);
                            }
                        endforeach;
                    }
                    render_category_rows($cat_tree);
                    ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
    echo pagination($total, $page, admin_url('categories.php?'), $per_page);
    ?>

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
    .badge-warning { background: #fffbe6; color: #faad14; }
    .badge-info { background: #e6f7ff; color: #1890ff; }
    .badge-default { background: #f5f5f5; color: #999; }
    .table-container { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 20px; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: #fafafa; padding: 12px 14px; text-align: left; font-size: 13px; font-weight: 600; color: #555; border-bottom: 1px solid #e8e8e8; }
    .data-table td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f0f0f0; color: #333; }
    .data-table tr:hover td { background: #fafafa; }
    .data-table a { color: #c41230; text-decoration: none; }
    .data-table a:hover { text-decoration: underline; }
    .table-actions { white-space: nowrap; }
    .table-actions form { display: inline-block; }
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
    $category = [
        'id' => 0,
        'parent_id' => 0,
        'name' => '',
        'slug' => '',
        'description' => '',
        'icon' => '',
        'sort_order' => 0,
        'status' => 1,
        'is_nav' => 0,
    ];

    if ($action === 'edit') {
        if ($id <= 0) {
            $error = '缺少分类ID';
        } else {
            $existing = db_fetch("SELECT * FROM categories WHERE id = ?", [$id]);
            if (!$existing) {
                $error = '分类不存在';
            } else {
                $category = $existing;
            }
        }
    }

    // 如果保存失败，将 POST 数据回填到表单
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $category['name'] = post('name', $category['name']);
        $category['parent_id'] = (int)post('parent_id', $category['parent_id']);
        $category['slug'] = post('slug', $category['slug']);
        $category['description'] = post('description', $category['description']);
        $category['icon'] = post('icon', $category['icon']);
        $category['sort_order'] = (int)post('sort_order', $category['sort_order']);
        $category['status'] = post('status', '1') === '1' ? 1 : 0;
        $category['is_nav'] = post('is_nav', '0') === '1' ? 1 : 0;
    }

    // 获取所有分类作为上级分类选项（排除自身）
    if ($category['id'] > 0) {
        $parent_options = db_fetch_all("SELECT * FROM categories WHERE id != ? ORDER BY sort_order ASC, id ASC", [$category['id']]);
    } else {
        $parent_options = db_fetch_all("SELECT * FROM categories ORDER BY sort_order ASC, id ASC");
    }

    $is_edit = ($action === 'edit' && $category['id'] > 0);
    $form_title = $is_edit ? '编辑分类' : '添加分类';

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title"><?php echo $form_title; ?></h2>
        <a href="<?php echo admin_url('categories.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> 返回列表
        </a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo admin_url('categories.php?action=save' . ($is_edit ? '&id=' . $category['id'] : '')); ?>" class="category-form">
        <?php echo csrf_field(); ?>
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="name">分类名称 <span class="required">*</span></label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required placeholder="请输入分类名称" maxlength="100">
            </div>
            <div class="form-group form-group-side">
                <label for="parent_id">上级分类</label>
                <select id="parent_id" name="parent_id">
                    <option value="0">-- 顶级分类 --</option>
                    <?php
                    // 递归渲染上级选项
                    function render_parent_options($cats, $level = 0, $current_parent_id = 0) {
                        foreach ($cats as $cat):
                            $prefix = $level > 0 ? str_repeat('&nbsp;&nbsp;', $level) . '└ ' : '';
                            $selected = ($current_parent_id == $cat['id']) ? 'selected' : '';
                            echo '<option value="' . $cat['id'] . '" ' . $selected . '>' . $prefix . htmlspecialchars($cat['name']) . '</option>';
                        endforeach;
                    }
                    render_parent_options($parent_options, 0, $category['parent_id']);
                    ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="slug">URL 别名</label>
                <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($category['slug']); ?>" placeholder="留空则自动生成，如：fanfu" maxlength="100">
                <span class="form-hint">用于生成分类页面链接，支持字母、数字、连字符和中文</span>
            </div>
            <div class="form-group form-group-side">
                <label for="icon">图标</label>
                <input type="text" id="icon" name="icon" value="<?php echo htmlspecialchars($category['icon']); ?>" placeholder="如：fas fa-folder" maxlength="50">
                <span class="form-hint">Font Awesome 图标类名</span>
            </div>
        </div>

        <div class="form-group">
            <label for="description">分类描述</label>
            <textarea id="description" name="description" rows="3" placeholder="简要描述此分类的内容"><?php echo htmlspecialchars($category['description']); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group form-group-side">
                <label for="sort_order">排序</label>
                <input type="number" id="sort_order" name="sort_order" value="<?php echo $category['sort_order']; ?>" min="0" max="9999">
                <span class="form-hint">数值越小越靠前</span>
            </div>
            <div class="form-group form-group-side">
                <label for="status">状态</label>
                <div class="toggle-group">
                    <label class="toggle-label">
                        <input type="radio" name="status" value="1" <?php echo $category['status'] == 1 ? 'checked' : ''; ?>>
                        <span class="toggle-option toggle-on">启用</span>
                    </label>
                    <label class="toggle-label">
                        <input type="radio" name="status" value="0" <?php echo $category['status'] == 0 ? 'checked' : ''; ?>>
                        <span class="toggle-option toggle-off">禁用</span>
                    </label>
                </div>
            </div>
            <div class="form-group form-group-side">
                <label for="is_nav">导航显示</label>
                <div class="toggle-group">
                    <label class="toggle-label">
                        <input type="radio" name="is_nav" value="1" <?php echo $category['is_nav'] == 1 ? 'checked' : ''; ?>>
                        <span class="toggle-option toggle-on">显示</span>
                    </label>
                    <label class="toggle-label">
                        <input type="radio" name="is_nav" value="0" <?php echo $category['is_nav'] == 0 ? 'checked' : ''; ?>>
                        <span class="toggle-option toggle-off">隐藏</span>
                    </label>
                </div>
                <span class="form-hint">是否在网站导航中显示</span>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?php echo $is_edit ? '更新分类' : '添加分类'; ?>
            </button>
            <a href="<?php echo admin_url('categories.php'); ?>" class="btn btn-secondary">取消</a>
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
    .category-form { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); padding: 24px; }
    .form-row { display: flex; gap: 20px; margin-bottom: 16px; flex-wrap: wrap; }
    .form-group { margin-bottom: 16px; }
    .form-group-main { flex: 1; min-width: 280px; }
    .form-group-side { width: 280px; flex-shrink: 0; }
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
    .toggle-group { display: flex; gap: 0; }
    .toggle-label { flex: 1; cursor: pointer; }
    .toggle-label input[type="radio"] { display: none; }
    .toggle-option { display: block; text-align: center; padding: 9px 0; font-size: 13px; font-weight: 600; border: 1px solid #d9d9d9; transition: all 0.3s; }
    .toggle-label:first-child .toggle-option { border-radius: 4px 0 0 4px; }
    .toggle-label:last-child .toggle-option { border-radius: 0 4px 4px 0; }
    .toggle-on { background: #f6ffed; color: #52c41a; }
    .toggle-off { background: #fff1f0; color: #ff4d4f; }
    .toggle-label input[type="radio"]:checked + .toggle-on { background: #52c41a; color: #fff; border-color: #52c41a; }
    .toggle-label input[type="radio"]:checked + .toggle-off { background: #ff4d4f; color: #fff; border-color: #ff4d4f; }
    .form-actions { margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f0f0; display: flex; gap: 12px; }
    </style>

    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// ==================== 其他未知操作，回退到列表 ====================
redirect(admin_url('categories.php'));