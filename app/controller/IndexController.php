<?php
namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Session;
use think\facade\Cache;
use think\facade\Request;

class IndexController extends BaseController
{
    public function index()
    {
        $categories = [
            ['id' => 1, 'name' => '游戏点卡', 'icon' => 'game', 'count' => 128],
            ['id' => 2, 'name' => '视频会员', 'icon' => 'video', 'count' => 86],
            ['id' => 3, 'name' => '音乐会员', 'icon' => 'music', 'count' => 64],
            ['id' => 4, 'name' => '软件激活', 'icon' => 'software', 'count' => 92],
            ['id' => 5, 'name' => '社交账号', 'icon' => 'social', 'count' => 56],
            ['id' => 6, 'name' => '学习教育', 'icon' => 'education', 'count' => 73],
            ['id' => 7, 'name' => '生活服务', 'icon' => 'life', 'count' => 45],
            ['id' => 8, 'name' => '更多分类', 'icon' => 'more', 'count' => 0],
        ];
        
        $goods = [
            ['id' => 1, 'name' => '腾讯视频VIP会员月卡', 'price' => 19.90, 'original_price' => 30.00, 'sales' => 2345, 'image' => ''],
            ['id' => 2, 'name' => '爱奇艺黄金会员季卡', 'price' => 45.00, 'original_price' => 68.00, 'sales' => 1876, 'image' => ''],
            ['id' => 3, 'name' => '网易云音乐黑胶年卡', 'price' => 88.00, 'original_price' => 158.00, 'sales' => 3421, 'image' => ''],
            ['id' => 4, 'name' => 'QQ音乐绿钻豪华版月卡', 'price' => 12.80, 'original_price' => 18.00, 'sales' => 2156, 'image' => ''],
            ['id' => 5, 'name' => 'Steam充值卡100元', 'price' => 95.00, 'original_price' => 100.00, 'sales' => 987, 'image' => ''],
            ['id' => 6, 'name' => '王者荣耀点券1000', 'price' => 98.00, 'original_price' => 100.00, 'sales' => 4532, 'image' => ''],
            ['id' => 7, 'name' => 'WPS超级会员年卡', 'price' => 69.00, 'original_price' => 179.00, 'sales' => 1543, 'image' => ''],
            ['id' => 8, 'name' => '百度网盘超级会员月卡', 'price' => 25.00, 'original_price' => 30.00, 'sales' => 2876, 'image' => ''],
        ];
        
        $notices = [
            ['id' => 1, 'title' => '系统维护通知：7月15日凌晨2-4点系统升级', 'time' => '2026-07-08'],
            ['id' => 2, 'title' => '新用户首单立减5元，限时优惠活动进行中', 'time' => '2026-07-07'],
            ['id' => 3, 'title' => '新增腾讯视频会员秒发功能，下单即时到账', 'time' => '2026-07-06'],
        ];
        
        $this->assign('categories', $categories);
        $this->assign('goods', $goods);
        $this->assign('notices', $notices);
        return $this->fetch('index/index');
    }

    public function goods($id)
    {
        $good = [
            'id' => $id,
            'name' => '腾讯视频VIP会员月卡',
            'price' => 19.90,
            'original_price' => 30.00,
            'sales' => 2345,
            'stock' => 9999,
            'description' => '腾讯视频VIP会员，海量高清视频免费看，跳过广告，专享蓝光1080P画质。支持手机、电脑、平板三端通用。',
            'images' => ['', '', ''],
            'category' => '视频会员',
            'specs' => [
                ['name' => '月卡', 'price' => 19.90],
                ['name' => '季卡', 'price' => 45.00],
                ['name' => '年卡', 'price' => 168.00],
            ],
        ];
        
        $related = [
            ['id' => 2, 'name' => '爱奇艺黄金会员季卡', 'price' => 45.00, 'sales' => 1876],
            ['id' => 3, 'name' => '优酷视频会员年卡', 'price' => 99.00, 'sales' => 1234],
            ['id' => 4, 'name' => '芒果TV会员月卡', 'price' => 15.00, 'sales' => 987],
            ['id' => 5, 'name' => '哔哩哔哩大会员月卡', 'price' => 22.00, 'sales' => 2156],
        ];
        
        $this->assign('good', $good);
        $this->assign('related', $related);
        return $this->fetch('index/goods');
    }

    public function category($id)
    {
        $this->assign('category_id', $id);
        $goods = [
            ['id' => 1, 'name' => '腾讯视频VIP会员月卡', 'price' => 19.90, 'sales' => 2345],
            ['id' => 2, 'name' => '爱奇艺黄金会员季卡', 'price' => 45.00, 'sales' => 1876],
            ['id' => 3, 'name' => '优酷视频会员年卡', 'price' => 99.00, 'sales' => 1234],
            ['id' => 4, 'name' => '芒果TV会员月卡', 'price' => 15.00, 'sales' => 987],
        ];
        $this->assign('goods', $goods);
        return $this->fetch('index/category');
    }

    public function search()
    {
        $keyword = Request::param('keyword', '');
        $this->assign('keyword', $keyword);
        $goods = [
            ['id' => 1, 'name' => '腾讯视频VIP会员月卡', 'price' => 19.90, 'sales' => 2345],
        ];
        $this->assign('goods', $goods);
        return $this->fetch('index/search');
    }

    public function cartAdd()
    {
        return $this->jsonSuccess('加入购物车成功');
    }

    public function orderCreate()
    {
        $orderNo = generate_order_no();
        return $this->jsonSuccess('订单创建成功', ['order_no' => $orderNo]);
    }

    public function order($order_no)
    {
        $order = [
            'order_no' => $order_no,
            'goods_name' => '腾讯视频VIP会员月卡',
            'price' => 19.90,
            'quantity' => 1,
            'total' => 19.90,
            'status' => 0,
            'create_time' => date('Y-m-d H:i:s'),
        ];
        $this->assign('order', $order);
        return $this->fetch('index/order');
    }

    public function captcha()
    {
        return $this->jsonSuccess('验证码接口');
    }

    public function sliderCaptcha()
    {
        $data = slider_captcha_generate();
        return $this->jsonSuccess('获取成功', $data);
    }

    public function sliderVerify()
    {
        $token = Request::param('token', '');
        $x = Request::param('x', 0);
        $result = slider_captcha_verify($token, $x);
        if ($result) {
            return $this->jsonSuccess('验证通过');
        }
        return $this->jsonError('验证失败');
    }
}
