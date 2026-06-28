<?php
/**
 * 总站后台 - 模板前端配置
 */
class Admin_Template extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->setLayout('layout/admin');
        if (!session('admin_user')) {
            redirect(url('login') . '?type=admin');
        }
        check_admin_role(['super', 'admin']);
    }

    /**
     * 通用配置读取
     */
    private function getConfig($keys)
    {
        $result = [];
        foreach ($keys as $key => $default) {
            $row = Db::fetch("SELECT cfg_value FROM jz_config WHERE cfg_key = ?", [$key]);
            $result[$key] = $row['cfg_value'] ?? $default;
        }
        return $result;
    }

    /**
     * 通用配置保存
     */
    public function save()
    {
        $group = input('group', 'template');
        if ($group !== 'template') {
            json_error('配置分组错误');
        }

        $data = input();
        unset($data['group']);

        foreach ($data as $key => $value) {
            $cfgKey = 'template_' . $key;
            Db::execute(
                "INSERT INTO jz_config (cfg_key, cfg_value, cfg_group, description) VALUES (?, ?, ?, '')
                 ON DUPLICATE KEY UPDATE cfg_value = VALUES(cfg_value), update_time = NOW()",
                [$cfgKey, $value, $group]
            );
        }

        admin_log('template_save', ['group' => $group, 'keys' => array_keys($data)]);
        json_success('配置保存成功');
    }

    /**
     * 首页模板
     */
    public function home()
    {
        $keys = [
            'template_home_seo_title' => '',
            'template_home_seo_keywords' => '',
            'template_home_seo_description' => '',
            'template_home_show_categories' => '1',
            'template_home_category_limit' => '12',
            'template_home_show_articles' => '1',
            'template_home_article_limit' => '5',
            'template_home_goods_order' => 'sold',
            'template_home_goods_limit' => '24',
            'template_home_show_stats' => '1',
            'template_home_stats_text' => '平台交易 安全快捷',
        ];
        $config = $this->getConfig($keys);

        $this->assign('title', '首页模板');
        $this->assign('config', $config);
        $this->fetch('admin/template/home');
    }

    /**
     * 购卡页模板
     */
    public function goods()
    {
        $keys = [
            'template_goods_seo_title' => '全部商品',
            'template_goods_page_size' => '24',
            'template_goods_default_sort' => 'sold',
            'template_goods_show_stock' => '1',
            'template_goods_show_sold' => '1',
            'template_goods_show_merchant' => '1',
            'template_goods_show_recommend' => '1',
            'template_goods_recommend_limit' => '6',
            'template_goods_empty_tip' => '暂无相关商品',
        ];
        $config = $this->getConfig($keys);

        $this->assign('title', '购卡页模板');
        $this->assign('config', $config);
        $this->fetch('admin/template/goods');
    }
}
