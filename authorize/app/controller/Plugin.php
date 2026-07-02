<?php
/**
 * 插件市场控制器
 */

namespace app\controller;

use app\BaseController;
use app\Db;

class Plugin extends BaseController
{
    public function index()
    {
        $page = max(1, (int) input('page', 1));
        $pageSize = 12;
        $keyword = trim(input('keyword', ''));

        $where = 'p.status = 1';
        $params = [];
        if ($keyword) {
            $where .= ' AND (p.name LIKE ? OR p.description LIKE ?)';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM qef_plugin p WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query("SELECT p.*, u.nickname as author FROM qef_plugin p LEFT JOIN qef_user u ON p.user_id = u.id WHERE {$where} ORDER BY p.id DESC LIMIT {$offset}, {$pageSize}", $params);

        $this->assign('title', '插件市场');
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('plugin/index');
    }

    public function detail()
    {
        $id = (int) input('id', 0);
        $plugin = Db::fetch(
            "SELECT p.*, u.nickname as author FROM qef_plugin p LEFT JOIN qef_user u ON p.user_id = u.id WHERE p.id = ? AND p.status = 1",
            [$id]
        );
        if (!$plugin) {
            throw new \Exception('插件不存在或已下架');
        }

        $owned = false;
        $user = get_user();
        if ($user) {
            $owned = Db::fetch("SELECT id FROM qef_user_plugin WHERE user_id = ? AND plugin_id = ?", [$user['id'], $id]);
        }

        $this->assign('title', $plugin['name']);
        $this->assign('plugin', $plugin);
        $this->assign('owned', (bool) $owned);
        $this->fetch('plugin/detail');
    }
}
