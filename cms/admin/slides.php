<?php
$activeMenu = 'slides';
$pageTitle = '轮播图管理';
include __DIR__ . '/header.php';

$msg = '';
$msgType = '';

// 确保表存在
if (!DB::tableExists('slides')) {
    DB::getInstance()->exec("CREATE TABLE slides (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT,
        image TEXT,
        link TEXT,
        sort_order INTEGER DEFAULT 0,
        status INTEGER DEFAULT 1,
        create_time DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
}

// 处理删除
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    DB::delete('slides', 'id=?', [$id]);
    $msg = '删除成功';
    $msgType = 'success';
}

// 处理状态切换
if (isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $slide = DB::fetchOne("SELECT status FROM slides WHERE id=?", [$id]);
    if ($slide) {
        $newStatus = $slide['status'] ? 0 : 1;
        DB::update('slides', ['status' => $newStatus], 'id=?', [$id]);
        $msg = '状态已更新';
        $msgType = 'success';
    }
}

// 处理添加/编辑
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $sortOrder = intval($_POST['sort_order'] ?? 0);
    $status = isset($_POST['status']) ? 1 : 0;
    $image = '';

    // 上传图片
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $result = uploadFile('image');
        if ($result['success']) {
            $image = $result['path'];
        } else {
            $msg = $result['error'];
            $msgType = 'error';
        }
    } elseif ($id > 0) {
        $old = DB::fetchOne("SELECT image FROM slides WHERE id=?", [$id]);
        $image = $old['image'] ?? '';
    }

    if (!$msg) {
        if ($id > 0) {
            DB::update('slides', [
                'title' => $title,
                'image' => $image,
                'link' => $link,
                'sort_order' => $sortOrder,
                'status' => $status,
            ], 'id=?', [$id]);
            $msg = '更新成功';
        } else {
            if (!$image) {
                $msg = '请上传轮播图图片';
                $msgType = 'error';
            } else {
                DB::insert('slides', [
                    'title' => $title,
                    'image' => $image,
                    'link' => $link,
                    'sort_order' => $sortOrder,
                    'status' => $status,
                ]);
                $msg = '添加成功';
            }
        }
        $msgType = 'success';
    }
}

// 获取编辑数据
$editSlide = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $editSlide = DB::fetchOne("SELECT * FROM slides WHERE id=?", [$id]);
}

// 获取列表
$slides = DB::fetchAll("SELECT * FROM slides ORDER BY sort_order ASC, id ASC");
?>

<div class="admin-card">
    <h3><?php echo $editSlide ? '编辑轮播图' : '添加轮播图'; ?></h3>

    <?php if ($msg): ?>
    <div class="alert alert-<?php echo $msgType; ?>"><?php echo e($msg); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $editSlide['id'] ?? 0; ?>">

        <div class="form-row">
            <div class="form-item">
                <label>标题</label>
                <input type="text" name="title" value="<?php echo e($editSlide['title'] ?? ''); ?>" placeholder="轮播图标题">
            </div>
            <div class="form-item">
                <label>排序</label>
                <input type="number" name="sort_order" value="<?php echo $editSlide['sort_order'] ?? 0; ?>" style="width:100px;">
            </div>
        </div>

        <div class="form-item" style="margin-bottom:15px;">
            <label>链接地址</label>
            <input type="text" name="link" value="<?php echo e($editSlide['link'] ?? ''); ?>" placeholder="点击跳转的链接，如 article.php?id=1">
        </div>

        <div class="form-item" style="margin-bottom:15px;">
            <label>图片 *</label>
            <input type="file" name="image" accept="image/*">
            <?php if ($editSlide && $editSlide['image']): ?>
            <div style="margin-top:10px;">
                <img src="../<?php echo UPLOAD_URL . e($editSlide['image']); ?>" style="max-width:300px; max-height:200px; border:1px solid #eee; border-radius:4px; padding:5px;">
            </div>
            <?php endif; ?>
            <p style="font-size:12px; color:#999; margin-top:5px;">建议尺寸：580x320像素，用于首页轮播展示</p>
        </div>

        <div class="form-item" style="margin-bottom:20px;">
            <label style="display:flex; align-items:center; cursor:pointer;">
                <input type="checkbox" name="status" value="1" <?php echo ($editSlide && $editSlide['status']) || !$editSlide ? 'checked' : ''; ?> style="width:auto; margin-right:5px;"> 启用
            </label>
        </div>

        <button type="submit" class="btn btn-primary"><?php echo $editSlide ? '更新' : '添加'; ?></button>
        <?php if ($editSlide): ?>
        <a href="?action=list" class="btn btn-secondary" style="margin-left:10px;">取消编辑</a>
        <?php endif; ?>
    </form>
</div>

<div class="admin-card">
    <h3>轮播图列表</h3>

    <?php if ($slides): ?>
    <table>
        <thead>
            <tr>
                <th style="width:60px;">ID</th>
                <th style="width:120px;">图片</th>
                <th>标题</th>
                <th>链接</th>
                <th style="width:60px;">排序</th>
                <th style="width:60px;">状态</th>
                <th style="width:140px;">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($slides as $s): ?>
            <tr>
                <td><?php echo $s['id']; ?></td>
                <td>
                    <?php if ($s['image']): ?>
                    <img src="../<?php echo UPLOAD_URL . e($s['image']); ?>" style="width:100px; height:60px; object-fit:cover; border-radius:2px;">
                    <?php else: ?>
                    <span style="color:#999;">无图片</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e($s['title'] ?: '-'); ?></td>
                <td><?php echo e($s['link'] ?: '-'); ?></td>
                <td><?php echo $s['sort_order']; ?></td>
                <td>
                    <?php if ($s['status']): ?>
                    <span style="color:#52c41a;">启用</span>
                    <?php else: ?>
                    <span style="color:#ff4d4f;">禁用</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="?edit=<?php echo $s['id']; ?>" style="color:#1890ff; margin-right:10px;">编辑</a>
                    <a href="?toggle=<?php echo $s['id']; ?>" style="color:#faad14; margin-right:10px;">切换状态</a>
                    <a href="?delete=<?php echo $s['id']; ?>" style="color:#ff4d4f;" onclick="return confirm('确定删除？');">删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align:center; color:#999; padding:30px 0;">暂无轮播图，请添加</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>