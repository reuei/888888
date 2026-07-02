<?php
/**
 * Migrated from main_legacy/controller/app/controller/admin/Article.php
 */
namespace app\controller\admin;

/**
 * 总站后台 - 文章公告管理
 */
class Article extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/admin');
        if (!session('admin_user')) {
            redirect(url('login') . '?type=admin');
        }
    }

    public function index()
    {
        $category = input('category', '');
        $keyword = input('keyword', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];

        if ($category) {
            $where .= ' AND category = ?';
            $params[] = $category;
        }
        if ($keyword) {
            $where .= ' AND (title LIKE ? OR content LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_article WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT * FROM jz_article WHERE {$where} ORDER BY sort DESC, id DESC LIMIT {$offset}, {$pageSize}",
            $params
        );

        $categoryMap = [
            'notice' => '公告',
            'help' => '帮助',
        ];

        $this->assign('title', '文章公告');
        $this->assign('list', $list);
        $this->assign('categoryMap', $categoryMap);
        $this->assign('category', $category);
        $this->assign('keyword', $keyword);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/article/index');
    }

    public function create()
    {
        $categoryMap = [
            'notice' => '公告',
            'help' => '帮助',
        ];

        $this->assign('title', '发布公告');
        $this->assign('categoryMap', $categoryMap);
        $this->fetch('admin/article/create');
    }

    public function save()
    {
        $id = (int) input('id', 0);
        $title = trim(input('title', ''));
        $content = trim(input('content', ''));
        $category = input('category', 'notice');
        $sort = (int) input('sort', 0);
        $status = (int) input('status', 1);

        if (!$title) {
            json_error('请输入标题');
        }
        if (!$content) {
            json_error('请输入内容');
        }

        $data = [
            'title' => $title,
            'content' => $content,
            'category' => $category,
            'sort' => $sort,
            'status' => $status,
        ];

        if ($id) {
            Db::update('jz_article', $data, 'id = ?', [$id]);
            admin_log('article_update', ['id' => $id, 'title' => $title, 'category' => $category]);
            json_success('文章更新成功');
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            $newId = Db::insert('jz_article', $data);
            admin_log('article_create', ['id' => $newId, 'title' => $title, 'category' => $category]);
            json_success('文章发布成功');
        }
    }

    public function delete()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }
        Db::execute("DELETE FROM jz_article WHERE id = ?", [$id]);
        admin_log('article_delete', ['id' => $id]);
        json_success('文章已删除');
    }

    public function status()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 1);
        if (!$id) {
            json_error('参数错误');
        }
        Db::execute("UPDATE jz_article SET status = ? WHERE id = ?", [$status, $id]);
        admin_log('article_status', ['id' => $id, 'status' => $status]);
        json_success('状态更新成功');
    }
}
