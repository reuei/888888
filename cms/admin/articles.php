<?php
$activeMenu = 'articles';
$pageTitle = '文章管理';
include __DIR__ . '/header.php';

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;
$catId = intval($_GET['cat_id'] ?? 0);
$keyword = trim($_GET['keyword'] ?? '');

$where = ['1=1'];
$params = [];
if ($catId) {
    $where[] = 'category_id=?';
    $params[] = $catId;
}
if ($keyword) {
    $where[] = '(title LIKE ? OR summary LIKE ?)';
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}
$whereStr = implode(' AND ', $where);

$total = DB::fetchOne("SELECT COUNT(*) as cnt FROM articles WHERE $whereStr", $params)['cnt'];
$articles = DB::fetchAll("SELECT * FROM articles WHERE $whereStr ORDER BY is_top DESC, id DESC LIMIT $offset, $perPage", $params);

$categories = DB::fetchAll("SELECT * FROM categories ORDER BY id ASC");
$catMap = [];
foreach ($categories as $c) {
    $catMap[$c['id']] = $c;
}

if (isset($_GET['delete']) && $_GET['delete']) {
    $id = intval($_GET['delete']);
    DB::delete('articles', 'id=?', [$id]);
    header('Location: articles.php?page=' . $page . ($catId ? '&cat_id=' . $catId : '') . ($keyword ? '&keyword=' . urlencode($keyword) : ''));
    exit;
}
?>

<div class="admin-card">
    <h3>文章管理</h3>
    <div style="margin-bottom:15px; display:flex; justify-content:space-between; align-items:center;">
        <form method="get" style="display:flex; gap:10px;">
            <select name="cat_id" style="padding:6px 10px; border:1px solid #ddd; border-radius:4px;">
                <option value="">全部栏目</option>
                <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c['id']; ?>" <?php echo $catId == $c['id'] ? 'selected' : ''; ?>>
                    <?php echo e($c['name']); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="keyword" value="<?php echo e($keyword); ?>" placeholder="搜索标题..." style="padding:6px 10px; border:1px solid #ddd; border-radius:4px; width:200px;">
            <button type="submit" class="btn-small btn-primary">搜索</button>
        </form>
        <a href="article_edit.php" class="btn-small btn-primary">+ 新建文章</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>标题</th>
                <th>栏目</th>
                <th>置顶</th>
                <th>状态</th>
                <th>浏览量</th>
                <th>发布时间</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articles as $art): ?>
            <tr>
                <td><?php echo $art['id']; ?></td>
                <td style="max-width:300px;"><?php echo e(truncateStr($art['title'], 35)); ?></td>
                <td><?php echo isset($catMap[$art['category_id']]) ? e($catMap[$art['category_id']]['name']) : '-'; ?></td>
                <td><?php echo $art['is_top'] ? '<span class="badge badge-danger">置顶</span>' : '-'; ?></td>
                <td>
                    <span class="badge <?php echo $art['status'] == 1 ? 'badge-success' : 'badge-warning'; ?>">
                        <?php echo $art['status'] == 1 ? '已发布' : '草稿'; ?>
                    </span>
                </td>
                <td><?php echo $art['views']; ?></td>
                <td><?php echo formatDate($art['publish_time']); ?></td>
                <td>
                    <a href="article_edit.php?id=<?php echo $art['id']; ?>" class="btn-small btn-default">编辑</a>
                    <a href="../article.php?id=<?php echo $art['id']; ?>" target="_blank" class="btn-small btn-default">查看</a>
                    <a href="articles.php?delete=<?php echo $art['id']; ?>&page=<?php echo $page; ?>" onclick="return confirm('确定删除？');" class="btn-small btn-danger">删除</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (!$articles): ?>
            <tr><td colspan="8" style="text-align:center; color:#999; padding:30px 0;">暂无文章</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php
        $url = 'articles.php?' . ($catId ? 'cat_id=' . $catId . '&' : '') . ($keyword ? 'keyword=' . urlencode($keyword) . '&' : '');
        echo paginate($total, $page, $perPage, $url);
        ?>
    </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
