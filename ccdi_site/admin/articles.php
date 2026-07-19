<?php
/**
 * 后台管理 - 文章管理
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
        $article = db_fetch("SELECT * FROM articles WHERE id = ?", [$id]);
        if (!$article) {
            $error = '文章不存在';
        } else {
            // 删除封面图片文件
            if (!empty($article['cover_image'])) {
                $cover_path = UPLOADS_PATH . $article['cover_image'];
                if (file_exists($cover_path)) {
                    @unlink($cover_path);
                }
            }
            $result = db_delete('articles', 'id = ?', [$id]);
            if ($result !== false) {
                add_log('article_delete', "删除文章：{$article['title']}");
                $message = '文章删除成功';
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
        $category_id = (int)post('category_id', 0);
        $slug = trim(post('slug', ''));
        $content = trim($_POST['content'] ?? '');
        $summary = trim(post('summary', ''));
        $source = trim(post('source', ''));
        $author = trim(post('author', ''));
        $keywords = trim(post('keywords', ''));
        $is_top = post('is_top', '0') === '1' ? 1 : 0;
        $is_recommend = post('is_recommend', '0') === '1' ? 1 : 0;
        $status = in_array(post('status', 'draft'), ['draft', 'publish']) ? post('status') : 'draft';
        $publish_time = trim(post('publish_time', ''));
        $cover_image = '';

        // 验证标题
        if (empty($title)) {
            $error = '请输入文章标题';
        }

        // 自动生成 slug
        if (empty($slug) && !empty($title)) {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9\x{4e00}-\x{9fa5}]+/u', '-', $title));
            $slug = trim($slug, '-');
        }
        if (empty($slug)) {
            $slug = 'article-' . time();
        }

        // 检查 slug 唯一性
        if (empty($error)) {
            if ($id > 0) {
                $existing = db_fetch("SELECT id FROM articles WHERE slug = ? AND id != ?", [$slug, $id]);
            } else {
                $existing = db_fetch("SELECT id FROM articles WHERE slug = ?", [$slug]);
            }
            if ($existing) {
                $slug .= '-' . time();
            }
        }

        // 处理封面图片上传
        if (empty($error) && isset($_FILES['cover_image_file']) && $_FILES['cover_image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload_result = upload_file($_FILES['cover_image_file'], 'covers');
            if (isset($upload_result['success']) && $upload_result['success']) {
                // 删除旧封面
                if ($id > 0) {
                    $old_article = db_fetch("SELECT cover_image FROM articles WHERE id = ?", [$id]);
                    if ($old_article && !empty($old_article['cover_image'])) {
                        $old_path = UPLOADS_PATH . $old_article['cover_image'];
                        if (file_exists($old_path)) {
                            @unlink($old_path);
                        }
                    }
                }
                $cover_image = $upload_result['path'];
            } elseif (isset($upload_result['error'])) {
                $error = $upload_result['error'];
            }
        }

        // 处理发布时间
        if (empty($publish_time)) {
            $publish_time = date('Y-m-d H:i:s');
        }

        if (empty($error)) {
            $data = [
                'category_id' => $category_id,
                'title' => $title,
                'slug' => $slug,
                'content' => $content,
                'summary' => $summary,
                'source' => $source,
                'author' => $author,
                'keywords' => $keywords,
                'is_top' => $is_top,
                'is_recommend' => $is_recommend,
                'status' => $status,
                'publish_time' => $publish_time,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (!empty($cover_image)) {
                $data['cover_image'] = $cover_image;
            }

            if ($id > 0) {
                // 编辑模式
                $result = db_update('articles', $data, 'id = ?', [$id]);
                if ($result !== false) {
                    add_log('article_update', "更新文章：{$title}");
                    $message = '文章更新成功';
                } else {
                    $error = '更新失败，请稍后重试';
                }
            } else {
                // 添加模式
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['view_count'] = 0;
                $new_id = db_insert('articles', $data);
                if ($new_id) {
                    add_log('article_create', "创建文章：{$title}");
                    $message = '文章添加成功';
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
    $total = db_count('articles');
    $offset = ($page - 1) * $per_page;
    $articles = db_fetch_all(
        "SELECT a.*, c.name AS category_name FROM articles a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.is_top DESC, a.updated_at DESC LIMIT ? OFFSET ?",
        [$per_page, $offset]
    );

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title">文章管理</h2>
        <a href="<?php echo admin_url('articles.php?action=add'); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> 添加文章
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
                    <th>标题</th>
                    <th width="120">分类</th>
                    <th width="80">状态</th>
                    <th width="150">发布时间</th>
                    <th width="120">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($articles)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center;color:#999;padding:40px;">暂无文章数据</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($articles as $a): ?>
                    <tr>
                        <td><?php echo $a['id']; ?></td>
                        <td>
                            <a href="<?php echo admin_url('articles.php?action=edit&id=' . $a['id']); ?>" title="<?php echo htmlspecialchars($a['title']); ?>">
                                <?php echo htmlspecialchars(str_cut($a['title'], 40)); ?>
                            </a>
                            <?php if ($a['is_top']): ?>
                                <span class="badge badge-danger">置顶</span>
                            <?php endif; ?>
                            <?php if ($a['is_recommend']): ?>
                                <span class="badge badge-info">推荐</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($a['category_name'] ?: '未分类'); ?></td>
                        <td>
                            <span class="badge <?php echo $a['status'] === 'publish' ? 'badge-success' : 'badge-warning'; ?>">
                                <?php echo $a['status'] === 'publish' ? '已发布' : '草稿'; ?>
                            </span>
                        </td>
                        <td><?php echo format_time($a['publish_time']); ?></td>
                        <td class="table-actions">
                            <a href="<?php echo admin_url('articles.php?action=edit&id=' . $a['id']); ?>" class="btn btn-sm btn-secondary" title="编辑">
                                <i class="fas fa-edit"></i> 编辑
                            </a>
                            <form method="post" action="<?php echo admin_url('articles.php?action=delete&id=' . $a['id']); ?>" style="display:inline;" onsubmit="return confirm('确定要删除文章「<?php echo htmlspecialchars(addslashes($a['title'])); ?>」吗？此操作不可恢复。');">
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
    echo pagination($total, $page, admin_url('articles.php?'), $per_page);
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
    .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 600; margin-left: 4px; }
    .badge-success { background: #f6ffed; color: #52c41a; }
    .badge-warning { background: #fffbe6; color: #faad14; }
    .badge-danger { background: #fff2f0; color: #ff4d4f; }
    .badge-info { background: #e6f7ff; color: #1890ff; }
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
    $article = [
        'id' => 0,
        'category_id' => 0,
        'title' => '',
        'slug' => '',
        'content' => '',
        'summary' => '',
        'cover_image' => '',
        'source' => '',
        'author' => '',
        'keywords' => '',
        'is_top' => 0,
        'is_recommend' => 0,
        'status' => 'draft',
        'publish_time' => date('Y-m-d H:i:s'),
    ];

    if ($action === 'edit') {
        if ($id <= 0) {
            $error = '缺少文章ID';
        } else {
            $existing = db_fetch("SELECT * FROM articles WHERE id = ?", [$id]);
            if (!$existing) {
                $error = '文章不存在';
            } else {
                $article = $existing;
            }
        }
    }

    // 如果保存失败，将 POST 数据回填到表单
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $article['title'] = post('title', $article['title']);
        $article['category_id'] = (int)post('category_id', $article['category_id']);
        $article['slug'] = post('slug', $article['slug']);
        $article['content'] = $_POST['content'] ?? $article['content'];
        $article['summary'] = post('summary', $article['summary']);
        $article['source'] = post('source', $article['source']);
        $article['author'] = post('author', $article['author']);
        $article['keywords'] = post('keywords', $article['keywords']);
        $article['is_top'] = post('is_top', '0') === '1' ? 1 : 0;
        $article['is_recommend'] = post('is_recommend', '0') === '1' ? 1 : 0;
        $article['status'] = in_array(post('status', 'draft'), ['draft', 'publish']) ? post('status') : 'draft';
        $article['publish_time'] = post('publish_time', $article['publish_time']);
    }

    $categories = db_fetch_all("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order ASC, id ASC");
    $is_edit = ($action === 'edit' && $article['id'] > 0);
    $form_title = $is_edit ? '编辑文章' : '添加文章';

    include __DIR__ . '/header.php';
    ?>

    <div class="admin-page-header">
        <h2 class="admin-page-title"><?php echo $form_title; ?></h2>
        <a href="<?php echo admin_url('articles.php'); ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> 返回列表
        </a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo admin_url('articles.php?action=save' . ($is_edit ? '&id=' . $article['id'] : '')); ?>" enctype="multipart/form-data" class="article-form">
        <?php echo csrf_field(); ?>
        <?php if ($is_edit): ?>
            <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="title">文章标题 <span class="required">*</span></label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required placeholder="请输入文章标题" maxlength="200">
            </div>
            <div class="form-group form-group-side">
                <label for="category_id">所属分类</label>
                <select id="category_id" name="category_id">
                    <option value="0">-- 请选择分类 --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $article['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="slug">URL 别名</label>
                <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($article['slug']); ?>" placeholder="留空则自动生成" maxlength="200">
                <span class="form-hint">用于生成文章链接，仅支持字母、数字、连字符和中文</span>
            </div>
            <div class="form-group form-group-side">
                <label for="status">状态</label>
                <select id="status" name="status">
                    <option value="draft" <?php echo $article['status'] === 'draft' ? 'selected' : ''; ?>>草稿</option>
                    <option value="publish" <?php echo $article['status'] === 'publish' ? 'selected' : ''; ?>>发布</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="summary">文章摘要</label>
            <textarea id="summary" name="summary" rows="2" placeholder="文章的简短摘要描述"><?php echo htmlspecialchars($article['summary']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="content">文章内容</label>
            <textarea id="content" name="content" rows="20" class="content-editor" placeholder="请输入文章内容（支持HTML）"><?php echo htmlspecialchars($article['content']); ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="cover_image_file">封面图片</label>
                <input type="file" id="cover_image_file" name="cover_image_file" accept="image/jpeg,image/png,image/gif,image/webp">
                <?php if (!empty($article['cover_image'])): ?>
                    <div class="current-cover">
                        <span>当前封面：</span>
                        <img src="<?php echo site_url('uploads/' . $article['cover_image']); ?>" alt="封面预览" style="max-width:200px;max-height:120px;border:1px solid #e8e8e8;border-radius:4px;margin-top:8px;">
                        <span class="form-hint">上传新图片将替换当前封面</span>
                    </div>
                <?php endif; ?>
                <span class="form-hint">支持 JPG、PNG、GIF、WebP 格式，最大 10MB</span>
            </div>
            <div class="form-group form-group-side">
                <label for="publish_time">发布时间</label>
                <input type="datetime-local" id="publish_time" name="publish_time" value="<?php echo date('Y-m-d\TH:i', strtotime($article['publish_time'])); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group form-group-main">
                <label for="source">来源</label>
                <input type="text" id="source" name="source" value="<?php echo htmlspecialchars($article['source']); ?>" placeholder="如：中央纪委国家监委网站" maxlength="100">
            </div>
            <div class="form-group form-group-side">
                <label for="author">作者</label>
                <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($article['author']); ?>" placeholder="文章作者" maxlength="50">
            </div>
        </div>

        <div class="form-group">
            <label for="keywords">关键词</label>
            <input type="text" id="keywords" name="keywords" value="<?php echo htmlspecialchars($article['keywords']); ?>" placeholder="多个关键词用逗号分隔，如：反腐败,纪检监察,巡视" maxlength="200">
        </div>

        <div class="form-row">
            <div class="form-check">
                <label>
                    <input type="checkbox" name="is_top" value="1" <?php echo $article['is_top'] ? 'checked' : ''; ?>>
                    置顶文章
                </label>
            </div>
            <div class="form-check">
                <label>
                    <input type="checkbox" name="is_recommend" value="1" <?php echo $article['is_recommend'] ? 'checked' : ''; ?>>
                    推荐文章
                </label>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?php echo $is_edit ? '更新文章' : '添加文章'; ?>
            </button>
            <a href="<?php echo admin_url('articles.php'); ?>" class="btn btn-secondary">取消</a>
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
    .article-form { background: #fff; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); padding: 24px; }
    .form-row { display: flex; gap: 20px; margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-group-main { flex: 1; }
    .form-group-side { width: 280px; flex-shrink: 0; }
    .form-group label { display: block; font-size: 14px; font-weight: 600; color: #555; margin-bottom: 6px; }
    .form-group input[type="text"],
    .form-group input[type="datetime-local"],
    .form-group select,
    .form-group textarea { width: 100%; padding: 9px 12px; border: 1px solid #d9d9d9; border-radius: 4px; font-size: 14px; font-family: inherit; transition: border-color 0.3s; box-sizing: border-box; }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus { border-color: #c41230; outline: none; box-shadow: 0 0 0 2px rgba(196,18,48,0.1); }
    .form-group textarea { resize: vertical; }
    .form-group input[type="file"] { padding: 6px 0; }
    .form-hint { display: block; font-size: 12px; color: #999; margin-top: 4px; }
    .required { color: #c41230; }
    .form-check { display: inline-flex; align-items: center; gap: 8px; margin-right: 24px; }
    .form-check label { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; color: #555; cursor: pointer; }
    .form-check input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; }
    .form-actions { margin-top: 24px; padding-top: 20px; border-top: 1px solid #f0f0f0; display: flex; gap: 12px; }
    .content-editor { font-family: "Courier New", Consolas, monospace; font-size: 13px; line-height: 1.6; }
    .current-cover { margin-top: 8px; }
    </style>

    <?php
    include __DIR__ . '/footer.php';
    exit;
}

// ==================== 其他未知操作，回退到列表 ====================
redirect(admin_url('articles.php'));