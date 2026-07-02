<?php
/**
 * 前台首页控制器
 */
class Index extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->assign('currentUser', get_user());
    }

    public function index()
    {
        $products = Db::query("SELECT * FROM qef_product WHERE status = 1 ORDER BY sort DESC, id DESC LIMIT 8");
        $plugins = Db::query("SELECT p.*, u.nickname as author FROM qef_plugin p LEFT JOIN qef_user u ON p.user_id = u.id WHERE p.status = 1 ORDER BY p.id DESC LIMIT 8");
        $articles = Db::query("SELECT id, title, create_time FROM qef_article WHERE status = 1 AND category = 'notice' ORDER BY sort DESC, id DESC LIMIT 5");

        $stats = Db::fetch("SELECT COUNT(*) AS total_products FROM qef_product WHERE status = 1");
        $pluginStats = Db::fetch("SELECT COUNT(*) AS total_plugins FROM qef_plugin WHERE status = 1");

        $this->assign('title', site_config('site_name'));
        $this->assign('products', $products);
        $this->assign('plugins', $plugins);
        $this->assign('articles', $articles);
        $this->assign('stats', array_merge($stats, $pluginStats));
        $this->fetch('index/index');
    }

    public function article()
    {
        $id = (int) input('id', 0);
        $article = Db::fetch("SELECT * FROM qef_article WHERE id = ? AND status = 1", [$id]);
        if (!$article) {
            throw new Exception('公告不存在');
        }
        $this->assign('title', $article['title']);
        $this->assign('article', $article);
        $this->fetch('index/article');
    }
}
