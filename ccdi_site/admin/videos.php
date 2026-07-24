<?php
/**
 * 后台管理 - 视频管理 v10.0.0
 * 视频管理：支持外部链接视频和本地视频上传
 * 包含封面图、视频链接、分类、排序等功能
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
        $video = db_fetch("SELECT * FROM videos WHERE id = ?", [$id]);
        if (!$video) {
            $error = '视频不存在';
        } else {
            if (!empty($video['cover'])) {
                $cover_path = UPLOADS_PATH . $video['cover'];
                if (file_exists($cover_path)) {
                    @unlink($cover_path);
                }
            }
            if (!empty($video['video_file'])) {
                $video_path = UPLOADS_PATH . $video['video_file'];
                if (file_exists($video_path)) {
                    @unlink($video_path);
                }
            }
            $result = db_delete('videos', 'id = ?', [$id]);
            if ($result !== false) {
                add_log('video_delete', "删除视频：{$video['title']}");
                $message = '视频删除成功';
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
        $video_url = trim(post('video_url', ''));
        $video_file = '';
        $description = trim(post('description', ''));
        $category_id = (int)post('category_id', 0);
        $sort_order = (int)post('sort_order', 0);
        $status = post('status', '1') === '1' ? 1 : 0;
        $cover = '';

        if (empty($title)) {
            $error = '请输入视频标题';
        }

        if (empty($error) && empty($video_url) && !isset($_FILES['video_file']) && $id <= 0) {
            $error = '请提供外部视频链接或上传本地视频文件';
        }

        if (empty($error) && empty($video_url) && !isset($_FILES['video_file']) && $id > 0) {
            $existing = db_fetch("SELECT video_url, video_file FROM videos WHERE id = ?", [$id]);
            if ($existing && empty($existing['video_url']) && empty($existing['video_file'])) {
                $error = '请提供外部视频链接或上传本地视频文件';
            }
        }

        // 处理封面上传
        if (empty($error) && isset($_FILES['cover_file']) && $_FILES['cover_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_file($_FILES['cover_file'], 'videos');
            if (isset($upload_result['success']) && $upload_result['success']) {
                if ($id > 0) {
                    $old_video = db_fetch("SELECT cover FROM videos WHERE id = ?", [$id]);
                    if ($old_video && !empty($old_video['cover'])) {
                        $old_path = UPLOADS_PATH . $old_video['cover'];
                        if (file_exists($old_path)) {
                            @unlink($old_path);
                        }
                    }
                }
                $cover = $upload_result['path'];
            } elseif (isset($upload_result['error'])) {
                $error = $upload_result['error'];
            }
        }

        // 编辑模式下如果没有上传新封面，保留原封面
        if (empty($error) && empty($cover) && $id > 0) {
            $existing = db_fetch("SELECT cover FROM videos WHERE id = ?", [$id]);
            if ($existing && !empty($existing['cover'])) {
                $cover = $existing['cover'];
            }
        }

        // 处理本地视频文件上传
        if (empty($error) && isset($_FILES['video_file']) && $_FILES['video_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_file($_FILES['video_file'], 'videos');
            if (isset($upload_result['success']) && $upload_result['success']) {
                if ($id > 0) {
                    $old_video = db_fetch("SELECT video_file FROM videos WHERE id = ?", [$id]);
                    if ($old_video && !empty($old_video['video_file'])) {
                        $old_path = UPLOADS_PATH . $old_video['video_file'];
                        if (file_exists($old_path)) {
                            @unlink($old_path);
                        }
                    }
                }
                $video_file = $upload_result['path'];
                $video_url = '';
            } elseif (isset($upload_result['error'])) {
                $error = $upload_result['error'];
            }
        }

        // 编辑模式下如果没有上传新视频文件，保留原视频文件
        if (empty($error) && empty($video_file) && empty($video_url) && $id > 0) {
            $existing = db_fetch("SELECT video_url, video_file FROM videos WHERE id = ?", [$id]);
            if ($existing) {
                $video_url = $existing['video_url'];
                $video_file = $existing['video_file'];
            }
        }

        if (empty($error)) {
            $data = [
                'title' => $title,
                'cover' => $cover,
                'video_url' => $video_url,
                'video_file' => $video_file,
                'description' => $description,
                'category_id' => $category_id,
                'sort_order' => $sort_order,
                'status' => $status
            ];

            if ($id > 0) {
                $result = db_update('videos', $data, 'id = ?', [$id]);
                if ($result !== false) {
                    add_log('video_update', "更新视频：{$title}");
                    $message = '视频更新成功';
                } else {
                    $error = '更新失败，请稍后重试';
                }
            } else {
                $data['view_count'] = 0;
                $data['created_at'] = date('Y-m-d H:i:s');
                $new_id = db_insert('videos', $data);
                if ($new_id) {
                    add_log('video_create', "创建视频：{$title}");
                    $message = '视频添加成功';
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
    $total = db_count('videos');
    $offset = ($page - 1) * $per_page;
    $videos = db_fetch_all(
        "SELECT v.*, c.name AS category_name FROM videos v LEFT JOIN categories c ON v.category_id = c.id ORDER BY v.sort_order ASC, v.id DESC LIMIT ? OFFSET ?",
        [$per_page, $offset]
    );

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3>视频管理</h3>
            <a href="<?php echo admin_url('videos.php?action=add'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> 添加视频
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
                        <th width="120">封面</th>
                        <th>标题</th>
                        <th width="100">视频来源</th>
                        <th width="80">分类</th>
                        <th width="70">观看</th>
                        <th width="70">状态</th>
                        <th width="150">创建时间</th>
                        <th width="120">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($videos)): ?>
                        <tr>
                            <td colspan="9" style="text-align:center;color:#999;padding:40px;">暂无视频数据</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($videos as $v): ?>
                        <tr>
                            <td><?php echo $v['id']; ?></td>
                            <td>
                                <?php if (!empty($v['cover'])): ?>
                                    <img src="<?php echo site_url('uploads/' . $v['cover']); ?>" alt="<?php echo htmlspecialchars($v['title']); ?>" style="max-width:120px;height:auto;object-fit:cover;border-radius:4px;border:1px solid #e8e8e8;">
                                <?php else: ?>
                                    <span class="no-image">无封面</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo admin_url('videos.php?action=edit&id=' . $v['id']); ?>" title="<?php echo htmlspecialchars($v['title']); ?>">
                                    <?php echo htmlspecialchars(str_cut($v['title'], 40)); ?>
                                </a>
                                <?php if (!empty($v['video_url'])): ?>
                                    <span class="has-link" title="外部链接：<?php echo htmlspecialchars($v['video_url']); ?>"><i class="fas fa-link"></i></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($v['video_file'])): ?>
                                    <span class="badge badge-info">本地视频</span>
                                <?php elseif (!empty($v['video_url'])): ?>
                                    <span class="badge badge-success">外部链接</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">未设置</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($v['category_name'] ?? '-'); ?></td>
                            <td><?php echo (int)$v['view_count']; ?></td>
                            <td>
                                <span class="badge <?php echo $v['status'] ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo $v['status'] ? '启用' : '禁用'; ?>
                                </span>
                            </td>
                            <td><?php echo format_time($v['created_at']); ?></td>
                            <td class="table-actions">
                                <a href="<?php echo admin_url('videos.php?action=edit&id=' . $v['id']); ?>" class="btn btn-sm btn-secondary" title="编辑">
                                    <i class="fas fa-edit"></i> 编辑
                                </a>
                                <form method="post" action="<?php echo admin_url('videos.php?action=delete&id=' . $v['id']); ?>" style="display:inline;" onsubmit="return confirm('确定要删除视频「<?php echo htmlspecialchars(addslashes($v['title'])); ?>」吗？此操作不可恢复。');">
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

        <?php echo pagination($total, $page, admin_url('videos.php?'), $per_page); ?>
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
    $video = [
        'id' => 0,
        'title' => '',
        'cover' => '',
        'video_url' => '',
        'video_file' => '',
        'description' => '',
        'category_id' => 0,
        'sort_order' => 0,
        'status' => 1,
    ];

    if ($action === 'edit') {
        if ($id <= 0) {
            $error = '缺少视频ID';
        } else {
            $existing = db_fetch("SELECT * FROM videos WHERE id = ?", [$id]);
            if (!$existing) {
                $error = '视频不存在';
            } else {
                $video = $existing;
            }
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $video['title'] = post('title', $video['title']);
        $video['video_url'] = post('video_url', $video['video_url']);
        $video['description'] = post('description', $video['description']);
        $video['category_id'] = (int)post('category_id', $video['category_id']);
        $video['sort_order'] = (int)post('sort_order', $video['sort_order']);
        $video['status'] = post('status', '1') === '1' ? 1 : 0;
    }

    $is_edit = ($action === 'edit' && $video['id'] > 0);
    $form_title = $is_edit ? '编辑视频' : '添加视频';

    // 获取分类列表
    $categories = db_fetch_all("SELECT id, name FROM categories ORDER BY sort_order ASC, id ASC");

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3><?php echo $form_title; ?></h3>
            <a href="<?php echo admin_url('videos.php'); ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> 返回列表
            </a>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo admin_url('videos.php?action=save' . ($is_edit ? '&id=' . $video['id'] : '')); ?>" enctype="multipart/form-data" class="card-form">
            <?php echo csrf_field(); ?>
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?php echo $video['id']; ?>">
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group form-group-main">
                    <label for="title">视频标题 <span class="required">*</span></label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($video['title']); ?>" required placeholder="请输入视频标题" maxlength="200">
                </div>
                <div class="form-group form-group-side">
                    <label for="sort_order">排序</label>
                    <input type="number" id="sort_order" name="sort_order" value="<?php echo $video['sort_order']; ?>" placeholder="数字越小越靠前" min="0" step="1">
                    <span class="form-hint">数字越小越靠前</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group form-group-main">
                    <label for="category_id">所属分类</label>
                    <select id="category_id" name="category_id">
                        <option value="0">-- 未分类 --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $video['category_id'] == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group form-group-side">
                    <label for="status">状态</label>
                    <select id="status" name="status">
                        <option value="1" <?php echo $video['status'] == 1 ? 'selected' : ''; ?>>启用</option>
                        <option value="0" <?php echo $video['status'] == 0 ? 'selected' : ''; ?>>禁用</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="video_url">外部视频链接</label>
                <input type="text" id="video_url" name="video_url" value="<?php echo htmlspecialchars($video['video_url']); ?>" placeholder="如：https://www.youtube.com/embed/xxx 或 https://player.bilibili.com/player.html?bvid=xxx" maxlength="500">
                <span class="form-hint">支持 YouTube、Bilibili 等外部视频嵌入链接，与本地视频文件二选一</span>
            </div>

            <div class="form-group">
                <label for="video_file">本地视频文件</label>
                <input type="file" id="video_file" name="video_file" accept="video/mp4,video/webm">
                <span class="form-hint">支持 MP4、WebM 格式，最大 10MB。与外部链接二选一，上传本地文件将清空外部链接</span>
                <?php if ($is_edit && !empty($video['video_file'])): ?>
                    <div class="current-file">
                        <span>当前文件：</span>
                        <code><?php echo htmlspecialchars($video['video_file']); ?></code>
                        <span class="form-hint">上传新文件将替换当前文件</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="description">视频描述</label>
                <textarea id="description" name="description" rows="4" placeholder="视频的简短描述（可选）"><?php echo htmlspecialchars($video['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="cover_file">视频封面</label>
                <input type="file" id="cover_file" name="cover_file" accept="image/jpeg,image/png,image/gif,image/webp">
                <span class="form-hint">支持 JPG、PNG、GIF、WebP 格式，最大 10MB。建议尺寸：640×360 像素</span>
                <?php if ($is_edit && !empty($video['cover'])): ?>
                    <div class="current-image">
                        <span>当前封面：</span>
                        <img src="<?php echo site_url('uploads/' . $video['cover']); ?>" alt="封面预览" style="max-width:400px;max-height:200px;border:1px solid #e8e8e8;border-radius:4px;margin-top:8px;display:block;">
                        <span class="form-hint">上传新封面将替换当前封面</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $is_edit ? '更新视频' : '添加视频'; ?>
                </button>
                <a href="<?php echo admin_url('videos.php'); ?>" class="btn btn-secondary">取消</a>
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
    .current-image { margin-top: 8px; }
    .current-file { margin-top: 8px; }
    .current-file code { display: block; padding: 4px 8px; background: #f5f5f5; border-radius: 3px; font-size: 12px; color: #666; margin-bottom: 4px; word-break: break-all; }
    .form-actions { margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f0f0; display: flex; gap: 12px; }
    </style>

    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// ==================== 其他未知操作，回退到列表 ====================
redirect(admin_url('videos.php'));