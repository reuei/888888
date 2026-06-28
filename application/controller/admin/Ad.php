<?php
/**
 * 总站后台 - 广告位管理
 */
class Admin_Ad extends Controller
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
        $position = input('position', '');
        $status = input('status', '');
        $keyword = input('keyword', '');
        $page = max(1, (int) input('page', 1));
        $pageSize = 20;

        $where = '1=1';
        $params = [];

        if ($position) {
            $where .= ' AND position = ?';
            $params[] = $position;
        }
        if ($status !== '') {
            $where .= ' AND status = ?';
            $params[] = (int) $status;
        }
        if ($keyword) {
            $where .= ' AND title LIKE ?';
            $params[] = '%' . $keyword . '%';
        }

        $count = Db::fetch("SELECT COUNT(*) AS total FROM jz_ad WHERE {$where}", $params);
        $total = (int) ($count['total'] ?? 0);
        $totalPages = max(1, ceil($total / $pageSize));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $pageSize;

        $list = Db::query(
            "SELECT * FROM jz_ad WHERE {$where} ORDER BY sort DESC, id DESC LIMIT {$offset}, {$pageSize}",
            $params
        );

        $positionMap = [
            'home_banner' => '首页轮播',
            'home_top' => '首页顶部',
            'category_top' => '分类顶部',
            'goods_bottom' => '商品详情底部',
        ];

        $this->assign('title', '广告位管理');
        $this->assign('list', $list);
        $this->assign('positionMap', $positionMap);
        $this->assign('position', $position);
        $this->assign('status', $status);
        $this->assign('keyword', $keyword);
        $this->assign('page', $page);
        $this->assign('totalPages', $totalPages);
        $this->assign('total', $total);
        $this->fetch('admin/ad/index');
    }

    public function save()
    {
        $id = (int) input('id', 0);
        $title = trim(input('title', ''));
        $image = trim(input('image', ''));
        $link = trim(input('link', ''));
        $position = input('position', 'home_banner');
        $sort = (int) input('sort', 0);
        $status = (int) input('status', 1);

        if (!$title) {
            json_error('请输入广告标题');
        }
        if (!$image) {
            json_error('请输入广告图片 URL');
        }

        $data = [
            'title' => $title,
            'image' => $image,
            'link' => $link,
            'position' => $position,
            'sort' => $sort,
            'status' => $status,
        ];

        if ($id) {
            Db::update('jz_ad', $data, 'id = ?', [$id]);
            admin_log('ad_update', ['id' => $id, 'title' => $title, 'position' => $position]);
            json_success('广告更新成功');
        } else {
            $data['create_time'] = date('Y-m-d H:i:s');
            $newId = Db::insert('jz_ad', $data);
            admin_log('ad_create', ['id' => $newId, 'title' => $title, 'position' => $position]);
            json_success('广告添加成功');
        }
    }

    public function delete()
    {
        $id = (int) input('id', 0);
        if (!$id) {
            json_error('参数错误');
        }
        Db::execute("DELETE FROM jz_ad WHERE id = ?", [$id]);
        admin_log('ad_delete', ['id' => $id]);
        json_success('广告已删除');
    }

    public function status()
    {
        $id = (int) input('id', 0);
        $status = (int) input('status', 1);
        if (!$id) {
            json_error('参数错误');
        }
        Db::execute("UPDATE jz_ad SET status = ? WHERE id = ?", [$status, $id]);
        admin_log('ad_status', ['id' => $id, 'status' => $status]);
        json_success('状态更新成功');
    }
}
