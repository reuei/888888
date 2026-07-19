<?php
/**
 * 后台管理 - 轮播图管理
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
        $slide = db_fetch("SELECT * FROM carousel WHERE id = ?", [$id]);
        if (!$slide) {
            $error = '轮播图不存在';
        } else {
            // 删除图片文件
            if (!empty($slide['image'])) {
                $image_path = UPLOADS_PATH . $slide['image'];
                if (file_exists($image_path)) {
                    @unlink($image_path);
                }
            }
            $result = db_delete('carousel', 'id = ?', [$id]);
            if ($result !== false) {
                add_log('carousel_delete', "删除轮播图：{$slide['title']}");
                $message = '轮播图删除成功';
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
        $title = trim(post('title', ''));
        $link = trim(post('link', ''));
        $description = trim(post('description', ''));
        $order = (int)post('order', 0);
        $status = post('status', '1') === '1' ? 1 : 0;
        $image = '';

        // 验证标题
        if (empty($title)) {
            $error = '请输入轮播图标题';
        }

        // 处理图片上传
        if (empty($error) && isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_file($_FILES['image_file'], 'carousel');
            if (isset($upload_result['success']) && $upload_result['success']) {
                // 删除旧图片
                if ($id > 0) {
                    $old_slide = db_fetch("SELECT image FROM carousel WHERE id = ?", [$id]);
                    if ($old_slide && !empty($old_slide['image'])) {
                        $old_path = UPLOADS_PATH . $old_slide['image'];
                        if (file_exists($old_path)) {
                            @unlink($old_path);
                        }
                    }
                }
                $image = $upload_result['path'];
            } elseif (isset($upload_result['error'])) {
                $error = $upload_result['error'];
            }
        }

        // 编辑模式下如果没有上传新图片，保留原图片
        if (empty($error) && empty($image) && $id > 0) {
            $existing = db_fetch("SELECT image FROM carousel WHERE id = ?", [$id]);
            if ($existing && !empty($existing['image'])) {
                $image = $existing['image'];
            } elseif ($existing && empty($existing['image'])) {
                $error = '请上传轮播图片';
            }
        }

        // 添加模式下必须上传图片
        if (empty($error) && empty($image) && $id <= 0) {
            $error = '请上传轮播图片';
        }

        if (empty($error)) {
            $data = [
                'title' => $title,
                'image' => $image,
                'link' => $link,
                'description' => $description,
                'order' => $order,
                'status' => $status
            ];

            if ($id > 0) {
                // 编辑模式
                $result = db_update('carousel', $data, 'id = ?', [$id]);
                if ($result !== false) {
                    add_log('carousel_update', "更新轮播图：{$title}");
                    $message = '轮播图更新成功';
                } else {
                    $error = '更新失败，请稍后重试';
                }
            } else {
                // 添加模式
                $data['created_at'] = date('Y-m-d H:i:s');
                $new_id = db_insert('carousel', $data);
                if ($new_id) {
                    add_log('carousel_create', "创建轮播图：{$title}");
                    $message = '轮播图添加成功';
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

// ==================== 列表视图 ====================
if ($action === 'list') {
    $total = db_count('carousel');
    $offset = ($page - 1) * $per_page;
    $slides = db_fetch_all(
        "SELECT * FROM carousel ORDER BY order ASC, id DESC LIMIT ? OFFSET ?",
        [$per_page, $offset]
    );

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title">轮播图管理</h2>
        <a href="<?php echo admin('carousel.php?action=add'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> 添加轮播图
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
                    <th width="120">缩略图</th>
                    <th>标题</th>
                    <th width="80">排序</th>
                    <th width="80">状态</th>
                    <th width="150">创建时间</th>
                    <th width="120">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($slides)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;color:#999;padding:40px;">暂无轮播图数据</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($slides as $s): ?>
                    <tr>
                        <td><?php echo $s['id']; ?></td>
                        <td>
                            <?php if (!empty($s['image'])): ?>
                                <img src="<?php echo site('uploads/' . $s['image']); ?>" alt="<?php echo htmlspecialchars($s['title']); ?>" class="thumbnail-preview">
                            <?php else: ?>
                                <span class="no-image">无图片</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo admin('carousel.php?action=edit&id=' . $s['id']); ?>" title="<?php echo htmlspecialchars($s['title']); ?>">
                                <?php echo htmlspecialchars(str_cut($s['title'], 40)); ?>
                            </a>
                            <?php if (!empty($s['link'])): ?>
                                <span class="has-link" title="链接：<?php echo htmlspecialchars($s['link']); ?>"><i class="fas fa-link"></i></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $s['order']; ?></td>
                        <td>
                            <span class="badge <?php echo $s['status'] ? 'badge-success' : 'badge-disabled'; ?>">
                                <?php echo $s['status'] ? '启用' : '禁用'; ?>
                            </span>
                        </td>
                        <td><?php echo format_time($s['created_at']); ?></td>
                        <td class="table-actions">
                            <a href="<?php echo admin('carousel.php?action=edit&id=' . $s['id']); ?>" class="btn btn-sm btn-secondary" title="编辑">
                                <i class="fas fa-edit"></i> 编辑
                            </a>
                            <form method="post" action="<?php echo admin('carousel.php?action=delete&id=' . $s['id']); ?>" style="display:inline;" onsubmit="return confirm('确定要删除轮播图「<?php echo htmlspecialchars(addslashes($s['title'])); ?>」吗？此操作将同时删除图片文件，不可恢复。');">
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

    <?php
    echo pagination($total, $page, admin('carousel.php?'), $per_page);
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
    .thumbnail-preview { width: 100px; height: 56px; object-fit: cover; border-radius: 4px; border: 1px solid #e8e8e8; }
    .no-image { color: #ccc; font-size: 12px; }
    .has-link { color: #c41230; margin-left: 6px; font-size: 12px; cursor: help; }
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
    $slide = [
        'id' => 0,
        'title' => '',
        'image' => '',
        'link' => '',
        'description' => '',
        'order' => 0,
        'status' => 1,
    ];

    if ($action === 'edit') {
        if ($id <= 0) {
            $error = '缺少轮播图ID';
        } else {
            $existing = db_fetch("SELECT * FROM carousel WHERE id = ?", [$id]);
            if (!$existing) {
                $error = '轮播图不存在';
            } else {
                $slide = $existing;
            }
        }
    }

    // 如果保存失败，将 POST 数据回填到表单
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $slide['title'] = post('title', $slide['title']);
        $slide['link'] = post('link', $slide['link']);
        $slide['description'] = post('description', $slide['description']);
        $slide['order'] = (int)post('order', $slide['order']);
        $slide['status'] = post('status', '1') === '1' ? 1 : 0;
    }

    $is_edit = ($action === 'edit' && $slide['id'] > 0);
    $form_title = $is_edit ? '编辑轮播图' : '添加轮播图';

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title"><?php echo $form_title; ?></h2>
        <a href="<?php echo admin('carousel.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> 返回列表
        </a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo admin('carousel.php?action=save' . ($is_edit ? '&id=' . $slide['id'] : '')); ?>" enctype="multipart/form-data" class="carousel-form">
        <?php echo csrf_field(); ?>
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $slide['id']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="title">轮播图标题 <span class="required">*</span></label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($slide['title']); ?>" required placeholder="请输入轮播图标题" maxlength="200">
            </div>
            <div class="form-group form-group-side">
                <label for="order">排序</label>
                <input type="number" id="order" name="order" value="<?php echo $slide['order']; ?>" placeholder="数字越小越靠前" min="0" step="1">
                <span class="form-hint">数字越小越靠前</span>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="link">链接地址</label>
                <input type="text" id="link" name="link" value="<?php echo htmlspecialchars($slide['link']); ?>" placeholder="如：/article.php?id=1 或 https://..." maxlength="500">
                <span class="form-hint">点击轮播图时跳转的链接地址</span>
            </div>
            <div class="form-group form-group-side">
                <label for="status">状态</label>
                <select id="status" name="status">
                    <option value="1" <?php echo $slide['status'] == 1 ? 'selected' : ''; ?>>启用</option>
                    <option value="0" <?php echo $slide['status'] == 0 ? 'selected' : ''; ?>>禁用</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="description">描述</label>
            <textarea id="description" name="description" rows="3" placeholder="轮播图的简短描述（可选）"><?php echo htmlspecialchars($slide['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="image_file">轮播图片 <?php echo $is_edit ? '' : '<span class="required">*</span>'; ?></label>
            <input type="file" id="image_file" name="image_file" accept="image/jpeg,image/png,image/gif,image/webp" <?php echo $is_edit ? '' : 'required'; ?>>
            <span class="form-hint">支持 JPG、PNG、GIF、WebP 格式，最大 10MB。建议尺寸：1920×600 像素</span>
            <?php if ($is_edit && !empty($slide['image'])): ?>
                <div class="current-image">
                    <span>当前图片：</span>
                    <img src="<?php echo site('uploads/' . $slide['image']); ?>" alt="轮播图预览" class="image-preview">
                    <span class="form-hint">上传新图片将替换当前图片</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?php echo $is_edit ? '更新轮播图' : '添加轮播图'; ?>
            </button>
            <a href="<?php echo admin('carousel.php'); ?>" class="btn btn-secondary">取消</a>
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
    .carousel-form { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); padding: 24px; }
    .form-row { display: flex; gap: 20px; margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-group-main { flex: 1; }
    .form-group-side { width: 200px; flex-shrink: 0; }
    .form-group label { display: block; font-size: 14px; font-weight: 600; color: #555; margin-bottom: 6px; }
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group select,
    .form-group textarea { width: 100%; padding: 9px 12px; border: 1px solid #d9d9d9; border-radius: 4px; font-size: 14px; font-family: inherit; transition: border-color 0.3s; box-sizing: border-box; }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { border-color: #c41230; outline: none; box-shadow: 0 0 0 2px rgba(196,18,48,0.1); }
    .form-group textarea { resize: vertical; }
    .form-group input[type="file"] { padding: 6px 0; }
    .form-hint { display: block; font-size: 12px; color: #999; margin-top: 4px; }
    .required { color: #c41230; }
    .current-image { margin-top: 12px; }
    .image-preview { display: block; max-width: 400px; max-height: 200px; border: 1px solid #e8e8e8; border-radius: 4px; margin-top: 8px; }
    .form-actions { margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f0f0; display: flex; gap: 12px; }
    </style>

    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// ==================== 其他未知操作，回退到列表 ====================
redirect(admin('carousel.php'));