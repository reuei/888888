<?php
/**
 * 后台管理 - 认证与配置
 */
require_once dirname(dirname(__FILE__)) . '/config.php';

// 认证检查
function check_auth() {
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }
}

// 登出
function do_logout() {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

// 保存数据处理
function handle_save() {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || empty($_POST['action'])) return;
    check_auth();

    $action = $_POST['action'];
    $data = load_site_data();

    if ($action === 'save_site') {
        $data['site'] = [
            'name' => trim($_POST['name'] ?? '语云科技'),
            'slogan' => trim($_POST['slogan'] ?? ''),
            'logo' => trim($_POST['logo'] ?? ''),
            'favicon' => trim($_POST['favicon'] ?? ''),
            'theme' => trim($_POST['theme'] ?? 'default'),
            'copyright' => trim($_POST['copyright'] ?? ''),
        ];
        save_site_data($data);
        echo json_encode(['code' => 0, 'msg' => '保存成功']);
        exit;
    }

    if ($action === 'save_contact') {
        $data['contact'] = [
            'phone' => trim($_POST['phone'] ?? ''),
            'phone_display' => trim($_POST['phone_display'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'wechat' => trim($_POST['wechat'] ?? ''),
            'qq' => trim($_POST['qq'] ?? ''),
            'qq_group' => trim($_POST['qq_group'] ?? ''),
        ];
        save_site_data($data);
        echo json_encode(['code' => 0, 'msg' => '保存成功']);
        exit;
    }

    if ($action === 'save_icp') {
        $data['icp'] = [
            'icp_number' => trim($_POST['icp_number'] ?? ''),
            'police_number' => trim($_POST['police_number'] ?? ''),
            'license_number' => trim($_POST['license_number'] ?? ''),
        ];
        save_site_data($data);
        echo json_encode(['code' => 0, 'msg' => '保存成功']);
        exit;
    }

    if ($action === 'save_slides') {
        $slides = [];
        if (!empty($_POST['slide_title']) && is_array($_POST['slide_title'])) {
            foreach ($_POST['slide_title'] as $i => $title) {
                if (empty(trim($title))) continue;
                $slides[] = [
                    'title' => trim($title),
                    'subtitle' => trim($_POST['slide_subtitle'][$i] ?? ''),
                    'desc' => trim($_POST['slide_desc'][$i] ?? ''),
                    'image' => trim($_POST['slide_image'][$i] ?? ''),
                    'link' => trim($_POST['slide_link'][$i] ?? '#'),
                    'color' => trim($_POST['slide_color'][$i] ?? '#1a73e8'),
                ];
            }
        }
        $data['slides'] = $slides;
        save_site_data($data);
        echo json_encode(['code' => 0, 'msg' => '保存成功']);
        exit;
    }

    if ($action === 'save_products') {
        $products = [];
        if (!empty($_POST['product_name']) && is_array($_POST['product_name'])) {
            foreach ($_POST['product_name'] as $i => $name) {
                if (empty(trim($name))) continue;
                $products[] = [
                    'name' => trim($name),
                    'desc' => trim($_POST['product_desc'][$i] ?? ''),
                    'icon' => trim($_POST['product_icon'][$i] ?? 'fa-server'),
                    'price' => trim($_POST['product_price'][$i] ?? ''),
                    'link' => trim($_POST['product_link'][$i] ?? 'products.php'),
                    'color' => trim($_POST['product_color'][$i] ?? '#1a73e8'),
                ];
            }
        }
        $data['products'] = $products;
        save_site_data($data);
        echo json_encode(['code' => 0, 'msg' => '保存成功']);
        exit;
    }

    if ($action === 'save_partners') {
        $partners = [];
        if (!empty($_POST['partner_name']) && is_array($_POST['partner_name'])) {
            foreach ($_POST['partner_name'] as $i => $name) {
                if (empty(trim($name))) continue;
                $partners[] = [
                    'name' => trim($name),
                    'logo' => trim($_POST['partner_logo'][$i] ?? ''),
                ];
            }
        }
        $data['partners'] = $partners;
        save_site_data($data);
        echo json_encode(['code' => 0, 'msg' => '保存成功']);
        exit;
    }

    if ($action === 'save_certs') {
        $certs = [];
        if (!empty($_POST['cert_name']) && is_array($_POST['cert_name'])) {
            foreach ($_POST['cert_name'] as $i => $name) {
                if (empty(trim($name))) continue;
                $certs[] = [
                    'name' => trim($name),
                    'image' => trim($_POST['cert_image'][$i] ?? ''),
                ];
            }
        }
        $data['certificates'] = $certs;
        save_site_data($data);
        echo json_encode(['code' => 0, 'msg' => '保存成功']);
        exit;
    }

    if ($action === 'save_employees') {
        $employees = [];
        if (!empty($_POST['emp_name']) && is_array($_POST['emp_name'])) {
            foreach ($_POST['emp_name'] as $i => $name) {
                if (empty(trim($name))) continue;
                $employees[] = [
                    'name' => trim($name),
                    'title' => trim($_POST['emp_title'][$i] ?? ''),
                    'avatar' => trim($_POST['emp_avatar'][$i] ?? ''),
                    'desc' => trim($_POST['emp_desc'][$i] ?? ''),
                ];
            }
        }
        $data['employees'] = $employees;
        save_site_data($data);
        echo json_encode(['code' => 0, 'msg' => '保存成功']);
        exit;
    }

    if ($action === 'save_testimonials') {
        $testimonials = [];
        if (!empty($_POST['tm_name']) && is_array($_POST['tm_name'])) {
            foreach ($_POST['tm_name'] as $i => $name) {
                if (empty(trim($name))) continue;
                $testimonials[] = [
                    'name' => trim($name),
                    'avatar' => trim($_POST['tm_avatar'][$i] ?? ''),
                    'content' => trim($_POST['tm_content'][$i] ?? ''),
                    'role' => trim($_POST['tm_role'][$i] ?? ''),
                ];
            }
        }
        $data['testimonials'] = $testimonials;
        save_site_data($data);
        echo json_encode(['code' => 0, 'msg' => '保存成功']);
        exit;
    }

    if ($action === 'save_news') {
        $news = [];
        if (!empty($_POST['news_title']) && is_array($_POST['news_title'])) {
            foreach ($_POST['news_title'] as $i => $title) {
                if (empty(trim($title))) continue;
                $news[] = [
                    'title' => trim($title),
                    'date' => trim($_POST['news_date'][$i] ?? ''),
                    'desc' => trim($_POST['news_desc'][$i] ?? ''),
                ];
            }
        }
        $data['news'] = $news;
        save_site_data($data);
        echo json_encode(['code' => 0, 'msg' => '保存成功']);
        exit;
    }

    if ($action === 'save_friendlinks') {
        $links = [];
        if (!empty($_POST['fl_name']) && is_array($_POST['fl_name'])) {
            foreach ($_POST['fl_name'] as $i => $name) {
                if (empty(trim($name))) continue;
                $links[] = [
                    'name' => trim($name),
                    'url' => trim($_POST['fl_url'][$i] ?? '#'),
                ];
            }
        }
        $data['friendlinks'] = $links;
        save_site_data($data);
        echo json_encode(['code' => 0, 'msg' => '保存成功']);
        exit;
    }

    if ($action === 'save_locations') {
        $locations = [];
        if (!empty($_POST['loc_region']) && is_array($_POST['loc_region'])) {
            foreach ($_POST['loc_region'] as $i => $region) {
                if (empty(trim($region))) continue;
                $cities = preg_split('/[,,、\s]+/', trim($_POST['loc_cities'][$i] ?? ''));
                $cities = array_filter($cities);
                $locations[] = [
                    'region' => trim($region),
                    'cities' => $cities,
                ];
            }
        }
        $data['locations'] = $locations;
        save_site_data($data);
        echo json_encode(['code' => 0, 'msg' => '保存成功']);
        exit;
    }

    if ($action === 'change_password') {
        $new_user = trim($_POST['admin_user'] ?? '');
        $new_pass = trim($_POST['admin_pass'] ?? '');
        if ($new_user && $new_pass) {
            // 写入配置文件
            $config_file = dirname(dirname(__FILE__)) . '/config.php';
            $config_content = file_get_contents($config_file);
            $config_content = preg_replace(
                "/define\('ADMIN_USER',\s*'[^']*'\);/",
                "define('ADMIN_USER', '" . addslashes($new_user) . "');",
                $config_content
            );
            $config_content = preg_replace(
                "/define\('ADMIN_PASS',\s*'[^']*'\);/",
                "define('ADMIN_PASS', '" . addslashes($new_pass) . "');",
                $config_content
            );
            file_put_contents($config_file, $config_content);
            echo json_encode(['code' => 0, 'msg' => '密码修改成功，请重新登录']);
        } else {
            echo json_encode(['code' => 1, 'msg' => '用户名和密码不能为空']);
        }
        exit;
    }

    if ($action === 'export_data') {
        $data = load_site_data();
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="yuyun-data-' . date('YmdHis') . '.json"');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    if ($action === 'reset_data') {
        // 不重置，保留默认
        $default = get_default_data();
        save_site_data($default);
        echo json_encode(['code' => 0, 'msg' => '已恢复默认数据']);
        exit;
    }
}

// 登录处理
function handle_login() {
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && !empty($_POST['login'])) {
        $user = trim($_POST['username'] ?? '');
        $pass = trim($_POST['password'] ?? '');
        if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_user'] = $user;
            header('Location: index.php');
            exit;
        }
        $GLOBALS['login_error'] = '用户名或密码错误';
    }
}
