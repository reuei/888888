<?php
// 基于文件的简易数据库操作类
class DB {
    
    private static function getPath($table) {
        return DATA_PATH . '/' . $table . '.json';
    }
    
    private static function read($table) {
        $file = self::getPath($table);
        if (!file_exists($file)) {
            $default = self::getDefault($table);
            file_put_contents($file, json_encode($default, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            return $default;
        }
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        if ($data === null) {
            return self::getDefault($table);
        }
        return $data;
    }
    
    private static function write($table, $data) {
        $file = self::getPath($table);
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($file, $json);
        return true;
    }
    
    private static function getDefault($table) {
        $defaults = [
            'settings' => [
                'site_name' => '森企动力',
                'site_title' => '森企动力 - 品牌数字化解决方案专家',
                'logo' => '/assets/images/logo.png',
                'favicon' => '/assets/images/favicon.ico',
                'icp' => '',
                'copyright' => '© 2024 森企动力. All Rights Reserved.',
                'contact_phone' => '400-888-8888',
                'contact_email' => 'info@senqidl.com',
                'contact_address' => '中国·深圳',
                'about_title' => '关于森企动力',
                'about_content' => '森企动力是一家专注于品牌数字化的服务公司，致力于为企业提供数字技术驱动的品牌成长解决方案。我们以"让数字化更有价值"为使命，帮助企业在数字时代实现品牌价值最大化。',
                'home_title' => '品牌数字化解决方案专家',
                'home_subtitle' => '提升企业的数字形象',
                'home_stats' => [
                    ['num' => '5000+', 'label' => '用户共同的选择'],
                    ['num' => '10+', 'label' => '年行业经验'],
                    ['num' => '200+', 'label' => '专业团队'],
                    ['num' => '100%', 'label' => '客户满意度']
                ]
            ],
            'slides' => [
                ['id' => 1, 'title' => '品牌数字化解决方案专家', 'subtitle' => '提升企业的数字形象', 'image' => '/assets/images/slide1.jpg', 'link' => '', 'sort' => 1, 'status' => 1],
                ['id' => 2, 'title' => '数字技术驱动的品牌成长', 'subtitle' => '品牌数字化成长的服务公司', 'image' => '/assets/images/slide2.jpg', 'link' => '', 'sort' => 2, 'status' => 1],
                ['id' => 3, 'title' => '智能外贸数字营销服务', 'subtitle' => '助力中国品牌全球化', 'image' => '/assets/images/slide3.jpg', 'link' => '', 'sort' => 3, 'status' => 1]
            ],
            'services' => [
                ['id' => 1, 'title' => '品牌网站建设', 'desc' => '专业的企业官网设计与开发', 'icon' => '🌐', 'sort' => 1, 'status' => 1],
                ['id' => 2, 'title' => 'SEO优化', 'desc' => '搜索引擎排名优化服务', 'icon' => '🔍', 'sort' => 2, 'status' => 1],
                ['id' => 3, 'title' => '外贸营销', 'desc' => '智能外贸数字营销解决方案', 'icon' => '🌍', 'sort' => 3, 'status' => 1],
                ['id' => 4, 'title' => '品牌设计', 'desc' => '品牌视觉形象设计', 'icon' => '🎨', 'sort' => 4, 'status' => 1],
                ['id' => 5, 'title' => '小程序开发', 'desc' => '微信小程序定制开发', 'icon' => '📱', 'sort' => 5, 'status' => 1],
                ['id' => 6, 'title' => '数字营销', 'desc' => '全渠道数字营销服务', 'icon' => '📈', 'sort' => 6, 'status' => 1]
            ],
            'cases' => [
                ['id' => 1, 'title' => '智能外贸数字营销平台', 'category' => '外贸营销', 'desc' => '为某知名外贸企业打造全流程数字营销平台', 'image' => '/assets/images/case1.jpg', 'content' => '项目背景：客户是一家领先的外贸企业，希望通过数字化手段提升全球市场竞争力。\n\n解决方案：我们为客户搭建了集AI、SEO、海关数据于一体的智能外贸数字营销平台，覆盖从获客到转化的全流程。\n\n项目成果：客户询盘量提升300%，转化率提升150%。', 'client' => '某外贸集团', 'date' => '2024-01-15', 'sort' => 1, 'status' => 1],
                ['id' => 2, 'title' => '品牌官网 redesign', 'category' => '网站建设', 'desc' => '为科技公司重塑品牌官网体验', 'image' => '/assets/images/case2.jpg', 'content' => '项目背景：客户品牌需要升级，官网形象陈旧。\n\n解决方案：全新设计风格，优化交互体验，响应式布局。\n\n项目成果：用户停留时长增加200%，跳出率降低40%。', 'client' => '某科技公司', 'date' => '2024-02-20', 'sort' => 2, 'status' => 1],
                ['id' => 3, 'title' => 'SEO全案优化', 'category' => 'SEO优化', 'desc' => '为电商平台提供全方位SEO服务', 'image' => '/assets/images/case3.jpg', 'content' => '项目背景：客户电商平台自然流量不足。\n\n解决方案：技术SEO+内容SEO+外链建设。\n\n项目成果：自然流量提升500%，核心词排名首页。', 'client' => '某电商平台', 'date' => '2024-03-10', 'sort' => 3, 'status' => 1],
                ['id' => 4, 'title' => '跨境电商品牌建设', 'category' => '品牌设计', 'desc' => '为跨境电商打造完整品牌体系', 'image' => '/assets/images/case4.jpg', 'content' => '项目背景：跨境电商品牌形象薄弱。\n\n解决方案：品牌VI设计、包装设计、视觉升级。\n\n项目成果：品牌认知度提升300%，复购率提升80%。', 'client' => '某跨境电商', 'date' => '2024-04-05', 'sort' => 4, 'status' => 1]
            ],
            'news' => [
                ['id' => 1, 'title' => '森企动力荣获2024年度最佳数字营销服务商', 'category' => '公司动态', 'content' => '在刚刚结束的行业评选中，森企动力凭借卓越的服务品质和创新能力，荣获"2024年度最佳数字营销服务商"奖项。这是对我们多年来深耕品牌数字化领域的肯定。', 'date' => '2024-06-01', 'image' => '/assets/images/news1.jpg', 'sort' => 1, 'status' => 1],
                ['id' => 2, 'title' => 'AI时代的外贸营销趋势', 'category' => '行业资讯', 'content' => '随着AI技术的不断发展，外贸营销正在经历前所未有的变革。本文探讨AI如何改变外贸营销的各个环节，包括客户开发、内容创作、数据分析等。', 'date' => '2024-05-20', 'image' => '/assets/images/news2.jpg', 'sort' => 2, 'status' => 1],
                ['id' => 3, 'title' => '品牌数字化转型的五个关键步骤', 'category' => '行业资讯', 'content' => '品牌数字化转型不是简单的技术升级，而是涉及战略、组织、文化的全方位变革。本文介绍品牌数字化转型的五个关键步骤。', 'date' => '2024-05-10', 'image' => '/assets/images/news3.jpg', 'sort' => 3, 'status' => 1]
            ],
            'admins' => [
                ['id' => 1, 'username' => 'admin', 'password' => 'senqidl201125', 'role' => 'superadmin', 'last_login' => '']
            ]
        ];
        return $defaults[$table] ?? [];
    }
    
    // 获取单条记录
    public static function getRow($table, $conditions = [], $orderBy = '', $orderDir = 'ASC') {
        $data = self::read($table);
        if (empty($conditions)) {
            if ($orderBy && isset($data[0][$orderBy])) {
                usort($data, function($a, $b) use ($orderBy, $orderDir) {
                    return $orderDir === 'DESC' ? ($b[$orderBy] ?? '') <=> ($a[$orderBy] ?? '') : ($a[$orderBy] ?? '') <=> ($b[$orderBy] ?? '');
                });
            }
            return $data[0] ?? null;
        }
        foreach ($data as $row) {
            foreach ($conditions as $key => $value) {
                if (!isset($row[$key]) || $row[$key] != $value) {
                    continue 2;
                }
            }
            return $row;
        }
        return null;
    }
    
    // 获取列表
    public static function getList($table, $conditions = [], $orderBy = '', $orderDir = 'ASC', $limit = 0, $offset = 0) {
        $data = self::read($table);
        $result = [];
        foreach ($data as $row) {
            $match = true;
            foreach ($conditions as $key => $value) {
                if (!isset($row[$key]) || $row[$key] != $value) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $result[] = $row;
            }
        }
        if ($orderBy && isset($result[0][$orderBy])) {
            usort($result, function($a, $b) use ($orderBy, $orderDir) {
                return $orderDir === 'DESC' ? ($b[$orderBy] ?? '') <=> ($a[$orderBy] ?? '') : ($a[$orderBy] ?? '') <=> ($b[$orderBy] ?? '');
            });
        }
        if ($limit > 0) {
            $result = array_slice($result, $offset, $limit);
        }
        return $result;
    }
    
    // 获取全部
    public static function getAll($table, $orderBy = '', $orderDir = 'ASC') {
        return self::getList($table, [], $orderBy, $orderDir);
    }
    
    // 插入
    public static function insert($table, $data) {
        $list = self::read($table);
        $maxId = 0;
        foreach ($list as $row) {
            if (isset($row['id']) && $row['id'] > $maxId) {
                $maxId = $row['id'];
            }
        }
        $data['id'] = $maxId + 1;
        $list[] = $data;
        self::write($table, $list);
        return $data['id'];
    }
    
    // 更新
    public static function update($table, $id, $data) {
        $list = self::read($table);
        foreach ($list as $key => $row) {
            if (isset($row['id']) && $row['id'] == $id) {
                $list[$key] = array_merge($row, $data);
                self::write($table, $list);
                return true;
            }
        }
        return false;
    }
    
    // 删除
    public static function delete($table, $id) {
        $list = self::read($table);
        foreach ($list as $key => $row) {
            if (isset($row['id']) && $row['id'] == $id) {
                unset($list[$key]);
                $list = array_values($list);
                self::write($table, $list);
                return true;
            }
        }
        return false;
    }
    
    // 按条件更新
    public static function updateByCondition($table, $conditions, $data) {
        $list = self::read($table);
        $updated = false;
        foreach ($list as $key => $row) {
            $match = true;
            foreach ($conditions as $k => $v) {
                if (!isset($row[$k]) || $row[$k] != $v) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $list[$key] = array_merge($row, $data);
                $updated = true;
            }
        }
        if ($updated) {
            self::write($table, $list);
        }
        return $updated;
    }
    
    // 获取设置
    public static function getSetting($key = '') {
        $settings = self::read('settings');
        if ($key) {
            return $settings[$key] ?? null;
        }
        return $settings;
    }
    
    // 更新设置
    public static function updateSettings($data) {
        return self::write('settings', $data);
    }
    
    // 统计
    public static function count($table, $conditions = []) {
        $data = self::read($table);
        if (empty($conditions)) {
            return count($data);
        }
        $count = 0;
        foreach ($data as $row) {
            $match = true;
            foreach ($conditions as $key => $value) {
                if (!isset($row[$key]) || $row[$key] != $value) {
                    $match = false;
                    break;
                }
            }
            if ($match) $count++;
        }
        return $count;
    }
    
    // 获取单条设置值
    public static function getSettingValue($key, $default = '') {
        $settings = self::read('settings');
        return $settings[$key] ?? $default;
    }
}
