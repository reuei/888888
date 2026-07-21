<?php
/**
 * 后台管理 - 页脚轮播图管理 v7.0.0
 * 管理页脚区域的轮播图，仅支持图片类型
 * 比主轮播图更简洁：无描述、无视频链接
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
        $slide = db_fetch("SELECT * FROM footer_carousel WHERE id = ?", [$id]);
        if (!$slide) {
            $error = '页脚轮播图不存在';
        } else {
            if (!empty($slide['image'])) {
                $image_path = UPLOADS_PATH . $slide['image'];
                if (file_exists($image_path)) {
                    @unlink($image_path);
                }
            }
            $result = db_delete('footer_carousel', 'id = ?', [$id]);
            if ($result !== false) {
                add_log('footer_carousel_delete', "删除页脚轮播图：{$slide['title']}");
                $message = '页脚轮播图删除成功';
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
        $type = in_array(post('type', 'image'), ['image', 'video']) ? post('type', 'image') : 'image';
        $link = trim(post('link', ''));
        $sort_order = (int)post('sort_order', 0);
        $status = post('status', '1') === '1' ? 1 : 0;
        $image = '';

        if (empty($title)) {
            $error = '请输入轮播图标题';
        }

        // 处理图片上传
        if (empty($error) && isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_file($_FILES['image_file'], 'footer_carousel');
            if (isset($upload_result['success']) && $upload_result['success']) {
                if ($id > 0) {
                    $old_slide = db_fetch("SELECT image FROM footer_carousel WHERE id = ?", [$id]);
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
            $existing = db_fetch("SELECT image FROM footer_carousel WHERE id = ?", [$id]);
            if ($existing && !empty($existing['image'])) {
                $image = $existing['image'];
            }
        }

        // 添加模式下必须上传图片
        if (empty($error) && empty($image) && $id <= 0) {
            $error = '请上传轮播图片';
        }

        if (empty($error)) {
            $data = [
                'title' => $title,
                'type' => $type,
                'image' => $image,
                'link' => $link,
                'sort_order' => $sort_order,
                'status' => $status
            ];

            if ($id > 0) {
                $result = db_update('footer_carousel', $data, 'id = ?', [$id]);
                if ($result !== false) {
                    add_log('footer_carousel_update', "更新页脚轮播图：{$title}");
                    $message = '页脚轮播图更新成功';
                } else {
                    $error = '更新失败，请稍后重试';
                }
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $new_id = db_insert('footer_carousel', $data);
                if ($new_id) {
                    add_log('footer_carousel_create', "创建页脚轮播图：{$title}");
                    $message = '页脚轮播图添加成功';
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
    $total = db_count('footer_carousel');
    $offset = ($page - 1) * $per_page;
    $slides = db_fetch_all(
        "SELECT * FROM footer_carousel ORDER BY sort_order ASC, id DESC LIMIT ? OFFSET ?",
        [$per_page, $offset]
    );

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3>页脚轮播图管理</h3>
            <a href="<?php echo admin_url('footer_carousel.php?action=add'); ?>" class="btn btn-primary">
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
                        <th width="80">类型</th>
                        <th width="70">状态</th>
                        <th width="150">创建时间</th>
                        <th width="120">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($slides)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;color:#999;padding:40px;">暂无页脚轮播图数据</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($slides as $s): ?>
                        <tr>
                            <td><?php echo $s['id']; ?></td>
                            <td>
                                <?php if (!empty($s['image'])): ?>
                                    <img src="<?php echo site_url('uploads/' . $s['image']); ?>" alt="<?php echo htmlspecialchars($s['title']); ?>" style="max-width:120px;height:auto;object-fit:cover;border-radius:4px;border:1px solid #e8e8e8;">
                                <?php else: ?>
                                    <span class="no-image">无图片</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo admin_url('footer_carousel.php?action=edit&id=' . $s['id']); ?>" title="<?php echo htmlspecialchars($s['title']); ?>">
                                    <?php echo htmlspecialchars(str_cut($s['title'], 40)); ?>
                                </a>
                                <?php if (!empty($s['link'])): ?>
                                    <span class="has-link" title="链接：<?php echo htmlspecialchars($s['link']); ?>"><i class="fas fa-link"></i></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-info">图片</span>
                            </td>
                            <td>
                                <span class="badge <?php echo $s['status'] ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo $s['status'] ? '启用' : '禁用'; ?>
                                </span>
                            </td>
                            <td><?php echo format_time($s['created_at']); ?></td>
                            <td class="table-actions">
                                <a href="<?php echo admin_url('footer_carousel.php?action=edit&id=' . $s['id']); ?>" class="btn btn-sm btn-secondary" title="编辑">
                                    <i class="fas fa-edit"></i> 编辑
                                </a>
                                <form method="post" action="<?php echo admin_url('footer_carousel.php?action=delete&id=' . $s['id']); ?>" style="display:inline;" onsubmit="return confirm('确定要删除页脚轮播图「<?php echo htmlspecialchars(addslashes($s['title'])); ?>」吗？此操作不可恢复。');">
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

        <?php echo pagination($total, $page, admin_url('footer_carousel.php?'), $per_page); ?>
    </div>

    <style>
    .admin-card { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 20px; }
    .admin-card-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f0f0f0; }
    .admin-card-header h3 { margin: 0; font-size: 18px; color: #333; }
    .btn { display: inline-block; padding: 8px 20px; font-size: 14px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; transition: all 0.3s; }
    .btn-primary { background: #c41230; color: #fff; }
    .btn-primary:hover { background: #a00e28; }
    .btn-secondary { background: #f0f0f0; color: #333; }
    .btn-secondary:hover { background: #e0e0e0; }
    .btn-danger { background: #ff4d4f; color: #fff; }
    .btn-danger:hover { background: #e04345; }
    .btn-sm { padding: 4px 12px; font-size: 12px; }
    .alert { padding: 12px 16px; margin: 16px 24px; border-radius: 4px; font-size: 14px; }
    .alert-success { background: #f6ffed; border: 1px solid #b7eb8f; color: #389e0d; }
    .alert-error { background: #fff2f0; border: 1px solid #ffccc7; color: #cf1322; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .badge-success { background: #f6ffed; color: #52c41a; }
    .badge-warning { background: #fffbe6; color: #faad14; }
    .badge-info { background: #e6f7ff; color: #1890ff; }
    .table-container { overflow-x: auto; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: #fafafa; padding: 12px 14px; text-align: left; font-size: 13px; font-weight: 600; color: #555; border-bottom: 1px solid #e8e8e8; }
    .data-table td { padding: 12px 14px; font-size: 13px; border-bottom: 1px solid #f0f0f0; color: #333; vertical-align: middle; }
    .data-table tr:hover td { background: #fafafa; }
    .data-table a { color: #c41230; text-decoration: none; }
    .data-table a:hover { text-decoration: underline; }
    .table-actions { white-space: nowrap; }
    .table-actions form { display: inline-block; }
    .no-image { color: #ccc; font-size: 12px; }
    .has-link { color: #c41230; margin-left: 6px; font-size: 12px; cursor: help; }
    .pagination { text-align: center; padding: 16px 24px; border-top: 1px solid #f0f0f0; }
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
        'type' => 'image',
        'image' => '',
        'link' => '',
        'sort_order' => 0,
        'status' => 1,
    ];

    if ($action === 'edit') {
        if ($id <= 0) {
            $error = '缺少轮播图ID';
        } else {
            $existing = db_fetch("SELECT * FROM footer_carousel WHERE id = ?", [$id]);
            if (!$existing) {
                $error = '页脚轮播图不存在';
            } else {
                $slide = $existing;
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $slide['title'] = post('title', $slide['title']);
        $slide['type'] = in_array(post('type', 'image'), ['image', 'video']) ? post('type', 'image') : 'image';
        $slide['link'] = post('link', $slide['link']);
        $slide['sort_order'] = (int)post('sort_order', $slide['sort_order']);
        $slide['status'] = post('status', '1') === '1' ? 1 : 0;
    }

    $is_edit = ($action === 'edit' && $slide['id'] > 0);
    $form_title = $is_edit ? '编辑页脚轮播图' : '添加页脚轮播图';

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3><?php echo $form_title; ?></h3>
            <a href="<?php echo admin_url('footer_carousel.php'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> 返回列表
            </a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo admin_url('footer_carousel.php?action=save' . ($is_edit ? '&id=' . $slide['id'] : '')); ?>" enctype="multipart/form-data" class="card-form">
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
                    <label for="sort_order">排序</label>
                    <input type="number" id="sort_order" name="sort_order" value="<?php echo $slide['sort_order']; ?>" placeholder="数字越小越靠前" min="0" step="1">
                    <span class="form-hint">数字越小越靠前</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group form-group-main">
                    <label>类型</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="type" value="image" <?php echo $slide['type'] === 'image' ? 'checked' : ''; ?>>
                            <span>图片</span>
                        </label>
                    </div>
                    <span class="form-hint">页脚轮播图仅支持图片类型</span>
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
                <label for="link">链接地址</label>
                <input type="text" id="link" name="link" value="<?php echo htmlspecialchars($slide['link']); ?>" placeholder="如：/article.php?id=1 或 https://..." maxlength="500">
                <span class="form-hint">点击轮播图时跳转的链接地址</span>
            </div>

            <div class="form-group">
                <label for="image_file">轮播图片 <?php echo ($is_edit && !empty($slide['image'])) ? '' : '<span class="required">*</span>'; ?></label>
                <input type="file" id="image_file" name="image_file" accept="image/jpeg,image/png,image/gif,image/webp">
                <span class="form-hint">支持 JPG、PNG、GIF、WebP 格式，最大 10MB。建议尺寸：适合页脚展示的横幅图片</span>
                <?php if ($is_edit && !empty($slide['image'])): ?>
                    <div class="current-image">
                        <span>当前图片：</span>
                        <img src="<?php echo site_url('uploads/' . $slide['image']); ?>" alt="轮播图预览" style="max-width:400px;max-height:200px;border:1px solid #e8e8e8;border-radius:4px;margin-top:8px;display:block;">
                        <span class="form-hint">上传新图片将替换当前图片</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $is_edit ? '更新轮播图' : '添加轮播图'; ?>
                </button>
                <a href="<?php echo admin_url('footer_carousel.php'); ?>" class="btn btn-secondary">取消</a>
            </div>
        </form>
    </div>

    <style>
    .admin-card { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 20px; }
    .admin-card-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f0f0f0; }
    .admin-card-header h3 { margin: 0; font-size: 18px; color: #333; }
    .btn { display: inline-block; padding: 8px 20px; font-size: 14px; font-weight: 600; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; transition: all 0.3s; }
    .btn-primary { background: #c41230; color: #fff; }
    .btn-primary:hover { background: #a00e28; }
    .btn-secondary { background: #f0f0f0; color: #333; }
    .btn-secondary:hover { background: #e0e0e0; }
    .alert { padding: 12px 16px; margin: 16px 24px; border-radius: 4px; font-size: 14px; }
    .alert-success { background: #f6ffed; border: 1px solid #b7eb8f; color: #389e0d; }
    .alert-error { background: #fff2f0; border: 1px solid #ffccc7; color: #cf1322; }
    .card-form { padding: 24px; }
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
    .radio-group { display: flex; gap: 24px; padding-top: 9px; }
    .radio-label { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; color: #555; cursor: pointer; }
    .radio-label input[type="radio"] { width: 16px; height: 16px; cursor: pointer; }
    .current-image { margin-top: 8px; }
    .form-actions { margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f0f0; display: flex; gap: 12px; }
    </style>

    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// ==================== 其他未知操作，回退到列表 ====================
redirect(admin_url('footer_carousel.php'));