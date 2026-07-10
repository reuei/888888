<?php
namespace Home\Controller;

use Framework\Controller;
use Framework\Database\Database;

class IndexController extends Controller
{
    protected $layout = 'home';

    public function index($request, $params = [])
    {
        $db = Database::getInstance();
        $categories = [];
        $goods = [];
        $notices = [];

        if ($db->isConnected()) {
            try {
                $categories = $db->table('category')
                    ->where('status', 1)
                    ->orderBy('sort', 'ASC')
                    ->get();
                $goods = $db->table('goods')
                    ->where('status', 1)
                    ->orderBy('id', 'DESC')
                    ->limit(12)
                    ->get();
                $notices = $db->table('article')
                    ->where('category', 'notice')
                    ->where('status', 1)
                    ->orderBy('id', 'DESC')
                    ->limit(5)
                    ->get();
            } catch (\Exception $e) {
                // 忽略数据库错误
            }
        }

        if (empty($categories)) {
            $categories = $this->getDefaultCategories();
        }
        if (empty($goods)) {
            $goods = $this->getDefaultGoods();
        }
        if (empty($notices)) {
            $notices = $this->getDefaultNotices();
        }

        $this->assign('categories', $categories);
        $this->assign('goods', $goods);
        $this->assign('notices', $notices);
        $this->assign('pageTitle', '首页');
        $this->view('home.index');
    }

    public function category($request, $params = [])
    {
        $id = (int) ($params['id'] ?? 0);
        $db = Database::getInstance();
        $category = ['id' => $id, 'name' => '分类'];
        $goods = [];

        if ($db->isConnected()) {
            try {
                $cat = $db->table('category')->find($id);
                if ($cat) {
                    $category = $cat;
                }
                $goods = $db->table('goods')
                    ->where('category_id', $id)
                    ->where('status', 1)
                    ->orderBy('id', 'DESC')
                    ->get();
            } catch (\Exception $e) {
            }
        }
        if (empty($goods)) {
            $goods = $this->getDefaultGoods();
        }
        $this->assign('category', $category);
        $this->assign('goods', $goods);
        $this->assign('pageTitle', $category['name']);
        $this->view('home.category');
    }

    public function goods($request, $params = [])
    {
        $id = (int) ($params['id'] ?? 0);
        $db = Database::getInstance();
        $good = null;

        if ($db->isConnected()) {
            try {
                $good = $db->table('goods')->find($id);
            } catch (\Exception $e) {
            }
        }
        if (!$good) {
            $defaults = $this->getDefaultGoods();
            $good = $defaults[0] ?? [];
            $good['id'] = $id;
        }
        $this->assign('good', $good);
        $this->assign('pageTitle', $good['name'] ?? '商品详情');
        $this->view('home.goods');
    }

    public function search($request, $params = [])
    {
        $keyword = $request->get('keyword', '');
        $db = Database::getInstance();
        $goods = [];
        if ($db->isConnected() && $keyword) {
            try {
                $goods = $db->table('goods')
                    ->whereRaw('name LIKE ?', ["%{$keyword}%"])
                    ->where('status', 1)
                    ->get();
            } catch (\Exception $e) {
            }
        }
        if (empty($goods)) {
            $goods = $this->getDefaultGoods();
        }
        $this->assign('keyword', $keyword);
        $this->assign('goods', $goods);
        $this->assign('pageTitle', '搜索：' . $keyword);
        $this->view('home.search');
    }

    public function notice($request, $params = [])
    {
        $db = Database::getInstance();
        $notices = [];
        if ($db->isConnected()) {
            try {
                $notices = $db->table('article')
                    ->where('category', 'notice')
                    ->where('status', 1)
                    ->orderBy('id', 'DESC')
                    ->get();
            } catch (\Exception $e) {
            }
        }
        if (empty($notices)) {
            $notices = $this->getDefaultNotices();
        }
        $this->assign('notices', $notices);
        $this->assign('pageTitle', '公告');
        $this->view('home.notice');
    }

    public function noticeDetail($request, $params = [])
    {
        $id = (int) ($params['id'] ?? 0);
        $db = Database::getInstance();
        $notice = null;
        if ($db->isConnected()) {
            try {
                $notice = $db->table('article')->find($id);
            } catch (\Exception $e) {
            }
        }
        if (!$notice) {
            $defaults = $this->getDefaultNotices();
            $notice = $defaults[0] ?? [];
        }
        $this->assign('notice', $notice);
        $this->assign('pageTitle', $notice['title'] ?? '公告详情');
        $this->view('home.notice_detail');
    }

    protected function getDefaultCategories()
    {
        return [
            ['id' => 1, 'name' => '游戏点卡', 'icon' => 'game'],
            ['id' => 2, 'name' => '视频会员', 'icon' => 'video'],
            ['id' => 3, 'name' => '音乐会员', 'icon' => 'music'],
            ['id' => 4, 'name' => '软件激活', 'icon' => 'software'],
            ['id' => 5, 'name' => '学习教育', 'icon' => 'edu'],
            ['id' => 6, 'name' => '生活服务', 'icon' => 'life'],
        ];
    }

    protected function getDefaultGoods()
    {
        return [
            ['id' => 1, 'name' => '腾讯视频VIP会员月卡', 'price' => 19.90, 'original_price' => 30.00, 'sold' => 2345, 'category_id' => 2],
            ['id' => 2, 'name' => '爱奇艺黄金会员季卡', 'price' => 45.00, 'original_price' => 68.00, 'sold' => 1876, 'category_id' => 2],
            ['id' => 3, 'name' => '网易云音乐黑胶年卡', 'price' => 88.00, 'original_price' => 158.00, 'sold' => 3421, 'category_id' => 3],
            ['id' => 4, 'name' => 'QQ音乐绿钻豪华月卡', 'price' => 12.80, 'original_price' => 18.00, 'sold' => 2156, 'category_id' => 3],
            ['id' => 5, 'name' => 'Steam充值卡100元', 'price' => 95.00, 'original_price' => 100.00, 'sold' => 987, 'category_id' => 1],
            ['id' => 6, 'name' => '王者荣耀点券1000', 'price' => 98.00, 'original_price' => 100.00, 'sold' => 4532, 'category_id' => 1],
            ['id' => 7, 'name' => 'WPS超级会员年卡', 'price' => 69.00, 'original_price' => 179.00, 'sold' => 1543, 'category_id' => 4],
            ['id' => 8, 'name' => '百度网盘超级会员月卡', 'price' => 25.00, 'original_price' => 30.00, 'sold' => 2876, 'category_id' => 4],
        ];
    }

    protected function getDefaultNotices()
    {
        return [
            ['id' => 1, 'title' => '欢迎使用玄武发卡 v1.0.5', 'content' => '欢迎使用全新的玄武发卡 v1.0.5，本次升级采用全新自研框架，带来更流畅的体验。', 'create_time' => date('Y-m-d H:i:s')],
            ['id' => 2, 'title' => '新用户首单优惠活动', 'content' => '即日起，新用户首次下单可享受立减5元优惠，活动时间有限。', 'create_time' => date('Y-m-d H:i:s', strtotime('-1 day'))],
            ['id' => 3, 'title' => '系统维护通知', 'content' => '系统将于每周日凌晨2:00-4:00进行例行维护。', 'create_time' => date('Y-m-d H:i:s', strtotime('-2 days'))],
        ];
    }
}
